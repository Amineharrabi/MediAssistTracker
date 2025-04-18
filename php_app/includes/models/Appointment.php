<?php

/**
 * Appointment Model (Supabase Version)
 */

class Appointment
{
    private $supabaseUrl;
    private $supabaseKey;
    private $table = 'appointment';

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
     * Get all appointments for a user
     *
     * @param int $user_id User ID
     * @return array|bool Array of appointments if successful, false otherwise
     */
    public function getAll($user_id)
    {
        $query = "?user_id=eq." . urlencode($user_id) . "&order=date.asc,time.asc";
        $result = $this->makeRequest('GET', $this->table, null, $query);

        return $result ?: false;
    }

    /**
     * Get appointments for a specific date
     *
     * @param int $user_id User ID
     * @param string $date Date in YYYY-MM-DD format
     * @return array|bool Array of appointments if successful, false otherwise
     */
    public function getByDate($user_id, $date)
    {
        $query = "?user_id=eq." . urlencode($user_id) . "&date=eq." . urlencode($date) . "&order=time.asc";
        $result = $this->makeRequest('GET', $this->table, null, $query);

        return $result ?: false;
    }

    /**
     * Get an appointment by ID
     *
     * @param int $id Appointment ID
     * @param int $user_id User ID (for security)
     * @return array|bool Appointment data if found, false otherwise
     */
    public function getById($id, $user_id)
    {
        $query = "?id=eq." . urlencode($id) . "&user_id=eq." . urlencode($user_id);
        $result = $this->makeRequest('GET', $this->table, null, $query);

        return $result[0] ?? false;
    }

    /**
     * Create a new appointment
     *
     * @param string $title Appointment title
     * @param string $doctor Doctor name
     * @param string $location Location
     * @param string $date Date in YYYY-MM-DD format
     * @param string $time Time in HH:MM format
     * @param string $notes Additional notes
     * @param int $user_id User ID
     * @return int|bool Appointment ID if successful, false otherwise
     */
    public function create($title, $doctor, $location, $date, $time, $notes, $user_id)
    {
        $data = [
            'title' => $title,
            'doctor' => $doctor,
            'location' => $location,
            'date' => $date,
            'time' => $time,
            'notes' => $notes,
            'user_id' => $user_id
        ];

        $result = $this->makeRequest('POST', $this->table, $data);

        if (isset($result[0]['id'])) {
            return $result[0]['id'];
        }

        add_flash_message('Error creating appointment.', 'danger');
        return false;
    }

    /**
     * Update an appointment
     *
     * @param int $id Appointment ID
     * @param string $title Appointment title
     * @param string $doctor Doctor name
     * @param string $location Location
     * @param string $date Date in YYYY-MM-DD format
     * @param string $time Time in HH:MM format
     * @param string $notes Additional notes
     * @param int $user_id User ID (for security)
     * @return bool True if successful, false otherwise
     */
    public function update($id, $title, $doctor, $location, $date, $time, $notes, $user_id)
    {
        $data = [
            'title' => $title,
            'doctor' => $doctor,
            'location' => $location,
            'date' => $date,
            'time' => $time,
            'notes' => $notes
        ];

        $query = "?id=eq." . urlencode($id) . "&user_id=eq." . urlencode($user_id);
        $result = $this->makeRequest('PATCH', $this->table, $data, $query);

        return $result ? true : false;
    }

    /**
     * Delete an appointment
     *
     * @param int $id Appointment ID
     * @param int $user_id User ID (for security)
     * @return bool True if successful, false otherwise
     */
    public function delete($id, $user_id)
    {
        $query = "?id=eq." . urlencode($id) . "&user_id=eq." . urlencode($user_id);
        $result = $this->makeRequest('DELETE', $this->table, null, $query);

        return $result ? true : false;
    }

    /**
     * Get upcoming appointments for a user
     *
     * @param int $user_id User ID
     * @param int $limit Number of appointments to return (default: 5)
     * @return array|bool Array of appointments if successful, false otherwise
     */
    public function getUpcoming($user_id, $limit = 5)
    {
        $today = date('Y-m-d');
        $query = "?user_id=eq." . urlencode($user_id) . "&date=gte." . urlencode($today) . "&order=date.asc,time.asc&limit=" . urlencode($limit);
        $result = $this->makeRequest('GET', $this->table, null, $query);

        return $result ?: false;
    }
}
