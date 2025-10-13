<?php
require_once __DIR__ . '/../config/config.php';

class Category {
    private $db;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }
    
    // Obtener todas las categorías
    public function getAllCategories() {
        try {
            $sql = "SELECT * FROM categories WHERE status = 'active' ORDER BY name ASC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch(PDOException $e) {
            return [];
        }
    }
    
    // Obtener categoría por ID
    public function getCategoryById($id) {
        try {
            $sql = "SELECT * FROM categories WHERE id = ? AND status = 'active'";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);
            return $stmt->fetch();
        } catch(PDOException $e) {
            return false;
        }
    }
    
    // Obtener categoría por slug
    public function getCategoryBySlug($slug) {
        try {
            $sql = "SELECT * FROM categories WHERE slug = ? AND status = 'active'";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$slug]);
            return $stmt->fetch();
        } catch(PDOException $e) {
            return false;
        }
    }
    
    // Crear categoría (Admin)
    public function createCategory($data) {
        try {
            $sql = "INSERT INTO categories (name, description, image, slug) VALUES (?, ?, ?, ?)";
            $stmt = $this->db->prepare($sql);

            if ($stmt->execute([
                $data['name'],
                $data['description'] ?? null,
                $data['image'] ?? null,
                $data['slug']
            ])) {
                return $this->db->lastInsertId();
            }

            return false;
        } catch(PDOException $e) {
            return false;
        }
    }
    
    // Actualizar categoría (Admin)
    public function updateCategory($id, $data) {
        try {
            $sql = "UPDATE categories SET name = ?, description = ?, image = ?, slug = ? WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            
            return $stmt->execute([
                $data['name'],
                $data['description'] ?? null,
                $data['image'] ?? null,
                $data['slug'],
                $id
            ]);
        } catch(PDOException $e) {
            return false;
        }
    }
    
    // Eliminar categoría (Admin)
    public function deleteCategory($id) {
        try {
            $sql = "UPDATE categories SET status = 'inactive' WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$id]);
        } catch(PDOException $e) {
            return false;
        }
    }
    
    // Verificar si el slug existe
    public function slugExists($slug, $excludeId = null) {
        try {
            $sql = "SELECT id FROM categories WHERE slug = ?";
            if ($excludeId) {
                $sql .= " AND id != ?";
            }
            
            $stmt = $this->db->prepare($sql);
            $params = [$slug];
            if ($excludeId) {
                $params[] = $excludeId;
            }
            
            $stmt->execute($params);
            return $stmt->fetch() !== false;
        } catch(PDOException $e) {
            return false;
        }
    }

    // Obtener categoría por nombre
    public function getCategoryByName($name) {
        try {
            $sql = "SELECT * FROM categories WHERE name = ? LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$name]);
            return $stmt->fetch();
        } catch(PDOException $e) {
            return false;
        }
    }

    // Alternar estado activo/inactivo
    public function toggleActive($id) {
        try {
            $cat = $this->getCategoryById($id);
            if (!$cat) return false;
            $newStatus = $cat['status'] === 'active' ? 'inactive' : 'active';
            $stmt = $this->db->prepare("UPDATE categories SET status = ? WHERE id = ?");
            if ($stmt->execute([$newStatus, $id])) {
                return $newStatus === 'active';
            }
            return false;
        } catch(PDOException $e) {
            return false;
        }
    }
    
    // Obtener categorías con conteo de productos
    public function getCategoriesWithProductCount() {
        try {
            $sql = "SELECT c.*, COUNT(p.id) as product_count 
                    FROM categories c 
                    LEFT JOIN products p ON c.id = p.category_id AND p.status = 'active'
                    WHERE c.status = 'active' 
                    GROUP BY c.id 
                    ORDER BY c.name ASC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch(PDOException $e) {
            return [];
        }
    }
}
?> 