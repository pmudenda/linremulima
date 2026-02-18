<?php
/**
 * Contact Form Handler
 * Processes contact form submissions and sends emails
 */

require_once 'config.php';

// Set response header
header('Content-Type: application/json');

// Initialize response array
$response = [
    'success' => false,
    'message' => '',
    'errors' => []
];

try {
    // Only accept POST requests
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }
    
    // Validate CSRF token (if implemented)
    // if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    //     throw new Exception('Invalid CSRF token');
    // }
    
    // Get and sanitize form data
    $first_name = sanitize_input($_POST['firstName'] ?? '');
    $last_name = sanitize_input($_POST['lastName'] ?? '');
    $email = sanitize_input($_POST['email'] ?? '');
    $phone = sanitize_input($_POST['phone'] ?? '');
    $service = sanitize_input($_POST['service'] ?? '');
    $message = sanitize_input($_POST['message'] ?? '');
    $consent = isset($_POST['consent']) ? 1 : 0;
    
    // Validate required fields
    $errors = [];
    
    if (empty($first_name)) {
        $errors['firstName'] = 'First name is required';
    } elseif (strlen($first_name) < 2) {
        $errors['firstName'] = 'First name must be at least 2 characters';
    }
    
    if (empty($last_name)) {
        $errors['lastName'] = 'Last name is required';
    } elseif (strlen($last_name) < 2) {
        $errors['lastName'] = 'Last name must be at least 2 characters';
    }
    
    if (empty($email)) {
        $errors['email'] = 'Email address is required';
    } elseif (!validate_email($email)) {
        $errors['email'] = 'Please enter a valid email address';
    }
    
    if (empty($phone)) {
        $errors['phone'] = 'Phone number is required';
    } elseif (!preg_match('/^[\d\s\-\+\(\)]+$/', $phone) || strlen(preg_replace('/\D/', '', $phone)) < 10) {
        $errors['phone'] = 'Please enter a valid phone number';
    }
    
    if (empty($service)) {
        $errors['service'] = 'Please select a service';
    }
    
    if (empty($message)) {
        $errors['message'] = 'Message is required';
    } elseif (strlen($message) < 10) {
        $errors['message'] = 'Message must be at least 10 characters long';
    }
    
    if (!$consent) {
        $errors['consent'] = 'You must agree to the privacy policy';
    }
    
    // If there are validation errors, return them
    if (!empty($errors)) {
        $response['errors'] = $errors;
        $response['message'] = 'Please correct the errors in the form';
        echo json_encode($response);
        exit;
    }
    
    // Save to database
    $stmt = $db->prepare("
        INSERT INTO contact_submissions 
        (first_name, last_name, email, phone, service, message, consent) 
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    
    $stmt->execute([$first_name, $last_name, $email, $phone, $service, $message, $consent]);
    $submission_id = $db->lastInsertId();
    
    // Send email notification
    $email_sent = send_notification_email($first_name, $last_name, $email, $phone, $service, $message);
    
    // Send auto-reply to client
    $auto_reply_sent = send_auto_reply($first_name, $email, $service);
    
    // Log the submission
    error_log("Contact form submission #{$submission_id}: {$email} - {$service}");
    
    // Return success response
    $response['success'] = true;
    $response['message'] = 'Thank you for your inquiry. We will contact you within 24 hours.';
    $response['submission_id'] = $submission_id;
    
} catch (Exception $e) {
    error_log("Contact form error: " . $e->getMessage());
    $response['message'] = 'An error occurred. Please try again later.';
}

echo json_encode($response);

/**
 * Send notification email to admin
 */
function send_notification_email($first_name, $last_name, $email, $phone, $service, $message) {
    try {
        $to = SITE_EMAIL;
        $subject = "New Contact Form Submission - {$first_name} {$last_name}";
        
        $service_names = [
            'corporate' => 'Corporate & Business Advisory',
            'commercial' => 'Commercial Law',
            'construction' => 'Construction Law',
            'governance' => 'Corporate Governance',
            'regulatory' => 'Regulatory & Compliance',
            'banking' => 'Banking & Finance Law',
            'property' => 'Property Law & Conveyancing',
            'employment' => 'Employment & Labour Law',
            'dispute' => 'Dispute Resolution',
            'other' => 'Other'
        ];
        
        $service_name = $service_names[$service] ?? $service;
        
        $body = "
        <html>
        <head>
            <title>New Contact Form Submission</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #171A32; color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; background: #f9f9f9; }
                .field { margin-bottom: 15px; }
                .label { font-weight: bold; color: #171A32; }
                .footer { text-align: center; padding: 20px; font-size: 12px; color: #666; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>Linire Mulima & Company</h1>
                    <p>New Contact Form Submission</p>
                </div>
                <div class='content'>
                    <div class='field'>
                        <span class='label'>Name:</span> {$first_name} {$last_name}
                    </div>
                    <div class='field'>
                        <span class='label'>Email:</span> {$email}
                    </div>
                    <div class='field'>
                        <span class='label'>Phone:</span> {$phone}
                    </div>
                    <div class='field'>
                        <span class='label'>Service:</span> {$service_name}
                    </div>
                    <div class='field'>
                        <span class='label'>Message:</span><br>
                        " . nl2br(htmlspecialchars($message)) . "
                    </div>
                    <div class='field'>
                        <span class='label'>Submitted:</span> " . date('Y-m-d H:i:s') . "
                    </div>
                </div>
                <div class='footer'>
                    <p>This email was sent from the contact form on " . SITE_NAME . "</p>
                </div>
            </div>
        </body>
        </html>
        ";
        
        $headers = [
            'MIME-Version: 1.0',
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . SITE_NAME . ' <' . SITE_EMAIL . '>',
            'Reply-To: ' . $first_name . ' ' . $last_name . ' <' . $email . '>'
        ];
        
        return mail($to, $subject, $body, implode("\r\n", $headers));
        
    } catch (Exception $e) {
        error_log("Failed to send notification email: " . $e->getMessage());
        return false;
    }
}

/**
 * Send auto-reply to client
 */
function send_auto_reply($first_name, $email, $service) {
    try {
        $to = $email;
        $subject = "Thank you for contacting Linire Mulima & Company";
        
        $body = "
        <html>
        <head>
            <title>Thank you for contacting us</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: #171A32; color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; background: #f9f9f9; }
                .footer { text-align: center; padding: 20px; font-size: 12px; color: #666; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>Linire Mulima & Company</h1>
                    <p>Integrity. Excellence. Results.</p>
                </div>
                <div class='content'>
                    <h2>Thank You for Your Inquiry</h2>
                    <p>Dear {$first_name},</p>
                    <p>Thank you for contacting Linire Mulima & Company. We have received your message regarding our {$service} services.</p>
                    <p>Our team will review your inquiry and get back to you within 24 hours. For urgent matters, please call us directly at +260 977 450621.</p>
                    <p><strong>Contact Information:</strong><br>
                    Phone: +260 977 450621<br>
                    Email: linire@liniremulima.com<br>
                    Address: Lot 3052/M/E Zambezi Road Extension, Foxdale, Lusaka, Zambia</p>
                    <p>We look forward to assisting you with your legal needs.</p>
                    <p>Best regards,<br>
                    The Team at Linire Mulima & Company</p>
                </div>
                <div class='footer'>
                    <p>This is an automated message. Please do not reply to this email.</p>
                    <p>&copy; " . date('Y') . " Linire Mulima & Company. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>
        ";
        
        $headers = [
            'MIME-Version: 1.0',
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . SITE_NAME . ' <' . SITE_EMAIL . '>'
        ];
        
        return mail($to, $subject, $body, implode("\r\n", $headers));
        
    } catch (Exception $e) {
        error_log("Failed to send auto-reply: " . $e->getMessage());
        return false;
    }
}
?>
