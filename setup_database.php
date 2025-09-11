<?php
// Database setup script
// Run this once to create the required tables

$host = "localhost"; 
$user = "root"; 
$pass = ""; 
$dbname = "test2"; 

try {
    // Connect to MySQL server first
    $pdo = new PDO("mysql:host=$host;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create database if it doesn't exist
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname`");
    
    // Use the database
    $pdo->exec("USE `$dbname`");
    
    // Create tables
    $sql = "
    CREATE TABLE IF NOT EXISTS users (
      id INT AUTO_INCREMENT PRIMARY KEY,
      username VARCHAR(100) NOT NULL UNIQUE,
      password_hash VARCHAR(255) NOT NULL,
      display_name VARCHAR(150) DEFAULT NULL,
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

    CREATE TABLE IF NOT EXISTS references_entity (
      id INT AUTO_INCREMENT PRIMARY KEY,
      title VARCHAR(255) NOT NULL,
      url TEXT,
      category ENUM('document', 'link', 'paper', 'tool', 'person', 'other') DEFAULT 'other',
      comment TEXT,
      created_by INT,
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
    );

    CREATE TABLE IF NOT EXISTS comments (
      id INT AUTO_INCREMENT PRIMARY KEY,
      reference_id INT NOT NULL,
      user_id INT,
      comment TEXT NOT NULL,
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      FOREIGN KEY (reference_id) REFERENCES references_entity(id) ON DELETE CASCADE,
      FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
    );
    ";
    
    $pdo->exec($sql);
    
    // Create a default user (username: admin, password: admin123)
    $defaultPassword = password_hash('admin123', PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT IGNORE INTO users (username, password_hash, display_name) VALUES (?, ?, ?)");
    $stmt->execute(['admin', $defaultPassword, 'Administrator']);
    
    echo "Database setup completed successfully!<br>";
    echo "Default login: admin / admin123<br>";
    echo "<a href='login.php'>Go to Login</a>";
    
} catch (PDOException $e) {
    die("Database setup failed: " . $e->getMessage());
}
?>
