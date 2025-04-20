<?php


class User {
    private $supabaseUrl;
    private $supabaseKey;
    private $table = 'user';

    public function __construct($supabaseUrl, $supabaseKey) {
        $this->supabaseUrl = $supabaseUrl;
        $this->supabaseKey = $supabaseKey;
    }

    private function makeRequest($method, $endpoint, $data = null, $queryParams = '') {
        $url = "$this->supabaseUrl/rest/v1/$endpoint$queryParams";

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
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            add_flash_message("cURL Error: $error", 'danger');
            return false;
        }

        return json_decode($response, true);
    }

    public function create($username, $email, $password) {
        $hash = hash_password($password);

        $data = [
            'username' => $username,
            'email' => $email,
            'password_hash' => $hash,
            'theme_preference' => 'light'
        ];

        $result = $this->makeRequest('POST', $this->table, $data);

        if (isset($result[0]['id'])) {
            return $result[0]['id'];
        }

        add_flash_message('Error creating user.', 'danger');
        return false;
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

    public function login($username_or_email, $password) {
        $user = $this->findByUsername($username_or_email);

        if (!$user) {
            $user = $this->findByEmail($username_or_email);
        }

        if ($user && verify_password($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_theme'] = $user['theme_preference'];

            return true;
        }

        return false;
    }

    public function logout() {
        unset($_SESSION['user_id']);
        unset($_SESSION['username']);
        unset($_SESSION['user_theme']);
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
