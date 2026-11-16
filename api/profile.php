<?php
declare(strict_types=1);
require_once __DIR__ . '/bootstrap.php';
$method = $_SERVER['REQUEST_METHOD'] ?? '';
if (!in_array($method, ['GET', 'POST'], true)) {
    header('Allow: GET, POST'); sendJson(405, ['success' => false, 'message' => 'Use GET or POST.']);
}
try {
    $pdo = getPDO();
    $user = requireAuthenticatedUser($pdo);
    if ($method === 'POST') {
        $body = readJsonBody();
        $name = trim(is_string($body['fullName'] ?? null) ? $body['fullName'] : '');
        $phone = trim(is_string($body['phoneNumber'] ?? null) ? $body['phoneNumber'] : '');
        if ($name === '' || mb_strlen($name) > 100 || !preg_match('/^[0-9+() -]{0,30}$/D', $phone)) {
            sendJson(422, ['success' => false, 'message' => 'Enter a name of up to 100 characters and a valid phone number.']);
        }
        $stmt = $pdo->prepare('UPDATE users SET full_name = ?, phone_number = ? WHERE id = ?');
        $stmt->execute([$name, $phone, $user['id']]);
        $user['full_name'] = $name;
        $user['phone_number'] = $phone;
    }
    sendJson(200, ['success' => true, 'message' => 'Profile loaded.', 'user' => userPayload($user)]);
} catch (Throwable $e) {
    error_log('Shared profile request failed: ' . get_class($e));
    sendJson(500, ['success' => false, 'message' => 'Unable to access your profile.']);
}
