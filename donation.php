<?php
require_once 'auth.php';
requireLogin();
$user = getCurrentUser();

$errorMessage = $_GET['error'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Donate - Home Of Hope</title>
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
                    <h2>Donation</h2>
                </div>
                <div class="col-12">
                    <a href="index.php">Home</a>
                    <a href="donation.php">Donation</a>
                </div>
            </div>
        </div>
    </div>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">
                        <h3 class="mb-4">Make a donation</h3>

                        <?php if ($errorMessage): ?>
                            <div class="alert alert-danger"><?php echo htmlspecialchars($errorMessage); ?></div>
                        <?php endif; ?>

                        <form action="donate_handler.php" method="POST">
                            <div class="form-group">
                                <label>Cardholder Name</label>
                                <input type="text" name="cardholder_name" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label>Card Number</label>
                                <input type="text" name="card_number" class="form-control" maxlength="19" placeholder="0000 0000 0000 0000" required>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Expiry Date</label>
                                    <input type="text" name="expiry_date" class="form-control" placeholder="MM/YY" required>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>CVV</label>
                                    <input type="text" name="cvv" class="form-control" maxlength="4" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>South African Bank</label>
                                <select name="bank" class="form-control" required>
                                    <option value="">Select your bank</option>
                                    <option value="Absa">Absa</option>
                                    <option value="Capitec">Capitec</option>
                                    <option value="FNB">FNB</option>
                                    <option value="Nedbank">Nedbank</option>
                                    <option value="Standard Bank">Standard Bank</option>
                                    <option value="Investec">Investec</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Donation Amount (ZAR)</label>
                                <input type="number" min="1" step="0.01" name="amount" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-custom">Submit Donation</button>
                        </form>

                        <div class="mt-4">
                            <a href="dashboard.php" class="btn btn-secondary">Back to dashboard</a>
                            <a href="donation_history.php" class="btn btn-outline-dark">View Donation History</a>
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
