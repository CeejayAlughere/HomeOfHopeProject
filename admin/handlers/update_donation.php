<?php
require_once '../config.php';
require_once '../auth.php';
require_once '../helpers.php';

AdminAuth::requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $name = sanitizeInput($_POST['name'] ?? '');
    $email = sanitizeInput($_POST['email'] ?? '');
    $amount = $_POST['amount'] ?? 0;
    $status = $_POST['status'] ?? 'pending';
    $notes = sanitizeInput($_POST['notes'] ?? '');
    
    if (!$id || !validateEmail($email)) {
        redirectWithAlert('../donations.php', 'Invalid input. Please try again.', 'error');
    }
    
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare('
            UPDATE donations 
            SET name = ?, email = ?, amount = ?, status = ?, notes = ? 
            WHERE id = ?
        ');
        $stmt->execute([$name, $email, $amount, $status, $notes, $id]);
        redirectWithAlert('../donations.php', 'Donation updated successfully!', 'success');
    } catch (PDOException $e) {
        error_log('Update donation error: ' . $e->getMessage());
        redirectWithAlert('../donations.php', 'Error updating donation.', 'error');
    }
}
?>
