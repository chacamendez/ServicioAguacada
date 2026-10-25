<?php

class Layout {
	public static function load($layout){

		if(isset($_GET['layout']) && !(isset($layout))){ $layout=$_GET['layout'];}
		if(self::isValid($layout)){
				include self::getBase()."/layouts/$layout.php";
		}else{
				self::Error("<b>404 NOT FOUND</b> Layout <b>$layout</b> folder  !!");
		}
	}

	public static function isValid($layout){
		$valid=false;
		if(file_exists($file = self::getBase()."/layouts/$layout.php")) $valid = true;
		return $valid;
	}

	public static function Error($message){
		print $message;
	}

	public static function getBase(){
		$base=__DIR__;
		$base=pathinfo($base);
		$base=$base['dirname'];
		return $base;

	}

}



?>
