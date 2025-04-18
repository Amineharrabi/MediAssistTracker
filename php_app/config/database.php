<?php
require_once __DIR__ . '/load_env.php';

// Load Supabase credentials from environment
$supabaseUrl = $_ENV['SUPABASE_URL'] ?? null;
$supabaseKey = $_ENV['SUPABASE_KEY'] ?? null;

if (!$supabaseUrl || !$supabaseKey) {
    die("Missing Supabase credentials in .env file.");
}


// Return credentials for use
return [
    'url' => $supabaseUrl,
    'key' => $supabaseKey
];
