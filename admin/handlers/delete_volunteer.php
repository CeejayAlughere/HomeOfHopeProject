<?php
require_once '../config.php';
require_once '../auth.php';
require_once '../helpers.php';

AdminAuth::requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    
    if (!$id) {
        redirectWithAlert('../volunteers.php', 'Invalid request.', 'error');
    }
    
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare('DELETE FROM volunteers WHERE id = ?');
        $stmt->execute([$id]);
        redirectWithAlert('../volunteers.php', 'Volunteer deleted successfully!', 'success');
    } catch (PDOException $e) {
        error_log('Delete volunteer error: ' . $e->getMessage());
        redirectWithAlert('../volunteers.php', 'Error deleting volunteer.', 'error');
    }
}
?>
