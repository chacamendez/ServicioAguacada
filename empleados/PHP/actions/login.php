<?php
function decryptData($data, $key, $iv) {
  $cipher = "AES-128-CBC";
  return openssl_decrypt($data, $cipher, $key, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING, $iv);
}
function encryptData($data, $key, $iv) {
  $cipher = "AES-128-CBC";
  return openssl_encrypt($data, $cipher, $key, OPENSSL_RAW_DATA, $iv);
}

$destino = $_GET['destino'] ?? $_POST['destino'] ?? '';
$JS = "";
//error_reporting(-1);ini_set("display_errors", 1);

switch ($destino){
  case "mDA":
    $mensaje = $_GET['mensaje'] ?? '';
    $JS = "bootbox.alert({
      buttons: {},
      title: 'Loggin',
      message: '$mensaje',
    });";
  break;

  case "token":
    require_once('./core/ext/challengeclass.php');
    $challenge = new ChallengeGenerator(true);
    $key = $challenge->getVar('key');
    $iv = $challenge->getVar('iv');
    echo $key . $iv;
  break;

  case "loggin":
    if(Session::issetUID() == "") require_once('./core/ext/challengeclass.php');
  break;

  case "logginProcess":
    session_start();
    
    $data = $_GET['data'] ?? '';
    if (empty($data)) {
        error_log("[DEBUG EMPLEADOS LOGIN] Error: data vacía en GET.");
        break;
    }

    $android = false;
    if (isset($_GET['android'])) $android = true;
    
    $data = base64_decode($data);
    $data = hex2bin($data);

    if (!isset($_SESSION['key']) || !isset($_SESSION['iv'])) {
        error_log("[DEBUG EMPLEADOS LOGIN] Error crítico: variables de sesión key/iv no existen.");
        break;
    }

    $key = $_SESSION['key'];
    $iv = $_SESSION['iv'];
    
    $user = decryptData($data, $key, $iv);
    $user = trim($user);

    incluir('empleados');
    
    $query = "SELECT COUNT(*) FROM empleados WHERE empleado = ?";
    $num = Database::ExecuteQuery($query, true, [$user]);
   
    if ($num == 1){
       $query = "SELECT id, clave, reset, nombre FROM empleados WHERE empleado = ?";
       $resultado = Database::ExecuteQuery($query, false, [$user]);
       
       if ($resultado) {
           $row = $resultado->fetch_row();
           
           $_SESSION['idEmpleado'] = $row[0];
           $hash = $row[1];
           $reset = $row[2];
           
           $_SESSION['reset'] = $reset ? 1 : 0;
           $_SESSION['clave'] = $hash;
           $_SESSION['empleado'] = $user;
           $_SESSION['idRol'] = 3;
           $_SESSION['nombre'] = $row[3];
           
           $salt = substr($hash, 0, 29);
           $nkey = substr($hash, 44);
           $niv = substr($hash, 8, 16);
           $token = $_SESSION['token'] ?? '';
           
           $_SESSION['key'] = $nkey;
           $_SESSION['iv'] = $niv;
           
           $token = encryptData($token, $nkey, $niv);
           $token = bin2hex($token);
           $datos = $salt . $token;
           
           $dataEncrypted = encryptData($datos, $key, $iv);
           
           error_log("[DEBUG EMPLEADOS LOGIN] logginProcess completado con éxito para '$user'.");
           echo bin2hex($dataEncrypted);
       }
    } else {
        error_log("[DEBUG EMPLEADOS LOGIN ERROR] El empleado '$user' no existe (num = $num).");
    }
  break;

  case "loginEnd":
    session_start();
    $data = $_GET['data'] ?? '';
    
    if (empty($data)) {
        error_log("[DEBUG LOGINEND ERROR] data está vacía.");
        break;
    }

    $data = base64_decode($data);
    $data = hex2bin($data);
    $key = $_SESSION['key'] ?? '';
    $iv = $_SESSION['iv'] ?? '';

    $response = decryptData($data, $key, $iv);
    // Aplicamos trim() igual que con el usuario para limpiar espacios de relleno
    $response = trim($response);

    error_log("[DEBUG LOGINEND] Response desencriptado: '$response' | Token esperado en SESSION: '" . ($_SESSION['token'] ?? '') . "'");

    if ($response == ($_SESSION['token'] ?? '')) {
        $_SESSION['loggin'] = true;
        if (method_exists('Session', 'init')) {
            Session::init();
        }
        error_log("[DEBUG LOGINEND] ¡Token correcto! Sesión marcada como logueada.");
    } else {
        error_log("[DEBUG LOGINEND ERROR] El token NO coincide.");
    }

    echo $_SESSION['token'] ?? '';
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
    $JS .= "animatePanelLateralLogginOff();slide(3);$('#panelAdd').css('display','none');$('#panelAuxiliar').css('display','none');iqwerty.toast.Toast('Bye',{settings:{duration:1000}});";
  break;

  case "cambiarClave":
    session_start();
    $clave = $_GET['data'] ?? '';
    if (empty($clave)) break;

    $clave = base64_decode($clave);
    $clave = hex2bin($clave);
    $key = $_SESSION['key'] ?? '';
    $iv = $_SESSION['iv'] ?? '';
    
    // Desencriptar la clave utilizando las variables de sesión actuales
    $clave = decryptData($clave, $key, $iv);
    // Limpiar el padding de ceros/espacios que envía JS
    $clave = trim($clave);
    
    // Generar el hash seguro de la contraseña limpia
    $claveHash = password_hash($clave, PASSWORD_DEFAULT);
    
    error_log("[DEBUG CAMBIAR CLAVE] Actualizando clave para idEmpleado: " . ($_SESSION['idEmpleado'] ?? 'NO SET'));

    // CORRECCIÓN: Usar Database::ExecuteQuery en lugar de ExecuteMultiQuery para sentencias preparadas con parámetros
    $query = "UPDATE empleados SET reset = 0, clave = ? WHERE id = ?";
    $resultado = Database::ExecuteQuery($query, false, [$claveHash, $_SESSION['idEmpleado']]);
    
    if ($resultado === false) {
        error_log("[DEBUG CAMBIAR CLAVE ERROR] Falló la actualización en base de datos. Error: " . Database::$errorMessage);
    } else {
        error_log("[DEBUG CAMBIAR CLAVE] Éxito: Clave actualizada y reset puesto a 0.");
    }
    
    $JS .= "iqwerty.toast.Toast('Clave actualizada con éxito',{settings:{duration:1000}});";
  break;
}
echo $JS;
?>