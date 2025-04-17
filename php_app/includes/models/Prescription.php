<?php
/**
 * Prescription Model
 */

class Prescription {
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
     * Get all prescriptions for a user
     *
     * @param int $user_id User ID
     * @return array|bool Array of prescriptions if successful, false otherwise
     */
    public function getAll($user_id) {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM prescription
                WHERE user_id = :user_id
                ORDER BY date DESC
            ");
            
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            add_flash_message('Error fetching prescriptions: ' . $e->getMessage(), 'danger');
            return false;
        }
    }
    
    /**
     * Get a prescription by ID
     *
     * @param int $id Prescription ID
     * @param int $user_id User ID (for security)
     * @return array|bool Prescription data if found, false otherwise
     */
    public function getById($id, $user_id) {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM prescription
                WHERE id = :id AND user_id = :user_id
            ");
            
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();
            
            return $stmt->fetch();
        } catch (PDOException $e) {
            add_flash_message('Error fetching prescription: ' . $e->getMessage(), 'danger');
            return false;
        }
    }
    
    /**
     * Create a new prescription
     *
     * @param string $title Prescription title
     * @param string $doctor Doctor name
     * @param string $date Date in YYYY-MM-DD format
     * @param string $image_data Base64 encoded image data
     * @param string $notes Additional notes
     * @param int $user_id User ID
     * @return int|bool Prescription ID if successful, false otherwise
     */
    public function create($title, $doctor, $date, $image_data, $notes, $user_id) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO prescription (title, doctor, date, image_data, notes, user_id)
                VALUES (:title, :doctor, :date, :image_data, :notes, :user_id)
                RETURNING id
            ");
            
            $stmt->bindParam(':title', $title);
            $stmt->bindParam(':doctor', $doctor);
            $stmt->bindParam(':date', $date);
            $stmt->bindParam(':image_data', $image_data);
            $stmt->bindParam(':notes', $notes);
            $stmt->bindParam(':user_id', $user_id);
            
            $stmt->execute();
            
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            add_flash_message('Error creating prescription: ' . $e->getMessage(), 'danger');
            return false;
        }
    }
    
    /**
     * Update a prescription
     *
     * @param int $id Prescription ID
     * @param string $title Prescription title
     * @param string $doctor Doctor name
     * @param string $date Date in YYYY-MM-DD format
     * @param string $image_data Base64 encoded image data (null to keep existing)
     * @param string $notes Additional notes
     * @param int $user_id User ID (for security)
     * @return bool True if successful, false otherwise
     */
    public function update($id, $title, $doctor, $date, $image_data, $notes, $user_id) {
        try {
            // If image_data is null, don't update it
            if ($image_data === null) {
                $stmt = $this->db->prepare("
                    UPDATE prescription
                    SET title = :title, doctor = :doctor,
                        date = :date, notes = :notes, updated_at = NOW()
                    WHERE id = :id AND user_id = :user_id
                ");
                
                $stmt->bindParam(':id', $id);
                $stmt->bindParam(':title', $title);
                $stmt->bindParam(':doctor', $doctor);
                $stmt->bindParam(':date', $date);
                $stmt->bindParam(':notes', $notes);
                $stmt->bindParam(':user_id', $user_id);
            } else {
                $stmt = $this->db->prepare("
                    UPDATE prescription
                    SET title = :title, doctor = :doctor,
                        date = :date, image_data = :image_data, notes = :notes, updated_at = NOW()
                    WHERE id = :id AND user_id = :user_id
                ");
                
                $stmt->bindParam(':id', $id);
                $stmt->bindParam(':title', $title);
                $stmt->bindParam(':doctor', $doctor);
                $stmt->bindParam(':date', $date);
                $stmt->bindParam(':image_data', $image_data);
                $stmt->bindParam(':notes', $notes);
                $stmt->bindParam(':user_id', $user_id);
            }
            
            return $stmt->execute();
        } catch (PDOException $e) {
            add_flash_message('Error updating prescription: ' . $e->getMessage(), 'danger');
            return false;
        }
    }
    
    /**
     * Delete a prescription
     *
     * @param int $id Prescription ID
     * @param int $user_id User ID (for security)
     * @return bool True if successful, false otherwise
     */
    public function delete($id, $user_id) {
        try {
            $stmt = $this->db->prepare("
                DELETE FROM prescription
                WHERE id = :id AND user_id = :user_id
            ");
            
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':user_id', $user_id);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            add_flash_message('Error deleting prescription: ' . $e->getMessage(), 'danger');
            return false;
        }
    }
}