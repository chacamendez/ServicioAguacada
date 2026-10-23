<?php
error_reporting(-1);ini_set("display_errors", 1);
session_start();
$base=__DIR__;
$base=dirname($base);
$base=dirname($base);
include "$base/PHP/LoadCore.php";
if(Session::isLogged()) {
    if (Session::allowed()){
    $archivo=classInvoker::nc('archivos','',true);
    $archivo->archivo=$_POST['idNombreArchivo'];
    $archivo->generarHash();
    $tmp_file_name = $_FILES['inputSubirArchivo']['tmp_name'];
    while ($archivo->existe()){$archivo->generarHash();}
    move_uploaded_file($tmp_file_name,"$base/PHP/files/{$archivo->idhash}");
    $extension = pathinfo($_FILES['inputSubirArchivo']['name'], PATHINFO_EXTENSION);
    $archivo->extension=$extension;
    $archivo->idComunidad=$_POST['idComunidad'];
    $archivo->idUsuario=$_POST['idUsuario'];
    $archivo->fecha=Strings::fechaSQL();
    $archivo->saveExt();
  }
}
