<?php
declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

requirePostRequest();
$body = readJsonBody();

$fullName = cleanText($body['fullName'] ?? '');
$idNumber = cleanText($body['idNumber'] ?? '');
$email = strtolower(cleanText($body['email'] ?? ''));
$username = cleanText($body['username'] ?? $email);
$password = (string) ($body['password'] ?? '');

if (
    $fullName === '' ||
    $idNumber === '' ||
    $email === '' ||
    $username === '' ||
    $password === ''
) {
    sendJson(422, [
        'success' => false,
        'message' => 'Please complete all registration fields.'
    ]);
}

if (!preg_match('/^\d{13}$/', $idNumber)) {
    sendJson(422, [
        'success' => false,
        'message' => 'South African ID number must contain exactly 13 digits.'
    ]);
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    sendJson(422, [
        'success' => false,
        'message' => 'Please enter a valid email address.'
    ]);
}

if (strlen($password) < 8) {
    sendJson(422, [
        'success' => false,
        'message' => 'Password must contain at least 8 characters.'
    ]);
}

try {
    $pdo = getPDO();

    $duplicate = $pdo->prepare(
        'SELECT username, email, sa_id FROM users
         WHERE LOWER(username) = LOWER(?)
            OR LOWER(email) = LOWER(?)
            OR sa_id = ?
         LIMIT 1'
    );
    $duplicate->execute([$username, $email, $idNumber]);
    $existing = $duplicate->fetch(PDO::FETCH_ASSOC);

    if ($existing) {
        if (strcasecmp((string) $existing['email'], $email) === 0) {
            $message = 'An account already uses this email address.';
        } elseif ((string) $existing['sa_id'] === $idNumber) {
            $message = 'This ID number is already registered.';
        } else {
            $message = 'That username is already in use.';
        }

        sendJson(409, [
            'success' => false,
            'message' => $message
        ]);
    }

    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    $pdo->beginTransaction();

    $insert = $pdo->prepare(
        'INSERT INTO users
            (full_name, sa_id, username, email, password_hash)
         VALUES (?, ?, ?, ?, ?)'
    );
    $insert->execute([
        $fullName,
        $idNumber,
        $username,
        $email,
        $passwordHash
    ]);

    $userId = (int) $pdo->lastInsertId();

    $user = [
        'id' => $userId,
        'full_name' => $fullName,
        'sa_id' => $idNumber,
        'username' => $username,
        'email' => $email,
        'created_at' => date('Y-m-d H:i:s')
    ];

    $token = issueApiToken($pdo, $userId);
    $pdo->commit();

    sendJson(201, [
        'success' => true,
        'message' => 'Registration successful.',
        'token' => $token,
        'user' => userPayload($user)
    ]);
} catch (Throwable $e) {
    if (isset($pdo) && $pdo instanceof PDO && $pdo->inTransaction()) {
        $pdo->rollBack();
    }

    error_log('Home of Hope API registration error: ' . $e->getMessage());
    sendJson(500, [
        'success' => false,
        'message' => 'Unable to register at this time. Please try again.'
    ]);
}
