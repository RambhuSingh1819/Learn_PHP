<?php
/**
 * Database Migration and Seeding Script
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/Database.php';

try {
    echo "Running Database Migration...\n";
    
    // Connect to MySQL server first
    $host = env('DB_HOST', '127.0.0.1');
    $port = env('DB_PORT', '3306');
    $user = env('DB_USER', 'root');
    $pass = env('DB_PASS', 'newpassword');
    $dbName = env('DB_NAME', 'internwork');
    
    $dsn = "mysql:host={$host};port={$port};charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    
    // Drop database first for fresh development migrations
    echo "Dropping database '{$dbName}' if exists for a fresh migration...\n";
    $pdo->exec("DROP DATABASE IF EXISTS `{$dbName}`");
    
    // Create database
    echo "Creating database '{$dbName}'...\n";
    $pdo->exec("CREATE DATABASE `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    
    // Reconnect using Database helper
    $db = Database::getConnection();
    echo "Connected to database successfully.\n";
    
    // Read schema file
    $schemaPath = __DIR__ . '/schema.sql';
    if (!file_exists($schemaPath)) {
        throw new Exception("Schema file not found at: {$schemaPath}");
    }
    
    $sql = file_get_contents($schemaPath);
    
    // Execute SQL schema statements
    echo "Executing schema.sql...\n";
    $db->exec($sql);
    echo "Tables and structures initialized successfully.\n";
    
    // Seed default Admin and User accounts
    echo "Seeding default Admin and User accounts...\n";
    
    // 1. Admin Account
    $adminName = 'System Admin';
    $adminEmail = 'admin@gmail.com';
    $adminPass = 'AdminPassword123!';
    $hashedAdminPass = password_hash($adminPass, PASSWORD_BCRYPT);
    
    $stmtAdmin = $db->prepare("
        INSERT INTO users (name, email, password, role, is_verified) 
        VALUES (:name, :email, :password, 'Admin', 1)
    ");
    $stmtAdmin->execute([
        ':name' => $adminName,
        ':email' => $adminEmail,
        ':password' => $hashedAdminPass
    ]);
    
    // 2. Standard User Account
    $userName = 'Regular User';
    $userEmail = 'user@gmail.com';
    $userPass = 'UserPassword123!';
    $hashedUserPass = password_hash($userPass, PASSWORD_BCRYPT);
    
    $stmtUser = $db->prepare("
        INSERT INTO users (name, email, password, role, is_verified) 
        VALUES (:name, :email, :password, 'User', 1)
    ");
    $stmtUser->execute([
        ':name' => $userName,
        ':email' => $userEmail,
        ':password' => $hashedUserPass
    ]);
    
    echo "\n==================================================\n";
    echo "SUCCESS: Database seeded successfully!\n";
    echo "Pre-seeded Accounts (Pre-Verified):\n\n";
    echo "1. ADMIN ACCOUNT:\n";
    echo "   Email: {$adminEmail}\n";
    echo "   Password: {$adminPass}\n\n";
    echo "2. USER ACCOUNT:\n";
    echo "   Email: {$userEmail}\n";
    echo "   Password: {$userPass}\n";
    echo "==================================================\n\n";
    
    echo "Database Migration Completed Successfully!\n";
    
} catch (Exception $e) {
    echo "MIGRATION FAILED: " . $e->getMessage() . "\n";
    exit(1);
}
