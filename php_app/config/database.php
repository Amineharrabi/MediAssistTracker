<?php
require_once __DIR__ . '/load_env.php';
$supabaseUrl = $_ENV['SUPABASE_URL'] ?? null;
$supabaseKey = $_ENV['SUPABASE_KEY'] ?? null;

if (!$supabaseUrl || !$supabaseKey) {
    die("Missing Supabase credentials in .env file.");
}


return [
    'url' => $supabaseUrl,
    'key' => $supabaseKey
];
