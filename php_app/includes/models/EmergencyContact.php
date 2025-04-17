<?php
/**
 * Emergency Contact Model
 */

class EmergencyContact {
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
     * Get all emergency contacts for a user
     *
     * @param int $user_id User ID
     * @return array|bool Array of contacts if successful, false otherwise
     */
    public function getAll($user_id) {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM emergency_contact
                WHERE user_id = :user_id
                ORDER BY name ASC
            ");
            
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            add_flash_message('Error fetching emergency contacts: ' . $e->getMessage(), 'danger');
            return false;
        }
    }
    
    /**
     * Get an emergency contact by ID
     *
     * @param int $id Contact ID
     * @param int $user_id User ID (for security)
     * @return array|bool Contact data if found, false otherwise
     */
    public function getById($id, $user_id) {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM emergency_contact
                WHERE id = :id AND user_id = :user_id
            ");
            
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':user_id', $user_id);
            $stmt->execute();
            
            return $stmt->fetch();
        } catch (PDOException $e) {
            add_flash_message('Error fetching emergency contact: ' . $e->getMessage(), 'danger');
            return false;
        }
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
    public function create($name, $relationship, $phone, $email, $notes, $user_id) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO emergency_contact (name, relationship, phone, email, notes, user_id)
                VALUES (:name, :relationship, :phone, :email, :notes, :user_id)
                RETURNING id
            ");
            
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':relationship', $relationship);
            $stmt->bindParam(':phone', $phone);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':notes', $notes);
            $stmt->bindParam(':user_id', $user_id);
            
            $stmt->execute();
            
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            add_flash_message('Error creating emergency contact: ' . $e->getMessage(), 'danger');
            return false;
        }
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
    public function update($id, $name, $relationship, $phone, $email, $notes, $user_id) {
        try {
            $stmt = $this->db->prepare("
                UPDATE emergency_contact
                SET name = :name, relationship = :relationship, phone = :phone,
                    email = :email, notes = :notes, updated_at = NOW()
                WHERE id = :id AND user_id = :user_id
            ");
            
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':relationship', $relationship);
            $stmt->bindParam(':phone', $phone);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':notes', $notes);
            $stmt->bindParam(':user_id', $user_id);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            add_flash_message('Error updating emergency contact: ' . $e->getMessage(), 'danger');
            return false;
        }
    }
    
    /**
     * Delete an emergency contact
     *
     * @param int $id Contact ID
     * @param int $user_id User ID (for security)
     * @return bool True if successful, false otherwise
     */
    public function delete($id, $user_id) {
        try {
            $stmt = $this->db->prepare("
                DELETE FROM emergency_contact
                WHERE id = :id AND user_id = :user_id
            ");
            
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':user_id', $user_id);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            add_flash_message('Error deleting emergency contact: ' . $e->getMessage(), 'danger');
            return false;
        }
    }
}