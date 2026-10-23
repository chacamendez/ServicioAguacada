<?php
error_reporting(-1);ini_set("display_errors", 1); 
include "LoadCore.php";
if(Session::getUID()!=""){
		print "<script>window.location='index.php?layout=home';</script>";
}
$action=$_GET['action'];
Action::exec($action,$_GET);
?>
