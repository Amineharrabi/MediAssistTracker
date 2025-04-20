<?php
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


    public function getAll($user_id)
    {
        $query = "?user_id=eq." . urlencode($user_id) . "&order=date.asc,time.asc";
        $result = $this->makeRequest('GET', $this->table, null, $query);

        return $result ?: false;
    }


    public function getByDate($user_id, $date)
    {
        $query = "?user_id=eq." . urlencode($user_id) . "&date=eq." . urlencode($date) . "&order=time.asc";
        $result = $this->makeRequest('GET', $this->table, null, $query);

        return $result ?: false;
    }

    public function getById($id, $user_id)
    {
        $query = "?id=eq." . urlencode($id) . "&user_id=eq." . urlencode($user_id);
        $result = $this->makeRequest('GET', $this->table, null, $query);

        return $result[0] ?? false;
    }


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


    public function delete($id, $user_id)
    {
        $query = "?id=eq." . urlencode($id) . "&user_id=eq." . urlencode($user_id);
        $result = $this->makeRequest('DELETE', $this->table, null, $query);

        return $result ? true : false;
    }

    public function getUpcoming($user_id, $limit = 5)
    {
        $today = date('Y-m-d');
        $query = "?user_id=eq." . urlencode($user_id) . "&date=gte." . urlencode($today) . "&order=date.asc,time.asc&limit=" . urlencode($limit);
        $result = $this->makeRequest('GET', $this->table, null, $query);

        return $result ?: false;
    }
}
