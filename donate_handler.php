<?php
require_once 'auth.php';
require_once 'cause_repository.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: donation.php');
    exit;
}

$pdo = getPDO();
$causeId = filter_input(INPUT_POST, 'cause_id', FILTER_VALIDATE_INT);
$bank = trim((string) ($_POST['bank'] ?? ''));
$amount = filter_var($_POST['amount'] ?? null, FILTER_VALIDATE_FLOAT);

$redirect = 'donation.php';
if ($causeId && $causeId > 0) {
    $redirect .= '?cause_id=' . (int) $causeId;
    $_SESSION['pending_cause_id'] = (int) $causeId;
}

$separator = strpos($redirect, '?') !== false ? '&' : '?';
$allowedBanks = [
    'Absa',
    'Capitec',
    'FNB',
    'Nedbank',
    'Standard Bank',
    'Investec',
    'Other'
];

if (!$causeId || $causeId <= 0) {
    header('Location: ' . $redirect . $separator . 'error=' . rawurlencode('Please select a cause.'));
    exit;
}

if (!in_array($bank, $allowedBanks, true)) {
    header('Location: ' . $redirect . $separator . 'error=' . rawurlencode('Please select a valid bank.'));
    exit;
}

if ($amount === false || $amount <= 0 || $amount > 1000000) {
    header('Location: ' . $redirect . $separator . 'error=' . rawurlencode('Donation amount must be between R1 and R1,000,000.'));
    exit;
}

try {
    $cause = findActiveCauseById($pdo, (int) $causeId);
    if ($cause === null) {
        header('Location: donation.php?error=' . rawurlencode('The selected cause is unavailable.'));
        exit;
    }

    $userStmt = $pdo->prepare(
        'SELECT id, full_name, email FROM users WHERE id = ? LIMIT 1'
    );
    $userStmt->execute([(int) $_SESSION['user_id']]);
    $user = $userStmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        header('Location: logout.php');
        exit;
    }

    $stmt = $pdo->prepare(
        "INSERT INTO donations
            (user_id, cause_id, name, email, amount, bank, donated_at)
         VALUES (?, ?, ?, ?, ?, ?, NOW())"
    );
    $stmt->execute([
        (int) $user['id'],
        (int) $causeId,
        (string) $user['full_name'],
        (string) $user['email'],
        (float) $amount,
        $bank
    ]);

    unset($_SESSION['pending_cause_id']);
    header('Location: donation_history.php?donated=1');
    exit;
} catch (Throwable $e) {
    error_log('Website donation error: ' . $e->getMessage());
    header('Location: ' . $redirect . $separator . 'error=' . rawurlencode('Unable to submit your donation at this time.'));
    exit;
}
