<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // Connect to MySQL
    $pdo = new PDO(
        "mysql:host=localhost",
        "root",
        "",
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    echo "MySQL Connection: OK<br>";

    // Create database
    $pdo->exec("CREATE DATABASE IF NOT EXISTS cruzewheelz_admin");
    $pdo->exec("USE cruzewheelz_admin");
    echo "Database created/selected: OK<br>";

    // Create admin_users table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS admin_users (
            id INT PRIMARY KEY AUTO_INCREMENT,
            username VARCHAR(50) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            name VARCHAR(100) NOT NULL,
            email VARCHAR(100) UNIQUE NOT NULL,
            status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
            last_login TIMESTAMP NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )
    ");
    echo "admin_users table created: OK<br>";

    // Create admin_logs table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS admin_logs (
            id INT PRIMARY KEY AUTO_INCREMENT,
            admin_id INT,
            action VARCHAR(50) NOT NULL,
            description TEXT,
            ip_address VARCHAR(45),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (admin_id) REFERENCES admin_users(id) ON DELETE SET NULL
        )
    ");
    echo "admin_logs table created: OK<br>";

    // Create default admin users
    $admins = [
        [
            'username' => 'admin',
            'password' => 'admin123',
            'name' => 'Admin',
            'email' => 'admin@cruzewheelz.com'
        ],
        [
            'username' => 'prembadiger9@gmail.com',
            'password' => '@SnehaPrem1',
            'name' => 'Prem Badiger',
            'email' => 'prembadiger9@gmail.com'
        ]
    ];

    $stmt = $pdo->prepare("
        INSERT INTO admin_users (username, password, name, email)
        VALUES (?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE
        password = VALUES(password),
        name = VALUES(name)
    ");

    foreach ($admins as $admin) {
        $hashed_password = password_hash($admin['password'], PASSWORD_DEFAULT);
        $stmt->execute([
            $admin['username'],
            $hashed_password,
            $admin['name'],
            $admin['email']
        ]);
        echo "Admin user {$admin['username']} created/updated: OK<br>";
    }

    // Verify admin users
    echo "<br>Current admin users:<br>";
    $stmt = $pdo->query("SELECT id, username, name, email, status FROM admin_users");
    while ($row = $stmt->fetch()) {
        echo "ID: {$row['id']}, Username: {$row['username']}, Name: {$row['name']}, Email: {$row['email']}, Status: {$row['status']}<br>";
    }

    echo "<br>Setup completed successfully!";

} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?> 