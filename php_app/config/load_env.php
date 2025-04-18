<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use Dotenv\Dotenv;

// Initialize Dotenv
$dotenv = Dotenv::createImmutable(__DIR__, null, true); // Enable putenv()
$dotenv->load();
