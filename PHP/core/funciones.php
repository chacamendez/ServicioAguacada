<?php
function getDir($option){
  $dir=dirname(__DIR__);
  $dir=dirname($dir);
  switch ($option) {
    case 'resources':
      return "$dir/RESOURCES/";
      break;

    default:
      // code...
      break;
  }
}
function incluir($class,$ext=false){
  $dir=dirname(__DIR__);
  include_once "$dir/dbclasses/$class.php";
  if ($ext){
    include_once "$dir/ext/{$class}Ext.php";
  }
}
 function incluirExt($class){
  $dir=dirname(__DIR__);
  include_once "$dir/core/ext/$class.php";
}
function minutosEspera(){
  $query="SELECT IFNULL(SUM(unidades),0) FROM comandas,comandasDetalle,articulos WHERE comandasDetalle.idArticulo=articulos.id AND (articulos.seccion=11 OR articulos.seccion=31) AND comandasDetalle.elaboracion<2 AND comandas.idTicket=0 AND comandas.id=comandasDetalle.idComanda";
  $numPizzas=Database::ExecuteQuery($query);
  $minutosEspera=10+1*$numPizzas;
  return $minutosEspera;
}
function idNotPresent($array,$id){
  if (!isset($array)) return true;
	foreach ($array as $valor){
		if ($valor==$id) return false;
	}
	return true;
}
function idExiste($tabla,$id){
  $query="SELECT COUNT(*) FROM $tabla WHERE id=$id";
  $count=Database::ExecuteQuery($query,true);
  if ($count==1) return true;
  else return false;
}


 ?>
