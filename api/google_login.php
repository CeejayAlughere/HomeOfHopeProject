<?php
declare(strict_types=1);
require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/../includes/firebase_identity.php';
require_once __DIR__ . '/../includes/google_accounts.php';
requirePostRequest();
$body = readJsonBody();
$web = ($body['client'] ?? '') === 'web';
if ($web && (!is_string($body['csrf'] ?? null) || !isset($_SESSION['google_csrf'])
    || !hash_equals($_SESSION['google_csrf'], $body['csrf']))) {
    sendJson(403, ['success' => false, 'message' => 'Reload the login page and try again.']);
}
try {
    $identity = firebaseGoogleIdentity(is_string($body['idToken'] ?? null) ? $body['idToken'] : '');
} catch (UnexpectedValueException $e) {
    sendJson(401, ['success' => false, 'message' => 'Google sign-in could not be verified. Sign in again.']);
} catch (Throwable $e) {
    error_log('Google token verification unavailable: ' . get_class($e));
    sendJson(503, ['success' => false, 'message' => 'Google verification is temporarily unavailable. Try again shortly.']);
}
try {
    $pdo = getPDO();
    [$status, $result] = resolveGoogleAccount($pdo, $identity, $body);
    if (!($result['success'] ?? false)) sendJson($status, $result);
    $user = $result['account'];
    $response = ['success' => true, 'message' => 'Google sign-in successful.', 'user' => userPayload($user)];
    if ($web) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = (int) $user['id'];
        $_SESSION['user'] = array_intersect_key($user, array_flip(['id', 'full_name', 'username', 'email']));
        unset($_SESSION['google_csrf']);
        $response['redirect'] = 'dashboard.php';
    } else {
        $response['token'] = issueApiToken($pdo, (int) $user['id']);
    }
    sendJson(200, $response);
} catch (Throwable $e) {
    error_log('Google account sign-in failed: ' . get_class($e));
    sendJson(500, ['success' => false, 'message' => 'Unable to open your website account. Check that the Google account migration has been installed.']);
}
