<?php
declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

requirePostRequest();
$token = getBearerToken();

if ($token !== null) {
    try {
        revokeApiToken(getPDO(), $token);
    } catch (Throwable $e) {
        error_log('Home of Hope API logout error: ' . $e->getMessage());
    }
}

sendJson(200, [
    'success' => true,
    'message' => 'Logged out successfully.'
]);
