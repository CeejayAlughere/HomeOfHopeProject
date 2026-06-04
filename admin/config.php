<?php
/**
 * Admin Dashboard Configuration
 * Centralized database and application settings
 */

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'homeofhope');
define('DB_USER', 'root');
define('DB_PASSWORD', '');
define('DB_CHARSET', 'utf8mb4');

// Application Settings
define('APP_NAME', 'Home of Hope Admin');
define('ADMIN_URL', '/HomeOfHopeProject/admin/');
define('SITE_URL', '/HomeOfHopeProject/');

// Session Configuration
define('SESSION_TIMEOUT', 3600); // 1 hour in seconds

// Initialize session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// PDO Connection Function
function getDBConnection() {
    try {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
        $pdo = new PDO($dsn, DB_USER, DB_PASSWORD, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]);
        return $pdo;
    } catch (PDOException $e) {
        die('Database connection failed: ' . $e->getMessage());
    }
}

// Global error handling
function handleError($message, $type = 'error') {
    $_SESSION['alert'] = [
        'type' => $type,
        'message' => $message
    ];
}

// Display alert messages
function displayAlert() {
    if (isset($_SESSION['alert'])) {
        $alert = $_SESSION['alert'];
        $class = $alert['type'] === 'success' ? 'alert-success' : 'alert-danger';
        echo '<div class="alert ' . $class . ' alert-dismissible fade show" role="alert">
                ' . htmlspecialchars($alert['message']) . '
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
              </div>';
        unset($_SESSION['alert']);
    }
}

// Redirect with message
function redirectWithAlert($url, $message, $type = 'success') {
    handleError($message, $type);
    header('Location: ' . $url);
    exit;
}
?>
