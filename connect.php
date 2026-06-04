<?php

// ── Database config ──────────────────────────────────────────
$host     = "localhost";
$dbName   = "homeofhope";
$username = "root";        // change to your DB username
$password = "";            // change to your DB password

// ── Connect ──────────────────────────────────────────────────
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbName;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die(json_encode(["status" => "error", "message" => "Database connection failed."]));
}

// ── Sanitize inputs ──────────────────────────────────────────
$name    = htmlspecialchars(trim($_POST['name']    ?? ''));
$email   = htmlspecialchars(trim($_POST['email']   ?? ''));
$subject = htmlspecialchars(trim($_POST['subject'] ?? ''));
$message = htmlspecialchars(trim($_POST['message'] ?? ''));

// ── Basic validation ─────────────────────────────────────────
if (!$name || !$email || !$subject || !$message) {
    die(json_encode(["status" => "error", "message" => "All fields are required."]));
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die(json_encode(["status" => "error", "message" => "Invalid email address."]));
}

// ── Insert into DB ────────────────────────────────────────────
try {
    $stmt = $pdo->prepare("
        INSERT INTO contact_messages (name, email, subject, message, submitted_at)
        VALUES (:name, :email, :subject, :message, NOW())
    ");

    $stmt->execute([
        ':name'    => $name,
        ':email'   => $email,
        ':subject' => $subject,
        ':message' => $message,
    ]);

    // Redirect back with success flag
    header("Location: contact.php?sent=1");
    exit;

} catch (PDOException $e) {
    die(json_encode(["status" => "error", "message" => "Failed to save message."]));
}
?>