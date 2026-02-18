# Linire Mulima & Company - Professional Law Firm Website

A modern, responsive HTML website with PHP backend for Linire Mulima & Company, a premier commercial law firm based in Lusaka, Zambia.

## 🏢 About the Firm

Linire Mulima & Company is a leading commercial law practice committed to empowering businesses with practical, strategic, and forward-thinking legal solutions. With over 27 years of experience, we specialize in:

- Corporate & Business Advisory
- Commercial Law
- Construction Law
- Corporate Governance
- Regulatory & Compliance
- Banking & Finance Law
- Property Law & Conveyancing
- Employment & Labour Law
- Dispute Resolution

## 🌐 Website Features

### Design & User Experience
- **Modern, Professional Design**: Clean, sophisticated layout reflecting the firm's prestige
- **Fully Responsive**: Optimized for desktop, tablet, and mobile devices
- **Accessibility**: WCAG 2.1 compliant with keyboard navigation and screen reader support
- **Fast Loading**: Optimized performance with lazy loading and efficient code
- **Professional Images**: High-quality Unsplash free stock photos throughout

### Backend Functionality
- **Contact Form Database**: All submissions stored in MySQL database
- **Email Notifications**: Automatic emails sent to linire@liniremulima.com
- **Admin Panel**: Secure admin area to manage contact submissions
- **Form Validation**: Client-side and server-side validation with security
- **Auto-Reply**: Automatic confirmation emails to clients

### Content Sections
- **Hero Section**: Compelling introduction with statistics and call-to-action
- **About Us**: Firm overview with values and achievements
- **Practice Areas**: Detailed service descriptions with 9 major practice areas
- **Team Profiles**: Professional team member presentations with credentials
- **Case Studies**: Recent success stories and client results
- **Testimonials**: Client feedback and endorsements
- **Legal Insights**: Blog posts and legal updates
- **Contact Form**: Interactive consultation request with validation

### Technical Features
- **Semantic HTML5**: Proper structure for SEO and accessibility
- **Modern CSS3**: Custom properties, Grid, Flexbox, animations
- **Vanilla JavaScript**: No dependencies, fast and secure
- **PHP Backend**: Secure form processing and database management
- **MySQL Database**: Efficient data storage and retrieval
- **Security Features**: Input sanitization, CSRF protection, secure sessions

## 🎨 Design System

### Brand Colors
- **Primary**: #171A32 (Deep Navy)
- **Secondary**: #1199CC (Professional Blue)
- **Accent**: #448DAF (Light Blue)
- **Dark**: #3F4151 (Charcoal)
- **Gray**: #81828B (Medium Gray)
- **Light Gray**: #CDD3D8 (Light Gray)

### Typography
- **Headings**: Playfair Display (serif) - Professional and authoritative
- **Body**: Inter (sans-serif) - Clean and readable

### Layout
- **Container**: Max-width 1200px, centered
- **Grid System**: CSS Grid and Flexbox for responsive layouts
- **Spacing**: Consistent margin/padding scale
- **Responsive Breakpoints**: 1024px, 768px, 480px

## 📁 Project Structure

```
linire/
├── index.html              # Main HTML file
├── css/
│   └── style.css          # Complete stylesheet with animations
├── js/
│   └── script.js          # Interactive JavaScript
├── images/
│   ├── logo.svg           # Company logo (SVG)
│   ├── logo-white.svg     # White logo variant
│   └── [Unsplash images]  # Professional stock photos
├── admin/
│   ├── index.php          # Admin panel - view submissions
│   ├── login.php          # Admin login page
│   └── logout.php         # Admin logout
├── config.php             # Database and application configuration
├── contact-handler.php    # Contact form processing
├── database-setup.sql     # Database creation script
├── logs/                  # Error logs directory
└── README.md              # This file
```

## 🚀 Getting Started

### Prerequisites
- Modern web browser (Chrome, Firefox, Safari, Edge)
- Local web server (Apache/Nginx with PHP)
- MySQL database
- PHP 7.4+ with PDO extension
- SMTP server for email sending

### Installation

1. **Database Setup**
   ```bash
   # Import the database structure
   mysql -u root -p < database-setup.sql
   ```

2. **Configuration**
   - Update `config.php` with your database credentials
   - Configure SMTP settings for email sending
   - Update email addresses as needed

3. **Upload Files**
   - Upload all files to your web server
   - Ensure the `logs/` directory is writable
   - Set appropriate file permissions

4. **Admin Access**
   - Visit `/admin/` to access the admin panel
   - Default login: username `admin`, password `admin123`
   - Change the default password immediately

### Local Development
```bash
# Using PHP built-in server
php -S localhost:8000

# Access the website
http://localhost:8000

# Access admin panel
http://localhost:8000/admin/
```

## ⚙️ Configuration

### Database Settings
Update these values in `config.php`:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'linire_website');
define('DB_USER', 'your_db_user');
define('DB_PASS', 'your_db_password');
```

### Email Configuration
Configure SMTP settings in `config.php`:
```php
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_USERNAME', 'linire@liniremulima.com');
define('SMTP_PASSWORD', 'your-app-password');
```

### Contact Information
Update contact details in `index.html`:
- Phone: +260 977 450621
- Email: linire@liniremulima.com
- Address: Lot 3052/M/E Zambezi Road Extension, Foxdale, Lusaka, Zambia

## 📧 Email Functionality

### Features
- **Client Notifications**: Emails sent to linire@liniremulima.com for new submissions
- **Auto-Reply**: Automatic confirmation emails to clients
- **HTML Templates**: Professional email templates with firm branding
- **Error Handling**: Graceful handling of email failures

### Email Templates
- **Notification Email**: Sent to admin with complete submission details
- **Auto-Reply**: Sent to client with confirmation and contact information

## 🔐 Admin Panel

### Features
- **Dashboard**: Overview of submission statistics
- **Submission Management**: View, filter, and update submission status
- **Status Tracking**: New → Read → Replied → Archived
- **Pagination**: Efficient handling of large numbers of submissions
- **Security**: Session-based authentication with secure login

### Access
- URL: `/admin/`
- Default credentials: admin / admin123
- **Important**: Change default password immediately

## 🎯 SEO Optimization

### Meta Tags
- **Title**: Optimized for search engines
- **Description**: Compelling firm description
- **Keywords**: Relevant legal practice terms
- **Open Graph**: Social media sharing optimization

### Structured Data
- **Schema.org**: Law Firm markup for rich snippets
- **Local Business**: Enhanced local search presence
- **Contact Information**: Structured contact details

### Performance
- **Image Optimization**: Compressed images with proper dimensions
- **Lazy Loading**: Images load as needed
- **Minification**: CSS and JS can be minified for production
- **Caching**: Browser caching headers configured

## 🔧 Customization

### Adding New Practice Areas
1. Update the `<select>` options in the contact form
2. Add corresponding enum values to the database
3. Update email templates if needed

### Customizing Images
- Replace Unsplash URLs with your own professional photos
- Ensure images are optimized for web (WebP format recommended)
- Maintain consistent aspect ratios

### Modifying Colors
Update CSS custom properties in `css/style.css`:
```css
:root {
    --primary-color: #your-color;
    --secondary-color: #your-color;
    /* Add other color variables */
}
```

## 📱 Browser Support

- **Modern Browsers**: Chrome 60+, Firefox 55+, Safari 12+, Edge 79+
- **Mobile**: iOS Safari 12+, Chrome Mobile 60+
- **Graceful Degradation**: Works in older browsers with reduced functionality

## 🔒 Security Features

- **Input Sanitization**: All user inputs are sanitized and validated
- **SQL Injection Prevention**: Using prepared statements with PDO
- **XSS Protection**: Output escaping and Content Security Policy
- **CSRF Protection**: Token-based CSRF protection (ready to implement)
- **Secure Sessions**: HttpOnly cookies and secure session handling
- **Password Hashing**: Bcrypt for secure password storage

## 📊 Admin Panel Features

### Statistics Dashboard
- Total submissions count
- New submissions (unread)
- Read submissions
- Replied submissions
- Filter by status

### Submission Management
- View all contact form submissions
- Update submission status
- Filter by date and status
- Pagination for large datasets
- Responsive design for mobile access

### Security
- Secure login with password verification
- Session-based authentication
- Automatic logout on session expiration
- Protection against unauthorized access

## 📞 Support & Contact

For technical support or inquiries:
- **Email**: linire@liniremulima.com
- **Phone**: +260 977 450621
- **Address**: Lot 3052/M/E Zambezi Road Extension, Foxdale, Lusaka, Zambia

## 📄 License

This website design and code is proprietary to Linire Mulima & Company. All rights reserved.

### Legal Notice
This website is for informational purposes only and does not constitute legal advice. No attorney-client relationship is formed by accessing this website.

---

**Linire Mulima & Company**  
*Integrity. Excellence. Results.*  
© 2023 All Rights Reserved
#   l i n r e m u l i m a  
 