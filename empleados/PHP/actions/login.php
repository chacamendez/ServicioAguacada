<?php
function decryptData($data, $key, $iv) {
  $cipher = "AES-128-CBC"; // Equivalente a MCRYPT_RIJNDAEL_128 en modo CBC
  return openssl_decrypt($data, $cipher, $key, OPENSSL_RAW_DATA, $iv);
}
function encryptData($data, $key, $iv) {
  $cipher = "AES-128-CBC";
  return openssl_encrypt($data, $cipher, $key, OPENSSL_RAW_DATA, $iv);
}

$destino=$_GET['destino'];
$JS="";
//error_reporting(-1);ini_set("display_errors", 1);

switch ($destino){
  case "mDA":
    $mensaje=$_GET['mensaje'];
    $JS="bootbox.alert({
      buttons: {},
      title: 'Loggin',
      message: '$mensaje',
    });";
  break;
  case "token":
    require_once('./core/ext/challengeclass.php');
    $challenge=new ChallengeGenerator(true);
    $key= $challenge->getVar('key');
    $iv=$challenge->getVar('iv');
    echo $key.$iv;
  break;
  case "loggin":
    if(Session::issetUID()=="") require_once('./core/ext/challengeclass.php');
  break;
  case "logginProcess":
  session_start();
  $data=$_GET['data'];
  $android=false;
  if (isset($_GET['android'])) $android=true;
  $data=base64_decode($data);
  $data=hex2bin ($data);
  $key=$_SESSION['key'];
  $iv=$_SESSION['iv'];
  $user=mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key ,$data , MCRYPT_MODE_CBC,$iv);

  if ($android){
   $user=urlencode($user);
   $user=rtrim($user,'%07');
  }
  else $user=rtrim($user);
  incluir('empleados');
  $query="SELECT COUNT(*) FROM empleados WHERE empleado='$user'";
  $num=Database::ExecuteQuery($query,true);
 
  if ($num==1){
    $query="SELECT id,clave,reset,nombre FROM empleados WHERE empleado='$user'";
    $resultado=Database::ExecuteQuery($query);
    $row=$resultado->fetch_row();
    $_SESSION['idEmpleado']=$row[0];
    $hash=$row[1];
    $reset=$row[2];
    if ($reset) $_SESSION['reset']=1;
    else $_SESSION['reset']=0;
    $_SESSION['clave']=$hash;
    $_SESSION['empleado']=$user;
    $_SESSION['idRol']=3;
    $_SESSION['nombre']=$row[3];
    $salt=substr($hash,0,29);
    $nkey=substr($hash,44);
    $niv=substr($hash,8,16);
    $token=$_SESSION['token'];
    $_SESSION['key']=$nkey;
    $_SESSION['iv']=$niv;
    $token=mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $nkey ,$token , MCRYPT_MODE_CBC,$niv);
    $token=bin2hex($token);
    $datos=$salt.$token;
    $data=mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key ,$datos , MCRYPT_MODE_CBC,$iv);
    echo bin2hex($data);
    
  }

  break;
  case "loginEnd":
  session_start();
    	$data=$_GET['data'];
      $data=base64_decode($data);
    	$data=hex2bin ($data);
    	$key=$_SESSION['key'];
    	$iv=$_SESSION['iv'];
    	$response=mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key ,$data , MCRYPT_MODE_CBC,$iv);
    	$response=rtrim($response);
    	if ($response==$_SESSION['token']){
        	$_SESSION['loggin']=true;
          Session::init();
      }
	    echo $_SESSION['token'];
  break;
  case "sessionStart":
    session_start();
    if (Session::allowedEmpleados()){
      echo "{\"idEmpleado\":\"{$_SESSION['idEmpleado']}\",\"reset\":\"{$_SESSION['reset']}\"}";
    }
    
    break;
  case "logout":
    session_start();
    Session::unsetUID();
    session_destroy();
    $JS.="animatePanelLateralLogginOff();slide(3);$('#panelAdd').css('display','none');$('#panelAuxiliar').css('display','none');iqwerty.toast.Toast('Bye',{settings:{duration:1000}});";
  break;
  case "cambiarClave":
    session_start();
    $clave=$_GET['data'];
    $clave=base64_decode($clave);
    $clave=hex2bin ($clave);
    $key=$_SESSION['key'];
    $iv=$_SESSION['iv'];
    $clave=mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key ,$clave , MCRYPT_MODE_CBC,$iv);
    $clave=rtrim($clave);
    $clave=password_hash($clave,PASSWORD_DEFAULT);
    $query="UPDATE empleados SET reset=0,clave='$clave' WHERE id={$_SESSION['idEmpleado']};";
    Database::ExecuteMultiQuery($query);
    $JS.="iqwerty.toast.Toast('Clave actualizada con éxito',{settings:{duration:1000}});";
  break;
}
echo $JS;

?>
