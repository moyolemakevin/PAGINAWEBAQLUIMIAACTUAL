<?php
require_once __DIR__ . '/../config/config.php';

class User {
    private $db;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }
    
    // Registrar nuevo usuario
    public function register($data) {
        try {
            $sql = "INSERT INTO users (username, email, password, first_name, last_name, phone, address, city, country, postal_code) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $this->db->prepare($sql);
            $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
            
            return $stmt->execute([
                $data['username'],
                $data['email'],
                $hashedPassword,
                $data['first_name'],
                $data['last_name'],
                $data['phone'] ?? null,
                $data['address'] ?? null,
                $data['city'] ?? null,
                $data['country'] ?? null,
                $data['postal_code'] ?? null
            ]);
        } catch(PDOException $e) {
            error_log('User: ' . $e->getMessage());
            return false;
        }
    }
    
    // Login de usuario
    public function login($username, $password) {
        try {
            $sql = "SELECT * FROM users WHERE (username = ? OR email = ?) AND status = 'active'";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$username, $username]);
            
            $user = $stmt->fetch();
            
            if ($user && password_verify($password, $user['password'])) {
                if (session_status() === PHP_SESSION_ACTIVE) {
                    session_regenerate_id(true);
                }

                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['user_role'] = normalizeUserRole($user['role'] ?? '');
                $_SESSION['user_name'] = trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? ''));
                if ($_SESSION['user_name'] === '') {
                    $_SESSION['user_name'] = $user['username'] ?? 'Usuario';
                }
                return true;
            }
            
            return false;
        } catch(PDOException $e) {
            error_log('User: ' . $e->getMessage());
            return false;
        }
    }
    
    // Obtener usuario por ID
    public function getUserById($id) {
        try {
            $sql = "SELECT * FROM users WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);
            return $stmt->fetch();
        } catch(PDOException $e) {
            error_log('User: ' . $e->getMessage());
            return false;
        }
    }
    
    // Obtener todos los usuarios
    public function getAllUsers() {
        try {
            $sql = "SELECT id, username, email, first_name, last_name, phone, role, status, created_at FROM users ORDER BY created_at DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch(PDOException $e) {
            error_log('User: ' . $e->getMessage());
            return [];
        }
    }

    // Obtener total de usuarios
    public function getUserCount() {
        try {
            $stmt = $this->db->query("SELECT COUNT(*) as total FROM users");
            $result = $stmt->fetch();
            return (int)($result['total'] ?? 0);
        } catch(PDOException $e) {
            error_log('User: ' . $e->getMessage());
            return 0;
        }
    }

    // Obtener usuario por email
    public function getUserByEmail($email) {
        try {
            $sql = "SELECT * FROM users WHERE email = ? LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$email]);
            return $stmt->fetch();
        } catch(PDOException $e) {
            error_log('User: ' . $e->getMessage());
            return false;
        }
    }

    // Crear usuario (Admin)
    public function createUser($name, $email, $password, $phone = null, $role = 'customer') {
        try {
            [$firstName, $lastName] = $this->splitName($name);
            $normalizedRole = $this->normalizeRole($role);
            $username = $this->generateUniqueUsername($name, $email);
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $sql = "INSERT INTO users (username, email, password, first_name, last_name, phone, role)
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute([
                $username,
                $email,
                $hashedPassword,
                $firstName,
                $lastName,
                $phone ?: null,
                $normalizedRole
            ]);

            if ($result) {
                return $this->db->lastInsertId();
            }

            return false;
        } catch(PDOException $e) {
            error_log('User: ' . $e->getMessage());
            return false;
        }
    }
    
    // Actualizar usuario
    public function updateUser($id, $data) {
        try {
            $sql = "UPDATE users SET first_name = ?, last_name = ?, email = ?, phone = ?, address = ?, city = ?, country = ?, postal_code = ? WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            
            return $stmt->execute([
                $data['first_name'],
                $data['last_name'],
                $data['email'],
                $data['phone'] ?? null,
                $data['address'] ?? null,
                $data['city'] ?? null,
                $data['country'] ?? null,
                $data['postal_code'] ?? null,
                $id
            ]);
        } catch(PDOException $e) {
            error_log('User: ' . $e->getMessage());
            return false;
        }
    }

    // Actualizar usuario desde admin
    public function updateAdminUser($id, $name, $email, $phone = null, $role = 'customer') {
        try {
            [$firstName, $lastName] = $this->splitName($name);
            $normalizedRole = $this->normalizeRole($role);

            $sql = "UPDATE users SET first_name = ?, last_name = ?, email = ?, phone = ?, role = ? WHERE id = ?";
            $stmt = $this->db->prepare($sql);

            return $stmt->execute([
                $firstName,
                $lastName,
                $email,
                $phone ?: null,
                $normalizedRole,
                $id
            ]);
        } catch(PDOException $e) {
            error_log('User: ' . $e->getMessage());
            return false;
        }
    }
    
    // Cambiar contraseña
    public function changePassword($userId, $newPassword) {
        try {
            $sql = "UPDATE users SET password = ? WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            
            return $stmt->execute([$hashedPassword, $userId]);
        } catch(PDOException $e) {
            error_log('User: ' . $e->getMessage());
            return false;
        }
    }

    // Actualizar contrasena (Admin)
    public function updatePassword($userId, $newPassword) {
        return $this->changePassword($userId, $newPassword);
    }

    // Desactivar usuario
    public function deleteUser($id) {
        try {
            $sql = "UPDATE users SET status = 'inactive' WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$id]);
        } catch(PDOException $e) {
            error_log('User: ' . $e->getMessage());
            return false;
        }
    }
    
    // Verificar si el email existe
    public function emailExists($email, $excludeId = null) {
        try {
            $sql = "SELECT id FROM users WHERE email = ?";
            if ($excludeId) {
                $sql .= " AND id != ?";
            }
            
            $stmt = $this->db->prepare($sql);
            $params = [$email];
            if ($excludeId) {
                $params[] = $excludeId;
            }
            
            $stmt->execute($params);
            return $stmt->fetch() !== false;
        } catch(PDOException $e) {
            error_log('User: ' . $e->getMessage());
            return false;
        }
    }
    
    // Verificar si el username existe
    public function usernameExists($username, $excludeId = null) {
        try {
            $sql = "SELECT id FROM users WHERE username = ?";
            if ($excludeId) {
                $sql .= " AND id != ?";
            }
            
            $stmt = $this->db->prepare($sql);
            $params = [$username];
            if ($excludeId) {
                $params[] = $excludeId;
            }
            
            $stmt->execute($params);
            return $stmt->fetch() !== false;
        } catch(PDOException $e) {
            error_log('User: ' . $e->getMessage());
            return false;
        }
    }

    private function normalizeRole($role) {
        return normalizeUserRole($role);
    }

    private function splitName($name) {
        $clean = trim((string)$name);
        if ($clean === '') {
            return ['Usuario', ''];
        }

        $parts = preg_split('/\s+/', $clean);
        $firstName = array_shift($parts);
        $lastName = implode(' ', $parts);

        return [$firstName, $lastName];
    }

    private function generateUniqueUsername($name, $email) {
        $base = '';
        if (!empty($email) && strpos($email, '@') !== false) {
            $base = substr($email, 0, strpos($email, '@'));
        }
        if ($base === '') {
            $base = $name;
        }

        $base = strtolower(preg_replace('/[^a-z0-9_]/', '', $base));
        if ($base === '') {
            $base = 'user';
        }
        $base = substr($base, 0, 30);

        $username = $base;
        $suffix = 1;
        while ($this->usernameExists($username)) {
            $suffix++;
            $username = substr($base, 0, 30 - strlen((string)$suffix)) . $suffix;
        }

        return $username;
    }
    
    // Logout
    public function logout() {
        session_destroy();
        return true;
    }
}
?> 
