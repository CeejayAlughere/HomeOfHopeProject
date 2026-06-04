<?php
/**
 * Admin Setup Verification Script
 * Run this to check and set up the admin account
 */

// Database Configuration
$dbHost = 'localhost';
$dbName = 'homeofhope';
$dbUser = 'root';
$dbPassword = '';

try {
    $pdo = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUser, $dbPassword);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>Admin Setup Verification</h2>";
    
    // Check if admin_users table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'admin_users'");
    $tableExists = $stmt->rowCount() > 0;
    
    if ($tableExists) {
        echo "<p style='color: green;'>✓ admin_users table exists</p>";
        
        // Check if admin user exists
        $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE username = 'admin'");
        $stmt->execute();
        $admin = $stmt->fetch();
        
        if ($admin) {
            echo "<p style='color: green;'>✓ Admin user exists</p>";
        } else {
            echo "<p style='color: red;'>✗ Admin user does not exist - Creating...</p>";
            $hash = password_hash('admin@123', PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO admin_users (username, password_hash) VALUES (?, ?)");
            $stmt->execute(['admin', $hash]);
            echo "<p style='color: green;'>✓ Admin user created with credentials: admin / admin@123</p>";
        }
    } else {
        echo "<p style='color: red;'>✗ admin_users table does not exist</p>";
        echo "<p>Creating tables and setting up admin user...</p>";
        
        // Create admin_users table
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS admin_users (
              id int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
              username varchar(100) NOT NULL UNIQUE,
              password_hash varchar(255) NOT NULL,
              created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        ");
        
        // Add columns to donations
        try {
            $pdo->exec("ALTER TABLE donations ADD COLUMN status varchar(20) NOT NULL DEFAULT 'pending' AFTER amount");
        } catch (Exception $e) {
            // Column might already exist
        }
        
        try {
            $pdo->exec("ALTER TABLE donations ADD COLUMN notes text AFTER status");
        } catch (Exception $e) {
            // Column might already exist
        }
        
        // Add columns to volunteers
        try {
            $pdo->exec("ALTER TABLE volunteers ADD COLUMN status varchar(20) NOT NULL DEFAULT 'new' AFTER reason");
        } catch (Exception $e) {
            // Column might already exist
        }
        
        try {
            $pdo->exec("ALTER TABLE volunteers ADD COLUMN notes text AFTER status");
        } catch (Exception $e) {
            // Column might already exist
        }
        
        // Add columns to contact_messages
        try {
            $pdo->exec("ALTER TABLE contact_messages ADD COLUMN notes text AFTER message");
        } catch (Exception $e) {
            // Column might already exist
        }
        
        try {
            $pdo->exec("ALTER TABLE contact_messages ADD COLUMN response_sent_at datetime AFTER notes");
        } catch (Exception $e) {
            // Column might already exist
        }
        
        // Insert admin user
        $hash = password_hash('admin@123', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO admin_users (username, password_hash) VALUES (?, ?)");
        $stmt->execute(['admin', $hash]);
        
        echo "<p style='color: green;'>✓ All tables created and admin user set up!</p>";
    }
    
    echo "<hr>";
    echo "<h3>Setup Complete!</h3>";
    echo "<p>You can now log in with:</p>";
    echo "<ul>";
    echo "<li><strong>Username:</strong> admin</li>";
    echo "<li><strong>Password:</strong> admin@123</li>";
    echo "</ul>";
    echo "<p><a href='admin/login.php'>Go to Admin Login →</a></p>";
    
} catch (PDOException $e) {
    echo "<p style='color: red;'><strong>Database Error:</strong> " . $e->getMessage() . "</p>";
    echo "<p>Make sure:</p>";
    echo "<ul>";
    echo "<li>MySQL/MariaDB is running</li>";
    echo "<li>Database 'homeofhope' exists</li>";
    echo "<li>Database credentials are correct (root / no password)</li>";
    echo "</ul>";
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Setup Verification</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            max-width: 600px;
        }
        h2, h3 {
            color: #333;
        }
        p {
            line-height: 1.6;
        }
        a {
            color: #667eea;
            text-decoration: none;
            font-weight: bold;
        }
        a:hover {
            text-decoration: underline;
        }
        hr {
            margin: 20px 0;
        }
    </style>
</head>
<body>
</body>
</html>
