<?php
class propietariosFincas {
  public $idPropietario=0;
  public $idFinca=0;
  public $coeficiente=0;
  public function __construct($params=false){
    if ($params){
      $type=gettype($params);
      switch($type){
        case 'integer':
          $row=Database::getRow("SELECT * FROM `propietariosFincas` WHERE `idFinca` = $params");
          if ($row){
            foreach($row as $key=>$value){
              if (property_exists($this, $key)) {
                $this->$key = is_numeric($value) ? $value : $value;
              }
            }
          }
        break;
        case 'string':
          $params=json_decode($params);
        case 'object':
          foreach ($params as $key => $value) {
            if (property_exists($this, $key)) {
              $this->$key=$value;
            }
          }
        break;
      }
    }
  }

  public function insert(){
    $fields = [];
    $placeholders = [];
    $values = [];
    foreach ($this as $key => $value) {
      if ($key == 'idFinca' || $key == 'detalles') continue;
      $fields[] = "`$key`";
      $placeholders[] = "?";
      $values[] = $value;
    }
    $sqlFields = implode(',', $fields);
    $sqlPlaceholders = implode(',', $placeholders);
    $query = "INSERT INTO `propietariosFincas` ($sqlFields) VALUES ($sqlPlaceholders)";
    Database::ExecuteQuery($query, false, $values);
    $this->idFinca = Database::lastInsertId();
  }

  public function update(){
    $sets = [];
    $values = [];
    foreach ($this as $key => $value) {
      if ($key == 'idFinca' || $key == 'detalles') continue;
      $sets[] = "`$key` = ?";
      $values[] = $value;
    }
    $values[] = $this->idFinca;
    $sqlSets = implode(',', $sets);
    $query = "UPDATE `propietariosFincas` SET $sqlSets WHERE `idFinca` = ?";
    Database::ExecuteQuery($query, false, $values);
  }

  public function delete(){
    $query = "DELETE FROM `propietariosFincas` WHERE `idFinca` = ?";
    Database::ExecuteQuery($query, false, [$this->idFinca]);
  }

  public static function getAll($query=false, $params=false){
    if (!$query){
      $query='SELECT * FROM `propietariosFincas` ORDER BY `idFinca` DESC';
    }
    $result = Database::ExecuteQuery($query, false, $params);
    $listado = [];
    if ($result) {
      while ($row = $result->fetch_assoc()) {
        $listado[] = $row;
      }
    }
    return empty($listado) ? false : $listado;
  }

  public static function getRow($query, $params=false){
    return Database::getRow($query, $params);
  }

  public static function getValue($query, $params=false){
    return Database::getValue($query, $params);
  }

  public static function toJSON($query=false, $params=false){
    $rows = self::toARRAY($query, $params);
    return $rows ? json_encode($rows) : null;
  }

  public static function toARRAY($query=false, $params=false){
    return self::getAll($query, $params);
  }
}
?>