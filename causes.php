<?php
require_once 'auth.php';
require_once 'cause_repository.php';

$causes = [];
$causesError = '';

try {
    $causes = fetchActiveCauses(getPDO());
} catch (Throwable $e) {
    error_log('Website causes error: ' . $e->getMessage());
    $causesError = 'Our cause totals are temporarily unavailable. Please try again shortly.';
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Home Of Hope</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <!-- Favicon -->
    <link href="img/favicon.ico" rel="icon">

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- CSS Libraries -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="lib/flaticon/font/flaticon.css" rel="stylesheet">
    <link href="lib/animate/animate.min.css" rel="stylesheet">
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">

    <!-- Stylesheet -->
    <link href="css/style.css" rel="stylesheet">
</head>

<body>
    <!-- Top Bar Start -->
    <div class="top-bar d-none d-md-block">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-8">
                    <div class="top-bar-left">
                        <div class="text">
                            <i class="fa fa-phone-alt"></i>
                            <p>084 212 4937</p>
                        </div>
                        <div class="text">
                            <i class="fa fa-envelope"></i>
                            <p>info@homeofhope.com</p>
                        </div>
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
    <!-- Top Bar End -->

    <!-- Nav Bar Start -->
    <div class="navbar navbar-expand-lg bg-dark navbar-dark">
        <div class="container-fluid">
            <a href="index.php" class="navbar-brand">Home Of Hope</a>
            <button type="button" class="navbar-toggler" data-toggle="collapse" data-target="#navbarCollapse">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse justify-content-between" id="navbarCollapse">
                <div class="navbar-nav ml-auto">
                    <a href="index.php" class="nav-item nav-link active">Home</a>
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
    <!-- Nav Bar End -->


    <!-- Page Header Start -->
    <div class="page-header">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h2>Popular Causes</h2>
                </div>
                <div class="col-12">
                    <a href="index.php">Home</a>
                    <a href="causes.php">Causes</a>
                </div>
            </div>
        </div>
    </div>
    <!-- Page Header End -->


    <!-- Causes Start -->
    <div class="causes">
        <div class="container">
            <div class="section-header text-center">
                <p>Popular Causes</p>
                <h2>Join us in making a difference right here at home</h2>
            </div>
            <?php if ($causesError): ?>
                <div class="alert alert-warning text-center">
                    <?php echo htmlspecialchars($causesError); ?>
                </div>
            <?php elseif (empty($causes)): ?>
                <div class="alert alert-info text-center">No active causes are available right now.</div>
            <?php else: ?>
                <div class="owl-carousel causes-carousel">
                    <?php foreach ($causes as $cause): ?>
                        <?php
                        $causeId = (int) $cause['id'];
                        $progress = (float) $cause['progress'];
                        $donateUrl = isLoggedIn()
                            ? 'donation.php?cause_id=' . $causeId
                            : 'donate.php?cause_id=' . $causeId;
                        ?>
                        <div class="causes-item">
                            <div class="causes-img">
                                <img
                                    src="<?php echo htmlspecialchars($cause['image_url']); ?>"
                                    alt="<?php echo htmlspecialchars($cause['title']); ?>"
                                >
                            </div>
                            <div class="causes-progress">
                                <div class="progress">
                                    <div
                                        class="progress-bar"
                                        role="progressbar"
                                        aria-valuenow="<?php echo htmlspecialchars((string) $progress); ?>"
                                        aria-valuemin="0"
                                        aria-valuemax="100"
                                    >
                                        <span><?php echo number_format($progress, 0); ?>%</span>
                                    </div>
                                </div>
                                <div class="progress-text">
                                    <p><strong>Raised:</strong> R<?php echo number_format($cause['raised_amount'], 2, '.', ' '); ?></p>
                                    <p><strong>Goal:</strong> R<?php echo number_format($cause['goal_amount'], 2, '.', ' '); ?></p>
                                </div>
                            </div>
                            <div class="causes-text">
                                <h3><?php echo htmlspecialchars($cause['title']); ?></h3>
                                <p><?php echo htmlspecialchars($cause['description']); ?></p>
                            </div>
                            <div class="causes-btn">
                                <a class="btn btn-custom" href="<?php echo htmlspecialchars($donateUrl); ?>">Donate Now</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <!-- Causes End -->


    <!-- Footer Start -->
    <div class="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-md-6">
                    <div class="footer-contact">
                        <h2>Our Head Office</h2>
                        <p><i class="fa fa-map-marker-alt"></i>35 Morocco St, Isivana, Thembisa, South Africa </p>
                        <p><i class="fa fa-phone-alt"></i>084 212 4937</p>
                        <p><i class="fa fa-envelope"></i>info@homeofhope.com</p>
                        <div class="footer-social">
                            <a class="btn btn-custom" href="#"><i class="fab fa-twitter"></i></a>
                            <a class="btn btn-custom" href="https://www.facebook.com/SiyazigabisaHomeOfHope/"><i class="fab fa-facebook-f"></i></a>
                            <a class="btn btn-custom" href="#"><i class="fab fa-youtube"></i></a>
                            <a class="btn btn-custom" href="https://www.instagram.com/siyazigabisa/"><i class="fab fa-instagram"></i></a>
                            <a class="btn btn-custom" href="#"><i class="fab fa-linkedin-in"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="footer-link">
                        <h2>Popular Links</h2>
                        <a href="about.php">About Us</a>
                        <a href="contact.php">Contact Us</a>
                        <a href="causes.php">Popular Causes</a>
                        <a href="event.php">Upcoming Events</a>
                        <a href="blog.php">Latest Blog</a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="footer-link">
                        <h2>Useful Links</h2>
                        <a href="terms.php">Terms of use</a>
                        <a href="privacy.php">Privacy policy</a>
                        <a href="cookies.php">Cookies</a>
                        <a href="help.php">Help</a>
                        <a href="fqa.php">FQAs</a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="footer-newsletter">
                        <h2>Subscribe to Our Monthly Newsletter</h2>

                        <?php if (isset($_GET['subscribed'])): ?>
                            <p style="color: #28a745; font-weight: 600;">Thanks for subscribing!</p>
                        <?php endif; ?>

                        <form action="newsletter_handler.php" method="POST">
                            <input class="form-control"
                                name="newsletter_email"
                                type="email"
                                placeholder="Enter your email address..."
                                required />
                            <button class="btn btn-custom" type="submit">Submit</button>
                            <label>Don't worry, we don't spam!</label>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="container copyright">
            <div class="row">
                <div class="col-md-6">
                    <p>&copy; <?php echo date('Y'); ?> <a href="#">Siyazigabisa Home Of Hope</a>, All Rights Reserved.</p>
                </div>
                <div class="col-md-6">
                    <p>Making a difference, one life at a time &hearts;</p>
                </div>
            </div>
        </div>
    </div>
    <!-- Footer End -->

    <!-- Back to top button -->
    <a href="#" class="back-to-top"><i class="fa fa-chevron-up"></i></a>

    <!-- Pre loader -->
    <div id="loader" class="show">
        <div class="loader"></div>
    </div>

    <!-- JavaScript libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>
    <script src="lib/waypoints/waypoints.min.js"></script>
    <script src="lib/counterup/counterup.min.js"></script>
    <script src="lib/parallax/parallax.min.js"></script>

    <!-- Contact Javascript file -->
    <script src="mail/jqBootstrapValidation.min.js"></script>
    <script src="mail/contact.js"></script>

    <!-- Javascript file -->
    <script src="js/main.js"></script>
</body>

</html>
