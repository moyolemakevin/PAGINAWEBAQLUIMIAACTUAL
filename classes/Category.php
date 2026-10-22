<?php
require_once __DIR__ . '/../config/config.php';

class Category {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    private function getPublicVisibilityFilter($alias = 'categories') {
        $hiddenSlugs = getPublicHiddenCategorySlugs();
        if (empty($hiddenSlugs)) {
            return ['sql' => '', 'params' => []];
        }

        $placeholders = implode(', ', array_fill(0, count($hiddenSlugs), '?'));

        return [
            'sql' => " AND {$alias}.slug NOT IN ({$placeholders})",
            'params' => $hiddenSlugs,
        ];
    }

    public function getAllCategories($publicOnly = false) {
        try {
            $sql = "SELECT * FROM categories WHERE status = 'active'";
            $params = [];

            if ($publicOnly) {
                $filter = $this->getPublicVisibilityFilter('categories');
                $sql .= $filter['sql'];
                $params = array_merge($params, $filter['params']);
            }

            $sql .= " ORDER BY name ASC";

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log('Category: ' . $e->getMessage());
            return [];
        }
    }

    public function getCategoryCount($includeInactive = false, $publicOnly = false) {
        try {
            $sql = "SELECT COUNT(*) as total FROM categories WHERE 1=1";
            $params = [];

            if (!$includeInactive) {
                $sql .= " AND status = 'active'";
            }

            if ($publicOnly) {
                $filter = $this->getPublicVisibilityFilter('categories');
                $sql .= $filter['sql'];
                $params = array_merge($params, $filter['params']);
            }

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $result = $stmt->fetch();
            return (int) ($result['total'] ?? 0);
        } catch (PDOException $e) {
            error_log('Category: ' . $e->getMessage());
            return 0;
        }
    }

    public function getCategoryById($id, $publicOnly = false) {
        try {
            $sql = "SELECT * FROM categories WHERE id = ? AND status = 'active'";
            $params = [$id];

            if ($publicOnly) {
                $filter = $this->getPublicVisibilityFilter('categories');
                $sql .= $filter['sql'];
                $params = array_merge($params, $filter['params']);
            }

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log('Category: ' . $e->getMessage());
            return false;
        }
    }

    public function getCategoryBySlug($slug, $publicOnly = false) {
        try {
            $sql = "SELECT * FROM categories WHERE slug = ? AND status = 'active'";
            $params = [$slug];

            if ($publicOnly) {
                $filter = $this->getPublicVisibilityFilter('categories');
                $sql .= $filter['sql'];
                $params = array_merge($params, $filter['params']);
            }

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log('Category: ' . $e->getMessage());
            return false;
        }
    }

    public function createCategory($data) {
        try {
            $sql = "INSERT INTO categories (name, description, image, slug) VALUES (?, ?, ?, ?)";
            $stmt = $this->db->prepare($sql);

            if ($stmt->execute([
                $data['name'],
                $data['description'] ?? null,
                $data['image'] ?? null,
                $data['slug'],
            ])) {
                return $this->db->lastInsertId();
            }

            return false;
        } catch (PDOException $e) {
            error_log('Category: ' . $e->getMessage());
            return false;
        }
    }

    public function updateCategory($id, $data) {
        try {
            $sql = "UPDATE categories SET name = ?, description = ?, image = ?, slug = ? WHERE id = ?";
            $stmt = $this->db->prepare($sql);

            return $stmt->execute([
                $data['name'],
                $data['description'] ?? null,
                $data['image'] ?? null,
                $data['slug'],
                $id,
            ]);
        } catch (PDOException $e) {
            error_log('Category: ' . $e->getMessage());
            return false;
        }
    }

    public function deleteCategory($id) {
        try {
            $sql = "UPDATE categories SET status = 'inactive' WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log('Category: ' . $e->getMessage());
            return false;
        }
    }

    public function slugExists($slug, $excludeId = null) {
        try {
            $sql = "SELECT id FROM categories WHERE slug = ?";
            $params = [$slug];

            if ($excludeId) {
                $sql .= " AND id != ?";
                $params[] = $excludeId;
            }

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetch() !== false;
        } catch (PDOException $e) {
            error_log('Category: ' . $e->getMessage());
            return false;
        }
    }

    public function getCategoryByName($name) {
        try {
            $sql = "SELECT * FROM categories WHERE name = ? LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$name]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log('Category: ' . $e->getMessage());
            return false;
        }
    }

    public function toggleActive($id) {
        try {
            $category = $this->getCategoryById($id);
            if (!$category) {
                return false;
            }

            $newStatus = ($category['status'] ?? 'inactive') === 'active' ? 'inactive' : 'active';
            $stmt = $this->db->prepare("UPDATE categories SET status = ? WHERE id = ?");

            if ($stmt->execute([$newStatus, $id])) {
                return $newStatus === 'active';
            }

            return false;
        } catch (PDOException $e) {
            error_log('Category: ' . $e->getMessage());
            return false;
        }
    }

    public function getCategoriesWithProductCount($publicOnly = false) {
        try {
            $sql = "SELECT c.*, COUNT(p.id) as product_count
                    FROM categories c
                    LEFT JOIN products p ON c.id = p.category_id AND p.status = 'active'
                    WHERE c.status = 'active'";
            $params = [];

            if ($publicOnly) {
                $filter = $this->getPublicVisibilityFilter('c');
                $sql .= $filter['sql'];
                $params = array_merge($params, $filter['params']);
            }

            $sql .= " GROUP BY c.id ORDER BY c.name ASC";

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log('Category: ' . $e->getMessage());
            return [];
        }
    }
}
