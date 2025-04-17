<?php
/**
 * Medication Model
 */

class Medication {
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
     * Get all medications for a user
     *
     * @param int $user_id User ID
     * @return array|bool Array of medications if successful, false otherwise
     */
    public function getAll($user_id) {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM medication
                WHERE user_id = :user_id
                ORDER BY name ASC
            ");
            
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            add_flash_message('Error fetching medications: ' . $e->getMessage(), 'danger');
            return false;
        }
    }
    
    /**
     * Get a medication by ID
     *
     * @param int $id Medication ID
     * @param int $user_id User ID (for security)
     * @return array|bool Medication data if found, false otherwise
     */
    public function getById($id, $user_id) {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM medication
                WHERE id = :id AND user_id = :user_id
            ");
            
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();
            
            return $stmt->fetch();
        } catch (PDOException $e) {
            add_flash_message('Error fetching medication: ' . $e->getMessage(), 'danger');
            return false;
        }
    }
    
    /**
     * Create a new medication
     *
     * @param string $name Medication name
     * @param string $dosage Dosage information
     * @param string $frequency Frequency information
     * @param array $times Times to take medication
     * @param string $notes Additional notes
     * @param int $user_id User ID
     * @return int|bool Medication ID if successful, false otherwise
     */
    public function create($name, $dosage, $frequency, $times, $notes, $user_id) {
        try {
            $time_json = json_encode($times);
            
            $stmt = $this->db->prepare("
                INSERT INTO medication (name, dosage, frequency, time, notes, user_id)
                VALUES (:name, :dosage, :frequency, :time, :notes, :user_id)
                RETURNING id
            ");
            
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':dosage', $dosage);
            $stmt->bindParam(':frequency', $frequency);
            $stmt->bindParam(':time', $time_json);
            $stmt->bindParam(':notes', $notes);
            $stmt->bindParam(':user_id', $user_id);
            
            $stmt->execute();
            
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            add_flash_message('Error creating medication: ' . $e->getMessage(), 'danger');
            return false;
        }
    }
    
    /**
     * Update a medication
     *
     * @param int $id Medication ID
     * @param string $name Medication name
     * @param string $dosage Dosage information
     * @param string $frequency Frequency information
     * @param array $times Times to take medication
     * @param string $notes Additional notes
     * @param int $user_id User ID (for security)
     * @return bool True if successful, false otherwise
     */
    public function update($id, $name, $dosage, $frequency, $times, $notes, $user_id) {
        try {
            $time_json = json_encode($times);
            
            $stmt = $this->db->prepare("
                UPDATE medication
                SET name = :name, dosage = :dosage, frequency = :frequency,
                    time = :time, notes = :notes, updated_at = NOW()
                WHERE id = :id AND user_id = :user_id
            ");
            
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':dosage', $dosage);
            $stmt->bindParam(':frequency', $frequency);
            $stmt->bindParam(':time', $time_json);
            $stmt->bindParam(':notes', $notes);
            $stmt->bindParam(':user_id', $user_id);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            add_flash_message('Error updating medication: ' . $e->getMessage(), 'danger');
            return false;
        }
    }
    
    /**
     * Delete a medication
     *
     * @param int $id Medication ID
     * @param int $user_id User ID (for security)
     * @return bool True if successful, false otherwise
     */
    public function delete($id, $user_id) {
        try {
            $stmt = $this->db->prepare("
                DELETE FROM medication
                WHERE id = :id AND user_id = :user_id
            ");
            
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':user_id', $user_id);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            add_flash_message('Error deleting medication: ' . $e->getMessage(), 'danger');
            return false;
        }
    }
}