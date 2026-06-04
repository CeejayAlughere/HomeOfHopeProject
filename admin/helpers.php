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
 * Get database statistics
 */
function getDatabaseStats() {
    try {
        $pdo = getDBConnection();
        
        $stats = [
            'total_donations' => $pdo->query('SELECT COUNT(*) as count FROM donations')->fetch()['count'],
            'total_amount' => $pdo->query('SELECT SUM(amount) as total FROM donations')->fetch()['total'],
            'total_volunteers' => $pdo->query('SELECT COUNT(*) as count FROM volunteers')->fetch()['count'],
            'total_contacts' => $pdo->query('SELECT COUNT(*) as count FROM contact_messages')->fetch()['count'],
            'total_subscribers' => $pdo->query('SELECT COUNT(*) as count FROM newsletter_subscribers')->fetch()['count'],
            'verified_donations' => $pdo->query('SELECT COUNT(*) as count FROM donations WHERE status = "verified"')->fetch()['count'],
            'contacted_volunteers' => $pdo->query('SELECT COUNT(*) as count FROM volunteers WHERE status = "contacted"')->fetch()['count']
        ];
        
        return $stats;
    } catch (PDOException $e) {
        error_log('Stats query error: ' . $e->getMessage());
        return [];
    }
}

/**
 * Get recent submissions
 */
function getRecentSubmissions($limit = 5) {
    try {
        $pdo = getDBConnection();
        
        $stmt = $pdo->prepare('
            SELECT "donation" as type, id, name, email, DATE(donated_at) as date, amount as data, status 
            FROM donations 
            UNION ALL
            SELECT "volunteer" as type, id, name, email, DATE(signed_up_at) as date, reason as data, status 
            FROM volunteers 
            UNION ALL
            SELECT "contact" as type, id, name, email, DATE(submitted_at) as date, subject as data, NULL as status 
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
