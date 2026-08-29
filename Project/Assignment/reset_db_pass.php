<?php
try {
    echo "Connecting to MySQL as root using current password 'newpassword'...\n";
    $db = new PDO("mysql:host=127.0.0.1;port=3306;charset=utf8mb4", "root", "newpassword", [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    
    $hosts = ['localhost', '127.0.0.1', '::1'];
    
    foreach ($hosts as $host) {
        echo "Resetting password for 'root'@'{$host}' to empty...\n";
        try {
            $db->exec("ALTER USER 'root'@'{$host}' IDENTIFIED BY ''");
            echo "  ALTER USER succeeded for {$host}.\n";
        } catch (Exception $e1) {
            try {
                $db->exec("SET PASSWORD FOR 'root'@'{$host}' = PASSWORD('')");
                echo "  SET PASSWORD succeeded for {$host}.\n";
            } catch (Exception $e2) {
                try {
                    $db->exec("UPDATE mysql.user SET authentication_string='', password_expired='N' WHERE User='root' AND Host='{$host}'");
                    echo "  UPDATE mysql.user succeeded for {$host}.\n";
                } catch (Exception $e3) {
                    echo "  FAILED for {$host}: " . $e3->getMessage() . "\n";
                }
            }
        }
    }
    
    $db->exec("FLUSH PRIVILEGES");
    echo "SUCCESS: Privileges flushed. MySQL root password is now empty!\n";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
