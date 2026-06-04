<?php
require_once '../config.php';
require_once '../auth.php';
require_once '../helpers.php';

AdminAuth::requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    
    if (!$id) {
        redirectWithAlert('../donations.php', 'Invalid request.', 'error');
    }
    
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare('DELETE FROM donations WHERE id = ?');
        $stmt->execute([$id]);
        redirectWithAlert('../donations.php', 'Donation deleted successfully!', 'success');
    } catch (PDOException $e) {
        error_log('Delete donation error: ' . $e->getMessage());
        redirectWithAlert('../donations.php', 'Error deleting donation.', 'error');
    }
}
?>
