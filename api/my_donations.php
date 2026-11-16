<?php
declare(strict_types=1);
require_once __DIR__ . '/bootstrap.php';
if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'GET') {
    header('Allow: GET'); sendJson(405, ['success' => false, 'message' => 'Use GET.']);
}
try {
    $pdo = getPDO();
    $user = requireAuthenticatedUser($pdo);
    $stmt = $pdo->prepare('SELECT d.id, d.amount, d.bank, UNIX_TIMESTAMP(d.donated_at) AS donated_timestamp, c.title AS cause_title FROM donations d LEFT JOIN causes c ON c.id = d.cause_id WHERE d.user_id = ? ORDER BY d.donated_at DESC, d.id DESC');
    $stmt->execute([$user['id']]);
    $donations = array_map(static function (array $row): array {
        return ['id' => (int) $row['id'], 'amount' => (float) $row['amount'],
            'bankName' => (string) ($row['bank'] ?? ''), 'date' => (int) $row['donated_timestamp'] * 1000,
            'causeTitle' => (string) ($row['cause_title'] ?? 'General / Unassigned')];
    }, $stmt->fetchAll(PDO::FETCH_ASSOC));
    sendJson(200, ['success' => true, 'message' => 'Donation records loaded.', 'user' => userPayload($user), 'donations' => $donations]);
} catch (Throwable $e) {
    error_log('Donation history request failed: ' . get_class($e));
    sendJson(500, ['success' => false, 'message' => 'Unable to load donation history.']);
}
