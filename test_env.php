<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "Script is running...<br>";

// Your Supabase project URL and API Key
$supabaseUrl = 'https://toxhzksisgnixojetdmg.supabase.co';
$supabaseKey = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6InRveGh6a3Npc2duaXhvamV0ZG1nIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NDQ5NzE3NzgsImV4cCI6MjA2MDU0Nzc3OH0.IuTiPayuQzf2EJtZ-q4U3eAu_H0nusLCP4s1SFOAldc'; // Replace with your actual anon/public key

// Function to query data from a table
function queryData($supabaseUrl, $supabaseKey, $tableName, $options = [])
{
    // Base URL for the REST API
    $url = "$supabaseUrl/rest/v1/$tableName";

    // Add query parameters if specified
    if (!empty($options)) {
        $queryParams = [];

        // Handle common options
        if (isset($options['select'])) {
            $queryParams[] = "select=" . urlencode($options['select']);
        }

        if (isset($options['order'])) {
            $queryParams[] = "order=" . urlencode($options['order']);
        }

        if (isset($options['limit'])) {
            $queryParams[] = "limit=" . urlencode($options['limit']);
        }

        if (isset($options['offset'])) {
            $queryParams[] = "offset=" . urlencode($options['offset']);
        }

        if (isset($options['filter'])) {
            foreach ($options['filter'] as $field => $value) {
                $queryParams[] = "$field=" . urlencode($value);
            }
        }

        if (!empty($queryParams)) {
            $url .= "?" . implode("&", $queryParams);
        }
    }

    // Initialize curl
    $curl = curl_init();

    // Set curl options
    curl_setopt_array($curl, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            "apikey: $supabaseKey",
            "Authorization: Bearer $supabaseKey",
            "Content-Type: application/json",
            "Prefer: return=representation"
        ]
    ]);

    // For debugging, print the exact URL being used
    echo "Requesting URL: $url<br>";

    // Execute the request
    $response = curl_exec($curl);
    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    $error = curl_error($curl);

    // Close curl
    curl_close($curl);

    // Check for errors
    if ($error) {
        return [
            'success' => false,
            'message' => "cURL Error: $error",
            'status' => 0
        ];
    }

    // Process response
    $data = json_decode($response, true);

    return [
        'success' => ($httpCode >= 200 && $httpCode < 300),
        'data' => $data,
        'status' => $httpCode
    ];
}

// Function to insert data
function insertData($supabaseUrl, $supabaseKey, $tableName, $data)
{
    $url = "$supabaseUrl/rest/v1/$tableName";

    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => json_encode($data),
        CURLOPT_HTTPHEADER => [
            "apikey: $supabaseKey",
            "Authorization: Bearer $supabaseKey",
            "Content-Type: application/json",
            "Prefer: return=representation"
        ]
    ]);

    $response = curl_exec($curl);
    $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    $error = curl_error($curl);

    curl_close($curl);

    if ($error) {
        return [
            'success' => false,
            'message' => "cURL Error: $error",
            'status' => 0
        ];
    }

    $data = json_decode($response, true);

    return [
        'success' => ($httpCode >= 200 && $httpCode < 300),
        'data' => $data,
        'status' => $httpCode
    ];
}

// Example usage
try {
    echo "Using Supabase REST API to query the medication table...<br>";

    // Query data from the medication table
    $tableName = 'medication'; // Using the medication table from your schema
    $result = queryData($supabaseUrl, $supabaseKey, $tableName, [
        'select' => '*',
        'limit' => 10,
        'order' => 'created_at.desc'
    ]);

    if ($result['success']) {
        echo "Query successful!<br>";
        echo "Data retrieved: <pre>" . print_r($result['data'], true) . "</pre>";
    } else {
        echo "Query failed with status code " . $result['status'] . "<br>";
        if (isset($result['message'])) {
            echo "Error message: " . $result['message'] . "<br>";
        }
    }

    // Example: To create a new medication entry, uncomment this code
    /*
    $newMedication = [
        'name' => 'Aspirin',
        'dosage' => '500mg',
        'frequency' => 'Once daily',
        'time' => 'Morning',
        'notes' => 'Take with food',
        'user_id' => 1 // Replace with an actual user ID from your database
    ];
    
    $insertResult = insertData($supabaseUrl, $supabaseKey, 'medication', $newMedication);
    
    if ($insertResult['success']) {
        echo "Insert successful!<br>";
        echo "New medication data: <pre>" . print_r($insertResult['data'], true) . "</pre>";
    } else {
        echo "Insert failed with status code " . $insertResult['status'] . "<br>";
        if (isset($insertResult['message'])) {
            echo "Error message: " . $insertResult['message'] . "<br>";
        }
    }
    */
} catch (Exception $e) {
    echo "An error occurred: " . $e->getMessage();
}
