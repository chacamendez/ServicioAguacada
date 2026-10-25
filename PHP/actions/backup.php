<?php
error_reporting(-1);
ini_set("display_errors", 1);
session_start();
$base = __DIR__;
$base = dirname($base);
$base = dirname($base);
include "$base/PHP/LoadCore.php";

if (Session::isLogged()) {
  if (Session::allowed()) {
    $tables = array();
    $query = "SHOW TABLES";
    $mysql = Database::ExecuteQuery($query);
    while ($row = $mysql->fetch_row()) {
        $tables[] = $row[0];
    }

    // Getting table structures and data
    $outsql = '';
    foreach ($tables as $table) {
      // Prepare SQL script for creating table structure
      $query = "SHOW CREATE TABLE `$table`";
      $mysql = Database::ExecuteQuery($query);
      $row = $mysql->fetch_row();
      $outsql .= "\n\n" . $row[1] . ";\n\n";

      // Prepare SQL script for dumping data for each table
      $query = "SELECT * FROM `$table`";
      $mysql = Database::ExecuteQuery($query);
      $columnCount = $mysql->columnCount();

      while ($row = $mysql->fetch_row()) {
          $outsql .= "INSERT INTO `$table` VALUES(";
          for ($j = 0; $j < $columnCount; $j++) {
              if (isset($row[$j])) {
                  // Escapamos de forma segura con PDO si fuera necesario, o dejamos el valor formateado
                  $escaped = addslashes($row[$j]);
                  $outsql .= "'{$escaped}'";
              } else {
                  $outsql .= "NULL";
              }
              if ($j < ($columnCount - 1)) {
                  $outsql .= ",";
              }
          }
          $outsql .= ");\n";
      }
      $outsql .= "\n";
    }

    // Save the SQL script to a backup file
    $fecha = Strings::fechaSQL();
    $backup_file_name = "backupAguacada$fecha.sql";
    $fileHandler = fopen($backup_file_name, 'w+');
    fwrite($fileHandler, $outsql);
    fclose($fileHandler);

    // Download the SQL backup file to the browser
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename=' . basename($backup_file_name));
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($backup_file_name));
    ob_clean();
    flush();
    readfile($backup_file_name);
    unlink($backup_file_name);
  }
}