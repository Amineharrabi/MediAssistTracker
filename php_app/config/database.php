<?php
require_once __DIR__ . '/../../load_env.php';

$db_host = $_ENV['DB_HOST'];
$db_port = $_ENV['DB_PORT'];
$db_name = $_ENV['DB_NAME'];
$db_user = $_ENV['DB_USER'];
$db_pass = $_ENV['DB_PASS'];

$dsn = "pgsql:host=$db_host;port=$db_port;dbname=$db_name;";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $db_user, $db_pass, $options);

    // Optional: set encoding
    $pdo->exec("SET NAMES 'utf8'");

    return $pdo;
} catch (PDOException $e) {
    die('Database Connection Error: ' . $e->getMessage());
}
