<?php
class CustomPDOStatement extends PDOStatement {
    public function fetch_row() {
        return $this->fetch(PDO::FETCH_NUM);
    }
}

class Database {
    public static $errno;
    public static $db;
    public static $con;
    public static $errorMessage;

    private $user;
    private $pass;
    private $host;
    private $DB;

    function __construct($host, $db, $user, $password) {
        $this->user = $user;
        $this->pass = $password;
        $this->host = $host;
        $this->DB = $db;
    }

    function connect() {
        try {
            $dsn = "mysql:host={$this->host};dbname={$this->DB};charset=utf8mb4";
            $con = new PDO($dsn, $this->user, $this->pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_NUM,
                PDO::ATTR_STATEMENT_CLASS => [CustomPDOStatement::class],
                PDO::MYSQL_ATTR_MULTI_STATEMENTS => true,
            ]);
            return $con;
        } catch (PDOException $e) {
            $error = $e->getMessage();
            echo "<error>{$error} reseteando usuario</error>";
            exit();
        }
    }

    public static function getCon($toUnReset = false) {
        self::$con = self::$db->connect();
        return self::$con;
    }

    public static function ExecuteMultiQuery($query, $value = false, $params = []) {
        $con = self::getCon();
        try {
            if ($value) {
                $stmt = $con->prepare($query);
                $stmt->execute($params);
                self::$errno = 0;
                $row = $stmt->fetch(PDO::FETCH_NUM);
                return $row ? $row[0] : null;
            } else {
                $affected = $con->exec($query);
                self::$errno = 0;
                return $affected;
            }
        } catch (PDOException $e) {
            self::$errno = $e->getCode();
            self::$errorMessage = $e->getMessage();
            return false;
        }
    }

    public static function ExecuteQuery($query, $value = false, $params = []) {
        $con = self::getCon();
        try {
            $stmt = $con->prepare($query);
            $stmt->execute($params);
            self::$errno = 0;

            if ($value) {
                $row = $stmt->fetch(PDO::FETCH_NUM);
                return $row ? $row[0] : self::$errorMessage;
            } else {
                return $stmt;
            }
        } catch (PDOException $e) {
            self::$errno = $e->getCode();
            self::$errorMessage = $e->getMessage();
            if ($value) {
                return self::$errorMessage;
            }
            return false;
        }
    }

    public static function backup($path = false) {
        Settings::saveDB();
        $clave = "Polifem@1";
        $fecha = date("d-m-Y-H-i-s");
        $archivo = "backupTPV$fecha.sql";
        if (!$path) {
            $base = __DIR__;
            $base = dirname($base);
            $base = dirname($base);
            $path = "$base/Backups/";
            $path .= $archivo;
        }
        exec("mysqldump -uroot -p$clave --no-create-db --no-create-info tpvAbogado > $path");
    }

    public static function restaurarTPV($file = false) {
        if ($file) {
            // Limpiar tablas
            $query = "SHOW TABLES";
            $tables = Database::ExecuteQuery($query);
            while ($row = $tables->fetch_row()) {
                $query = "TRUNCATE TABLE `" . $row[0] . "`";
                Database::ExecuteQuery($query);
            }
            
            // Restaurar archivo
            $templine = '';
            $lines = file($file);
            foreach ($lines as $line) {
                if (substr(trim($line), 0, 2) == '--' || trim($line) == '') continue;
                $templine .= $line;
                if (substr(trim($line), -1, 1) == ';') {
                    Database::ExecuteQuery($templine);
                    $templine = '';
                }
            }
            
            // Restablecer archivos (Corregido error tipográfico en 'Databse')
            $query = "SELECT nombreArchivo, archivo FROM archivos";
            $resultado = Database::ExecuteQuery($query);
            while ($row = $resultado->fetch_row()) {
                $base = __DIR__;
                $base = dirname($base);
                $base = dirname($base);
                $nombreArchivo = $row[0];
                $archivo = $row[1];
                file_put_contents("$base/RESOURCES/$nombreArchivo", $archivo);
            }
        } else {
            // codigo con file por defecto
        }
    }
}
?>