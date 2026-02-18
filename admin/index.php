<?php
/**
 * Admin Panel - Contact Submissions Management
 */

require_once '../config.php';

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

// Handle status updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $submission_id = intval($_POST['id'] ?? 0);
    $action = $_POST['action'];
    
    if ($submission_id > 0) {
        try {
            $stmt = $db->prepare("UPDATE contact_submissions SET status = ? WHERE id = ?");
            $stmt->execute([$action, $submission_id]);
            
            $_SESSION['flash']['success'] = 'Status updated successfully';
            header('Location: index.php');
            exit();
        } catch (Exception $e) {
            $_SESSION['flash']['error'] = 'Failed to update status';
        }
    }
}

// Get submissions with pagination
$page = max(1, intval($_GET['page'] ?? 1));
$per_page = 20;
$offset = ($page - 1) * $per_page;

// Filter by status
$status_filter = $_GET['status'] ?? 'all';

$where_clause = '';
$params = [];
if ($status_filter !== 'all') {
    $where_clause = "WHERE status = ?";
    $params[] = $status_filter;
}

// Get total count
$count_sql = "SELECT COUNT(*) as total FROM contact_submissions $where_clause";
$stmt = $db->prepare($count_sql);
$stmt->execute($params);
$total = $stmt->fetch()['total'];
$total_pages = ceil($total / $per_page);

// Get submissions
$sql = "SELECT * FROM contact_submissions $where_clause ORDER BY created_at DESC LIMIT $per_page OFFSET $offset";
$stmt = $db->prepare($sql);
$stmt->execute($params);
$submissions = $stmt->fetchAll();

// Get statistics
$stats = [
    'total' => $db->query("SELECT COUNT(*) as count FROM contact_submissions")->fetch()['count'],
    'new' => $db->query("SELECT COUNT(*) as count FROM contact_submissions WHERE status = 'new'")->fetch()['count'],
    'read' => $db->query("SELECT COUNT(*) as count FROM contact_submissions WHERE status = 'read'")->fetch()['count'],
    'replied' => $db->query("SELECT COUNT(*) as count FROM contact_submissions WHERE status = 'replied'")->fetch()['count']
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - <?php echo SITE_NAME; ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #f8f9fa;
            color: #333;
            line-height: 1.6;
        }
        
        .header {
            background: #171A32;
            color: white;
            padding: 1rem 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
        }
        
        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo {
            font-size: 1.5rem;
            font-weight: bold;
        }
        
        .nav-links {
            display: flex;
            gap: 2rem;
            align-items: center;
        }
        
        .nav-links a {
            color: white;
            text-decoration: none;
            transition: color 0.3s;
        }
        
        .nav-links a:hover {
            color: #1199CC;
        }
        
        .btn {
            background: #1199CC;
            color: white;
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: background 0.3s;
        }
        
        .btn:hover {
            background: #0d7a9e;
        }
        
        .btn-danger {
            background: #dc3545;
        }
        
        .btn-danger:hover {
            background: #c82333;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin: 2rem 0;
        }
        
        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: #1199CC;
        }
        
        .stat-label {
            color: #666;
            font-size: 0.9rem;
        }
        
        .filters {
            background: white;
            padding: 1rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 1rem;
        }
        
        .filter-group {
            display: flex;
            gap: 1rem;
            align-items: center;
            flex-wrap: wrap;
        }
        
        .filter-group select {
            padding: 0.5rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            background: white;
        }
        
        .submissions-table {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th, td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        
        th {
            background: #f8f9fa;
            font-weight: 600;
            color: #333;
        }
        
        tr:hover {
            background: #f8f9fa;
        }
        
        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
            text-transform: uppercase;
        }
        
        .status-new {
            background: #d4edda;
            color: #155724;
        }
        
        .status-read {
            background: #cce5ff;
            color: #004085;
        }
        
        .status-replied {
            background: #fff3cd;
            color: #856404;
        }
        
        .status-archived {
            background: #f8f9fa;
            color: #6c757d;
        }
        
        .pagination {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            margin: 2rem 0;
        }
        
        .pagination a, .pagination span {
            padding: 0.5rem 0.75rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            text-decoration: none;
            color: #333;
        }
        
        .pagination a:hover {
            background: #f8f9fa;
        }
        
        .pagination .current {
            background: #1199CC;
            color: white;
            border-color: #1199CC;
        }
        
        .alert {
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 1rem;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .actions {
            display: flex;
            gap: 0.5rem;
        }
        
        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.8rem;
        }
        
        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                gap: 1rem;
            }
            
            .filter-group {
                flex-direction: column;
                align-items: stretch;
            }
            
            .submissions-table {
                overflow-x: auto;
            }
            
            table {
                min-width: 600px;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="container">
            <div class="header-content">
                <div class="logo">Linire Mulima & Company - Admin</div>
                <nav class="nav-links">
                    <a href="index.php">Submissions</a>
                    <a href="logout.php">Logout</a>
                </nav>
            </div>
        </div>
    </header>

    <main class="container">
        <?php if ($message = get_flash_message('success')): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        
        <?php if ($message = get_flash_message('error')): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <h1 style="margin: 2rem 0;">Contact Submissions</h1>
        
        <!-- Statistics -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['total']; ?></div>
                <div class="stat-label">Total Submissions</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['new']; ?></div>
                <div class="stat-label">New</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['read']; ?></div>
                <div class="stat-label">Read</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['replied']; ?></div>
                <div class="stat-label">Replied</div>
            </div>
        </div>

        <!-- Filters -->
        <div class="filters">
            <form method="GET" class="filter-group">
                <label for="status">Filter by status:</label>
                <select name="status" id="status" onchange="this.form.submit()">
                    <option value="all" <?php echo $status_filter === 'all' ? 'selected' : ''; ?>>All</option>
                    <option value="new" <?php echo $status_filter === 'new' ? 'selected' : ''; ?>>New</option>
                    <option value="read" <?php echo $status_filter === 'read' ? 'selected' : ''; ?>>Read</option>
                    <option value="replied" <?php echo $status_filter === 'replied' ? 'selected' : ''; ?>>Replied</option>
                    <option value="archived" <?php echo $status_filter === 'archived' ? 'selected' : ''; ?>>Archived</option>
                </select>
            </form>
        </div>

        <!-- Submissions Table -->
        <div class="submissions-table">
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Service</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($submissions)): ?>
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 2rem;">No submissions found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($submissions as $submission): ?>
                            <tr>
                                <td><?php echo date('M j, Y H:i', strtotime($submission['created_at'])); ?></td>
                                <td><?php echo htmlspecialchars($submission['first_name'] . ' ' . $submission['last_name']); ?></td>
                                <td><?php echo htmlspecialchars($submission['email']); ?></td>
                                <td><?php echo htmlspecialchars($submission['phone']); ?></td>
                                <td><?php echo htmlspecialchars(ucfirst($submission['service'])); ?></td>
                                <td>
                                    <span class="status-badge status-<?php echo $submission['status']; ?>">
                                        <?php echo htmlspecialchars($submission['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="actions">
                                        <?php if ($submission['status'] !== 'read'): ?>
                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="action" value="read">
                                                <input type="hidden" name="id" value="<?php echo $submission['id']; ?>">
                                                <button type="submit" class="btn btn-sm">Mark Read</button>
                                            </form>
                                        <?php endif; ?>
                                        
                                        <?php if ($submission['status'] !== 'replied'): ?>
                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="action" value="replied">
                                                <input type="hidden" name="id" value="<?php echo $submission['id']; ?>">
                                                <button type="submit" class="btn btn-sm">Mark Replied</button>
                                            </form>
                                        <?php endif; ?>
                                        
                                        <a href="view.php?id=<?php echo $submission['id']; ?>" class="btn btn-sm">View</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="?page=<?php echo $page - 1; ?>&status=<?php echo $status_filter; ?>">«</a>
                <?php endif; ?>
                
                <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                    <?php if ($i == $page): ?>
                        <span class="current"><?php echo $i; ?></span>
                    <?php else: ?>
                        <a href="?page=<?php echo $i; ?>&status=<?php echo $status_filter; ?>"><?php echo $i; ?></a>
                    <?php endif; ?>
                <?php endfor; ?>
                
                <?php if ($page < $total_pages): ?>
                    <a href="?page=<?php echo $page + 1; ?>&status=<?php echo $status_filter; ?>">»</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </main>
</body>
</html>
