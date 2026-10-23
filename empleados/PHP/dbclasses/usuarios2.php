<?php 
class usuarios2 {
  public $usuario='';
  public $clave='';
  public $nombre='Vacio';
  public $permisos='Ninguno';
  public $comunidad=0;
  public $primera=1;
  public $piso='Ninguno';
  public $id=0;
  public $idhash='';
  public $claveDefecto='';
  public $claveIV='';
  public $sesionIV='';
  public $usuarioDefecto='';
 public function __construct($params=false){
  if ($params){
   $type=gettype($params);
    switch($type){
     case 'integer':
      $query="SELECT * FROM usuarios2 WHERE id=$params";
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
  $query="INSERT INTO usuarios2 (clave,nombre,permisos,comunidad,primera,piso,id,idhash,claveDefecto,claveIV,sesionIV,usuarioDefecto) VALUES ($listadoValores);SELECT id FROM usuarios2 ORDER BY id DESC LIMIT 1";
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
 $query="UPDATE usuarios2 SET $listadoValores WHERE id={$this->id}";
  Database::ExecuteQuery($query);
 }
 public function delete(){
  $query="DELETE FROM usuarios2 WHERE id={$this->id}";
  Database::ExecuteQuery($query);
 }
 
 
 
 public static function getAll($query=false,$ext=false){
   if (!$query){
    $query='SELECT *  FROM usuarios2 ';
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
  $object=new usuarios2();
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
   if (!$query) $query='SELECT * FROM usuarios2 ORDER BY id DESC';
    $usuarios2=self::toARRAY($query);    if ($usuarios2) return json_encode($usuarios2);
    else return null;
 }
  public static function toARRAY($query=false){
   if (!$query) $query='SELECT * FROM usuarios2 ORDER BY id DESC';
   $usuarios2=Database::ExecuteQuery($query);
   if ($usuarios2->num_rows!=0){
    while ($row=$usuarios2->fetch_assoc()){
      $rows[]=$row;
    }
    return $rows;
   }
   else return false;
 }
}

?>