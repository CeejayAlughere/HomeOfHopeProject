<?php
require_once '../config.php';
require_once '../auth.php';
require_once '../helpers.php';

AdminAuth::requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    
    if (!$id) {
        redirectWithAlert('../contacts.php', 'Invalid request.', 'error');
    }
    
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare('DELETE FROM contact_messages WHERE id = ?');
        $stmt->execute([$id]);
        redirectWithAlert('../contacts.php', 'Contact deleted successfully!', 'success');
    } catch (PDOException $e) {
        error_log('Delete contact error: ' . $e->getMessage());
        redirectWithAlert('../contacts.php', 'Error deleting contact.', 'error');
    }
}
?>
