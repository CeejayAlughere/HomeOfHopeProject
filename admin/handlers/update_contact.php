<?php
require_once '../config.php';
require_once '../auth.php';
require_once '../helpers.php';

AdminAuth::requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $notes = sanitizeInput($_POST['notes'] ?? '');
    $markResponded = isset($_POST['mark_responded']) ? 1 : 0;
    
    if (!$id) {
        redirectWithAlert('../contacts.php', 'Invalid request.', 'error');
    }
    
    try {
        $pdo = getDBConnection();
        
        // Update notes
        $stmt = $pdo->prepare('
            UPDATE contact_messages 
            SET notes = ?
        ');
        $stmt->execute([$notes]);
        
        // Update response status if checked
        if ($markResponded) {
            $stmt = $pdo->prepare('
                UPDATE contact_messages 
                SET response_sent_at = NOW() 
                WHERE id = ? AND response_sent_at IS NULL
            ');
            $stmt->execute([$id]);
        }
        
        // Update notes again with proper id
        $stmt = $pdo->prepare('UPDATE contact_messages SET notes = ? WHERE id = ?');
        $stmt->execute([$notes, $id]);
        
        redirectWithAlert('../contacts.php', 'Contact updated successfully!', 'success');
    } catch (PDOException $e) {
        error_log('Update contact error: ' . $e->getMessage());
        redirectWithAlert('../contacts.php', 'Error updating contact.', 'error');
    }
}
?>
