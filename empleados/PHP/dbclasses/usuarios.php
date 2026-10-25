<?php
class usuarios {
  public $id=0;
  public $usuario='';
  public $clave='';
  public $imagen=0;
  public $idRol=0;
  public $reset=1;
  public $estado='creada';
  public $claveDefecto='';
  public $nombre='';
  public $idComunidad=0;
  public $piso='';
  public $correspondencia=0;
  public function __construct($params=false){
    if ($params){
      $type=gettype($params);
      switch($type){
        case 'integer':
          $row=Database::getRow("SELECT * FROM `usuarios` WHERE `id` = $params");
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
    $query = "INSERT INTO `usuarios` ($sqlFields) VALUES ($sqlPlaceholders)";
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
    $query = "UPDATE `usuarios` SET $sqlSets WHERE `id` = ?";
    Database::ExecuteQuery($query, $values);
  }

  public function delete(){
    $query = "DELETE FROM `usuarios` WHERE `id` = ?";
    Database::ExecuteQuery($query, [$this->id]);
  }

  public static function getAll($query=false, $params=false){
    if (!$query){
      $query='SELECT * FROM `usuarios` ORDER BY `id` DESC';
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