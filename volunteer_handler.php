<?php

$host   = "localhost";
$dbName = "homeofhope";
$dbUser = "root";
$dbPass = "";

$redirect = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'index.php';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbName;charset=utf8", $dbUser, $dbPass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $name   = htmlspecialchars(trim($_POST['name']   ?? ''));
    $email  = htmlspecialchars(trim($_POST['email']  ?? ''));
    $reason = htmlspecialchars(trim($_POST['reason'] ?? ''));

    // Validate
    if (empty($name) || !filter_var($email, FILTER_VALIDATE_EMAIL) || empty($reason)) {
        header("Location: " . $redirect . "?volunteer=error");
        exit;
    }

    $stmt = $pdo->prepare("
        INSERT INTO volunteers (name, email, reason, signed_up_at)
        VALUES (:name, :email, :reason, NOW())
    ");
    $stmt->execute([
        ':name'   => $name,
        ':email'  => $email,
        ':reason' => $reason,
    ]);

    header("Location: " . $redirect . "?volunteer=1");
    exit;

} catch (PDOException $e) {
    header("Location: " . $redirect . "?volunteer=error");
    exit;
}
?>