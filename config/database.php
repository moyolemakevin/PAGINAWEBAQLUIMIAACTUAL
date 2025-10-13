<?php
class Database {
    private $host;
    private $dbname;
    private $username;
    private $password;
    private $pdo;
    private $useSQLite = false;

    public function __construct() {
        // Permitir usar SQLite cuando la variable de entorno USE_SQLITE está activada
        $env = getenv('USE_SQLITE');
        $this->useSQLite = $env === '1' || strtolower($env) === 'true';

        if ($this->useSQLite) {
            $this->host = __DIR__ . '/../data/test.sqlite';
        } else {
            // Utiliza las constantes definidas en config.php para evitar valores duplicados
            $this->host = defined('DB_HOST') ? DB_HOST : 'localhost';
            $this->dbname = defined('DB_NAME') ? DB_NAME : '';
            $this->username = defined('DB_USER') ? DB_USER : 'root';
            $this->password = defined('DB_PASS') ? DB_PASS : '';
        }
    }

    public function connect() {
        try {
            if ($this->useSQLite) {
                $this->pdo = new PDO('sqlite:' . $this->host);
                $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            } else {
                $this->pdo = new PDO(
                    "mysql:host={$this->host};dbname={$this->dbname};charset=utf8",
                    $this->username,
                    $this->password,
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES => false
                    ]
                );
            }
            return $this->pdo;
        } catch(PDOException $e) {
            die("Error de conexión: " . $e->getMessage());
        }
    }

    public function getConnection() {
        if (!$this->pdo) {
            $this->connect();
        }
        return $this->pdo;
    }

    public function isSQLite() {
        return $this->useSQLite;
    }
}
?>