<?php
require_once __DIR__ . '/Model.php';

class User extends Model {

    /**
     * Finds a user by their email address.
     * @param string $email The email to search for.
     * @return array|false The user data as an array, or false if not found.
     */
    public function findByEmail(string $email) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->fetch(); 
    }

    /**
     * Creates a new user in the database.
     * @param string $username
     * @param string $email
     * @param string $password The plain-text password, which will be hashed.
     * @param string|null $dateOfBirth Date of birth in Y-m-d format (optional)
     * @return bool True on success, false on failure.
     */
    public function create(string $username, string $email, string $password, ?string $dateOfBirth = null): bool {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        try {
            $stmt = $this->db->prepare(
                "INSERT INTO users (username, email, password_hash, date_of_birth) VALUES (:username, :email, :password_hash, :date_of_birth)"
            );
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password_hash', $passwordHash);
            $stmt->bindParam(':date_of_birth', $dateOfBirth);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Finds a user by their ID.
     * @param int $id The user ID to search for.
     * @return array|false The user data as an array, or false if not found.
     */
    public function findById(int $id) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    /**
     * Updates user profile information.
     * @param int $userId
     * @param string $username
     * @param string $email
     * @param string|null $dateOfBirth Date of birth in Y-m-d format (optional)
     * @return bool True on success, false on failure.
     */
    public function updateProfile(int $userId, string $username, string $email, ?string $dateOfBirth = null): bool {
        try {
            $stmt = $this->db->prepare(
                "UPDATE users SET username = :username, email = :email, date_of_birth = :date_of_birth WHERE id = :id"
            );
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':date_of_birth', $dateOfBirth);
            $stmt->bindParam(':id', $userId);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Updates user password.
     * @param int $userId
     * @param string $newPassword
     * @return bool True on success, false on failure.
     */
    public function updatePassword(int $userId, string $newPassword): bool {
        $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);
        
        try {
            $stmt = $this->db->prepare(
                "UPDATE users SET password_hash = :password_hash WHERE id = :id"
            );
            $stmt->bindParam(':password_hash', $passwordHash);
            $stmt->bindParam(':id', $userId);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Updates user profile picture.
     * @param int $userId
     * @param string $profilePicturePath
     * @return bool True on success, false on failure.
     */
    public function updateProfilePicture(int $userId, string $profilePicturePath): bool {
        try {
            $stmt = $this->db->prepare(
                "UPDATE users SET profile_pic = :profile_pic WHERE id = :id"
            );
            $stmt->bindParam(':profile_pic', $profilePicturePath);
            $stmt->bindParam(':id', $userId);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Verifies user password.
     * @param int $userId
     * @param string $password
     * @return bool True if password is correct, false otherwise.
     */
    public function verifyPassword(int $userId, string $password): bool {
        $user = $this->findById($userId);
        if (!$user) {
            return false;
        }
        
        return password_verify($password, $user['password_hash']);
    }

    /**
     * Gets user's profile picture path.
     * @param int $userId
     * @return string|null Profile picture path or null if not set.
     */
    public function getProfilePicture(int $userId): ?string {
        $user = $this->findById($userId);
        return $user ? $user['profile_pic'] : null;
    }

    /**
     * Updates only user's date of birth.
     * @param int $userId
     * @param string $dateOfBirth Date of birth in Y-m-d format
     * @return bool True on success, false on failure.
     */
    public function updateDateOfBirth(int $userId, string $dateOfBirth): bool {
        try {
            $stmt = $this->db->prepare(
                "UPDATE users SET date_of_birth = :date_of_birth WHERE id = :id"
            );
            $stmt->bindParam(':date_of_birth', $dateOfBirth);
            $stmt->bindParam(':id', $userId);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Calculate age from date of birth.
     * @param string|null $dateOfBirth Date of birth in Y-m-d format
     * @return int|null Age in years, or null if date of birth is not provided
     */
    public function calculateAge(?string $dateOfBirth): ?int {
        if (!$dateOfBirth) {
            return null;
        }
        
        try {
            $dob = new DateTime($dateOfBirth);
            $now = new DateTime();
            $age = $now->diff($dob)->y;
            
            return $age;
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Get user's age calculated from their date of birth.
     * @param int $userId
     * @return int|null Age in years, or null if user not found or date of birth not set
     */
    public function getUserAge(int $userId): ?int {
        $user = $this->findById($userId);
        if (!$user) {
            return null;
        }
        
        return $this->calculateAge($user['date_of_birth']);
    }
}