<?php
require_once 'auth.php';
requireLogin();
$user = getCurrentUser();

$donations = [];

try {
    $stmt = getPDO()->prepare('SELECT amount, donated_at, bank FROM donations WHERE user_id = ? ORDER BY donated_at DESC');
    $stmt->execute([$user['id']]);
    $donations = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $donations = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Donation History - Home Of Hope</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <link href="img/favicon.ico" rel="icon">
    <link rel="icon" type="image/x-icon" href="img/favicon.ico">
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
    <div class="navbar navbar-expand-lg bg-dark navbar-dark">
        <div class="container-fluid">
            <a href="index.php" class="navbar-brand">Home Of Hope</a>
            <button type="button" class="navbar-toggler" data-toggle="collapse" data-target="#navbarCollapse">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-between" id="navbarCollapse">
                <div class="navbar-nav ml-auto">
                    <a href="index.php" class="nav-item nav-link">Home</a>
                    <a href="about.php" class="nav-item nav-link">About</a>
                    <a href="causes.php" class="nav-item nav-link">Causes</a>
                    <a href="event.php" class="nav-item nav-link">Events</a>
                    <a href="blog.php" class="nav-item nav-link">Blog</a>
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">Pages</a>
                        <div class="dropdown-menu">
                            <a href="service.php" class="dropdown-item">Our Services</a>
                            <a href="donate.php" class="dropdown-item">Donate Now</a>
                            <a href="volunteer.php" class="dropdown-item">Become A Volunteer</a>
                        </div>
                    </div>
                    <a href="contact.php" class="nav-item nav-link">Contact</a>
                </div>
            </div>
        </div>
    </div>

    <div class="page-header">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h2>Donation History</h2>
                </div>
                <div class="col-12">
                    <a href="index.php">Home</a>
                    <a href="donation_history.php">Donation History</a>
                </div>
            </div>
        </div>
    </div>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <h3 class="mb-1">Your donations</h3>
                                <p class="mb-0">All donations made by <?php echo htmlspecialchars($user['full_name']); ?>.</p>
                            </div>
                            <div>
                                <a href="donation.php" class="btn btn-custom">Make another donation</a>
                            </div>
                        </div>

                        <?php if (isset($_GET['donated']) && $_GET['donated'] == '1'): ?>
                            <div class="alert alert-success">Your donation was successful. Thank you for your support.</div>
                        <?php endif; ?>

                        <?php if (empty($donations)): ?>
                            <div class="alert alert-info">No donation history found yet. Make your first donation from your dashboard.</div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Amount</th>
                                            <th>Date</th>
                                            <th>Time</th>
                                            <th>Bank</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($donations as $donation): ?>
                                            <tr>
                                                <td>R<?php echo number_format($donation['amount'], 2); ?></td>
                                                <td><?php echo date('Y-m-d', strtotime($donation['donated_at'])); ?></td>
                                                <td><?php echo date('H:i', strtotime($donation['donated_at'])); ?></td>
                                                <td><?php echo htmlspecialchars($donation['bank']); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>

                        <div class="mt-4">
                            <a href="dashboard.php" class="btn btn-custom btn-block">Back to dashboard</a>
                            <a href="logout.php" class="btn btn-custom btn-block">Logout</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>
</body>
</html>
