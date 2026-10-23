<?php
include "funciones.php";
$usuario=comprobar_cokkies();
if (strcmp ($usuario, "admin")) {
		cerrarSesion();
		exit;
} 
$cadena=explode('.',$_FILES['inputUploadFile']['name']);
$pos=sizeof($cadena)-1;
$ext=$cadena[$pos];
$inputNombreArchivo=$_POST['inputNombreArchivo'];
$selectDestinatarioArchivosA=$_POST['selectDestinatarioArchivosA'];
$idComunidad=$_POST['selectDestinatarioArchivosB'];
$idUsuario=$_POST['selectDestinatarioArchivosC'];
if (isset($_POST['selectDestinatarioArchivosA'])&&$_POST['selectDestinatarioArchivosA']!="A todos"){
	if ($idUsuario=='1') $idhash=crear_archivo($inputNombreArchivo,$idComunidad,$idUsuario,$ext);
	else $idhash=crear_archivo($inputNombreArchivo,$idComunidad,$idUsuario,$ext);
}
else $idhash=crear_archivo($inputNombreArchivo,0,0,$ext);
$tmp_file_name = $_FILES['inputUploadFile']['tmp_name'];
move_uploaded_file($tmp_file_name,"../clientes/archivos/$idhash");
?>
