<?php
/**
 * Notification Model
 */

class Notification {
    private $db;
    
    /**
     * Constructor
     *
     * @param PDO $db Database connection
     */
    public function __construct($db) {
        $this->db = $db;
    }
    
    /**
     * Get all notifications for a user
     *
     * @param int $user_id User ID
     * @param int $limit Number of notifications to return (0 for all)
     * @return array|bool Array of notifications if successful, false otherwise
     */
    public function getAll($user_id, $limit = 0) {
        try {
            $sql = "
                SELECT * FROM notification
                WHERE user_id = :user_id
                ORDER BY scheduled_time DESC
            ";
            
            if ($limit > 0) {
                $sql .= " LIMIT :limit";
            }
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':user_id', $user_id);
            
            if ($limit > 0) {
                $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            }
            
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            add_flash_message('Error fetching notifications: ' . $e->getMessage(), 'danger');
            return false;
        }
    }
    
    /**
     * Get unread notifications for a user
     *
     * @param int $user_id User ID
     * @param int $limit Number of notifications to return (0 for all)
     * @return array|bool Array of notifications if successful, false otherwise
     */
    public function getUnread($user_id, $limit = 0) {
        try {
            $sql = "
                SELECT * FROM notification
                WHERE user_id = :user_id AND is_read = false
                ORDER BY scheduled_time DESC
            ";
            
            if ($limit > 0) {
                $sql .= " LIMIT :limit";
            }
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':user_id', $user_id);
            
            if ($limit > 0) {
                $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            }
            
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            add_flash_message('Error fetching unread notifications: ' . $e->getMessage(), 'danger');
            return false;
        }
    }
    
    /**
     * Get a notification by ID
     *
     * @param int $id Notification ID
     * @param int $user_id User ID (for security)
     * @return array|bool Notification data if found, false otherwise
     */
    public function getById($id, $user_id) {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM notification
                WHERE id = :id AND user_id = :user_id
            ");
            
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();
            
            return $stmt->fetch();
        } catch (PDOException $e) {
            add_flash_message('Error fetching notification: ' . $e->getMessage(), 'danger');
            return false;
        }
    }
    
    /**
     * Create a new notification
     *
     * @param string $type Notification type ('medication' or 'appointment')
     * @param int $item_id ID of the medication or appointment
     * @param string $scheduled_time Scheduled time for notification (YYYY-MM-DD HH:MM:SS)
     * @param string $message Notification message
     * @param int $user_id User ID
     * @return int|bool Notification ID if successful, false otherwise
     */
    public function create($type, $item_id, $scheduled_time, $message, $user_id) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO notification (type, item_id, scheduled_time, message, user_id)
                VALUES (:type, :item_id, :scheduled_time, :message, :user_id)
                RETURNING id
            ");
            
            $stmt->bindParam(':type', $type);
            $stmt->bindParam(':item_id', $item_id, PDO::PARAM_INT);
            $stmt->bindParam(':scheduled_time', $scheduled_time);
            $stmt->bindParam(':message', $message);
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            
            $stmt->execute();
            
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            add_flash_message('Error creating notification: ' . $e->getMessage(), 'danger');
            return false;
        }
    }
    
    /**
     * Mark a notification as read
     *
     * @param int $id Notification ID
     * @param int $user_id User ID (for security)
     * @return bool True if successful, false otherwise
     */
    public function markAsRead($id, $user_id) {
        try {
            $stmt = $this->db->prepare("
                UPDATE notification
                SET is_read = true
                WHERE id = :id AND user_id = :user_id
            ");
            
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':user_id', $user_id);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            add_flash_message('Error marking notification as read: ' . $e->getMessage(), 'danger');
            return false;
        }
    }
    
    /**
     * Mark all notifications as read for a user
     *
     * @param int $user_id User ID
     * @return bool True if successful, false otherwise
     */
    public function markAllAsRead($user_id) {
        try {
            $stmt = $this->db->prepare("
                UPDATE notification
                SET is_read = true
                WHERE user_id = :user_id AND is_read = false
            ");
            
            $stmt->bindParam(':user_id', $user_id);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            add_flash_message('Error marking all notifications as read: ' . $e->getMessage(), 'danger');
            return false;
        }
    }
    
    /**
     * Delete old notifications for a user
     *
     * @param int $user_id User ID
     * @param int $days Number of days to keep (default: 30)
     * @return bool True if successful, false otherwise
     */
    public function deleteOld($user_id, $days = 30) {
        try {
            $cutoff_date = date('Y-m-d H:i:s', strtotime("-$days days"));
            
            $stmt = $this->db->prepare("
                DELETE FROM notification
                WHERE user_id = :user_id AND created_at < :cutoff_date AND is_read = true
            ");
            
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':cutoff_date', $cutoff_date);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            add_flash_message('Error deleting old notifications: ' . $e->getMessage(), 'danger');
            return false;
        }
    }
    
    /**
     * Create notification for a medication
     *
     * @param array $medication Medication data
     * @param int $user_id User ID
     * @return bool True if successful, false otherwise
     */
    public function createMedicationNotification($medication, $user_id) {
        try {
            // Get medication times
            $times = json_decode_safe($medication['time']);
            if (empty($times)) {
                return false;
            }
            
            $medication_id = $medication['id'];
            $medication_name = $medication['name'];
            $today = date('Y-m-d');
            $success = true;
            
            // Create a notification for each time
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
    
    /**
     * Create notification for an appointment
     *
     * @param array $appointment Appointment data
     * @param int $user_id User ID
     * @param int $hours_before Hours before appointment to notify (default: 24)
     * @return int|bool Notification ID if successful, false otherwise
     */
    public function createAppointmentNotification($appointment, $user_id, $hours_before = 24) {
        try {
            $appointment_id = $appointment['id'];
            $appointment_title = $appointment['title'];
            $appointment_date = $appointment['date'];
            $appointment_time = $appointment['time'];
            
            // Calculate notification time (24 hours before appointment)
            $appointment_datetime = "$appointment_date $appointment_time";
            $notification_time = date('Y-m-d H:i:s', strtotime("$appointment_datetime - $hours_before hours"));
            
            // Format appointment time for message
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