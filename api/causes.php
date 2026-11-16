<?php
declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/../cause_repository.php';

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'GET') {
    header('Allow: GET');
    sendJson(405, [
        'success' => false,
        'message' => 'Only GET requests are allowed.'
    ]);
}

try {
    $causes = array_map(
        static function (array $cause): array {
            return [
                'id' => $cause['id'],
                'slug' => $cause['slug'],
                'title' => $cause['title'],
                'description' => $cause['description'],
                'imageUrl' => $cause['image_url'],
                'goal' => $cause['goal_amount'],
                'raised' => $cause['raised_amount'],
                'progress' => $cause['progress']
            ];
        },
        fetchActiveCauses(getPDO())
    );

    sendJson(200, [
        'success' => true,
        'message' => 'Causes loaded successfully.',
        'causes' => $causes
    ]);
} catch (Throwable $e) {
    error_log('Load causes API error: ' . $e->getMessage());
    sendJson(500, [
        'success' => false,
        'message' => 'Unable to load causes right now.'
    ]);
}
