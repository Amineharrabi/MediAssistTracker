<?php
require_once __DIR__ . '/php_app/config/load_env.php';

echo 'DB_HOST: ' . $_ENV['DB_HOST'] . PHP_EOL;
echo 'DB_NAME: ' . $_ENV['DB_NAME'] . PHP_EOL;
