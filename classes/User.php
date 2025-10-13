<?php
require_once __DIR__ . '/../config/config.php';

class User {
    private $db;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }
    
    /**
     * Registrar nuevo usuario desde el frontend.
     */
    public function register(array $data) {
        try {
            [$firstName, $lastName] = $this->splitFullName($data['first_name'] ?? '', $data['last_name'] ?? '');

            $username = $data['username'] ?? $this->generateUsernameFromEmail($data['email']);
            $username = $this->ensureUniqueUsername($username);

            $sql = "INSERT INTO users (
                        username,
                        email,
                        password,
                        first_name,
                        last_name,
                        phone,
                        address,
                        city,
                        country,
                        postal_code,
                        role,
                        status,
                        created_at,
                        updated_at
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
            
            $stmt = $this->db->prepare($sql);
            $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

            return $stmt->execute([
                $username,
                $data['email'],
                $hashedPassword,
                $firstName,
                $lastName,
                $data['phone'] ?? null,
                $data['address'] ?? null,
                $data['city'] ?? null,
                $data['country'] ?? null,
                $data['postal_code'] ?? null,
                $data['role'] ?? 'customer',
                $data['status'] ?? 'active'
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }
    
    /**
     * Iniciar sesión.
     */
    public function login($username, $password) {
        try {
            $sql = "SELECT * FROM users WHERE (username = ? OR email = ?) AND status = 'active'";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$username, $username]);
            
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['user_role'] = $user['role'];
                $_SESSION['user_name'] = trim($user['first_name'] . ' ' . $user['last_name']);
                return true;
            }
            
            return false;
        } catch (PDOException $e) {
            return false;
        }
    }
    
    /**
     * Obtener usuario por ID.
     */
    public function getUserById($id) {
        try {
            $sql = "SELECT * FROM users WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Obtener usuario por email.
     */
    public function getUserByEmail($email) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
            $stmt->execute([$email]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }
    }
    
    /**
     * Obtener todos los usuarios.
     */
    public function getAllUsers() {
        try {
            $sql = "SELECT id, username, email, first_name, last_name, phone, role, status, created_at 
                    FROM users 
                    ORDER BY created_at DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
    
    /**
     * Actualizar datos de perfil (usuario final).
     */
    public function updateUser($id, array $data) {
        try {
            [$firstName, $lastName] = $this->splitFullName($data['first_name'] ?? '', $data['last_name'] ?? '');

            $sql = "UPDATE users 
                    SET first_name = ?, last_name = ?, email = ?, phone = ?, address = ?, city = ?, country = ?, postal_code = ?, updated_at = NOW() 
                    WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            
            return $stmt->execute([
                $firstName,
                $lastName,
                $data['email'],
                $data['phone'] ?? null,
                $data['address'] ?? null,
                $data['city'] ?? null,
                $data['country'] ?? null,
                $data['postal_code'] ?? null,
                $id
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Actualizar datos desde el panel de administración.
     */
    public function updateUserByAdmin($id, array $data) {
        try {
            [$firstName, $lastName] = $this->splitFullName($data['first_name'] ?? '', $data['last_name'] ?? '');

            $sql = "UPDATE users 
                    SET first_name = ?, 
                        last_name = ?, 
                        email = ?, 
                        phone = ?, 
                        role = ?, 
                        status = ?, 
                        updated_at = NOW() 
                    WHERE id = ?";
            $stmt = $this->db->prepare($sql);

            return $stmt->execute([
                $firstName,
                $lastName,
                $data['email'],
                $data['phone'] ?? null,
                $data['role'] ?? 'customer',
                $data['status'] ?? 'active',
                $id
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }
    
    /**
     * Crear usuario desde el panel de administración.
     */
    public function createUser(array $data) {
        try {
            [$firstName, $lastName] = $this->splitFullName($data['first_name'] ?? '', $data['last_name'] ?? '', $data['name'] ?? '');

            $username = $data['username'] ?? $this->generateUsernameFromEmail($data['email']);
            $username = $this->ensureUniqueUsername($username);

            $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

            $stmt = $this->db->prepare("
                INSERT INTO users (
                    username,
                    email,
                    password,
                    first_name,
                    last_name,
                    phone,
                    role,
                    status,
                    created_at,
                    updated_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
            ");

            $result = $stmt->execute([
                $username,
                $data['email'],
                $hashedPassword,
                $firstName,
                $lastName,
                $data['phone'] ?? null,
                $data['role'] ?? 'customer',
                $data['status'] ?? 'active'
            ]);

            return $result ? (int)$this->db->lastInsertId() : false;
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Eliminar (lógicamente) un usuario.
     */
    public function deleteUser($id) {
        try {
            $stmt = $this->db->prepare("UPDATE users SET status = 'inactive', updated_at = NOW() WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            return false;
        }
    }
    
    /**
     * Cambiar contraseña.
     */
    public function changePassword($userId, $newPassword) {
        try {
            $sql = "UPDATE users SET password = ?, updated_at = NOW() WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            
            return $stmt->execute([$hashedPassword, $userId]);
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Alias para compatibilidad con process_user.
     */
    public function updatePassword($userId, $newPassword) {
        return $this->changePassword($userId, $newPassword);
    }
    
    /**
     * Verificar si el email existe.
     */
    public function emailExists($email, $excludeId = null) {
        try {
            $sql = "SELECT id FROM users WHERE email = ?";
            $params = [$email];

            if ($excludeId) {
                $sql .= " AND id != ?";
                $params[] = $excludeId;
            }
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return (bool)$stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }
    }
    
    /**
     * Verificar si el username existe.
     */
    public function usernameExists($username, $excludeId = null) {
        try {
            $sql = "SELECT id FROM users WHERE username = ?";
            $params = [$username];

            if ($excludeId) {
                $sql .= " AND id != ?";
                $params[] = $excludeId;
            }
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return (bool)$stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }
    }
    
    /**
     * Cerrar sesión.
     */
    public function logout() {
        session_destroy();
        return true;
    }

    /**
     * Generar username basado en el email.
     */
    private function generateUsernameFromEmail($email) {
        $parts = explode('@', $email);
        $base = preg_replace('/[^a-z0-9]/i', '', strtolower($parts[0] ?? 'user'));
        return substr($base ?: 'user', 0, 20);
    }

    /**
     * Asegurar que el username sea único.
     */
    private function ensureUniqueUsername($username, $excludeId = null) {
        $original = $username;
        $counter = 1;
        while ($this->usernameExists($username, $excludeId)) {
            $username = $original . $counter;
            $counter++;
        }
        return $username;
    }

    /**
     * Separar nombre completo en nombre y apellido.
     */
    private function splitFullName($firstName, $lastName, $fallback = '') {
        if ($firstName && $lastName) {
            return [$firstName, $lastName];
        }

        $full = trim($firstName . ' ' . $lastName);
        if (!$full) {
            $full = trim($fallback);
        }

        if (!$full) {
            return ['Usuario', ''];
        }

        $parts = preg_split('/\s+/', $full);
        $first = array_shift($parts);
        $last = implode(' ', $parts);

        return [$first ?: 'Usuario', $last ?: ''];
    }
}
?> 
