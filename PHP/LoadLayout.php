<?php
error_reporting(-1);ini_set("display_errors", 1);
include "LoadCore.php";
if(Session::getUID()!=""){
		print "<script>window.location.href='http://restauranteelabogado.es'</script>";
}
$layout=$_GET['layout'];
Layout::load($layout);
?>
