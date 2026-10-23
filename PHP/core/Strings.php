<?php

class Strings{
	public $cadena;
	public $error;
	public function __construct($cadena=""){
			$this->error=new Errores();
		  $this->cadena=$cadena;

	}
	public function chop($data) {
  		$data = stripslashes($data);
  		$data = htmlspecialchars($data);
			$data = trim($data);
		  return $data;
  }
	public function validEmail($email){
		if (!(filter_var($email, FILTER_VALIDATE_EMAIL)))  {$this->error->setError("Email inválido");return false;}
		else return true;
	}

	public static function SvalidEmail($email){
		if (!(filter_var($email, FILTER_VALIDATE_EMAIL)))  return false;
		else return true;
	}


	public function error($mensajeError){
		$this->error=$mensajeError;
	}
	public static function fecha($fechaSQL=""){
		$fecha = new DateTime($fechaSQL);
		return $fecha->format('d/m/Y');
	}
	public static function fechaSQL($fecha=""){
		if ($fecha=="") $fecha= date('d/m/Y');
		$fecha=explode("/",$fecha);
		$fecha="{$fecha[2]}-{$fecha[1]}-{$fecha[0]}";
		return $fecha;
	}
	public static function fechaHoraSQL($fecha="",$format='Y-m-d H:i:s'){
		$fecha=new DateTime($fecha);
		return $fecha->format($format);
	}
	public static function fechaHora($fechaSQL=""){
		$fecha = new DateTime($fechaSQL);
		return $fecha->format('Y-m-d H:i:s');
	}

}
?>
