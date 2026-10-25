<?php
class Errores{
	public $success;
	public $error;
  public $errno;

	public function __construct(){
		$this->success=true;
		$this->error="";
		$this->errno=0;
	}

	public function setError($mensajeError,$errno=0){
		$this->success=false;
		if ($this->error==""){
			$this->errno=$errno;
			$this->error=$mensajeError;
		}

	}
	public function getError(){
		return $this->error;
	}

}
?>
