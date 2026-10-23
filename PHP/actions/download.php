<?php
error_reporting(-1);ini_set("display_errors", 1);
session_start();
$base=__DIR__;
$base=dirname($base);
$base=dirname($base);
include "$base/PHP/LoadCore.php";
if(Session::isLogged()) {
    $idArchivo=$_GET['idArchivo'];
    incluir('archivos');
    $archivo=classInvoker::nc('archivos',intval($idArchivo),true);
    if (!$archivo->hasPermissions()) return;
    $dirFiles=dirname(__DIR__);
    $dirFiles="$dirFiles/files";
    $filepath="$dirFiles/{$archivo->idhash}";
    if ($archivo->extension!='') $fileName="{$archivo->archivo}.{$archivo->extension}";
    else $fileName="{$archivo['archivo']}";
    if(file_exists($filepath)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="'.$fileName.'"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filepath));
        flush(); // Flush system output buffer
		ob_end_clean();
        readfile($filepath);
        $archivo->numDescargas+=1;
        $archivo->addHistorial();
        $archivo->update();
        die();
    } else {
      die();
    }

}
