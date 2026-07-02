<?php
/**
 * Helper Functions for Admin Dashboard
 */

/**
 * Format currency in South African Rand
 */
function formatCurrency($amount) {
    return 'R' . number_format((float)$amount, 2, '.', ',');
}

/**
 * Format date
 */
function formatDate($datetime) {
    if (empty($datetime)) return 'N/A';
    return date('M d, Y \a\t h:i A', strtotime($datetime));
}

/**
 * Format short date
 */
function formatDateShort($datetime) {
    if (empty($datetime)) return 'N/A';
    return date('M d, Y', strtotime($datetime));
}

/**
 * Sanitize input
 */
function sanitizeInput($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

/**
 * Get status badge HTML
 */
function getStatusBadge($status) {
    $badges = [
        'pending' => '<span class="badge bg-warning text-dark">Pending</span>',
        'verified' => '<span class="badge bg-success">Verified</span>',
        'new' => '<span class="badge bg-info">New</span>',
        'contacted' => '<span class="badge bg-primary">Contacted</span>',
        'declined' => '<span class="badge bg-danger">Declined</span>',
        'accepted' => '<span class="badge bg-success">Accepted</span>'
    ];
    return $badges[$status] ?? '<span class="badge bg-secondary">' . ucfirst($status) . '</span>';
}

/**
 * Get status dropdown HTML
 */
function getStatusDropdown($currentStatus, $fieldName = 'status') {
    $options = [
        'pending' => 'Pending',
        'verified' => 'Verified'
    ];
    
    $html = '<select class="form-select form-select-sm" name="' . $fieldName . '">';
    foreach ($options as $value => $label) {
        $selected = $currentStatus === $value ? 'selected' : '';
        $html .= '<option value="' . $value . '" ' . $selected . '>' . $label . '</option>';
    }
    $html .= '</select>';
    return $html;
}

/**
 * Truncate text
 */
function truncateText($text, $length = 50) {
    if (strlen($text) > $length) {
        return substr($text, 0, $length) . '...';
    }
    return $text;
}

/**
 * Export array to CSV
 */
function exportToCSV($filename, $data, $headers = []) {
    // Set headers for CSV download
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    
    // Open output stream
    $output = fopen('php://output', 'w');
    
    // Add CSV headers
    if (!empty($headers)) {
        fputcsv($output, $headers);
    }
    
    // Add data rows
    foreach ($data as $row) {
        fputcsv($output, $row);
    }
    
    fclose($output);
    exit;
}

/**
 * Get days ago string
 */
function getDaysAgo($datetime) {
    $date = new DateTime($datetime);
    $now = new DateTime();
    $interval = $now->diff($date);
    
    if ($interval->days == 0) {
        return 'Today';
    } elseif ($interval->days == 1) {
        return 'Yesterday';
    } else {
        return $interval->days . ' days ago';
    }
}

/**
 * Validate email
 */
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Check whether a column exists in a table
 */
function columnExists(PDO $pdo, $table, $column) {
    $stmt = $pdo->prepare('SHOW COLUMNS FROM ' . $table . ' LIKE ?');
    $stmt->execute([$column]);
    return (bool) $stmt->fetch();
}

/**
 * Get database statistics
 */
function getDatabaseStats() {
    try {
        $pdo = getDBConnection();
        
        $totalDonations = $pdo->query('SELECT COUNT(*) as count FROM donations')->fetch()['count'] ?? 0;
        $totalAmount = $pdo->query('SELECT SUM(amount) as total FROM donations')->fetch()['total'] ?? 0;
        $totalVolunteers = $pdo->query('SELECT COUNT(*) as count FROM volunteers')->fetch()['count'] ?? 0;
        $totalContacts = $pdo->query('SELECT COUNT(*) as count FROM contact_messages')->fetch()['count'] ?? 0;
        $totalSubscribers = $pdo->query('SELECT COUNT(*) as count FROM newsletter_subscribers')->fetch()['count'] ?? 0;
        $verifiedDonations = 0;

        if (columnExists($pdo, 'donations', 'status')) {
            $verifiedDonations = $pdo->query('SELECT COUNT(*) as count FROM donations WHERE status = "verified"')->fetch()['count'] ?? 0;
        }

        return [
            'total_donations' => (int) $totalDonations,
            'total_amount' => $totalAmount !== null ? $totalAmount : 0,
            'total_volunteers' => (int) $totalVolunteers,
            'total_contacts' => (int) $totalContacts,
            'total_subscribers' => (int) $totalSubscribers,
            'verified_donations' => (int) $verifiedDonations,
            'contacted_volunteers' => (int) ($pdo->query('SELECT COUNT(*) as count FROM volunteers WHERE status = "contacted"')->fetch()['count'] ?? 0)
        ];
    } catch (PDOException $e) {
        error_log('Stats query error: ' . $e->getMessage());
        return [
            'total_donations' => 0,
            'total_amount' => 0,
            'total_volunteers' => 0,
            'total_contacts' => 0,
            'total_subscribers' => 0,
            'verified_donations' => 0,
            'contacted_volunteers' => 0
        ];
    }
}

/**
 * Get recent submissions
 */
function getRecentSubmissions($limit = 5) {
    try {
        $pdo = getDBConnection();
        
        $stmt = $pdo->prepare('
            SELECT "donation" as type, donations.id, donations.name, donations.email, DATE(donations.donated_at) as date, donations.amount as data, donations.status, users.full_name as member_full_name, users.sa_id as member_sa_id 
            FROM donations 
            LEFT JOIN users ON donations.user_id = users.id 
            UNION ALL
            SELECT "volunteer" as type, id, name, email, DATE(signed_up_at) as date, reason as data, status, NULL as member_full_name, NULL as member_sa_id 
            FROM volunteers 
            UNION ALL
            SELECT "contact" as type, id, name, email, DATE(submitted_at) as date, subject as data, NULL as status, NULL as member_full_name, NULL as member_sa_id 
            FROM contact_messages 
            ORDER BY date DESC 
            LIMIT ?
        ');
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log('Recent submissions error: ' . $e->getMessage());
        return [];
    }
}
?>
