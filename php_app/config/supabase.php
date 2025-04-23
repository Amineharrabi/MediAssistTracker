<?php

class SupabaseConfig {
    private static $maxRetries = 3;
    private static $retryDelay = 1; // seconds
    
    public static function getRequestOptions() {
        return [
            'timeout' => 30, // 30 seconds timeout for requests
            'connect_timeout' => 10, // 10 seconds timeout for connection
            'headers' => [
                'apikey' => $_ENV['SUPABASE_KEY'],
                'Authorization' => 'Bearer ' . $_ENV['SUPABASE_KEY']
            ]
        ];
    }
    
    public static function handleRequest($callback) {
        $attempts = 0;
        $lastException = null;
        
        while ($attempts < self::$maxRetries) {
            try {
                return $callback();
            } catch (Exception $e) {
                $lastException = $e;
                $attempts++;
                
                if ($attempts < self::$maxRetries) {
                    sleep(self::$retryDelay * $attempts);
                }
            }
        }
        
        throw $lastException;
    }
} 