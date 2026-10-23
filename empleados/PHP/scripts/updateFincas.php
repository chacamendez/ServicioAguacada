<?php
error_reporting(-1);ini_set("display_errors", 1);
session_start();
$base=__DIR__;
$base=dirname($base);
$base=dirname($base);
include "$base/PHP/LoadCore.php";

echo "starting||";
if(Session::isLogged()) {
  echo "logged||";
  if (Session::allowed()){
    echo "onTheway||";
    $query="UPDATE fincas SET defecto=0";
    Database::ExecuteQuery($query);
    $query="SELECT * FROM usuarios WHERE idRol=0";
    incluir('usuarios');
    $usuarios=usuarios::getAll($query);

    if ($usuarios){

      foreach ($usuarios as $usuario) {
        $idUsuario=$usuario['id'];
        $fincaS=$usuario['piso'];
        $fincaO=classInvoker::nc('fincas');
        $fincaO->finca=$fincaS;
        $fincaO->idUsuario=$idUsuario;
        $fincaO->defecto=1;
        print_r($fincaO);
        $fincaO->insert();
      }

    }

  }
  echo "executed";
}
?>
