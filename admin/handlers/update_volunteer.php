<?php
require_once '../config.php';
require_once '../auth.php';
require_once '../helpers.php';

AdminAuth::requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $name = sanitizeInput($_POST['name'] ?? '');
    $email = sanitizeInput($_POST['email'] ?? '');
    $reason = sanitizeInput($_POST['reason'] ?? '');
    $status = $_POST['status'] ?? 'new';
    $notes = sanitizeInput($_POST['notes'] ?? '');
    
    if (!$id || !validateEmail($email)) {
        redirectWithAlert('../volunteers.php', 'Invalid input. Please try again.', 'error');
    }
    
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare('
            UPDATE volunteers 
            SET name = ?, email = ?, reason = ?, status = ?, notes = ? 
            WHERE id = ?
        ');
        $stmt->execute([$name, $email, $reason, $status, $notes, $id]);
        redirectWithAlert('../volunteers.php', 'Volunteer updated successfully!', 'success');
    } catch (PDOException $e) {
        error_log('Update volunteer error: ' . $e->getMessage());
        redirectWithAlert('../volunteers.php', 'Error updating volunteer.', 'error');
    }
}
?>
