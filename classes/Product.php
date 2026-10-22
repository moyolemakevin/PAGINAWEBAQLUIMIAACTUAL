<?php
require_once __DIR__ . '/../config/config.php';

class Product {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    private function getPublicVisibilityFilter($categoryAlias = 'c') {
        $hiddenSlugs = getPublicHiddenCategorySlugs();
        if (empty($hiddenSlugs)) {
            return ['sql' => '', 'params' => []];
        }

        $placeholders = implode(', ', array_fill(0, count($hiddenSlugs), '?'));

        return [
            'sql' => " AND {$categoryAlias}.slug NOT IN ({$placeholders})",
            'params' => $hiddenSlugs,
        ];
    }

    public function getAllProducts($limit = null, $offset = 0, $publicOnly = false) {
        try {
            $sql = "SELECT p.*, c.name as category_name FROM products p
                    LEFT JOIN categories c ON p.category_id = c.id
                    WHERE p.status = 'active'";
            $params = [];

            if ($publicOnly) {
                $filter = $this->getPublicVisibilityFilter('c');
                $sql .= $filter['sql'];
                $params = array_merge($params, $filter['params']);
            }

            $sql .= " ORDER BY p.created_at DESC";

            if ($limit) {
                $sql .= " LIMIT ? OFFSET ?";
                $params[] = (int) $limit;
                $params[] = (int) $offset;
            }

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);

            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log('Product: ' . $e->getMessage());
            return [];
        }
    }

    public function getProductCount($includeInactive = false, $publicOnly = false) {
        try {
            $sql = "SELECT COUNT(*) as total FROM products p";
            $params = [];

            if ($publicOnly) {
                $sql .= " LEFT JOIN categories c ON p.category_id = c.id";
            }

            $sql .= " WHERE 1=1";

            if (!$includeInactive) {
                $sql .= " AND p.status = 'active'";
            }

            if ($publicOnly) {
                $filter = $this->getPublicVisibilityFilter('c');
                $sql .= $filter['sql'];
                $params = array_merge($params, $filter['params']);
            }

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $result = $stmt->fetch();
            return (int) ($result['total'] ?? 0);
        } catch (PDOException $e) {
            error_log('Product: ' . $e->getMessage());
            return 0;
        }
    }

    public function getFeaturedProducts($limit = 8, $publicOnly = false) {
        try {
            $sql = "SELECT p.*, c.name as category_name FROM products p
                    LEFT JOIN categories c ON p.category_id = c.id
                    WHERE p.status = 'active' AND p.is_featured = 1";
            $params = [];

            if ($publicOnly) {
                $filter = $this->getPublicVisibilityFilter('c');
                $sql .= $filter['sql'];
                $params = array_merge($params, $filter['params']);
            }

            $sql .= " ORDER BY p.created_at DESC LIMIT ?";
            $params[] = (int) $limit;

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);

            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log('Product: ' . $e->getMessage());
            return [];
        }
    }

    public function getProductById($id, $publicOnly = false) {
        try {
            $sql = "SELECT p.*, c.name as category_name FROM products p
                    LEFT JOIN categories c ON p.category_id = c.id
                    WHERE p.id = ? AND p.status = 'active'";
            $params = [$id];

            if ($publicOnly) {
                $filter = $this->getPublicVisibilityFilter('c');
                $sql .= $filter['sql'];
                $params = array_merge($params, $filter['params']);
            }

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);

            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log('Product: ' . $e->getMessage());
            return false;
        }
    }

    public function getProductBySlug($slug, $publicOnly = false) {
        try {
            $sql = "SELECT p.*, c.name as category_name FROM products p
                    LEFT JOIN categories c ON p.category_id = c.id
                    WHERE p.slug = ? AND p.status = 'active'";
            $params = [$slug];

            if ($publicOnly) {
                $filter = $this->getPublicVisibilityFilter('c');
                $sql .= $filter['sql'];
                $params = array_merge($params, $filter['params']);
            }

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);

            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log('Product: ' . $e->getMessage());
            return false;
        }
    }

    public function getProductsByCategory($categoryId, $limit = null, $offset = 0, $publicOnly = false) {
        try {
            $sql = "SELECT p.*, c.name as category_name FROM products p
                    LEFT JOIN categories c ON p.category_id = c.id
                    WHERE p.category_id = ? AND p.status = 'active'";
            $params = [$categoryId];

            if ($publicOnly) {
                $filter = $this->getPublicVisibilityFilter('c');
                $sql .= $filter['sql'];
                $params = array_merge($params, $filter['params']);
            }

            $sql .= " ORDER BY p.created_at DESC";

            if ($limit) {
                $sql .= " LIMIT ? OFFSET ?";
                $params[] = (int) $limit;
                $params[] = (int) $offset;
            }

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);

            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log('Product: ' . $e->getMessage());
            return [];
        }
    }

    public function searchProducts($query, $limit = 20, $publicOnly = false) {
        try {
            $sql = "SELECT p.*, c.name as category_name FROM products p
                    LEFT JOIN categories c ON p.category_id = c.id
                    WHERE p.status = 'active'";
            $params = [];

            if ($publicOnly) {
                $filter = $this->getPublicVisibilityFilter('c');
                $sql .= $filter['sql'];
                $params = array_merge($params, $filter['params']);
            }

            $sql .= " AND (
                        p.name LIKE ? OR
                        p.description LIKE ? OR
                        p.short_description LIKE ? OR
                        p.sku LIKE ? OR
                        c.name LIKE ? OR
                        c.slug LIKE ?
                    ) ORDER BY p.created_at DESC LIMIT ?";

            $searchTerm = '%' . $query . '%';
            $slugTerm = function_exists('generateSlug')
                ? '%' . generateSlug($query) . '%'
                : $searchTerm;

            $params = array_merge($params, [
                $searchTerm,
                $searchTerm,
                $searchTerm,
                $searchTerm,
                $searchTerm,
                $slugTerm,
                (int) $limit,
            ]);

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);

            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log('Product: ' . $e->getMessage());
            return [];
        }
    }

    public function createProduct($data) {
        try {
            $sql = "INSERT INTO products (name, description, short_description, price, discount_price, sku, stock_quantity, category_id, images, features, specifications, weight, dimensions, is_featured, meta_title, meta_description, slug)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $this->db->prepare($sql);

            $result = $stmt->execute([
                $data['name'],
                $data['description'],
                $data['short_description'],
                $data['price'],
                $data['discount_price'] ?? null,
                $data['sku'],
                $data['stock_quantity'] ?? 0,
                $data['category_id'],
                $data['images'] ?? null,
                $data['features'] ?? null,
                $data['specifications'] ?? null,
                $data['weight'] ?? null,
                $data['dimensions'] ?? null,
                $data['is_featured'] ?? 0,
                $data['meta_title'] ?? null,
                $data['meta_description'] ?? null,
                $data['slug'],
            ]);

            if ($result) {
                return $this->db->lastInsertId();
            }

            return false;
        } catch (PDOException $e) {
            error_log('Product: ' . $e->getMessage());
            return false;
        }
    }

    public function updateProduct($id, $data) {
        try {
            $sql = "UPDATE products SET name = ?, description = ?, short_description = ?, price = ?, discount_price = ?, sku = ?, stock_quantity = ?, category_id = ?, images = ?, features = ?, specifications = ?, weight = ?, dimensions = ?, is_featured = ?, meta_title = ?, meta_description = ?, slug = ? WHERE id = ?";

            $stmt = $this->db->prepare($sql);

            return $stmt->execute([
                $data['name'],
                $data['description'],
                $data['short_description'],
                $data['price'],
                $data['discount_price'] ?? null,
                $data['sku'],
                $data['stock_quantity'] ?? 0,
                $data['category_id'],
                $data['images'] ?? null,
                $data['features'] ?? null,
                $data['specifications'] ?? null,
                $data['weight'] ?? null,
                $data['dimensions'] ?? null,
                $data['is_featured'] ?? 0,
                $data['meta_title'] ?? null,
                $data['meta_description'] ?? null,
                $data['slug'],
                $id,
            ]);
        } catch (PDOException $e) {
            error_log('Product: ' . $e->getMessage());
            return false;
        }
    }

    public function deleteProduct($id) {
        try {
            $sql = "UPDATE products SET status = 'inactive' WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log('Product: ' . $e->getMessage());
            return false;
        }
    }

    public function skuExists($sku, $excludeId = null) {
        try {
            $sql = "SELECT id FROM products WHERE sku = ?";
            $params = [$sku];

            if ($excludeId) {
                $sql .= " AND id != ?";
                $params[] = $excludeId;
            }

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetch() !== false;
        } catch (PDOException $e) {
            error_log('Product: ' . $e->getMessage());
            return false;
        }
    }

    public function updateStock($productId, $quantity) {
        try {
            $sql = "UPDATE products SET stock_quantity = stock_quantity - ? WHERE id = ? AND stock_quantity >= ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$quantity, $productId, $quantity]);
        } catch (PDOException $e) {
            error_log('Product: ' . $e->getMessage());
            return false;
        }
    }

    public function toggleFeatured($id) {
        try {
            $product = $this->getProductById($id);
            if (!$product) {
                return false;
            }

            $newStatus = ($product['is_featured'] ?? 0) == 1 ? 0 : 1;
            $stmt = $this->db->prepare("UPDATE products SET is_featured = ? WHERE id = ?");

            if ($stmt->execute([$newStatus, $id])) {
                return $newStatus === 1;
            }

            return false;
        } catch (PDOException $e) {
            error_log('Product: ' . $e->getMessage());
            return false;
        }
    }

    public function getRelatedProducts($categoryId, $excludeId, $limit = 4, $publicOnly = false) {
        try {
            $sql = "SELECT p.*, c.name as category_name FROM products p
                    LEFT JOIN categories c ON p.category_id = c.id
                    WHERE p.category_id = ? AND p.id != ? AND p.status = 'active'";
            $params = [$categoryId, $excludeId];

            if ($publicOnly) {
                $filter = $this->getPublicVisibilityFilter('c');
                $sql .= $filter['sql'];
                $params = array_merge($params, $filter['params']);
            }

            $sql .= " ORDER BY p.created_at DESC LIMIT ?";
            $params[] = (int) $limit;

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);

            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log('Product: ' . $e->getMessage());
            return [];
        }
    }

    public function getProductCountByCategory($categoryId, $publicOnly = false) {
        try {
            $sql = "SELECT COUNT(*) as total FROM products p
                    LEFT JOIN categories c ON p.category_id = c.id
                    WHERE p.category_id = ? AND p.status = 'active'";
            $params = [$categoryId];

            if ($publicOnly) {
                $filter = $this->getPublicVisibilityFilter('c');
                $sql .= $filter['sql'];
                $params = array_merge($params, $filter['params']);
            }

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);

            $result = $stmt->fetch();
            return (int) ($result['total'] ?? 0);
        } catch (PDOException $e) {
            error_log('Product: ' . $e->getMessage());
            return 0;
        }
    }

    public static function getGalleryImages($product) {
        $imagesJson = is_array($product) ? ($product['images'] ?? '') : $product;
        $images = sanitizeStoredImages($imagesJson);
        $gallery = [];

        foreach ($images as $image) {
            $candidate = is_array($image)
                ? ($image['original'] ?? ($image['thumbnail'] ?? ''))
                : $image;

            $safeImage = sanitizeLocalImagePath($candidate, true);
            if ($safeImage) {
                $gallery[] = $safeImage;
            }
        }

        if (!empty($gallery)) {
            return array_values(array_unique($gallery));
        }

        $slug = is_array($product) ? ($product['slug'] ?? '') : '';
        if ($slug) {
            foreach (getAllowedImageExtensions() as $extension) {
                $path = "assets/images/products/{$slug}.{$extension}";
                $safeFallback = sanitizeLocalImagePath($path, true);
                if ($safeFallback) {
                    return [$safeFallback];
                }
            }
        }

        return ['assets/images/placeholder.jpg'];
    }

    public static function getImagePath($product) {
        $images = self::getGalleryImages($product);
        return $images[0] ?? 'assets/images/placeholder.jpg';
    }
}
