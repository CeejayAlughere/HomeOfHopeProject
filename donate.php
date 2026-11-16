<?php
require_once 'auth.php';
require_once 'cause_repository.php';

$requestedCauseId = filter_input(INPUT_GET, 'cause_id', FILTER_VALIDATE_INT);
$selectedCause = null;

if ($requestedCauseId && $requestedCauseId > 0) {
    try {
        $selectedCause = findActiveCauseById(getPDO(), (int) $requestedCauseId);
        if ($selectedCause) {
            $_SESSION['pending_cause_id'] = (int) $requestedCauseId;
        }
    } catch (Throwable $e) {
        error_log('Select cause before login error: ' . $e->getMessage());
    }
}

if (isLoggedIn()) {
    $targetCauseId = $selectedCause['id'] ?? ($_SESSION['pending_cause_id'] ?? null);
    $target = 'donation.php';
    if ($targetCauseId) {
        $target .= '?cause_id=' . (int) $targetCauseId;
    }
    header('Location: ' . $target);
    exit;
}
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
    <div class="top-bar d-none d-md-block">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-8">
                    <div class="top-bar-left">
                        <div class="text"><i class="fa fa-phone-alt"></i><p>084 212 4937</p></div>
                        <div class="text"><i class="fa fa-envelope"></i><p>info@homeofhope.com</p></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="top-bar-right">
                        <div class="social">
                            <a href=""><i class="fab fa-twitter"></i></a>
                            <a href="https://www.facebook.com/SiyazigabisaHomeOfHope/"><i class="fab fa-facebook-f"></i></a>
                            <a href=""><i class="fab fa-linkedin-in"></i></a>
                            <a href="https://www.instagram.com/siyazigabisa/"><i class="fab fa-instagram"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
                    <a href="login.php" class="nav-item nav-link btn btn-custom text-white ml-3" style="margin-left: 15px;">Member</a>
                </div>
            </div>
        </div>
    </div>

    <div class="page-header">
        <div class="container">
            <div class="row">
                <div class="col-12"><h2>Donate Now</h2></div>
                <div class="col-12"><a href="index.php">Home</a><a href="donate.php">Donate Now</a></div>
            </div>
        </div>
    </div>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-5">
                        <h3 class="mb-4">Register or login to donate</h3>
                        <?php if ($selectedCause): ?>
                            <div class="alert alert-info">
                                You selected <strong><?php echo htmlspecialchars($selectedCause['title']); ?></strong>.
                                We will keep this selection while you sign in.
                            </div>
                        <?php endif; ?>
                        <p class="mb-4">To make a donation, first create an account or sign in. Your donation record will be linked to your member profile. No payment is processed in this version.</p>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <a href="register.php" class="btn btn-custom btn-block">Register</a>
                            </div>
                            <div class="col-md-6 mb-3">
                                <a href="login.php" class="btn btn-custom btn-block">Login</a>
                            </div>
                        </div>
                        <hr>
                        <p>Recorded donations are added to the cause total. No payment is processed in this version.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>
</body>
</html>
