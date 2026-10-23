<?php
class Database {
  public static $errno;
	public static $db;
	public static $con;
  public static $errorMessage;

	function __construct($host,$db,$user,$password){
		$this->user=$user;$this->pass=$password;$this->host=$host;$this->DB=$db;
    //$this->user="usuario55";$this->pass="pLy0#r55";$this->host="localhost";$this->DB="tpvWeb";
	}

	function connect(){
		$con = new mysqli($this->host,$this->user,$this->pass,$this->DB);
    if (mysqli_connect_errno()) {
      $error=$con->connect_error;
      echo "<error>$error reseteando usuario</error>";
      exit();
    }
		$con->set_charset("utf8");
		return $con;
	}

	public static function getCon($toUnReset=false){
	  self::$db=new Database('localhost:3306','aguacada','aguacadaAdmin', '9lLg3u$98');
	  //self::$db=new Database('localhost:3306','aguacada','root','Polifem@1');
	  self::$con = self::$db->connect();
	  return self::$con;
	}
  public static function ExecuteMultiQuery($query,$value=false){
		$con=self::getCon();
		$resultado=$con->multi_query($query);
		self::$errno=$con->errno;
		if ($value){
      $con->next_result();
	    $resultado=$con->store_result();
	    $row=$resultado->fetch_row();
			return $row[0];
		}
		else {
      while ($con->more_results()) {
        $con->next_result();
      }
    }
	}
	public static function ExecuteQuery($query,$value=false){
		$con=self::getCon();
		$resultado=$con->query($query);
		self::$errno=$con->errno;
    self::$errorMessage=$con->error;
    if ($value){
      if ($resultado){
        $row=$resultado->fetch_row();
  			return $row[0];
      }
      else return self::$errorMessage;

		}
		else return $resultado;
	}
  public static function backup($path=false){
    Settings::saveDB();
    $clave="Polifem@1";
    $fecha=date("d-m-Y-H-i-s");
    $archivo="backupTPV$fecha.sql";
    if (!$path){
        $base=__DIR__;
        $base=dirname($base);
        $base=dirname($base);
        $path="$base/Backups/";
        $path.=$archivo;
    }
    exec("mysqldump -uroot -p$clave --no-create-db --no-create-info tpvAbogado>$path");
    //copy($path,"/home/elabogado/Dropbox/backup/$archivo");
  }
  public static function restaurarTPV($file=false){
    if ($file){
      //limpiarTablas
      $query="SHOW TABLES";
      $tables=Database::ExecuteQuery($query);
		  while ($row = $tables->fetch_row()) {
		      $query="TRUNCATE TABLE " . $row[0];
	        Database::ExecuteQuery($query);
	    }
      //Restaurar archivo
      $templine = '';
	    $lines = file($file);
	    foreach ($lines as $line){
	       if (substr($line, 0, 2) == '--' || $line == '')	continue;
 	       $templine .= $line;
    	    if (substr(trim($line), -1, 1) == ';') {
            Database::ExecuteQuery($templine);
            $templine = '';
    	    }
      }
      //restablecer archivos
	    $query="SELECT nombreArchivo,archivo FROM archivos";
	    Databse::ExecuteQuery($query);
	    while ($row = $resultado->fetch_row()) {
          $base=__DIR__;
          $base=dirname($base);
          $base=dirname($base);
		      $nombreArchivo=$row[0];
		      $archivo = $row[1];
		      file_put_contents("$base/RESOURCES/$nombreArchivo", $archivo);
	    }
    }
    else{
      //codigo con file por defecto
    }
  }
}
?>
