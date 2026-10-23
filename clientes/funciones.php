<?php
function asignar_variables($usuario) {
	session_start();
	$mysql=conectar();
	$query="SELECT nombre,usuario,permisos,comunidad,piso,id FROM usuarios".
	" WHERE usuario='$usuario'";
	$resultado=$mysql->query($query);
	$row = $resultado->fetch_row();
	$_SESSION['Nombre']=$row[0];
	$_SESSION['Usuario']=$row[1];
	$_SESSION['Permisos']=$row[2];
	$_SESSION['Comunidad']=$row[3];
	$_SESSION['Piso']=$row[4];
	$_SESSION['idUsuario']=$row[5];
	$mysql->close;
}
function cerrar_sesion() {
	setcookie("SESION",$_COOKIE['SESION'],time() - 300,'/'); 
	setcookie("PHPSESSID",$_COOKIE['PHPSESSID'],time() - 300,'/');
}
function comprobar_cokkies() {
	$usuario=$_COOKIE['SESION'];
	$sesion=$_COOKIE['PHPSESSID'];
	if (!(isset($sesion))||!(isset($usuario))) {
		cerrar_sesion(); 
		return 0;
	}
	$usuario=desencriptar($usuario,'sesion',1,substr($sesion,-8));
	$usuario=trim($usuario);
	if (!comprueba_usuario($usuario)) {
		cerrar_sesion();
		return 0;
	}
	asignar_variables($usuario);
	if (!isset($_SESSION['idUsuario'])){
		cerrar_sesion();
		return 0;
	}
	if ($_SESSION['idUsuario']=='1'){
		setcookie("SESION",$_COOKIE['SESION'],0,'/'); 
		setcookie("PHPSESSID",$_COOKIE['PHPSESSID'],0,'/');
	}
	else {
		setcookie("SESION",$_COOKIE['SESION'],time() + 300,'/'); 
		setcookie("PHPSESSID",$_COOKIE['PHPSESSID'],time() + 300,'/');
	}
	return $usuario;
}
function comprobar_credenciales ($usuario,$clave) {
	$root=$usuario;
	$usuario = encriptar($usuario);
	$clave = encriptar($clave);
	$mysql=conectar();
	$query="SELECT usuario,clave FROM usuarios";
	$resultado=$mysql->query($query);
	while ($row = $resultado->fetch_row()) {
		if ($row[0]==$usuario and $row[1]==$clave){
		$resultado->close();
		$mysql->close();
		return true;
		}
	}
		$mysql->close();
		return false;
}
function comprobar_primera ($usuario) {
	$mysql=conectar();
	$query="SELECT primera FROM usuarios WHERE usuario='$usuario'";
	$resultado=$mysql->query($query);
	$row=$resultado->fetch_row();
	$mysql->close;
	if ($row[0]) return true;
	return false;
}
function comprueba_usuario($usuario) {
	$mysql=conectar();
	$query="SELECT usuario FROM usuarios WHERE usuario='$usuario'";
	$resultado=$mysql->query($query);
	$mysql->close();
	$numRows=$resultado->num_rows;
	if ($numRows==1) return true;
	else return false;
}
function conectar() {
	//$mysql=mysqli_connect('localhost', 'root', 'Polifem@1', 'aguacada');
	$mysql=mysqli_connect('localhost', 'aguacadaAdmin', 'Persef@ne2', 'aguacada');
	if (mysqli_connect_errno()) {
    	printf("Fallo en la conexion: %s\n", mysqli_connect_error());
    	exit();
	}
	return $mysql;
}
function crear_archivo($archivo,$comunidad,$usuario,$ext){
	$idhash=RandomString(10,true,true,false);
	$mysql=conectar();
    $fecha=date("Y-m-d");
	$query="INSERT INTO archivos (idhash,archivo,comunidad,usuario,fecha,extension) VALUES ('$idhash','$archivo',$comunidad,$usuario,'$fecha','$ext')";
	$mysql->query($query);
	$mysql->close();
	return $idhash;
}
function crear_comunidad($comunidad) {
	$mysql=conectar();
	$idhash=RandomString(10,true,true,false);
	$comunidad=encriptar($comunidad);
	$comunidad=$mysql->real_escape_string($comunidad);
	$query="INSERT INTO comunidades (comunidad,idhash) VALUES ('$comunidad','$idhash')";
	$resultado=$mysql->query($query);
	$mysql->close;
	if ($resultado) return true;
	return false;
}
function crear_incidencia($resumen,$detalle,$comunidad){
	$idhash=RandomString(10,true,true,false);
	date_default_timezone_set('UTC');
	$fecha=date("Y-m-d");
	$mysql=conectar();
	$resumen=$mysql->real_escape_string(encriptar($resumen));
	$detalle=$mysql->real_escape_string(encriptar($detalle));
	$comunidad=$mysql->real_escape_string(devolver_idhash('comunidades',encriptar($comunidad)));
	$estado=$mysql->real_escape_string(encriptar('Iniciado'));
	$usuario=$mysql->real_escape_string(devolver_idhash('usuarios',encriptar($_SESSION['Usuario'])));
	$query="INSERT INTO incidencias (idhash,resumen,detalle,comunidad,usuario,fecha,estado) VALUES ('$idhash','$resumen','$detalle','$comunidad','$usuario','$fecha','$estado')";
	$mysql->query($query);
	$mysql->close();
	header('Location:Administracion_incidencias.php');
}
function crear_mensaje($destino,$destinatario,$asunto,$contenido,$remitente){
	$mysql=conectar();
	$fecha=date('Y-m-d');
	if (isset($remitente)){
		$query="INSERT INTO mensajes (asunto,contenido,fecha,usuario,alerta,remitente) VALUES ('$asunto','$contenido','$fecha',1,1,$remitente)";
		$mysql->query($query);
		return;
	}
	if ($destino==0) {
		$usuarios=matrix_usuarios();
		while ($row=$usuarios->fetch_row()){
			$query="INSERT INTO mensajes (asunto,contenido,fecha,usuario,remitente) VALUES ('$asunto','$contenido','$fecha',$row[6],1)";
			$mysql->query($query);
		}
	}
	else {
		if	($destinatario==0){
			$usuarios=matrix_usuarios('comunidades.id',$destino);
			while ($row=$usuarios->fetch_row()){
				$query="INSERT INTO mensajes (asunto,contenido,fecha,usuario,remitente) VALUES ('$asunto','$contenido','$fecha',$row[6],1)";
				$mysql->query($query);
			}
		}
		else {
			$query="INSERT INTO mensajes (asunto,contenido,fecha,usuario,remitente) VALUES ('$asunto','$contenido','$fecha',$destinatario,1)";
			$mysql->query($query);	
		}
	}

}
function crear_usuarios_comunidad($numero,$comunidad) {
	$mysql=conectar();
	for ($i=0;$i<$numero;$i++) {
		$idhash=RandomString(10,true,true,false);
		$usuario=RandomString(6,true,false,FALSE);
		$clave=RandomString(6,true,true,FALSE);
		$query="INSERT INTO usuarios (usuario,claveDefecto,permisos,primera,comunidad,idhash,claveIV,sesionIV,usuarioDefecto) ".
		"VALUES ('$usuario','$clave','Cliente',1,$comunidad,'$idhash','','','$usuario')";
		$resultado=$mysql->query($query);
	}
	$mysql->close;
}
function descargar_archivo($idhash){
	$mysql=conectar();
	$query="SELECT archivo,extension FROM archivos WHERE idhash='$idhash'";
	$resultado=$mysql->query($query);
	$row=$resultado->fetch_row();
	$archivo=desencriptar($row[0]);
	$extension=desencriptar($row[1]);
	header("Pragma: public");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Cache-Control: private",false);
   	header('Content-Disposition: attachment; filename="' . $archivo . '.' . $extension . '"');
   	header("Content-Transfer-Encoding: binary"); 
	@readfile('./archivos/' . $idhash);
	exit;
}
function desencriptar($valor,$destino,$idUsuario,$key='UnaBuena') {
	$td = mcrypt_module_open('rijndael-256', '', 'cbc', '');
	$iv='ó*s÷õUÎÇ{k*Ê¡×RÞ7jcK¾<Çª!ú';
   
        if (mcrypt_generic_init($td, $key, $iv) != -1) {
		$valor = mdecrypt_generic($td, $valor);
       /* Clean up */
        	mcrypt_generic_deinit($td);
        	mcrypt_module_close($td);
	return $valor;
    }
}
function devolver_datos($usuario){
	$mysql=conectar();
	$query="SELECT nombre,clave,id,piso FROM usuarios WHERE usuario='$usuario'";
	$resultado=$mysql->query($query);
	$mysql->close;
	$row=$resultado->fetch_row();
	return $row;
}

function enviar_query($query) {
	$mysql=conectar();
	$resultado=$mysql->query($query);
	$row = $resultado->fetch_row();
	$mysql->close;
	return $row[0];
}
function encriptar($valor,$destino,$idUsuario,$key='UnaBuena') {
    $td =mcrypt_module_open('rijndael-256', '', 'cbc', '');
    $key = substr($key, 0, mcrypt_enc_get_key_size($td));
    $iv_size = mcrypt_enc_get_iv_size($td);
    $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
    $iv='ó*s÷õUÎÇ{k*Ê¡×RÞ7jcK¾<Çª!ú';
    if (mcrypt_generic_init($td, $key, $iv) != -1) {
        $textoEncriptado = mcrypt_generic($td, $valor);
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        return $textoEncriptado;
    }
}
function fecha($cadena){
	$fecha = new DateTime($cadena);
	return $fecha->format('d/m/Y');
}
function fechaHora($cadena){
	$fecha = new DateTime($cadena);
	return $fecha->format('d-m-Y H:i:s');
}
function  guardar_IV($iv,$destino,$idUsuario){
	$mysql=conectar();
	//$iv=base64_encode($iv);
	$query="UPDATE usuarios SET $destino"."IV='$iv' WHERE id=$idUsuario";
	$resultado=$mysql->query($query);
	return $resultado;
}
function leer_incidencia($idhash){
	$mysql=conectar();
	$query="SELECT id,usuario,fecha,detalle,resumen,comunidad,estado FROM incidencias WHERE idhash='$idhash' ORDER BY fecha";
	$resultado=$mysql->query($query);
	$mysql->close();
	return $resultado;
}
function obtener_valor_sesion ($usuario) {
	$key = substr(session_id(), -8);
	$valor=encriptar($usuario,'sesion',1,$key);
	return $valor;
}
function matrix_alertas(){
	$mysql=conectar();
	$arrayResultado;
	$i=0;
	$query="SELECT mensajes.id,usuarios.usuario,asunto,contenido FROM mensajes,usuarios WHERE alerta=1 AND mensajes.remitente=usuarios.id ORDER BY fecha DESC";
	$resultado=$mysql->query($query);
	while ($row=$resultado->fetch_row()){
		$arrayResultado[$i][0]="mensaje";
		$arrayResultado[$i][1]=$row[0];
		$arrayResultado[$i][2]=$row[1];
		$arrayResultado[$i][3]=$row[2];
		$arrayResultado[$i][4]=$row[3];
		$i++;
	}
	$query="SELECT incidencias.id,usuarios.usuario,resumen,detalle FROM incidencias,usuarios WHERE alerta=1 AND incidencias.usuario=usuarios.id ORDER BY fecha DESC";
	$resultado=$mysql->query($query);
	while ($row=$resultado->fetch_row()){
		$arrayResultado[$i][0]="incidencia";
		$arrayResultado[$i][1]=$row[0];
		$arrayResultado[$i][2]=$row[1];
		$arrayResultado[$i][3]=$row[2];
		$arrayResultado[$i][4]=$row[3];
		$i++;
	}
	return $arrayResultado;

}
function matrix_archivos($limit,$offset,$filtro,$valor) {
	$mysql=conectar();
	if (isset($valor) && $valor !='') {
		$query="SELECT id,archivo,usuario,comunidad,fecha FROM archivos WHERE ($filtro=$valor OR comunidad=1) ORDER BY fecha LIMIT $limit,$offset";
	}
	else $query="SELECT id,archivo,usuario,comunidad,fecha FROM archivos ORDER BY fecha LIMIT $limit,$offset";
	$resultado=$mysql->query($query);
	$mysql->close;
	return $resultado;
	
}
function matrix_comunidades() {
	$mysql=conectar();
	$query="SELECT comunidad,id FROM comunidades";
	$resultado=$mysql->query($query);
	$mysql->close;
	return $resultado;
}
function matrix_contenido_incidencia($idIncidencia){
	$mysql=conectar();
	$query="SELECT incidencias.id,detalle,estado,fecha,resumen,nombre FROM incidencias,usuarios WHERE ".
	" (idIncidencia=$idIncidencia OR incidencias.id=$idIncidencia) AND incidencias.usuario=usuarios.id ORDER BY fecha DESC";
	$resultado=$mysql->query($query);
	$mysql->close;
	return $resultado;
}
function matrix_incidencias($limit,$offset,$filtro,$valor){
	$mysql=conectar();
	if (isset($valor) and $valor != '') {
		$query="SELECT incidencias.id,resumen,fecha,estado,comunidades.comunidad,idIncidencia FROM incidencias,comunidades WHERE" .
	"  incidencias.comunidad=comunidades.id AND $filtro=$valor ORDER BY fecha DESC LIMIT $limit,$offset";
	}
	else $query="SELECT incidencias.id,resumen,fecha,estado,comunidades.comunidad,idIncidencia FROM incidencias,comunidades WHERE" .
	"  incidencias.comunidad=comunidades.id ORDER BY fecha DESC LIMIT $limit,$offset";
	$resultado=$mysql->query($query);
	$mysql->close;
	return $resultado;
}

function matrix_mensajes($limit,$offset,$filtro,$valor) {
	$mysql=conectar();
	if (isset($filtro)){
	 $query="SELECT mensajes.id,asunto,usuarios.nombre,comunidades.comunidad,fecha,leido,remitente FROM mensajes,usuarios,comunidades".
	" WHERE usuarios.comunidad=comunidades.id AND mensajes.usuario=usuarios.id AND $filtro=$valor ORDER BY fecha DESC LIMIT $limit,$offset";
	}
	else {
	$query="SELECT mensajes.id,asunto,usuarios.nombre,comunidades.comunidad,fecha,leido,remitente FROM mensajes,usuarios,comunidades".
	" WHERE usuarios.comunidad=comunidades.id AND mensajes.usuario=usuarios.id ORDER BY fecha DESC LIMIT $limit,$offset";
	}
	$resultado=$mysql->query($query);
	$mysql->close;
	return $resultado;
}
function matrix_mensajes_clientes($limit,$offset,$idUsuario){
	$mysql=conectar();
	$query="SELECT mensajes.id,asunto,usuarios.nombre,comunidades.comunidad,fecha,leido FROM mensajes,usuarios,comunidades".
		" WHERE usuarios.comunidad=comunidades.id AND mensajes.usuario=usuarios.id AND usuarios.id=$idUsuario ORDER BY fecha DESC LIMIT $limit,$offset";
	$resultado=$mysql->query($query);
	$mysql->close;
	return $resultado;
}
function matrix_usuarios($filtro,$valor) {
	$mysql=conectar();
	if (isset($valor) && $valor!='')
	$query="SELECT usuarioDefecto,nombre,usuarios.comunidad,piso,claveDefecto,comunidades.comunidad,usuarios.id FROM usuarios,comunidades".
	" WHERE usuarios.comunidad=comunidades.id AND $filtro=$valor";
	else 
		$query="SELECT usuarioDefecto,nombre,usuarios.comunidad,piso,claveDefecto,comunidades.comunidad,usuarios.id FROM usuarios,comunidades".
	" WHERE usuarios.comunidad=comunidades.id";
	$resultado=$mysql->query($query);
	$mysql->close;
	return $resultado;
}
function recuperar_IV($destino,$idUsuario){
	$mysql=conectar();
	$query="SELECT $destino"."IV FROM usuarios WHERE id=$idUsuario";
	$resultado=$mysql->query($query);
	$row=$resultado->fetch_row();
	$mysql->close;
	return $row[0];
}
function RandomString($length=6,$uc=TRUE,$n=TRUE,$sc=FALSE){
    $source = 'abcdefghijklmnopqrstuvwxyz';
	if($uc==1) $source .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    if($n==1) $source .= '1234567890';
    if($sc==1) $source .= '|@#~$%()=^*+[]{}-_';
    if($length>0){
          $rstr = "";
          $source = str_split($source,1);
          for($i=1; $i<=$length; $i++){
	      	mt_srand((double)microtime() * 1000000);
          	$num = mt_rand(1,count($source));
          	$rstr .= $source[$num-1];
          }

    }
   return $rstr;
}
?>
