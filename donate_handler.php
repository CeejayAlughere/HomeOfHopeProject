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
    $amount = htmlspecialchars(trim($_POST['amount'] ?? ''));

    // Validate
    if (empty($name) || !filter_var($email, FILTER_VALIDATE_EMAIL) || empty($amount)) {
        header("Location: " . $redirect . "?donated=error");
        exit;
    }

    $stmt = $pdo->prepare("
        INSERT INTO donations (name, email, amount, donated_at)
        VALUES (:name, :email, :amount, NOW())
    ");
    $stmt->execute([
        ':name'   => $name,
        ':email'  => $email,
        ':amount' => $amount,
    ]);

    header("Location: " . $redirect . "?donated=1");
    exit;

} catch (PDOException $e) {
    header("Location: " . $redirect . "?donated=error");
    exit;
}
?>