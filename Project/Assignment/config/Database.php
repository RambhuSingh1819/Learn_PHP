<?php
/**
 * Database Connection Manager (PDO)
 */

class Database {
    private static ?PDO $connection = null;

    /**
     * Get or create a PDO connection instance
     * 
     * @return PDO
     * @throws PDOException
     */
    public static function getConnection(): PDO {
        if (self::$connection === null) {
            $host = env('DB_HOST', '127.0.0.1');
            $port = env('DB_PORT', '3306');
            $dbName = env('DB_NAME', 'auth_system');
            $user = env('DB_USER', 'root');
            $pass = env('DB_PASS', '');
            
            // Build DSN
            $dsn = "mysql:host={$host};port={$port};dbname={$dbName};charset=utf8mb4";
            
            // Resolve MYSQL_ATTR_INIT_COMMAND dynamically to support PHP 8.5+ deprecation
            $initCommandKey = (defined('Pdo\Mysql::ATTR_INIT_COMMAND')) 
                ? constant('Pdo\Mysql::ATTR_INIT_COMMAND') 
                : (defined('PDO::MYSQL_ATTR_INIT_COMMAND') ? PDO::MYSQL_ATTR_INIT_COMMAND : 1002);
            
            // Connection Options for secure, high-performance database interactions
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false, // Use true prepared statements to prevent SQLi
                $initCommandKey              => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
            ];
            
            try {
                self::$connection = new PDO($dsn, $user, $pass, $options);
            } catch (PDOException $e) {
                error_log("Database connection failure: " . $e->getMessage());
                if (env('APP_ENV') === 'development') {
                    throw new PDOException("Database connection error: " . $e->getMessage(), (int)$e->getCode());
                } else {
                    throw new PDOException("A database connection error occurred. Please contact the administrator.", 500);
                }
            }
        }
        
        return self::$connection;
    }
}
