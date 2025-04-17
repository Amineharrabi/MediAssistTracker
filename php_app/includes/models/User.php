<?php
/**
 * User Model
 */

class User {
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
     * Create a new user
     *
     * @param string $username Username
     * @param string $email Email
     * @param string $password Password
     * @return int|bool User ID if successful, false otherwise
     */
    public function create($username, $email, $password) {
        try {
            $hash = hash_password($password);
            
            $stmt = $this->db->prepare("
                INSERT INTO \"user\" (username, email, password_hash, theme_preference)
                VALUES (:username, :email, :password_hash, 'light')
                RETURNING id
            ");
            
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password_hash', $hash);
            
            $stmt->execute();
            
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            // Check for duplicate key error
            if ($e->getCode() == '23505') {
                if (strpos($e->getMessage(), 'username') !== false) {
                    add_flash_message('Username already exists.', 'danger');
                } else if (strpos($e->getMessage(), 'email') !== false) {
                    add_flash_message('Email already exists.', 'danger');
                } else {
                    add_flash_message('User already exists.', 'danger');
                }
            } else {
                add_flash_message('Error creating user: ' . $e->getMessage(), 'danger');
            }
            
            return false;
        }
    }
    
    /**
     * Find a user by username
     *
     * @param string $username Username
     * @return array|bool User data if found, false otherwise
     */
    public function findByUsername($username) {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM \"user\" WHERE username = :username
            ");
            
            $stmt->bindParam(':username', $username);
            $stmt->execute();
            
            return $stmt->fetch();
        } catch (PDOException $e) {
            add_flash_message('Error finding user: ' . $e->getMessage(), 'danger');
            return false;
        }
    }
    
    /**
     * Find a user by email
     *
     * @param string $email Email
     * @return array|bool User data if found, false otherwise
     */
    public function findByEmail($email) {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM \"user\" WHERE email = :email
            ");
            
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            
            return $stmt->fetch();
        } catch (PDOException $e) {
            add_flash_message('Error finding user: ' . $e->getMessage(), 'danger');
            return false;
        }
    }
    
    /**
     * Find a user by ID
     *
     * @param int $id User ID
     * @return array|bool User data if found, false otherwise
     */
    public function findById($id) {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM \"user\" WHERE id = :id
            ");
            
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            
            return $stmt->fetch();
        } catch (PDOException $e) {
            add_flash_message('Error finding user: ' . $e->getMessage(), 'danger');
            return false;
        }
    }
    
    /**
     * Login a user
     *
     * @param string $username_or_email Username or email
     * @param string $password Password
     * @return bool True if login successful, false otherwise
     */
    public function login($username_or_email, $password) {
        // Try finding by username first
        $user = $this->findByUsername($username_or_email);
        
        // If not found, try finding by email
        if (!$user) {
            $user = $this->findByEmail($username_or_email);
        }
        
        // If user found, verify password
        if ($user && verify_password($password, $user['password_hash'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_theme'] = $user['theme_preference'];
            
            return true;
        }
        
        return false;
    }
    
    /**
     * Logout the current user
     *
     * @return void
     */
    public function logout() {
        // Unset session variables
        unset($_SESSION['user_id']);
        unset($_SESSION['username']);
        unset($_SESSION['user_theme']);
        
        // Destroy the session
        session_destroy();
    }
    
    /**
     * Set theme preference for user
     *
     * @param int $user_id User ID
     * @param string $theme Theme preference (light or dark)
     * @return bool True if successful, false otherwise
     */
    public function setTheme($user_id, $theme) {
        try {
            $stmt = $this->db->prepare("
                UPDATE \"user\" SET theme_preference = :theme
                WHERE id = :user_id
            ");
            
            $stmt->bindParam(':theme', $theme);
            $stmt->bindParam(':user_id', $user_id);
            
            $result = $stmt->execute();
            
            // Update session if successful
            if ($result) {
                $_SESSION['user_theme'] = $theme;
            }
            
            return $result;
        } catch (PDOException $e) {
            add_flash_message('Error setting theme preference: ' . $e->getMessage(), 'danger');
            return false;
        }
    }
}