<?php
include "funciones.php";
$funcion=$_GET['funcion'];
//error_reporting(-1);
//ini_set("display_errors", 1); 
call_user_func($funcion);
function cerrarSesion(){
	setcookie("SESION",$_COOKIE['SESION'],time() - 300,'/'); 
	setcookie("PHPSESSID",$_COOKIE['PHPSESSID'],time() - 300,'/');
	echo 1;
}
function comprobarCokkies(){
	$resultado=comprobar_cokkies();
	echo $resultado;
}
function comprobarPrimera(){
	$usuario=comprobar_cokkies();
	if ($usuario=='0') {
		exit;
	} 
	$usuario=$_SESSION['Usuario'];
	if (comprobar_primera($usuario)) echo 1;
	else echo 0;
}
function crearMensajeXML(){
	$usuario=comprobar_cokkies();
	if ($usuario=='0') {
		exit;
	} 
	$remitente=$_GET['remitente'];
	$idComunidad=$_GET['destino'];
	$idUsuario=$_GET['destinatario'];
	$asunto=$_GET['asunto'];
	$contenido=$_GET['contenido'];
	if (mb_check_encoding($asunto, 'UTF-8'))	$asunto=utf8_decode($asunto);
	if (mb_check_encoding($contenido, 'UTF-8'))	$contenido=utf8_decode($contenido);
	if (strcmp ($usuario, "admin")) {
		crear_mensaje($idComunidad,$idUsuario,$asunto,$contenido,$remitente);
	}
	else {
		
		crear_mensaje($idComunidad,$idUsuario,$asunto,$contenido);

	}
	
}
function challengeGenerator(){
	require_once('challengeclass.php');
	// instantiate a ChallengeGenerator object
	$chlgen=new ChallengeGenerator();
	// register challenge variable
	$chlgen->setChallengeVar('aguacada');
	echo $chlgen->getChallengeVar('aguacada');
}
function datosPersonalesXML(){
	$usuario=comprobar_cokkies();
	if ($usuario=='0') {
		exit;
	} 
	$mysql=conectar();
	$query="SELECT id,usuario,nombre FROM usuarios WHERE usuario=$usuario";
	$resultado=$mysql->query($query);
	$row=$resultado->fetch_row();
	echo "<usuario id='$row[0]'><user>$row[1]</user><nombre>$row[2]</nombre></usuario>";
	$mysql->close;
}
function descargarArchivo(){
	$usuario=comprobar_cokkies();
	if ($usuario=='0') {
		exit;
	} 
	$idArchivo=$_GET['idArchivo'];
	$mysql=conectar();
	$query="SELECT idhash,extension,archivo FROM archivos WHERE id=$idArchivo";
	$resultado=$mysql->query($query);
	$mysql->close;
	$row=$resultado->fetch_row();
	header('Content-Type:text/xml; charset=iso-8859-1');
	$fichero=$row[0];
	$archivo=$row[2].".".$row[1];
	$file="./archivos/$fichero";
	if (file_exists($file)) {
    	header('Content-Description: File Transfer');
    	header('Content-Type: application/octet-stream');
    	header('Content-Disposition: attachment; filename="'.$archivo.'"');
    	header('Content-Transfer-Encoding: binary');
    	header('Expires: 0');
    	header('Cache-Control: must-revalidate');
    	header('Pragma: public');
    	header('Content-Length: ' . filesize($file));
    	ob_clean();
    	flush();
    	readfile($file);
    	exit;
	}
}
function eliminarArchivos(){
	$usuario=comprobar_cokkies();
	if (strcmp ($usuario, "admin")) {
		cerrarSesion();
		exit;
	} 
	$idArchivo=$_GET['idArchivo'];
	$mysql=conectar();
	$query="SELECT idhash FROM archivos WHERE id=$idArchivo";
	$resultado=$mysql->query($query);
	$row=$resultado->fetch_row();
	$fichero="./archivos/$row[0]";
	$prueba=unlink($fichero);
	$query="DELETE FROM archivos WHERE id=$idArchivo";
	$resultado=$mysql->query($query);
	$mysql->close;
}
function insertarDatosPersonales(){
	$usuario=comprobar_cokkies();
	if ($usuario=='0') {
		exit;
	} 
	$idUsuario=$_SESSION['idUsuario'];
	$nombre=$_GET['nombre'];
	$user=$_GET['usuario'];
	$clave=encriptar($_GET['clave']);
        $clave=base64_encode($clave);
	$query="UPDATE usuarios SET usuario='$user',nombre='$nombre',clave='$clave',primera=0 WHERE id=$idUsuario";
	$mysql=conectar();
	$resultado=$mysql->query($query);
	$mysql->close;
	$valor=logginPrimera($user,$clave);
	echo $valor;
}
function insertarUsuariosXML(){
	$usuario=comprobar_cokkies();
	if (strcmp ($usuario, "admin")) {
		cerrarSesion();
		exit;
	} 
	 
	$numeroUsuarios=$_GET['numero'];
	$idComunidad=$_GET['idComunidad'];
	crear_usuarios_comunidad($numeroUsuarios,$idComunidad);
}
function listadoAlertasXML(){
	$usuario=comprobar_cokkies();
	if (strcmp ($usuario, "admin")) {
		cerrarSesion();
		exit;
	} 
	header('Content-Type:text/xml; charset=iso-8859-1');
	echo "<alertas>";
	$resultado=matrix_alertas();
	for ($i=0;$i<count($resultado);$i++){
		$id=$resultado[$i][1];
		$tipo=$resultado[$i][0];
		$remitente=$resultado[$i][2];
		$asunto=$resultado[$i][3];
		$contenido=$resultado[$i][4];
		echo "<alerta id='$id'><tipo>$tipo</tipo><remitente>$remitente</remitente><asunto>$asunto</asunto><contenido>$contenido</contenido></alerta>";
	}
	echo "</alertas>";
}
function listadoArchivosXML(){
	$usuario=comprobar_cokkies();
	if ($usuario=='0') {
		exit;
	} 
	if($_SESSION['Comunidad']==1){
		$valor=$_GET['valor'];
		$filtro='comunidad';
	}
//echo "valor es:$valor";
	$limit=$_GET['limit'];
	$offset=$_GET['offset'];
	header('Content-Type:text/xml; charset=iso-8859-1');
	echo "<archivos>";
	$resultado=matrix_archivos($limit,$offset,$filtro,$valor);
	while ($row=$resultado->fetch_row()){
		if ($row[2]!=0) {
			if($_SESSION['idUsuario']!=$row[2]&&$_SESSION['Comunidad']!=1){
				continue;
			}
			$mysql=conectar();
			$query="SELECT nombre FROM usuarios WHERE id=$row[2]";
			$resultado2=$mysql->query($query);
			$row2=$resultado2->fetch_row();
			$destinatario=$row2[0];
			$mysql->close;
		} 
		else if ($row[3]!=0){
			if($_SESSION['Comunidad']!=$row[3]&&$_SESSION['Comunidad']!=1){
				continue;
			}
			$mysql=conectar();
			$query="SELECT comunidad FROM comunidades WHERE id=$row[3]";
			$resultado2=$mysql->query($query);
			$row2=$resultado2->fetch_row();
			$destinatario=$row2[0];
			$mysql->close;
		}
		else $destinatario="A todos";
		echo "<archivo id='$row[0]'><nombre>$row[1]</nombre><destinatario>$destinatario</destinatario><fecha>$row[4]</fecha></archivo>";
	}
	echo "</archivos>";
}
function listadoComunidadesXML(){
	$usuario=comprobar_cokkies();
	if ($usuario=='0') {
		exit;
	} 
	header('Content-Type:text/xml; charset=iso-8859-1');
	echo "<comunidades>";
	$resultado=matrix_comunidades($filtro,$valor);
	while ($row=$resultado->fetch_row()){
		echo "<comunidad id='$row[1]'>$row[0]</comunidad>";
	}
	echo "</comunidades>";
}
function listadoContenidoIncidenciasXML(){
	$usuario=comprobar_cokkies();
	if ($usuario=='0') {
		exit;
	} 
	header('Content-Type:text/xml; charset=iso-8859-1');
	$idIncidencia=$_GET['idIncidencia'];
	echo "<incidencias>";
	$resultado=matrix_contenido_incidencia($idIncidencia);
	while ($row=$resultado->fetch_row()){
		echo "<incidencia id='$row[0]'><contenido>$row[1]</contenido><estado>$row[2]</estado><fecha>$row[3]</fecha>".
		"<asunto>$row[4]</asunto><usuario>$row[5]</usuario></incidencia>";
	}
	echo "</incidencias>";
}
function listadoIncidenciasXML(){
	$usuario=comprobar_cokkies();
	if (strcmp ($usuario, "admin")) {
		cerrarSesion();
		exit;
	} 
	header('Content-Type:text/xml; charset=iso-8859-1');
	echo "<incidencias>";
	$limit=$_GET['limit'];
	$offset=$_GET['offset'];
	$valor=$_GET['valor'];
	$filtro='comunidades.id';
	$resultado=matrix_incidencias($limit,$offset,$filtro,$valor);
	while ($row=$resultado->fetch_row()){
		$fecha=fechaHora($row[2]);
		echo "<incidencia id='$row[0]'><asunto>$row[1]</asunto><fecha>$fecha</fecha><estado>$row[3]</estado>".
		"<comunidad>$row[4]</comunidad><idIncidencia>$row[5]</idIncidencia></incidencia>";
	}
	echo "</incidencias>";
}
function listadoIncidenciasClientesXML(){
	$usuario=comprobar_cokkies();
	if ($usuario=='0') {
		exit;
	} 
	header('Content-Type:text/xml; charset=iso-8859-1');
	echo "<incidencias>";
	$limit=$_GET['limit'];
	$offset=$_GET['offset'];
	$filtro=$_GET['filtro'];
	$valor=$_GET['valor'];
	if($_SESSION['Comunidad']!=0){
		$valor=$_SESSION['Comunidad'];
		$filtro="comunidades.id";
	}
	else {
		$valor=$_GET['valor'];
		$filtro='comunidades.id';
	}
	$resultado=matrix_incidencias($limit,$offset,$filtro,$valor);
	while ($row=$resultado->fetch_row()){
		$fecha=fechaHora($row[2]);
		echo "<incidencia id='$row[0]'><asunto>$row[1]</asunto><fecha>$fecha</fecha><estado>$row[3]</estado>".
		"<comunidad>$row[4]</comunidad><idIncidencia>$row[5]</idIncidencia></incidencia>";
	}
	echo "</incidencias>";
}
function listadoMensajesXML(){
	$usuario=comprobar_cokkies();
	if (strcmp ($usuario, "admin")) {
		cerrarSesion();
		exit;
	} 
	header('Content-Type:text/xml; charset=iso-8859-1');
	$limit=$_GET['limit'];
	$offset=$_GET['offset'];
	$valor=trim($_GET['valor']);
	if (isset($valor)&&$valor!="") $filtro="comunidades.id";
	echo "<mensajes>";
	$resultado=matrix_mensajes($limit,$offset,$filtro,$valor);
	while ($row=$resultado->fetch_row()){
		$fecha=fecha($row[4]);
		echo "<mensaje id='$row[0]'><asunto>$row[1]</asunto><destinatario>$row[2]</destinatario><comunidad>$row[3]</comunidad><fecha>$fecha</fecha><leido>$row[5]</leido><remitente>$row[6]</remitente></mensaje>";
	}
	echo "</mensajes>";
}
function listadoMensajesClientesXML(){
	$usuario=comprobar_cokkies();
	if ($usuario=='0') {
		exit;
	} 
	header('Content-Type:text/xml; charset=iso-8859-1');
	$limit=$_GET['limit'];
	$offset=$_GET['offset'];
	$idUsuario=$_SESSION['idUsuario'];
	echo "<mensajes>";
	$resultado=matrix_mensajes_clientes($limit,$offset,$idUsuario);
	while ($row=$resultado->fetch_row()){
		$fecha=fecha($row[4]);
		echo "<mensaje id='$row[0]'><asunto>$row[1]</asunto><destinatario>$row[2]</destinatario><comunidad>$row[3]</comunidad><fecha>$fecha</fecha><leido>$row[5]</leido></mensaje>";
	}
	echo "</mensajes>";
}

function listadoUsuariosXML(){
	$usuario=comprobar_cokkies();
	if (strcmp ($usuario, "admin")) {
		cerrarSesion();
		exit;
	} 
	header('Content-Type:text/xml; charset=iso-8859-1');
	echo "<usuarios>";
	$valor=$_GET['valor'];
	$filtro='comunidades.id';
	$resultado=matrix_usuarios($filtro,$valor);
	while ($row=$resultado->fetch_row()){
		echo "<usuario id='$row[6]'><user>$row[0]</user><nombre>$row[1]</nombre><comunidad id='$row[2]'>$row[5]</comunidad><piso>$row[3]</piso><claveDefecto>$row[4]</claveDefecto></usuario>";
	}
	echo "</usuarios>";
}
function loggin(){
	require_once('challengeclass.php');
	$chlgen=new ChallengeGenerator(false);
	$challenge=$chlgen->getChallengeVar('aguacada');
	$usuario=$_GET['usuario'];
	$mysql=conectar();
	$query="SELECT usuario,clave,primera,claveDefecto,id FROM usuarios WHERE usuario='$usuario'";
	$resultado=$mysql->query($query);
	$row=$resultado->fetch_row();
	if ($row[2]) $clave=$row[3];
	else {
		$clave=base64_decode($row[1]);
		$clave=desencriptar($clave);
	}
       	$clave=trim($clave);
  	if(md5($clave.$challenge)==$_GET['challenge']&&$row[0]==$usuario){
		asignar_variables($usuario);
		$valor_sesion=obtener_valor_sesion($usuario);
		$permisos=$_SESSION['Permisos'];
		$chlgen->clearVars();
		if ($permisos=='Administracion') {
			setcookie("SESION",$valor_sesion,0,'/');
			echo 1;
		}
		else {
			setcookie("SESION",$valor_sesion,time() + 300,'/');
			echo 2;
		} 
  	}
	else echo 0;
	
	// delete session data and destroy session
	
}
function logginPrimera($user,$key){
	$mysql=conectar();
	$query="SELECT usuario,clave,primera,claveDefecto,id FROM usuarios WHERE usuario='$usuario'";
	$resultado=$mysql->query($query);
	$row=$resultado->fetch_row();
	if (isset($user)){
		if (strcmp ($key, $row[1])){
			asignar_variables($user);
			$valor_sesion=obtener_valor_sesion($user);
  			setcookie("SESION",$valor_sesion,time() + 300,'/');
  			return 1;
		}
		else {
			cerrar_sesion();
			return 0;
		}
	}
}
function query(){
	$usuario=comprobar_cokkies();
	if ($usuario=='0') {
		exit;
	} 
	$decode=$_GET['decode'];
	$query=$_GET['query'];
	if (mb_check_encoding($query, 'UTF-8'))	$query=utf8_decode($query);
	$query=stripslashes($query);
	$resultado=enviar_query($query);
	if (mb_check_encoding($resultado, 'UTF-8')) $resultado=utf8_encode($resultado);
	if($decode==false) $resultado=utf8_encode($resultado);
	echo $resultado;
}
