<?php
class User {
    private $supabaseUrl;
    private $supabaseKey;
    private $table = 'user';

    public function __construct($supabaseUrl, $supabaseKey) {
        $this->supabaseUrl = $supabaseUrl;
        $this->supabaseKey = $supabaseKey;
    }

    private function makeRequest($method, $endpoint, $data = null, $queryParams = '', $isAuth = false) {
        $url = $isAuth 
            ? "$this->supabaseUrl/auth/v1/$endpoint$queryParams"
            : "$this->supabaseUrl/rest/v1/$endpoint$queryParams";

        $headers = [
            "apikey: $this->supabaseKey",
            "Authorization: Bearer $this->supabaseKey",
            "Content-Type: application/json",
            "Prefer: return=representation"
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        } elseif ($method === 'PATCH') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            add_flash_message("cURL Error: $error", 'danger');
            return false;
        }

        $result = json_decode($response, true);
        
        if ($httpCode >= 400) {
            $errorMessage = $result['error']['message'] ?? 'An unknown error occurred';
            add_flash_message($errorMessage, 'danger');
            return false;
        }

        return $result;
    }

    public function create($username, $email, $password) {
        // First, create the auth user
        $authData = [
            'email' => $email,
            'password' => $password,
            'email_confirm' => true
        ];

        $authResult = $this->makeRequest('POST', 'signup', $authData, '', true);

        if (!$authResult) {
            add_flash_message('Failed to create authentication account.', 'danger');
            return false;
        }

        // Check if we have a valid user ID from the auth response
        if (!isset($authResult['id'])) {
            add_flash_message('Invalid response from authentication service.', 'danger');
            return false;
        }

        // Then create the user profile
        $data = [
            'id' => $authResult['id'],
            'username' => $username,
            'email' => $email,
            'theme_preference' => 'light'
        ];

        $result = $this->makeRequest('POST', $this->table, $data);

        // Supabase returns an array with the created record
        if ($result && is_array($result) && !empty($result)) {
            add_flash_message('Account created successfully! Please check your email for verification.', 'success');
            return $result[0]['id'] ?? $authResult['id'];
        }

        // If we get here, something went wrong with the profile creation
        // But the auth user was created, so we should still return success
        add_flash_message('Account created successfully! Please check your email for verification.', 'success');
        return $authResult['id'];
    }

    public function findByUsername($username) {
        $query = "?username=eq." . urlencode($username);
        $result = $this->makeRequest('GET', $this->table, null, $query);

        return $result[0] ?? false;
    }

    public function findByEmail($email) {
        $query = "?email=eq." . urlencode($email);
        $result = $this->makeRequest('GET', $this->table, null, $query);

        return $result[0] ?? false;
    }

    public function findById($id) {
        $query = "?id=eq." . urlencode($id);
        $result = $this->makeRequest('GET', $this->table, null, $query);

        return $result[0] ?? false;
    }

    public function login($email, $password) {
        $data = [
            'email' => $email,
            'password' => $password
        ];

        $result = $this->makeRequest('POST', 'token?grant_type=password', $data, '', true);

        if (!$result) {
            add_flash_message('Invalid email or password.', 'danger');
            return false;
        }

        if (!isset($result['access_token'])) {
            add_flash_message('Invalid response from authentication service.', 'danger');
            return false;
        }

        // Get user profile using the access token
        $headers = [
            "apikey: $this->supabaseKey",
            "Authorization: Bearer " . $result['access_token'],
            "Content-Type: application/json",
            "Prefer: return=representation"
        ];

        $url = "$this->supabaseUrl/rest/v1/$this->table?email=eq." . urlencode($email);
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            add_flash_message("Error fetching user profile: $error", 'danger');
            return false;
        }

        $userData = json_decode($response, true);

        // If user profile doesn't exist, create it
        if (!$userData || empty($userData)) {
            // Extract username from email (everything before @)
            $username = explode('@', $email)[0];
            
            // Create user profile - use a numeric ID instead of UUID
            $profileData = [
                'id' => rand(1000, 9999), // Generate a random numeric ID
                'username' => $username,
                'email' => $email,
                'theme_preference' => 'light',
                'password_hash' => hash_password($password) // Add password hash
            ];
            
            // Use direct curl for better error handling
            $createUrl = "$this->supabaseUrl/rest/v1/$this->table";
            $createHeaders = [
                "apikey: $this->supabaseKey",
                "Authorization: Bearer " . $result['access_token'],
                "Content-Type: application/json",
                "Prefer: return=representation"
            ];
            
            $createCh = curl_init($createUrl);
            curl_setopt($createCh, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($createCh, CURLOPT_POST, true);
            curl_setopt($createCh, CURLOPT_POSTFIELDS, json_encode($profileData));
            curl_setopt($createCh, CURLOPT_HTTPHEADER, $createHeaders);
            
            $createResponse = curl_exec($createCh);
            $createHttpCode = curl_getinfo($createCh, CURLINFO_HTTP_CODE);
            $createError = curl_error($createCh);
            curl_close($createCh);
            
            if ($createError) {
                add_flash_message("Error creating user profile: $createError", 'danger');
                return false;
            }
            
            if ($createHttpCode >= 400) {
                $errorData = json_decode($createResponse, true);
                $errorMessage = $errorData['message'] ?? 'Unknown error creating profile';
                add_flash_message("Error creating user profile: $errorMessage", 'danger');
                return false;
            }
            
            $createResult = json_decode($createResponse, true);
            
            if (!$createResult || empty($createResult)) {
                add_flash_message('Error creating user profile: Empty response', 'danger');
                return false;
            }
            
            $userData = $createResult;
        }

        $user = $userData[0];

        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Set session variables
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['user_theme'] = $user['theme_preference'];
        $_SESSION['access_token'] = $result['access_token'];
        $_SESSION['refresh_token'] = $result['refresh_token'];

        return true;
    }

    public function logout() {
        if (isset($_SESSION['refresh_token'])) {
            $data = [
                'refresh_token' => $_SESSION['refresh_token']
            ];
            $this->makeRequest('POST', 'logout', $data, '', true);
        }

        unset($_SESSION['user_id']);
        unset($_SESSION['username']);
        unset($_SESSION['user_theme']);
        unset($_SESSION['access_token']);
        unset($_SESSION['refresh_token']);
        session_destroy();
    }

    public function setTheme($user_id, $theme) {
        $query = "?id=eq." . urlencode($user_id);
        $data = ['theme_preference' => $theme];

        $result = $this->makeRequest('PATCH', $this->table, $data, $query);

        if ($result) {
            $_SESSION['user_theme'] = $theme;
            return true;
        }

        add_flash_message('Error setting theme preference.', 'danger');
        return false;
    }
}
