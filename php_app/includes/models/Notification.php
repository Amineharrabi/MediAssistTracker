<?php
class Notification
{
    private $supabaseUrl;
    private $supabaseKey;
    private $table = 'notification';

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

    public function getAll($user_id, $limit = 0)
    {
        $query = "?user_id=eq.$user_id&order=scheduled_time.desc";
        if ($limit > 0) {
            $query .= "&limit=$limit";
        }

        return $this->makeRequest('GET', $this->table, null, $query);
    }

    public function getUnread($user_id, $limit = 0)
    {
        $query = "?user_id=eq.$user_id&is_read=eq.false&order=scheduled_time.desc";
        if ($limit > 0) {
            $query .= "&limit=$limit";
        }

        return $this->makeRequest('GET', $this->table, null, $query);
    }

    public function getById($id, $user_id)
    {
        $query = "?id=eq.$id&user_id=eq.$user_id";
        $result = $this->makeRequest('GET', $this->table, null, $query);

        return $result[0] ?? false;
    }

    public function create($type, $item_id, $scheduled_time, $message, $user_id)
    {
        $data = [
            'type' => $type,
            'item_id' => $item_id,
            'scheduled_time' => $scheduled_time,
            'message' => $message,
            'user_id' => $user_id
        ];

        $result = $this->makeRequest('POST', $this->table, $data);

        return $result[0]['id'] ?? false;
    }

    public function markAsRead($id, $user_id)
    {
        $query = "?id=eq.$id&user_id=eq.$user_id";
        $data = ['is_read' => true];

        return (bool) $this->makeRequest('PATCH', $this->table, $data, $query);
    }

    public function markAllAsRead($user_id)
    {
        $query = "?user_id=eq.$user_id&is_read=eq.false";
        $data = ['is_read' => true];

        return (bool) $this->makeRequest('PATCH', $this->table, $data, $query);
    }

    public function deleteOld($user_id, $days = 30)
    {
        $cutoff_date = date('Y-m-d\TH:i:s', strtotime("-$days days"));
        $query = "?user_id=eq.$user_id&created_at=lt.$cutoff_date&is_read=eq.true";

        return (bool) $this->makeRequest('DELETE', $this->table, null, $query);
    }

    public function createMedicationNotification($medication, $user_id)
    {
        try {
            $times = json_decode_safe($medication['time']);
            if (empty($times)) {
                return false;
            }

            $medication_id = $medication['id'];
            $medication_name = $medication['name'];
            $today = date('Y-m-d');
            $success = true;

            foreach ($times as $time) {
                $scheduled_time = "$today $time:00";
                $message = "Time to take your medication: $medication_name - $medication[dosage]";

                $result = $this->create('medication', $medication_id, $scheduled_time, $message, $user_id);
                if (!$result) {
                    $success = false;
                }
            }

            return $success;
        } catch (Exception $e) {
            add_flash_message('Error creating medication notification: ' . $e->getMessage(), 'danger');
            return false;
        }
    }

    public function createAppointmentNotification($appointment, $user_id, $hours_before = 24)
    {
        try {
            $appointment_id = $appointment['id'];
            $appointment_title = $appointment['title'];
            $appointment_date = $appointment['date'];
            $appointment_time = $appointment['time'];

            $appointment_datetime = "$appointment_date $appointment_time";
            $notification_time = date('Y-m-d H:i:s', strtotime("$appointment_datetime - $hours_before hours"));

            $formatted_time = date('g:i A', strtotime($appointment_time));
            $formatted_date = date('l, F j', strtotime($appointment_date));

            $message = "Upcoming appointment: $appointment_title on $formatted_date at $formatted_time";

            return $this->create('appointment', $appointment_id, $notification_time, $message, $user_id);
        } catch (Exception $e) {
            add_flash_message('Error creating appointment notification: ' . $e->getMessage(), 'danger');
            return false;
        }
    }
}
