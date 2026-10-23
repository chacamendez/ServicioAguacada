<?php
class Settings{
	public $settings=false;
	public function __construct(){
		$base=__DIR__;
		$base=dirname($base);
		$base=dirname($base);
		$path="$base/RESOURCES/";
		$json = file_get_contents("$path/Settings.json");
		$this->settings=json_decode($json);
	}
	public static function save($json){
		file_put_contents("../RESOURCES/Settings.json", $json);
	}
	public static function saveDB(){
		$nombreArchivo="Settings.json";
		$base=__DIR__;
		$base=dirname($base);
		$base=dirname($base);
		$path="$base/RESOURCES/";
    	if($fp = fopen($path.$nombreArchivo, "rb")){
    	    $contenido = fread($fp, filesize($path.$nombreArchivo));
	    		$contenido = addslashes($contenido);
    	    fclose($fp);
        }
		$query="DELETE FROM archivos WHERE nombreArchivo='$nombreArchivo'";
		Database::ExecuteQuery($query);
		$query = "INSERT INTO archivos (nombreArchivo,archivo) VALUES ('$nombreArchivo','$contenido')";
		Database::ExecuteQuery($query);
	}
	public function toJSON(){
		if ($this->settings){
			return json_encode($this->settings);
		}
	}
}
?>
