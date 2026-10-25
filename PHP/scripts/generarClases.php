<?php
// Configuración de la conexión a la base de datos usando PDO
$host = 'localhost';
$dbname = 'aguacada';
$username = 'root';
$password = 'Polifem@1';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Obtener todas las tablas de la base de datos
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

    // Directorio donde se guardarán las clases generadas
    $outputDir = __DIR__ . '/../dbclasses/';
    
    // Crear el directorio si no existe
    if (!file_exists($outputDir)) {
        mkdir($outputDir, 0777, true);
    }

    foreach ($tables as $table) {
        // Obtener las columnas de la tabla
        $colStmt = $pdo->prepare("SHOW COLUMNS FROM `$table`");
        $colStmt->execute();
        $columns = $colStmt->fetchAll(PDO::FETCH_ASSOC);

        $properties = [];
        $fieldNames = [];
        $primaryKey = 'id';

        foreach ($columns as $col) {
            $field = $col['Field'];
            $fieldNames[] = $field;
            if ($col['Key'] == 'PRI') {
                $primaryKey = $field;
            }
            
            // Determinar tipo por defecto basado en el tipo SQL
            $type = strtolower($col['Type']);
            $defaultVal = $col['Default'];
            
            if (strpos($type, 'int') !== false || strpos($type, 'float') !== false || strpos($type, 'double') !== false) {
                $val = ($defaultVal !== null) ? $defaultVal : 0;
                $properties[$field] = $val;
            } else {
                $val = ($defaultVal !== null) ? "'$defaultVal'" : "''";
                $properties[$field] = $val;
            }
        }

        // Construir el código de la clase dinámicamente
        $classCode = "<?php\n";
        $classCode .= "class $table {\n";

        // Declaración de propiedades
        foreach ($properties as $key => $val) {
            $classCode .= "  public \$$key=$val;\n";
        }

        // Constructor
        $classCode .= "  public function __construct(\$params=false){\n";
        $classCode .= "    if (\$params){\n";
        $classCode .= "      \$type=gettype(\$params);\n";
        $classCode .= "      switch(\$type){\n";
        $classCode .= "        case 'integer':\n";
        $classCode .= "          \$row=Database::getRow(\"SELECT * FROM `$table` WHERE `$primaryKey` = \$params\");\n";
        $classCode .= "          if (\$row){\n";
        $classCode .= "            foreach(\$row as \$key=>\$value){\n";
        $classCode .= "              if (property_exists(\$this, \$key)) {\n";
        $classCode .= "                \$this->\$key = is_numeric(\$value) ? \$value : \$value;\n";
        $classCode .= "              }\n";
        $classCode .= "            }\n";
        $classCode .= "          }\n";
        $classCode .= "        break;\n";
        $classCode .= "        case 'string':\n";
        $classCode .= "          \$params=json_decode(\$params);\n";
        $classCode .= "        case 'object':\n";
        $classCode .= "          foreach (\$params as \$key => \$value) {\n";
        $classCode .= "            if (property_exists(\$this, \$key)) {\n";
        $classCode .= "              \$this->\$key=\$value;\n";
        $classCode .= "            }\n";
        $classCode .= "          }\n";
        $classCode .= "        break;\n";
        $classCode .= "      }\n";
        $classCode .= "    }\n";
        $classCode .= "  }\n\n";

        // Método Insert
        $classCode .= "  public function insert(){\n";
        $classCode .= "    \$fields = [];\n";
        $classCode .= "    \$placeholders = [];\n";
        $classCode .= "    \$values = [];\n";
        $classCode .= "    foreach (\$this as \$key => \$value) {\n";
        $classCode .= "      if (\$key == '$primaryKey' || \$key == 'detalles') continue;\n";
        $classCode .= "      \$fields[] = \"`\$key`\";\n";
        $classCode .= "      \$placeholders[] = \"?\";\n";
        $classCode .= "      \$values[] = \$value;\n";
        $classCode .= "    }\n";
        $classCode .= "    \$sqlFields = implode(',', \$fields);\n";
        $classCode .= "    \$sqlPlaceholders = implode(',', \$placeholders);\n";
        $classCode .= "    \$query = \"INSERT INTO `$table` (\$sqlFields) VALUES (\$sqlPlaceholders)\";\n";
        $classCode .= "    Database::ExecuteQuery(\$query, \$values);\n";
        $classCode .= "    \$this->$primaryKey = Database::lastInsertId();\n";
        $classCode .= "  }\n\n";

        // Método Update
        $classCode .= "  public function update(){\n";
        $classCode .= "    \$sets = [];\n";
        $classCode .= "    \$values = [];\n";
        $classCode .= "    foreach (\$this as \$key => \$value) {\n";
        $classCode .= "      if (\$key == '$primaryKey' || \$key == 'detalles') continue;\n";
        $classCode .= "      \$sets[] = \"`\$key` = ?\";\n";
        $classCode .= "      \$values[] = \$value;\n";
        $classCode .= "    }\n";
        $classCode .= "    \$values[] = \$this->$primaryKey;\n";
        $classCode .= "    \$sqlSets = implode(',', \$sets);\n";
        $classCode .= "    \$query = \"UPDATE `$table` SET \$sqlSets WHERE `$primaryKey` = ?\";\n";
        $classCode .= "    Database::ExecuteQuery(\$query, \$values);\n";
        $classCode .= "  }\n\n";

        // Método Delete
        $classCode .= "  public function delete(){\n";
        $classCode .= "    \$query = \"DELETE FROM `$table` WHERE `$primaryKey` = ?\";\n";
        $classCode .= "    Database::ExecuteQuery(\$query, [\$this->$primaryKey]);\n";
        $classCode .= "  }\n\n";

        // Método getAll corregido para usar Database::ExecuteQuery y fetch_assoc
        $classCode .= "  public static function getAll(\$query=false, \$params=false){\n";
        $classCode .= "    if (!\$query){\n";
        $classCode .= "      \$query='SELECT * FROM `$table` ORDER BY `$primaryKey` DESC';\n";
        $classCode .= "    }\n";
        $classCode .= "    \$result = Database::ExecuteQuery(\$query, false, \$params);\n";
        $classCode .= "    \$listado = [];\n";
        $classCode .= "    if (\$result) {\n";
        $classCode .= "      while (\$row = \$result->fetch_assoc()) {\n";
        $classCode .= "        \$listado[] = \$row;\n";
        $classCode .= "      }\n";
        $classCode .= "    }\n";
        $classCode .= "    return empty(\$listado) ? false : \$listado;\n";
        $classCode .= "  }\n\n";

        // Método getRow
        $classCode .= "  public static function getRow(\$query, \$params=false){\n";
        $classCode .= "    return Database::getRow(\$query, \$params);\n";
        $classCode .= "  }\n\n";

        // Método getValue
        $classCode .= "  public static function getValue(\$query, \$params=false){\n";
        $classCode .= "    return Database::getValue(\$query, \$params);\n";
        $classCode .= "  }\n\n";

        // Métodos JSON y Array
        $classCode .= "  public static function toJSON(\$query=false, \$params=false){\n";
        $classCode .= "    \$rows = self::toARRAY(\$query, \$params);\n";
        $classCode .= "    return \$rows ? json_encode(\$rows) : null;\n";
        $classCode .= "  }\n\n";

        $classCode .= "  public static function toARRAY(\$query=false, \$params=false){\n";
        $classCode .= "    return self::getAll(\$query, \$params);\n";
        $classCode .= "  }\n";

        $classCode .= "}\n";
        $classCode .= "?>";

        // Guardar el archivo sobrescribiendo el anterior
        file_put_contents($outputDir . $table . '.php', $classCode);
        echo "Clase generada/actualizada con éxito: $table.php<br>";
    }

    echo "<br><strong>¡Todas las clases se han regenerado y actualizado en /PHP/dbclases/ correctamente!</strong>";

} catch (PDOException $e) {
    echo "Error de conexión o consulta: " . $e->getMessage();
}
?>