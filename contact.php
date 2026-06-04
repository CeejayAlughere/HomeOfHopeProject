<?php
// ── Newsletter handler ────────────────────────────────────────
if (isset($_POST['newsletter_email'])) {
    $host   = "localhost";
    $dbName = "homeofhope";
    $dbUser = "root";
    $dbPass = "";

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbName;charset=utf8", $dbUser, $dbPass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $email = htmlspecialchars(trim($_POST['newsletter_email']));

        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $check = $pdo->prepare("SELECT id FROM newsletter_subscribers WHERE email = :email");
            $check->execute([':email' => $email]);

            if ($check->rowCount() == 0) {
                $stmt = $pdo->prepare("INSERT INTO newsletter_subscribers (email, subscribed_at) VALUES (:email, NOW())");
                $stmt->execute([':email' => $email]);
            }
        }
    } catch (PDOException $e) {
        // silently fail, stay on page
    }

    header("Location: contact.php?subscribed=1");
    exit;
}
?>

<?php if (isset($_GET['sent'])): ?>
    <div class="alert alert-success text-center">
        Thank you! Your message has been received.
    </div>
<?php endif; ?>

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
                    <h2>Contact Us</h2>
                </div>
                <div class="col-12">
                    <a href="index.php">Home</a>
                    <a href="contact.php">Contact</a>
                </div>
            </div>
        </div>
    </div>
    <!-- Page Header End -->


    <!-- Contact Start -->
    <div class="contact">
        <div class="container">
            <div class="section-header text-center">
                <p>Get In Touch</p>
                <h2>Contact for any query</h2>
            </div>
            <div class="contact-img">
                <img src="img/contact.jpg" alt="Image">
            </div>

            <!-- Success / Error Messages -->
            <?php if (isset($_GET['sent']) && $_GET['sent'] == '1'): ?>
                <div class="alert alert-success text-center" role="alert">
                    Thank you! Your message has been received. We will get back to you shortly.
                </div>
            <?php elseif (isset($_GET['error'])): ?>
                <div class="alert alert-danger text-center" role="alert">
                    Something went wrong. Please try again.
                </div>
            <?php endif; ?>

            <!-- Contact Form -->
            <div class="contact-form">
                <form action="connect.php" method="POST" name="sentMessage" id="contactForm" novalidate="novalidate">
                    <div class="control-group">
                        <input type="text"
                            class="form-control"
                            id="name"
                            name="name"
                            placeholder="Your Name"
                            required="required"
                            data-validation-required-message="Please enter your name" />
                        <p class="help-block text-danger"></p>
                    </div>
                    <div class="control-group">
                        <input type="email"
                            class="form-control"
                            id="email"
                            name="email"
                            placeholder="Your Email"
                            required="required"
                            data-validation-required-message="Please enter your email" />
                        <p class="help-block text-danger"></p>
                    </div>
                    <div class="control-group">
                        <input type="text"
                            class="form-control"
                            id="subject"
                            name="subject"
                            placeholder="Subject"
                            required="required"
                            data-validation-required-message="Please enter a subject" />
                        <p class="help-block text-danger"></p>
                    </div>
                    <div class="control-group">
                        <textarea class="form-control"
                            id="message"
                            name="message"
                            placeholder="Message"
                            required="required"
                            data-validation-required-message="Please enter your message"></textarea>
                        <p class="help-block text-danger"></p>
                    </div>
                    <div>
                        <button class="btn btn-custom" type="submit" id="sendMessageButton">Send Message</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
    <!-- Contact End -->


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

    <!-- Pre Loader -->
    <div id="loader" class="show">
        <div class="loader"></div>
    </div>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>
    <script src="lib/waypoints/waypoints.min.js"></script>
    <script src="lib/counterup/counterup.min.js"></script>
    <script src="lib/parallax/parallax.min.js"></script>

    <!-- Javascript -->
    <script src="js/main.js"></script>
</body>

</html>