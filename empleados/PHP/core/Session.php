<?php

Class Session{
	public static function init(){
		
	}
	public static function allowed(){
		if ($_SESSION['idRol']==1) return	true;
		else return false;
	}
	public static function allowedEmpleados(){
		if ($_SESSION['idRol']==3) return	true;
		else return false;

	}

	public static function unsetUID(){
		if(isset($_SESSION['IdUsuario']))
			unset($_SESSION['IdUsuario']);
		}
	public static function isHashValid($hash){
		$valid=false;
		$query="SELECT COUNT(*) FROM usuarios WHERE hash='$hash'";
		$num=Database::ExecuteQuery($query,true);
		if ($num==1) $valid=true;
		return $valid;
	}

	public static function issetUID(){
		if(isset($_SESSION['IdUsuario']))
			return true;
		else return false;
	}
	public static function isLogged(){
		if(isset($_SESSION['loggin'])) {
			if ($_SESSION['loggin']) return true;
			else {
				header('Location: http://www.aguacadagesfincas.es/');
				exit;
			}
		}
		else return false;
	}

	public static function getUID(){
		if(isset($_SESSION['idUsuario']))
			return $_SESSION['idUsuario'];
		else return false;
	}

	public static function setHash($hash){
		$_SESSION['hash']=$hash;
	}
	public static function getHash(){
		if (isset($_SESSION['hash'])) return $_SESSION['hash'];
		else return false;
	}
	public static function unsetHash(){
		if (isset($_SESSION['hash'])) unset($_SESSION['hash']);
	}
	public static function fechaHoraActual(){
		$tz = 'Europe/London';
		$timestamp = time();
		$dt = new DateTime("now", new DateTimeZone($tz));
		$dt->setTimestamp($timestamp);
		return $dt->format('Y-m-d H:i:s');
	}
	public static function fechaEstimada($minutos){
		$tz = 'Europe/London';
		$timestamp = time();
		$dt = new DateTime("now", new DateTimeZone($tz));
		$dt->setTimestamp($timestamp);
		$dt->add(new DateInterval('PT' . $minutos . 'M'));
		return $dt->format('Y-m-d H:i:s');
	}

}

?>
