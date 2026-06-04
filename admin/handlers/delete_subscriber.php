<?php
require_once '../config.php';
require_once '../auth.php';
require_once '../helpers.php';

AdminAuth::requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    
    if (!$id) {
        redirectWithAlert('../newsletter.php', 'Invalid request.', 'error');
    }
    
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare('DELETE FROM newsletter_subscribers WHERE id = ?');
        $stmt->execute([$id]);
        redirectWithAlert('../newsletter.php', 'Subscriber removed successfully!', 'success');
    } catch (PDOException $e) {
        error_log('Delete subscriber error: ' . $e->getMessage());
        redirectWithAlert('../newsletter.php', 'Error removing subscriber.', 'error');
    }
}
?>
