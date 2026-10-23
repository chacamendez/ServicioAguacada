<?php 
class archivos {
  public $id=0;
  public $idhash='';
  public $archivo='unnamed';
  public $idComunidad=0;
  public $fecha='1977-10-10';
  public $idUsuario=0;
  public $extension='';
  public $numDescargas=0;
  public $historialDescargas='';
 public function __construct($params=false){
  if ($params){
   $type=gettype($params);
    switch($type){
     case 'integer':
      $query="SELECT * FROM archivos WHERE id=$params";
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
  $query="INSERT INTO archivos (idhash,archivo,idComunidad,fecha,idUsuario,extension,numDescargas,historialDescargas) VALUES ($listadoValores);SELECT id FROM archivos ORDER BY id DESC LIMIT 1";
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
 $query="UPDATE archivos SET $listadoValores WHERE id={$this->id}";
  Database::ExecuteQuery($query);
 }
 public function delete(){
  $query="DELETE FROM archivos WHERE id={$this->id}";
  Database::ExecuteQuery($query);
 }
 
 
 
 public static function getAll($query=false,$ext=false){
   if (!$query){
    $query='SELECT *  FROM archivos ';
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
  $object=new archivos();
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
   if (!$query) $query='SELECT * FROM archivos ORDER BY id DESC';
    $archivos=self::toARRAY($query);    if ($archivos) return json_encode($archivos);
    else return null;
 }
  public static function toARRAY($query=false){
   if (!$query) $query='SELECT * FROM archivos ORDER BY id DESC';
   $archivos=Database::ExecuteQuery($query);
   if ($archivos->num_rows!=0){
    while ($row=$archivos->fetch_assoc()){
      $rows[]=$row;
    }
    return $rows;
   }
   else return false;
 }
}

?>