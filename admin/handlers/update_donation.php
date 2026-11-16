<?php
require_once '../config.php';
require_once '../auth.php';
require_once '../helpers.php';

AdminAuth::requireLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $name = sanitizeInput($_POST['name'] ?? '');
    $email = sanitizeInput($_POST['email'] ?? '');
    $causeId = filter_var($_POST['cause_id'] ?? null, FILTER_VALIDATE_INT);
    $amount = filter_var($_POST['amount'] ?? null, FILTER_VALIDATE_FLOAT);
    $notes = sanitizeInput($_POST['notes'] ?? '');

    if (($_POST['cause_id'] ?? '') === '') {
        $causeId = null;
    }

    if (
        !$id ||
        !validateEmail($email) ||
        $amount === false ||
        $amount <= 0
    ) {
        redirectWithAlert('../donations.php', 'Invalid input. Please try again.', 'error');
    }

    try {
        $pdo = getDBConnection();

        if ($causeId !== null) {
            $causeStmt = $pdo->prepare('SELECT id FROM causes WHERE id = ? LIMIT 1');
            $causeStmt->execute([(int) $causeId]);
            if (!$causeStmt->fetch()) {
                redirectWithAlert('../donations.php', 'The selected cause does not exist.', 'error');
            }
        }

        $stmt = $pdo->prepare('
            UPDATE donations
            SET name = ?, email = ?, cause_id = ?, amount = ?, notes = ?
            WHERE id = ?
        ');
        $stmt->execute([$name, $email, $causeId, $amount, $notes, $id]);
        redirectWithAlert('../donations.php', 'Donation updated successfully!', 'success');
    } catch (PDOException $e) {
        error_log('Update donation error: ' . $e->getMessage());
        redirectWithAlert('../donations.php', 'Error updating donation.', 'error');
    }
}
?>
