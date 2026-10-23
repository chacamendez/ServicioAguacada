<?php 
class incidenciasDetalle {
  public $id=0;
  public $fechaHora='1977-10-10 00:00:00';
  public $detalle='';
  public $idIncidencias=0;
  public $idUsuario=0;
  public $idComunidad=0;
 public function __construct($params=false){
  if ($params){
   $type=gettype($params);
    switch($type){
     case 'integer':
      $query="SELECT * FROM incidenciasDetalle WHERE id=$params";
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
  $query="INSERT INTO incidenciasDetalle (fechaHora,detalle,idIncidencias,idUsuario,idComunidad) VALUES ($listadoValores);SELECT id FROM incidenciasDetalle ORDER BY id DESC LIMIT 1";
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
 $query="UPDATE incidenciasDetalle SET $listadoValores WHERE id={$this->id}";
  Database::ExecuteQuery($query);
 }
 public function delete(){
  $query="DELETE FROM incidenciasDetalle WHERE id={$this->id}";
  Database::ExecuteQuery($query);
 }
 
 
 
 public static function getAll($query=false,$ext=false){
   if (!$query){
    $query='SELECT *  FROM incidenciasDetalle ';
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
  $object=new incidenciasDetalle();
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
   if (!$query) $query='SELECT * FROM incidenciasDetalle ORDER BY id DESC';
    $incidenciasDetalle=self::toARRAY($query);    if ($incidenciasDetalle) return json_encode($incidenciasDetalle);
    else return null;
 }
  public static function toARRAY($query=false){
   if (!$query) $query='SELECT * FROM incidenciasDetalle ORDER BY id DESC';
   $incidenciasDetalle=Database::ExecuteQuery($query);
   if ($incidenciasDetalle->num_rows!=0){
    while ($row=$incidenciasDetalle->fetch_assoc()){
      $rows[]=$row;
    }
    return $rows;
   }
   else return false;
 }
}

?>