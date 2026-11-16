<?php
require_once '../config.php';
require_once '../auth.php';
require_once '../helpers.php';

AdminAuth::requireLogin();

$table = $_GET['table'] ?? null;

// Allowed tables
$allowedTables = ['donations', 'volunteers', 'contact_messages', 'newsletter_subscribers'];

if (!$table || !in_array($table, $allowedTables)) {
    die('Invalid table specified.');
}

try {
    $pdo = getDBConnection();

    // Fetch data based on table
    switch ($table) {
        case 'donations':
            $stmt = $pdo->query('SELECT donations.id, donations.name, donations.email, donations.amount, donations.notes, donations.donated_at, users.full_name AS member_full_name, users.sa_id AS member_sa_id FROM donations LEFT JOIN users ON donations.user_id = users.id ORDER BY donations.donated_at DESC');
            $data = $stmt->fetchAll();
            $headers = ['ID', 'Member Name', 'Member ID Number', 'Contact Email', 'Donation Amount', 'Notes', 'Donation Date'];
            $filename = 'donations_' . date('Y-m-d_His') . '.csv';

            // Convert to CSV format
            $csvData = [];
            foreach ($data as $row) {
                $csvData[] = [
                    $row['id'],
                    $row['member_full_name'] ?: $row['name'],
                    $row['member_sa_id'] ?: '',
                    $row['email'],
                    $row['amount'],
                    $row['notes'],
                    $row['donated_at']
                ];
            }
            break;

        case 'volunteers':
            $stmt = $pdo->query('SELECT id, name, email, reason, status, notes, signed_up_at FROM volunteers ORDER BY signed_up_at DESC');
            $data = $stmt->fetchAll();
            $headers = ['ID', 'Name', 'Email', 'Reason', 'Status', 'Notes', 'Sign-up Date'];
            $filename = 'volunteers_' . date('Y-m-d_His') . '.csv';

            // Convert to CSV format
            $csvData = [];
            foreach ($data as $row) {
                $csvData[] = [
                    $row['id'],
                    $row['name'],
                    $row['email'],
                    $row['reason'],
                    $row['status'],
                    $row['notes'],
                    $row['signed_up_at']
                ];
            }
            break;

        case 'contact_messages':
            $stmt = $pdo->query('SELECT id, name, email, subject, message, notes, response_sent_at, submitted_at FROM contact_messages ORDER BY submitted_at DESC');
            $data = $stmt->fetchAll();
            $headers = ['ID', 'Name', 'Email', 'Subject', 'Message', 'Notes', 'Responded', 'Date'];
            $filename = 'contacts_' . date('Y-m-d_His') . '.csv';

            // Convert to CSV format
            $csvData = [];
            foreach ($data as $row) {
                $csvData[] = [
                    $row['id'],
                    $row['name'],
                    $row['email'],
                    $row['subject'],
                    $row['message'],
                    $row['notes'],
                    $row['response_sent_at'] ? 'Yes' : 'No',
                    $row['submitted_at']
                ];
            }
            break;

        case 'newsletter_subscribers':
            $stmt = $pdo->query('SELECT id, email, subscribed_at FROM newsletter_subscribers ORDER BY subscribed_at DESC');
            $data = $stmt->fetchAll();
            $headers = ['ID', 'Email', 'Subscribed Date'];
            $filename = 'newsletter_subscribers_' . date('Y-m-d_His') . '.csv';

            // Convert to CSV format
            $csvData = [];
            foreach ($data as $row) {
                $csvData[] = [
                    $row['id'],
                    $row['email'],
                    $row['subscribed_at']
                ];
            }
            break;
    }

    // Export to CSV
    exportToCSV($filename, $csvData, $headers);

} catch (PDOException $e) {
    error_log('Export CSV error: ' . $e->getMessage());
    die('Error exporting data.');
}
?>
