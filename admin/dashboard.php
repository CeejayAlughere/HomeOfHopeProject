<?php
require_once 'config.php';
require_once 'auth.php';
require_once 'helpers.php';

AdminAuth::requireLogin();

$stats = getDatabaseStats();
$recentSubmissions = getRecentSubmissions(6);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Home of Hope</title>
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
        <!-- Sidebar -->
        <div class="row">
            <div class="col-lg-2">
                <div class="admin-sidebar">
                    <div class="sidebar-menu">
                        <a href="dashboard.php" class="menu-item active">
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
                        <a href="newsletter.php" class="menu-item">
                            <i class="fas fa-newspaper"></i> Newsletter
                        </a>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-lg-10">
                <div class="content-area">
                    <?php displayAlert(); ?>
                    
                    <h1 class="page-title mb-4">
                        <i class="fas fa-chart-line"></i> Dashboard
                    </h1>

                    <!-- Stats Grid -->
                    <div class="row mb-4">
                        <div class="col-md-3 mb-3">
                            <div class="stat-card">
                                <div class="stat-icon bg-success">
                                    <i class="fas fa-donate"></i>
                                </div>
                                <div class="stat-content">
                                    <h6>Total Donations</h6>
                                    <h3><?php echo $stats['total_donations']; ?></h3>
                                    <small class="text-muted">Amount: <?php echo formatCurrency($stats['total_amount']); ?></small>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3 mb-3">
                            <div class="stat-card">
                                <div class="stat-icon bg-info">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                                <div class="stat-content">
                                    <h6>Verified Donations</h6>
                                    <h3><?php echo $stats['verified_donations']; ?></h3>
                                    <small class="text-muted"><?php echo round(($stats['verified_donations']/$stats['total_donations'])*100, 1); ?>% of total</small>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3 mb-3">
                            <div class="stat-card">
                                <div class="stat-icon bg-primary">
                                    <i class="fas fa-users"></i>
                                </div>
                                <div class="stat-content">
                                    <h6>Total Volunteers</h6>
                                    <h3><?php echo $stats['total_volunteers']; ?></h3>
                                    <small class="text-muted"><?php echo $stats['contacted_volunteers']; ?> contacted</small>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3 mb-3">
                            <div class="stat-card">
                                <div class="stat-icon bg-warning">
                                    <i class="fas fa-envelope"></i>
                                </div>
                                <div class="stat-content">
                                    <h6>Submissions</h6>
                                    <h3><?php echo $stats['total_contacts']; ?></h3>
                                    <small class="text-muted"><?php echo $stats['total_subscribers']; ?> subscribers</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Submissions Table -->
                    <div class="card">
                        <div class="card-header bg-light">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-history"></i> Recent Submissions
                            </h5>
                        </div>
                        <div class="card-body p-0">
                            <?php if (!empty($recentSubmissions)): ?>
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead>
                                            <tr>
                                                <th>Type</th>
                                                <th>Name</th>
                                                <th>Email</th>
                                                <th>Data</th>
                                                <th>Date</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($recentSubmissions as $submission): ?>
                                                <tr>
                                                    <td>
                                                        <?php
                                                        $typeIcons = [
                                                            'donation' => '<span class="badge bg-success">Donation</span>',
                                                            'volunteer' => '<span class="badge bg-primary">Volunteer</span>',
                                                            'contact' => '<span class="badge bg-warning text-dark">Contact</span>'
                                                        ];
                                                        echo $typeIcons[$submission['type']];
                                                        ?>
                                                    </td>
                                                    <td><?php echo htmlspecialchars($submission['name']); ?></td>
                                                    <td><?php echo htmlspecialchars($submission['email']); ?></td>
                                                    <td>
                                                        <?php 
                                                        if ($submission['type'] === 'donation') {
                                                            echo formatCurrency($submission['data']);
                                                        } else {
                                                            echo truncateText($submission['data'], 30);
                                                        }
                                                        ?>
                                                    </td>
                                                    <td><?php echo formatDateShort($submission['date']); ?></td>
                                                    <td>
                                                        <?php
                                                        $url = $submission['type'] === 'donation' ? 'donations.php' : ($submission['type'] === 'volunteer' ? 'volunteers.php' : 'contacts.php');
                                                        ?>
                                                        <a href="<?php echo $url; ?>" class="btn btn-sm btn-outline-primary">
                                                            View
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-info m-3 mb-0">No submissions yet.</div>
                            <?php endif; ?>
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
