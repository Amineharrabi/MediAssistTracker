<?php
require_once __DIR__ . '/vendor/vlucas/phpdotenv/src/Dotenv.php';
require_once __DIR__ . '/vendor/vlucas/phpdotenv/src/Loader.php';
require_once __DIR__ . '/vendor/vlucas/phpdotenv/src/Parser.php';
require_once __DIR__ . '/vendor/vlucas/phpdotenv/src/Validator.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();
