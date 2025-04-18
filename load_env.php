<?php
require_once __DIR__ . '/phpdotenv/src/Dotenv.php';
require_once __DIR__ . '/phpdotenv/src/Loader/Loader.php';
require_once __DIR__ . '/phpdotenv/src/Parser/Parser.php';
require_once __DIR__ . '/phpdotenv/src/Validator.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();
