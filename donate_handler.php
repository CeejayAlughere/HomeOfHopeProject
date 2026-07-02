<?php
require_once 'auth.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$user = getCurrentUser();

$cardholderName = sanitize($_POST['cardholder_name'] ?? '');
$cardNumber = preg_replace('/\D/', '', $_POST['card_number'] ?? '');
$expiryDate = sanitize($_POST['expiry_date'] ?? '');
$cvv = preg_replace('/\D/', '', $_POST['cvv'] ?? '');
$bank = sanitize($_POST['bank'] ?? '');
$amount = sanitize($_POST['amount'] ?? '');

$redirect = 'donation.php';

if (!$cardholderName || !$cardNumber || !$expiryDate || !$cvv || !$bank || !$amount) {
    header('Location: ' . $redirect . '?error=Please complete all fields.');
    exit;
}

if (strlen($cardNumber) < 12 || strlen($cardNumber) > 19) {
    header('Location: ' . $redirect . '?error=Card number must be valid.');
    exit;
}

if (!preg_match('/^(0[1-9]|1[0-2])\/(\d{2})$/', $expiryDate)) {
    header('Location: ' . $redirect . '?error=Expiry date must use MM/YY format.');
    exit;
}

if (strlen($cvv) < 3 || strlen($cvv) > 4) {
    header('Location: ' . $redirect . '?error=CVV must be 3 or 4 digits.');
    exit;
}

if (!is_numeric($amount) || $amount <= 0) {
    header('Location: ' . $redirect . '?error=Donation amount must be a positive number.');
    exit;
}

try {
    $stmt = getPDO()->prepare('INSERT INTO donations (user_id, name, email, amount, bank, donated_at) VALUES (?, ?, ?, ?, ?, NOW())');
    $stmt->execute([
        $user['id'], 
        $cardholderName,
        $user['username'] . '@homeofhope.local',
        $amount,
        $bank
    ]);

    header('Location: donation_history.php?donated=1');
    exit;
} catch (PDOException $e) {
    header('Location: ' . $redirect . '?error=Unable to process donation at this time.');
    exit;
}
