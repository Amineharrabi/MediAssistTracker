<?php

$envPath = __DIR__ . '/../public/.env';

if (!file_exists($envPath)) {
    die(".env file not found at $envPath");
}

$lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

foreach ($lines as $line) {
    if (str_starts_with(trim($line), '#') || !str_contains($line, '=')) {
        continue;
    }

    list($key, $value) = explode('=', $line, 2);
    $key = trim($key);
    $value = trim($value, " \t\n\r\0\x0B\"'"); // trim quotes and whitespace

    $_ENV[$key] = $value;
    putenv("$key=$value"); // Makes getenv() work
}
