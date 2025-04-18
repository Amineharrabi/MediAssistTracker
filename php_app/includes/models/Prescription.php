<?php

/**
 * Prescription Model (Supabase Version)
 */

class Prescription
{
    private $supabaseUrl;
    private $supabaseKey;
    private $table = 'prescription';

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

    public function getAll($user_id)
    {
        $query = "?user_id=eq." . urlencode($user_id) . "&order=date.desc";
        return $this->makeRequest('GET', $this->table, null, $query);
    }

    public function getById($id, $user_id)
    {
        $query = "?id=eq." . urlencode($id) . "&user_id=eq." . urlencode($user_id);
        $result = $this->makeRequest('GET', $this->table, null, $query);
        return $result[0] ?? false;
    }

    public function create($title, $doctor, $date, $image_data, $notes, $user_id)
    {
        $data = [
            'title' => $title,
            'doctor' => $doctor,
            'date' => $date,
            'image_data' => $image_data,
            'notes' => $notes,
            'user_id' => $user_id
        ];

        $result = $this->makeRequest('POST', $this->table, $data);
        return $result[0]['id'] ?? false;
    }

    public function update($id, $title, $doctor, $date, $image_data, $notes, $user_id)
    {
        $query = "?id=eq." . urlencode($id) . "&user_id=eq." . urlencode($user_id);
        $data = [
            'title' => $title,
            'doctor' => $doctor,
            'date' => $date,
            'notes' => $notes,
            'updated_at' => date('c')
        ];

        if ($image_data !== null) {
            $data['image_data'] = $image_data;
        }

        $result = $this->makeRequest('PATCH', $this->table, $data, $query);
        return $result !== false;
    }

    public function delete($id, $user_id)
    {
        $query = "?id=eq." . urlencode($id) . "&user_id=eq." . urlencode($user_id);
        $result = $this->makeRequest('DELETE', $this->table, null, $query);
        return $result !== false;
    }
}
