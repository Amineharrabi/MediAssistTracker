<?php
/**
 * Appointment Model
 */

class Appointment {
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
     * Get all appointments for a user
     *
     * @param int $user_id User ID
     * @return array|bool Array of appointments if successful, false otherwise
     */
    public function getAll($user_id) {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM appointment
                WHERE user_id = :user_id
                ORDER BY date ASC, time ASC
            ");
            
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            add_flash_message('Error fetching appointments: ' . $e->getMessage(), 'danger');
            return false;
        }
    }
    
    /**
     * Get appointments for a specific date
     *
     * @param int $user_id User ID
     * @param string $date Date in YYYY-MM-DD format
     * @return array|bool Array of appointments if successful, false otherwise
     */
    public function getByDate($user_id, $date) {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM appointment
                WHERE user_id = :user_id AND date = :date
                ORDER BY time ASC
            ");
            
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':date', $date);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            add_flash_message('Error fetching appointments: ' . $e->getMessage(), 'danger');
            return false;
        }
    }
    
    /**
     * Get an appointment by ID
     *
     * @param int $id Appointment ID
     * @param int $user_id User ID (for security)
     * @return array|bool Appointment data if found, false otherwise
     */
    public function getById($id, $user_id) {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM appointment
                WHERE id = :id AND user_id = :user_id
            ");
            
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();
            
            return $stmt->fetch();
        } catch (PDOException $e) {
            add_flash_message('Error fetching appointment: ' . $e->getMessage(), 'danger');
            return false;
        }
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
    public function create($title, $doctor, $location, $date, $time, $notes, $user_id) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO appointment (title, doctor, location, date, time, notes, user_id)
                VALUES (:title, :doctor, :location, :date, :time, :notes, :user_id)
                RETURNING id
            ");
            
            $stmt->bindParam(':title', $title);
            $stmt->bindParam(':doctor', $doctor);
            $stmt->bindParam(':location', $location);
            $stmt->bindParam(':date', $date);
            $stmt->bindParam(':time', $time);
            $stmt->bindParam(':notes', $notes);
            $stmt->bindParam(':user_id', $user_id);
            
            $stmt->execute();
            
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            add_flash_message('Error creating appointment: ' . $e->getMessage(), 'danger');
            return false;
        }
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
    public function update($id, $title, $doctor, $location, $date, $time, $notes, $user_id) {
        try {
            $stmt = $this->db->prepare("
                UPDATE appointment
                SET title = :title, doctor = :doctor, location = :location,
                    date = :date, time = :time, notes = :notes, updated_at = NOW()
                WHERE id = :id AND user_id = :user_id
            ");
            
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':title', $title);
            $stmt->bindParam(':doctor', $doctor);
            $stmt->bindParam(':location', $location);
            $stmt->bindParam(':date', $date);
            $stmt->bindParam(':time', $time);
            $stmt->bindParam(':notes', $notes);
            $stmt->bindParam(':user_id', $user_id);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            add_flash_message('Error updating appointment: ' . $e->getMessage(), 'danger');
            return false;
        }
    }
    
    /**
     * Delete an appointment
     *
     * @param int $id Appointment ID
     * @param int $user_id User ID (for security)
     * @return bool True if successful, false otherwise
     */
    public function delete($id, $user_id) {
        try {
            $stmt = $this->db->prepare("
                DELETE FROM appointment
                WHERE id = :id AND user_id = :user_id
            ");
            
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':user_id', $user_id);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            add_flash_message('Error deleting appointment: ' . $e->getMessage(), 'danger');
            return false;
        }
    }
    
    /**
     * Get upcoming appointments for a user
     *
     * @param int $user_id User ID
     * @param int $limit Number of appointments to return (default: 5)
     * @return array|bool Array of appointments if successful, false otherwise
     */
    public function getUpcoming($user_id, $limit = 5) {
        try {
            $today = date('Y-m-d');
            
            $stmt = $this->db->prepare("
                SELECT * FROM appointment
                WHERE user_id = :user_id AND date >= :today
                ORDER BY date ASC, time ASC
                LIMIT :limit
            ");
            
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':today', $today);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            add_flash_message('Error fetching upcoming appointments: ' . $e->getMessage(), 'danger');
            return false;
        }
    }
}