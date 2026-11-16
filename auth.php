<?php
session_start();

function getPDO() {
    static $pdo;

    if ($pdo) {
        return $pdo;
    }

    $host = 'localhost';
    $dbName = 'homeofhope';
    $dbUser = 'root';
    $dbPass = '';

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbName;charset=utf8", $dbUser, $dbPass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        ensureUserTable($pdo);
        ensureUserColumns($pdo);
        ensureDonationColumns($pdo);
        return $pdo;
    } catch (PDOException $e) {
        die('Database connection failed.');
    }
}

function ensureUserTable(PDO $pdo) {
    $pdo->exec("CREATE TABLE IF NOT EXISTS `users` (
        `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `full_name` varchar(150) NOT NULL,
        `sa_id` varchar(13) NOT NULL,
        `username` varchar(100) NOT NULL UNIQUE,
        `email` varchar(150) NOT NULL,
        `password_hash` varchar(255) NOT NULL,
        `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;");
}

function ensureUserColumns(PDO $pdo) {
    $column = $pdo->query("SHOW COLUMNS FROM users LIKE 'email'")->fetch(PDO::FETCH_ASSOC);
    if (!$column) {
        $pdo->exec("ALTER TABLE users ADD COLUMN email varchar(150) NOT NULL AFTER username");
    }
}

function ensureDonationColumns(PDO $pdo) {
    $column = $pdo->query("SHOW COLUMNS FROM donations LIKE 'user_id'")->fetch(PDO::FETCH_ASSOC);
    if (!$column) {
        $pdo->exec("ALTER TABLE donations ADD COLUMN user_id int(11) NULL AFTER id");
    }

    $column = $pdo->query("SHOW COLUMNS FROM donations LIKE 'bank'")->fetch(PDO::FETCH_ASSOC);
    if (!$column) {
        $pdo->exec("ALTER TABLE donations ADD COLUMN bank varchar(100) NULL AFTER amount");
    }
}

function sanitize($value) {
    return htmlspecialchars(trim($value));
}

function isLoggedIn() {
    return !empty($_SESSION['user_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }

    try {
        $stmt = getPDO()->prepare('SELECT id, full_name, username, email, phone_number FROM users WHERE id = ?');
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $_SESSION['user'] = $user;
            return $user;
        }
    } catch (PDOException $e) {
        return null;
    }

    return null;
}

function findUserByUsername($username) {
    try {
        $stmt = getPDO()->prepare('SELECT * FROM users WHERE username = ?');
        $stmt->execute([$username]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return false;
    }
}

function createUser($fullName, $saId, $username, $email, $passwordHash) {
    $stmt = getPDO()->prepare('INSERT INTO users (full_name, sa_id, username, email, password_hash) VALUES (?, ?, ?, ?, ?)');
    return $stmt->execute([$fullName, $saId, $username, $email, $passwordHash]);
}
