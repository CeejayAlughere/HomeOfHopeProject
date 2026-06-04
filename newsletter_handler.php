<?php

$host   = "localhost";
$dbName = "homeofhope";
$dbUser = "root";
$dbPass = "";

// Where to send the user back after submitting
$redirect = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'index.php';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbName;charset=utf8", $dbUser, $dbPass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $email = htmlspecialchars(trim($_POST['newsletter_email'] ?? ''));

    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $check = $pdo->prepare("SELECT id FROM newsletter_subscribers WHERE email = :email");
        $check->execute([':email' => $email]);

        if ($check->rowCount() == 0) {
            $stmt = $pdo->prepare("INSERT INTO newsletter_subscribers (email, subscribed_at) VALUES (:email, NOW())");
            $stmt->execute([':email' => $email]);
        }
    }
} catch (PDOException $e) {
    // silently fail
}

header("Location: " . $redirect . "?subscribed=1");
exit;
?>