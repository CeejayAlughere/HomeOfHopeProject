<?php
/**
 * Authentication Handler
 * Manages login, logout, and session validation
 */

require_once __DIR__ . '/config.php';

class AdminAuth {
    
    /**
     * Check if user is logged in
     */
    public static function isLoggedIn() {
        return isset($_SESSION['admin_id']) && isset($_SESSION['admin_username']);
    }
    
    /**
     * Check session timeout
     */
    public static function checkSessionTimeout() {
        if (isset($_SESSION['last_activity'])) {
            if (time() - $_SESSION['last_activity'] > SESSION_TIMEOUT) {
                self::logout();
                return false;
            }
        }
        $_SESSION['last_activity'] = time();
        return true;
    }
    
    /**
     * Require admin login
     */
    public static function requireLogin() {
        if (!self::isLoggedIn() || !self::checkSessionTimeout()) {
            header('Location: ' . ADMIN_URL . 'login.php');
            exit;
        }
    }
    
    /**
     * Authenticate user
     */
    public static function login($username, $password) {
        try {
            $pdo = getDBConnection();
            
            // Sanitize username
            $username = trim(htmlspecialchars($username));
            
            // Query database
            $stmt = $pdo->prepare('SELECT id, username, password_hash FROM admin_users WHERE username = ?');
            $stmt->execute([$username]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($password, $user['password_hash'])) {
                // Set session variables
                $_SESSION['admin_id'] = $user['id'];
                $_SESSION['admin_username'] = $user['username'];
                $_SESSION['last_activity'] = time();
                return true;
            }
            
            return false;
        } catch (PDOException $e) {
            error_log('Login error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Logout user
     */
    public static function logout() {
        session_destroy();
        header('Location: ' . ADMIN_URL . 'login.php');
        exit;
    }
    
    /**
     * Get current logged-in admin username
     */
    public static function getCurrentUsername() {
        return $_SESSION['admin_username'] ?? 'Unknown';
    }
}

// Check if token/session needs refresh
if (isset($_SESSION['admin_id'])) {
    AdminAuth::checkSessionTimeout();
}
?>
