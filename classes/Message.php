<?php
require_once __DIR__ . '/../config/config.php';

class Message {
    private $db;
    private $hasPhoneColumn = null;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    private function checkPhoneColumn($driver) {
        try {
            if ($driver === 'sqlite') {
                $stmt = $this->db->query("PRAGMA table_info(contact_messages)");
                $columns = $stmt ? $stmt->fetchAll() : [];
                foreach ($columns as $column) {
                    if (!empty($column['name']) && strtolower($column['name']) === 'phone') {
                        return true;
                    }
                }
                return false;
            }

            $stmt = $this->db->prepare(
                "SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'contact_messages' AND COLUMN_NAME = 'phone'"
            );
            $stmt->execute();
            return (int)$stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log('Message: ' . $e->getMessage());
            return false;
        }
    }

    private function canUsePhoneColumn() {
        if ($this->hasPhoneColumn !== null) {
            return $this->hasPhoneColumn;
        }

        $driver = $this->db->getAttribute(PDO::ATTR_DRIVER_NAME);
        $this->hasPhoneColumn = $this->checkPhoneColumn($driver);
        if ($this->hasPhoneColumn) {
            return true;
        }

        try {
            if ($driver === 'sqlite') {
                $this->db->exec("ALTER TABLE contact_messages ADD COLUMN phone TEXT");
            } else {
                $this->db->exec("ALTER TABLE contact_messages ADD COLUMN phone varchar(30) DEFAULT NULL");
            }
        } catch (PDOException $e) {
            error_log('Message: ' . $e->getMessage());
            $this->hasPhoneColumn = false;
            return false;
        }

        $this->hasPhoneColumn = $this->checkPhoneColumn($driver);
        return $this->hasPhoneColumn;
    }

    public function createMessage($data) {
        try {
            $usePhone = $this->canUsePhoneColumn();
            if ($usePhone) {
                $sql = "INSERT INTO contact_messages (name, email, phone, subject, message, status) VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $this->db->prepare($sql);
                $result = $stmt->execute([
                    $data['name'],
                    $data['email'],
                    $data['phone'] ?? null,
                    $data['subject'] ?? null,
                    $data['message'],
                    $data['status'] ?? 'unread'
                ]);
            } else {
                $sql = "INSERT INTO contact_messages (name, email, subject, message, status) VALUES (?, ?, ?, ?, ?)";
                $stmt = $this->db->prepare($sql);
                $result = $stmt->execute([
                    $data['name'],
                    $data['email'],
                    $data['subject'] ?? null,
                    $data['message'],
                    $data['status'] ?? 'unread'
                ]);
            }

            if ($result) {
                return $this->db->lastInsertId();
            }

            return false;
        } catch (PDOException $e) {
            error_log('Message: ' . $e->getMessage());
            return false;
        }
    }

    public function getAllMessages($limit = null) {
        try {
            $sql = "SELECT * FROM contact_messages ORDER BY created_at DESC";
            if ($limit) {
                $sql .= " LIMIT ?";
            }
            $stmt = $this->db->prepare($sql);
            if ($limit) {
                $stmt->execute([(int)$limit]);
            } else {
                $stmt->execute();
            }
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log('Message: ' . $e->getMessage());
            return [];
        }
    }

    public function getMessageById($id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM contact_messages WHERE id = ? LIMIT 1");
            $stmt->execute([$id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log('Message: ' . $e->getMessage());
            return false;
        }
    }

    public function updateStatus($id, $status) {
        $allowed = ['unread', 'read', 'replied'];
        if (!in_array($status, $allowed, true)) {
            return false;
        }
        try {
            $stmt = $this->db->prepare("UPDATE contact_messages SET status = ? WHERE id = ?");
            return $stmt->execute([$status, $id]);
        } catch (PDOException $e) {
            error_log('Message: ' . $e->getMessage());
            return false;
        }
    }

    public function deleteMessage($id) {
        try {
            $stmt = $this->db->prepare("DELETE FROM contact_messages WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            error_log('Message: ' . $e->getMessage());
            return false;
        }
    }

    public function markAllRead() {
        try {
            $stmt = $this->db->prepare("UPDATE contact_messages SET status = 'read' WHERE status = 'unread'");
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log('Message: ' . $e->getMessage());
            return false;
        }
    }

    public function deleteAllMessages() {
        try {
            $stmt = $this->db->prepare("DELETE FROM contact_messages");
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log('Message: ' . $e->getMessage());
            return false;
        }
    }

    public function getMessageStats() {
        try {
            $stats = [
                'total' => 0,
                'unread' => 0,
                'read' => 0,
                'replied' => 0,
                'today' => 0
            ];

            $stmt = $this->db->query("SELECT COUNT(*) as total FROM contact_messages");
            $stats['total'] = (int)($stmt->fetch()['total'] ?? 0);

            $stmt = $this->db->query("SELECT status, COUNT(*) as count FROM contact_messages GROUP BY status");
            foreach ($stmt->fetchAll() as $row) {
                if (!empty($row['status']) && isset($stats[$row['status']])) {
                    $stats[$row['status']] = (int)$row['count'];
                }
            }

            $driver = $this->db->getAttribute(PDO::ATTR_DRIVER_NAME);
            if ($driver === 'sqlite') {
                $stmt = $this->db->query("SELECT COUNT(*) as total FROM contact_messages WHERE date(created_at) = date('now')");
            } else {
                $stmt = $this->db->query("SELECT COUNT(*) as total FROM contact_messages WHERE DATE(created_at) = CURDATE()");
            }
            $stats['today'] = (int)($stmt->fetch()['total'] ?? 0);

            return $stats;
        } catch (PDOException $e) {
            error_log('Message: ' . $e->getMessage());
            $messages = $this->getAllMessages();
            $stats = [
                'total' => count($messages),
                'unread' => 0,
                'read' => 0,
                'replied' => 0,
                'today' => 0
            ];
            $today = date('Y-m-d');
            foreach ($messages as $message) {
                $status = $message['status'] ?? 'unread';
                if (isset($stats[$status])) {
                    $stats[$status] += 1;
                }
                if (!empty($message['created_at']) && strpos($message['created_at'], $today) === 0) {
                    $stats['today'] += 1;
                }
            }
            return $stats;
        }
    }
}
