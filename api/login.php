<?php
declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

requirePostRequest();
$body = readJsonBody();

$identifier = cleanText($body['identifier'] ?? '');
$password = (string) ($body['password'] ?? '');

if ($identifier === '' || $password === '') {
    sendJson(422, [
        'success' => false,
        'message' => 'Please enter your username or email and password.'
    ]);
}

try {
    $pdo = getPDO();
    $user = findUserByIdentifier($pdo, $identifier);

    if (!$user || !password_verify($password, $user['password_hash'])) {
        sendJson(401, [
            'success' => false,
            'message' => 'Invalid username/email or password.'
        ]);
    }

    if (password_needs_rehash($user['password_hash'], PASSWORD_DEFAULT)) {
        $newHash = password_hash($password, PASSWORD_DEFAULT);
        $rehash = $pdo->prepare(
            'UPDATE users SET password_hash = ? WHERE id = ?'
        );
        $rehash->execute([$newHash, $user['id']]);
    }

    $token = issueApiToken($pdo, (int) $user['id']);

    sendJson(200, [
        'success' => true,
        'message' => 'Login successful.',
        'token' => $token,
        'user' => userPayload($user)
    ]);
} catch (Throwable $e) {
    error_log('Home of Hope API login error: ' . $e->getMessage());
    sendJson(500, [
        'success' => false,
        'message' => 'Unable to log in at this time. Please try again.'
    ]);
}
