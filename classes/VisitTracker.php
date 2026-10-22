<?php
require_once __DIR__ . '/../config/config.php';

class VisitTracker {
    private $db;
    private static $tableAvailable = null;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public static function shouldTrackRequest() {
        if (PHP_SAPI === 'cli' || PHP_SAPI === 'phpdbg') {
            return false;
        }

        $method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
        if ($method !== 'GET') {
            return false;
        }

        $uri = $_SERVER['REQUEST_URI'] ?? '';
        if ($uri === '') {
            return false;
        }

        $path = parse_url($uri, PHP_URL_PATH);
        $path = $path !== null ? $path : $uri;
        $path = strtolower($path);

        $blockedSegments = ['/admin/', '/auth/', '/api/', '/assets/'];
        foreach ($blockedSegments as $segment) {
            if (strpos($path, $segment) !== false) {
                return false;
            }
        }

        if (preg_match('/\\.(css|js|png|jpe?g|gif|svg|webp|ico|map|woff2?|ttf|eot)$/', $path)) {
            return false;
        }

        $requestedWith = strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '');
        if ($requestedWith === 'xmlhttprequest') {
            return false;
        }

        $accept = strtolower($_SERVER['HTTP_ACCEPT'] ?? '');
        if ($accept !== '' && strpos($accept, 'text/html') === false && strpos($accept, '*/*') === false) {
            return false;
        }

        return true;
    }

    public function trackVisit() {
        if (!self::shouldTrackRequest()) {
            return false;
        }

        if (!$this->ensureTable()) {
            return false;
        }

        $sessionId = session_id();
        if ($sessionId === '') {
            return false;
        }

        $visitDate = date('Y-m-d');
        $userId = $_SESSION['user_id'] ?? null;
        $path = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
        $path = $path !== null ? substr($path, 0, 255) : null;
        $ip = $_SERVER['REMOTE_ADDR'] ?? null;
        $ip = $ip !== null ? substr($ip, 0, 45) : null;
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? null;
        $userAgent = $userAgent !== null ? substr($userAgent, 0, 255) : null;

        $driver = $this->db->getAttribute(PDO::ATTR_DRIVER_NAME);
        $sql = $driver === 'sqlite'
            ? "INSERT OR IGNORE INTO site_visits (visit_date, session_id, user_id, path, ip, user_agent) VALUES (?, ?, ?, ?, ?, ?)"
            : "INSERT IGNORE INTO site_visits (visit_date, session_id, user_id, path, ip, user_agent) VALUES (?, ?, ?, ?, ?, ?)";

        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                $visitDate,
                $sessionId,
                $userId,
                $path,
                $ip,
                $userAgent
            ]);
        } catch (PDOException $e) {
            error_log('VisitTracker: ' . $e->getMessage());
            return false;
        }
    }

    public function getVisitSummary() {
        if (!$this->ensureTable()) {
            return [
                'total' => 0,
                'today' => 0,
                'month' => 0
            ];
        }

        $today = date('Y-m-d');
        $monthStart = date('Y-m-01');

        try {
            $total = (int)$this->db->query("SELECT COUNT(*) FROM site_visits")->fetchColumn();

            $stmt = $this->db->prepare("SELECT COUNT(*) FROM site_visits WHERE visit_date = ?");
            $stmt->execute([$today]);
            $todayCount = (int)$stmt->fetchColumn();

            $stmt = $this->db->prepare("SELECT COUNT(*) FROM site_visits WHERE visit_date >= ?");
            $stmt->execute([$monthStart]);
            $monthCount = (int)$stmt->fetchColumn();

            return [
                'total' => $total,
                'today' => $todayCount,
                'month' => $monthCount
            ];
        } catch (PDOException $e) {
            error_log('VisitTracker: ' . $e->getMessage());
            return [
                'total' => 0,
                'today' => 0,
                'month' => 0
            ];
        }
    }

    public function getMonthlyVisits($months = 6) {
        $months = max(1, (int)$months);
        [$monthKeys, $startDate] = $this->buildMonthKeys($months);

        if (!$this->ensureTable()) {
            return [
                'months' => array_keys($monthKeys),
                'totals' => array_values($monthKeys)
            ];
        }

        try {
            $driver = $this->db->getAttribute(PDO::ATTR_DRIVER_NAME);
            $sql = $driver === 'sqlite'
                ? "SELECT strftime('%Y-%m', visit_date) as month, COUNT(*) as total FROM site_visits WHERE visit_date >= ? GROUP BY month"
                : "SELECT DATE_FORMAT(visit_date, '%Y-%m') as month, COUNT(*) as total FROM site_visits WHERE visit_date >= ? GROUP BY month";

            $stmt = $this->db->prepare($sql);
            $stmt->execute([$startDate]);
            foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $key = $row['month'] ?? '';
                if ($key !== '' && isset($monthKeys[$key])) {
                    $monthKeys[$key] = (int)$row['total'];
                }
            }
        } catch (PDOException $e) {
            error_log('VisitTracker: ' . $e->getMessage());
            // keep defaults
        }

        return [
            'months' => array_keys($monthKeys),
            'totals' => array_values($monthKeys)
        ];
    }

    private function buildMonthKeys($months) {
        $start = new DateTime('first day of this month');
        $start->modify('-' . ($months - 1) . ' months');
        $keys = [];

        for ($i = 0; $i < $months; $i++) {
            $keys[$start->format('Y-m')] = 0;
            $start->modify('+1 month');
        }

        $firstMonth = array_key_first($keys);
        $startDate = $firstMonth ? $firstMonth . '-01' : date('Y-m-01');

        return [$keys, $startDate];
    }

    private function ensureTable() {
        if (self::$tableAvailable !== null) {
            return self::$tableAvailable;
        }

        try {
            $driver = $this->db->getAttribute(PDO::ATTR_DRIVER_NAME);
            if ($driver === 'sqlite') {
                $this->db->exec("
                    CREATE TABLE IF NOT EXISTS site_visits (
                        id INTEGER PRIMARY KEY AUTOINCREMENT,
                        visit_date TEXT NOT NULL,
                        session_id TEXT NOT NULL,
                        user_id INTEGER NULL,
                        path TEXT NULL,
                        ip TEXT NULL,
                        user_agent TEXT NULL,
                        created_at TEXT DEFAULT CURRENT_TIMESTAMP,
                        UNIQUE(visit_date, session_id)
                    )
                ");
            } else {
                $this->db->exec("
                    CREATE TABLE IF NOT EXISTS site_visits (
                        id INT(11) NOT NULL AUTO_INCREMENT,
                        visit_date DATE NOT NULL,
                        session_id VARCHAR(64) NOT NULL,
                        user_id INT(11) DEFAULT NULL,
                        path VARCHAR(255) DEFAULT NULL,
                        ip VARCHAR(45) DEFAULT NULL,
                        user_agent VARCHAR(255) DEFAULT NULL,
                        created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
                        PRIMARY KEY (id),
                        UNIQUE KEY uniq_visit (visit_date, session_id)
                    ) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci
                ");
            }

            self::$tableAvailable = true;
        } catch (PDOException $e) {
            error_log('VisitTracker: ' . $e->getMessage());
            self::$tableAvailable = false;
        }

        return self::$tableAvailable;
    }
}
?>
