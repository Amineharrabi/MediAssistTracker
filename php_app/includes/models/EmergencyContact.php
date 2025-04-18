<?php

/**
 * Emergency Contact Model (Supabase Version)
 */

class EmergencyContact
{
    private $supabaseUrl;
    private $supabaseKey;
    private $table = 'emergency_contact';

    public function __construct($supabaseUrl, $supabaseKey)
    {
        $this->supabaseUrl = $supabaseUrl;
        $this->supabaseKey = $supabaseKey;
    }

    private function makeRequest($method, $endpoint, $data = null, $queryParams = '')
    {
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
        } elseif ($method === 'DELETE') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
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

    /**
     * Get all emergency contacts for a user
     *
     * @param int $user_id User ID
     * @return array|bool Array of contacts if successful, false otherwise
     */
    public function getAll($user_id)
    {
        $query = "?user_id=eq." . urlencode($user_id);
        $result = $this->makeRequest('GET', $this->table, null, $query);

        return $result ?: false;
    }

    /**
     * Get an emergency contact by ID
     *
     * @param int $id Contact ID
     * @param int $user_id User ID (for security)
     * @return array|bool Contact data if found, false otherwise
     */
    public function getById($id, $user_id)
    {
        $query = "?id=eq." . urlencode($id) . "&user_id=eq." . urlencode($user_id);
        $result = $this->makeRequest('GET', $this->table, null, $query);

        return $result[0] ?? false;
    }

    /**
     * Create a new emergency contact
     *
     * @param string $name Contact name
     * @param string $relationship Relationship to user
     * @param string $phone Phone number
     * @param string $email Email address
     * @param string $notes Additional notes
     * @param int $user_id User ID
     * @return int|bool Contact ID if successful, false otherwise
     */
    public function create($name, $relationship, $phone, $email, $notes, $user_id)
    {
        $data = [
            'name' => $name,
            'relationship' => $relationship,
            'phone' => $phone,
            'email' => $email,
            'notes' => $notes,
            'user_id' => $user_id
        ];

        $result = $this->makeRequest('POST', $this->table, $data);

        if (isset($result[0]['id'])) {
            return $result[0]['id'];
        }

        add_flash_message('Error creating emergency contact.', 'danger');
        return false;
    }

    /**
     * Update an emergency contact
     *
     * @param int $id Contact ID
     * @param string $name Contact name
     * @param string $relationship Relationship to user
     * @param string $phone Phone number
     * @param string $email Email address
     * @param string $notes Additional notes
     * @param int $user_id User ID (for security)
     * @return bool True if successful, false otherwise
     */
    public function update($id, $name, $relationship, $phone, $email, $notes, $user_id)
    {
        $data = [
            'name' => $name,
            'relationship' => $relationship,
            'phone' => $phone,
            'email' => $email,
            'notes' => $notes
        ];

        $query = "?id=eq." . urlencode($id) . "&user_id=eq." . urlencode($user_id);
        $result = $this->makeRequest('PATCH', $this->table, $data, $query);

        return $result ? true : false;
    }

    /**
     * Delete an emergency contact
     *
     * @param int $id Contact ID
     * @param int $user_id User ID (for security)
     * @return bool True if successful, false otherwise
     */
    public function delete($id, $user_id)
    {
        $query = "?id=eq." . urlencode($id) . "&user_id=eq." . urlencode($user_id);
        $result = $this->makeRequest('DELETE', $this->table, null, $query);

        return $result ? true : false;
    }
}
