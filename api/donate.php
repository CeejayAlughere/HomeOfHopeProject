<?php
declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/../cause_repository.php';

requirePostRequest();

$pdo = getPDO();
$user = requireAuthenticatedUser($pdo);
$body = readJsonBody();

$causeId = filter_var($body['causeId'] ?? null, FILTER_VALIDATE_INT);
$bankName = cleanText($body['bankName'] ?? '');
$amount = filter_var($body['amount'] ?? null, FILTER_VALIDATE_FLOAT);
$allowedBanks = [
    'Absa',
    'Capitec',
    'FNB',
    'Nedbank',
    'Standard Bank',
    'Investec',
    'Other'
];

if ($causeId === false || $causeId <= 0) {
    sendJson(422, [
        'success' => false,
        'message' => 'Please select a valid cause.'
    ]);
}

if (!in_array($bankName, $allowedBanks, true)) {
    sendJson(422, [
        'success' => false,
        'message' => 'Please select a valid bank.'
    ]);
}

if ($amount === false || $amount <= 0 || $amount > 1000000) {
    sendJson(422, [
        'success' => false,
        'message' => 'Donation amount must be between R1 and R1,000,000.'
    ]);
}

try {
    $cause = findActiveCauseById($pdo, (int) $causeId);

    if ($cause === null) {
        sendJson(404, [
            'success' => false,
            'message' => 'The selected cause is unavailable.'
        ]);
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
        $bankName
    ]);

    sendJson(201, [
        'success' => true,
        'message' => 'Donation recorded. No payment was processed.',
        'donationId' => (int) $pdo->lastInsertId()
    ]);
} catch (Throwable $e) {
    error_log('Create donation API error: ' . $e->getMessage());
    sendJson(500, [
        'success' => false,
        'message' => 'Unable to submit your donation right now.'
    ]);
}
