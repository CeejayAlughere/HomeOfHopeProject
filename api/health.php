<?php
declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

sendJson(200, [
    'success' => true,
    'message' => 'Home of Hope account API is available.'
]);
