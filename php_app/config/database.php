<?php
/**
 * Database Configuration
 */

// Get environment variables or set defaults
$db_host = getenv('PGHOST') ?: 'localhost';
$db_port = getenv('PGPORT') ?: '5432';
$db_name = getenv('PGDATABASE') ?: 'mediassist';
$db_user = getenv('PGUSER') ?: 'postgres';
$db_pass = getenv('PGPASSWORD') ?: '';

// Build DSN (Data Source Name)
$dsn = "pgsql:host=$db_host;port=$db_port;dbname=$db_name;";

// PDO options
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    // Create a new PDO instance
    $pdo = new PDO($dsn, $db_user, $db_pass, $options);
    
    // Set connection encoding
    $pdo->exec("SET NAMES 'utf8'");
    
    return $pdo;
} catch (PDOException $e) {
    // Handle connection error
    die('Database Connection Error: ' . $e->getMessage());
}