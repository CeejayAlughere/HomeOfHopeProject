<?php
require_once 'config.php';
require_once 'auth.php';
require_once 'helpers.php';

AdminAuth::requireLogin();

$pdo = getDBConnection();

// Get search query
$searchQuery = $_GET['search'] ?? '';

// Build query
$query = 'SELECT * FROM newsletter_subscribers WHERE 1=1';
$params = [];

if (!empty($searchQuery)) {
    $query .= ' AND email LIKE ?';
    $params[] = '%' . $searchQuery . '%';
}

$query .= ' ORDER BY subscribed_at DESC';

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$subscribers = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Newsletter - Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/admin-style.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="dashboard.php">
                <i class="fas fa-heart text-danger"></i> Home of Hope Admin Dashboard
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <span class="nav-link">Welcome, <strong><?php echo htmlspecialchars(AdminAuth::getCurrentUsername()); ?></strong></span>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid mt-4">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-lg-2">
                <div class="admin-sidebar">
                    <div class="sidebar-menu">
                        <a href="dashboard.php" class="menu-item">
                            <i class="fas fa-chart-line"></i> Dashboard
                        </a>
                        <a href="donations.php" class="menu-item">
                            <i class="fas fa-donate"></i> Donations
                        </a>
                        <a href="volunteers.php" class="menu-item">
                            <i class="fas fa-users"></i> Volunteers
                        </a>
                        <a href="contacts.php" class="menu-item">
                            <i class="fas fa-envelope"></i> Contacts
                        </a>
                        <a href="newsletter.php" class="menu-item active">
                            <i class="fas fa-newspaper"></i> Newsletter
                        </a>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-lg-10">
                <div class="content-area">
                    <?php displayAlert(); ?>
                    
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h1 class="page-title">
                            <i class="fas fa-newspaper"></i> Newsletter Subscribers
                        </h1>
                        <a href="handlers/export_csv.php?table=newsletter_subscribers" class="btn btn-success" title="Export to CSV">
                            <i class="fas fa-download"></i> Export CSV
                        </a>
                    </div>

                    <!-- Stats -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="stat-card">
                                <div class="stat-content">
                                    <h6>Total Subscribers</h6>
                                    <h3><?php echo count($subscribers); ?></h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filters -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <form method="GET" class="row g-3">
                                <div class="col-md-6">
                                    <input 
                                        type="text" 
                                        name="search" 
                                        class="form-control" 
                                        placeholder="Search by email"
                                        value="<?php echo htmlspecialchars($searchQuery); ?>"
                                    >
                                </div>
                                <div class="col-md-6">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search"></i> Search
                                    </button>
                                    <a href="newsletter.php" class="btn btn-secondary">Reset</a>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Subscribers Table -->
                    <div class="card">
                        <div class="table-responsive">
                            <?php if (!empty($subscribers)): ?>
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Email</th>
                                            <th>Subscribed Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($subscribers as $subscriber): ?>
                                            <tr>
                                                <td>#<?php echo $subscriber['id']; ?></td>
                                                <td>
                                                    <a href="mailto:<?php echo htmlspecialchars($subscriber['email']); ?>">
                                                        <?php echo htmlspecialchars($subscriber['email']); ?>
                                                    </a>
                                                </td>
                                                <td><?php echo formatDate($subscriber['subscribed_at']); ?></td>
                                                <td>
                                                    <form method="POST" action="handlers/delete_subscriber.php" style="display:inline;">
                                                        <input type="hidden" name="id" value="<?php echo $subscriber['id']; ?>">
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure?')">
                                                            <i class="fas fa-trash"></i> Remove
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php else: ?>
                                <div class="alert alert-info m-3 mb-0">No subscribers found.</div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="card mt-4">
                        <div class="card-header bg-light">
                            <h5 class="card-title mb-0">Quick Actions</h5>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info mb-0">
                                <i class="fas fa-info-circle"></i>
                                <strong>Bulk Email Feature</strong> - To send emails to all subscribers, copy all email addresses and use your preferred email service (Gmail, Mailchimp, etc.)
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/admin-script.js"></script>
</body>
</html>
