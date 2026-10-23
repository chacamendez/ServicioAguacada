<?php
class Action {
	public static function exec($action,$params=false){

		if(isset($_GET['layout'])){ $action=$_GET['action'];}
		if(self::isValid($action)){
				include self::getBase()."/actions/$action.php";
		}else{
				self::Error("<b>404 NOT FOUND</b> Action <b>$action</b> file  !!");
		}
	}

	public static function isValid($action){
		$valid=false;
		if(file_exists($file = self::getBase()."/actions/$action.php")) $valid = true;
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
