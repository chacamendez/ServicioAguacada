<?php
error_reporting(-1);ini_set("display_errors", 1);
session_start();
$base=__DIR__;
$base=dirname($base);
$base=dirname($base);
echo "hello";
include "$base/PHP/LoadCore.php";
if(Session::isLogged()) {
  if (Session::allowed()){
    $idUsuario=$_GET["idUsuarioExportar"];
    $usuarioExt=classInvoker::nc('usuarios',intval($idUsuario),true);
    $content=$usuarioExt->exportarUsuario2();
    $contacto_file_name = "contacto.csv";
    $fileHandler = fopen($contacto_file_name, 'w+');
    fwrite($fileHandler, $content);
    fclose($fileHandler);

    // Download the SQL backup file to the browser
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename=' . basename($contacto_file_name));
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($contacto_file_name));
    ob_clean();
    flush();
    readfile($contacto_file_name);
    unlink($contacto_file_name);
  }
}
