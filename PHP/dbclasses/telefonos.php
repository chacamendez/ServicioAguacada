<?php 
class telefonos {
  public $id=0;
  public $telefono='';
  public $tipo='';
  public $contacto='';
  public $origen='';
  public $defecto=0;
  public $prefijo='+34';
 public function __construct($params=false){
  if ($params){
   $type=gettype($params);
    switch($type){
     case 'integer':
      $query="SELECT * FROM telefonos WHERE id=$params";
      $result=Database::ExecuteQuery($query);
      $row=$result->fetch_assoc();
       foreach($row as $key=>$value){
        $type=gettype($value);
         if ($type=='string') eval("\$this->$key='$value';");
         else  eval("\$this->$key=$value;");
       }
     break;
     case 'string':
     $params=json_decode($params);
     case 'object':
      foreach ($params as $key => $value) {
      $type=gettype($value);
      if ($type=='string') eval("\$this->$key='$value';");
      else  eval("\$this->$key=$value;");
      }
     break;
    
   }
  }
 }
 public function insert(){
  $listadoValores='';
  foreach ($this as $key => $value) {
   if ($key=='id' || $key=='detalles') continue;
   if (gettype($value)=='string') $listadoValores.="'$value',";
   else $listadoValores.="$value,";
  }
  $listadoValores=substr($listadoValores, 0, -1);
  $query="INSERT INTO telefonos (telefono,tipo,contacto,origen,defecto,prefijo) VALUES ($listadoValores);SELECT id FROM telefonos ORDER BY id DESC LIMIT 1";
  $this->id=Database::ExecuteMultiQuery($query,true);
 }
 public function update(){
  $listadoValores='';
  foreach ($this as $key => $value) {
   if ($key=='id' || $key=='detalles') continue;
   if (gettype($value)=='string') $listadoValores.="$key='$value',";
   else $listadoValores.="$key=$value,";
  }
 $listadoValores=substr($listadoValores, 0, -1);
 $query="UPDATE telefonos SET $listadoValores WHERE id={$this->id}";
  Database::ExecuteQuery($query);
 }
 public function delete(){
  $query="DELETE FROM telefonos WHERE id={$this->id}";
  Database::ExecuteQuery($query);
 }
 
 
 
 public static function getAll($query=false,$ext=false){
   if (!$query){
    $query='SELECT *  FROM telefonos ';
    if ($ext) $query .= $ext;
    else $query.=' ORDER BY id DESC';
   }
  $result = Database::ExecuteQuery($query);
  $listado=false;
  while ($row=$result->fetch_assoc()){
   	$listado[]=$row;
  }
  if (!$listado) return false;
  else return $listado;
 }
 
 public static function getRow($query){
  $result = Database::ExecuteQuery($query);
  $listado=false;
  while ($row=$result->fetch_assoc()){
   	$listado[]=$row;
  }
  if (!$listado) return false;
  else return $listado[0];
 }
 
 public static function getValue($query){
  $result = Database::ExecuteQuery($query);
  $row=$result->fetch_row();
  return $row[0];
 }
 public static function jsonAdapter($json){
  if ($json){
  $object=new telefonos();
  $type=gettype($json);
  switch($type){
   case 'string':
    $json=json_decode($json);
    break;
   }
   foreach($json as $key=>$value){
    if (!isset($value) || $value=="") continue;
    if ($type=='string') eval("\$object->$key='$value';");
    else   eval("\$object->$key=$value;");
   }
   return $object;
   }
  }
  public static function toJSON($query=false){
   if (!$query) $query='SELECT * FROM telefonos ORDER BY id DESC';
    $telefonos=self::toARRAY($query);    if ($telefonos) return json_encode($telefonos);
    else return null;
 }
  public static function toARRAY($query=false){
   if (!$query) $query='SELECT * FROM telefonos ORDER BY id DESC';
   $telefonos=Database::ExecuteQuery($query);
   if ($telefonos->num_rows!=0){
    while ($row=$telefonos->fetch_assoc()){
      $rows[]=$row;
    }
    return $rows;
   }
   else return false;
 }
}

?>