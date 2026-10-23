<?php
error_reporting(-1);ini_set("display_errors", 1);
session_start();
$base=__DIR__;
$base=dirname($base);
$base=dirname($base);
include "$base/PHP/LoadCore.php";
if(Session::isLogged()) {
  if (Session::allowed()){
    if (empty($_SESSION['idCorrespondencia'])){
      $correspondencia=classInvoker::nc('correspondencias');
      $correspondencia->correspondencia=$_SESSION['correspondencia'];
      $correspondencia->destinatarios=json_encode($_SESSION['destinatarios']);
      $correspondencia->texto=base64_encode($_POST['textoCorrespondencia']);
      $correspondencia->insert();
    }
    else{
      $correspondencia=classInvoker::nc('correspondencias',intval($_SESSION['idCorrespondencia']));
      $correspondencia->correspondencia=$_SESSION['correspondencia'];
      $correspondencia->destinatarios=json_encode($_SESSION['destinatarios']);
      $correspondencia->texto=base64_encode($_POST['textoCorrespondencia']);
      $correspondencia->update();
    }
  }
}
?>
