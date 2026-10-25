<?php
class incidencias {
  public $id=0;
  public $resumen='Vacio';
  public $estado='Ninguno';
  public $alerta=0;
  public function __construct($params=false){
    if ($params){
      $type=gettype($params);
      switch($type){
        case 'integer':
          $row=Database::getRow("SELECT * FROM `incidencias` WHERE `id` = $params");
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
      if ($key == 'id' || $key == 'detalles') continue;
      $fields[] = "`$key`";
      $placeholders[] = "?";
      $values[] = $value;
    }
    $sqlFields = implode(',', $fields);
    $sqlPlaceholders = implode(',', $placeholders);
    $query = "INSERT INTO `incidencias` ($sqlFields) VALUES ($sqlPlaceholders)";
    Database::ExecuteQuery($query, $values);
    $this->id = Database::lastInsertId();
  }

  public function update(){
    $sets = [];
    $values = [];
    foreach ($this as $key => $value) {
      if ($key == 'id' || $key == 'detalles') continue;
      $sets[] = "`$key` = ?";
      $values[] = $value;
    }
    $values[] = $this->id;
    $sqlSets = implode(',', $sets);
    $query = "UPDATE `incidencias` SET $sqlSets WHERE `id` = ?";
    Database::ExecuteQuery($query, $values);
  }

  public function delete(){
    $query = "DELETE FROM `incidencias` WHERE `id` = ?";
    Database::ExecuteQuery($query, [$this->id]);
  }

  public static function getAll($query=false, $params=false){
    if (!$query){
      $query='SELECT * FROM `incidencias` ORDER BY `id` DESC';
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