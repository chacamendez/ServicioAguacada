<?php
// Usar POST en lugar de GET para mayor seguridad con datos sensibles
$destino = $_POST['destino'] ?? $_GET['destino'] ?? '';
$JS = "";
error_reporting(-1);
ini_set("display_errors", 1);

switch ($destino) {
    case "mDA":
        $mensaje = $_POST['mensaje'] ?? $_GET['mensaje'] ?? '';
        $JS = "bootbox.alert({
          buttons: {},
          title: 'Loggin',
          message: '" . addslashes($mensaje) . "',
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
        if (Session::issetUID() == "") {
            require_once('./core/ext/challengeclass.php');
        }
        break;

    case "logginProcess":
        session_start();
        
        $data = $_POST['data'] ?? '';
        
        if (empty($data)) {
            $rawInput = file_get_contents('php://input');
            $jsonInput = json_decode($rawInput, true);
            if (isset($jsonInput['data'])) {
                $data = $jsonInput['data'];
            } else {
                parse_str($rawInput, $parsedInput);
                if (isset($parsedInput['data'])) {
                    $data = $parsedInput['data'];
                } elseif (!empty($rawInput)) {
                    $data = $rawInput;
                }
            }
        }
        
        $android = isset($_POST['android']) || isset($jsonInput['android']) || isset($parsedInput['android']);
        
        error_log("[DEBUG LOGIN] data recibida: " . (!empty($data) ? "SÍ (longitud: " . strlen($data) . ")" : "VACÍA"));
        error_log("[DEBUG LOGIN] SESSION key existe: " . (isset($_SESSION['key']) ? "SÍ" : "NO"));
        error_log("[DEBUG LOGIN] SESSION iv existe: " . (isset($_SESSION['iv']) ? "SÍ" : "NO"));

        if (empty($data) || !isset($_SESSION['key']) || !isset($_SESSION['iv'])) {
            error_log("[DEBUG LOGIN ERROR] Faltan datos o variables de sesión.");
            break;
        }

        $data = hex2bin($data);
        $key = $_SESSION['key'];
        $iv = $_SESSION['iv'];
        
        $user = openssl_decrypt($data, 'aes-128-cbc', $key, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING, $iv);
        
        if ($android) {
            $user = urlencode($user);
            $user = rtrim($user, '%07');
        } else {
            // CAMBIO: Usar trim() para eliminar todos los espacios y bytes de relleno del padding de JS
            $user = trim($user);
        }

        error_log("[DEBUG LOGIN] Usuario limpio para buscar en DB: '" . $user . "'");

        $stmt = Database::ExecuteQuery("SELECT COUNT(*) FROM usuarios WHERE usuario = ?", false, [$user]);
        
        if ($stmt === false) {
            error_log("[DEBUG LOGIN ERROR] Falló la consulta SQL de COUNT. Error DB: " . Database::$errorMessage);
        }

        $num = $stmt ? $stmt->fetchColumn() : 0;
        error_log("[DEBUG LOGIN] Usuarios encontrados con ese nombre: " . $num);

        if ($num == 1) {
            $stmt = Database::ExecuteQuery("SELECT id, clave, reset, idRol FROM usuarios WHERE usuario = ?", false, [$user]);
            $row = $stmt ? $stmt->fetch(PDO::FETCH_NUM) : null;

            if (!$row) {
                error_log("[DEBUG LOGIN ERROR] El conteo dio 1 pero no se pudo recuperar la fila del usuario.");
            }

            if ($row) {
                $_SESSION['idUsuario'] = $row[0];
                $hash = $row[1];
                $reset = $row[2];
                $_SESSION['reset'] = $reset ? 1 : 0;
                $_SESSION['clave'] = $hash;
                $_SESSION['usuario'] = $user;
                $_SESSION['idRol'] = $row[3];

                $salt = substr($hash, 0, 29);
                $nkey = substr($hash, 44);
                $niv = substr($hash, 8, 16);
                
                error_log("[DEBUG LOGIN] SESSION token existe: " . (isset($_SESSION['token']) ? "SÍ" : "NO"));
                $token = $_SESSION['token'] ?? '';

                $_SESSION['key'] = $nkey;
                $_SESSION['iv'] = $niv;

                $token = openssl_encrypt($token, 'aes-128-cbc', $nkey, OPENSSL_RAW_DATA, $niv);
                $token = bin2hex($token);
                $datos = $salt . $token;
                
                $dataEncrypted = openssl_encrypt($datos, 'aes-128-cbc', $key, OPENSSL_RAW_DATA, $iv);
                
                error_log("[DEBUG LOGIN] Login exitoso, enviando respuesta cifrada.");
                echo bin2hex($dataEncrypted);
            }
        } else {
            error_log("[DEBUG LOGIN ERROR] El usuario no existe o hay más de un registro (num = " . $num . ").");
        }
        break;
        session_start();
        $data = $_POST['data'] ?? '';
        $android = isset($_POST['android']);
        
        // DEBUG 1: Comprobar si llega el dato y las variables de sesión
        error_log("[DEBUG LOGIN] data recibida: " . ($data ? "SÍ (longitud: " . strlen($data) . ")" : "VACÍA"));
        error_log("[DEBUG LOGIN] SESSION key existe: " . (isset($_SESSION['key']) ? "SÍ" : "NO"));
        error_log("[DEBUG LOGIN] SESSION iv existe: " . (isset($_SESSION['iv']) ? "SÍ" : "NO"));

        if (empty($data) || !isset($_SESSION['key']) || !isset($_SESSION['iv'])) {
            error_log("[DEBUG LOGIN ERROR] Faltan datos o variables de sesión.");
            break;
        }

        $data = hex2bin($data);
        $key = $_SESSION['key'];
        $iv = $_SESSION['iv'];
        
        // Sustitución de mcrypt por OpenSSL (AES-128-CBC)
        $user = openssl_decrypt($data, 'aes-128-cbc', $key, OPENSSL_RAW_DATA, $iv);
        
        // DEBUG 2: Ver el usuario desencriptado
        error_log("[DEBUG LOGIN] Usuario desencriptado: " . var_export($user, true));

        if ($android) {
            $user = urlencode($user);
            $user = rtrim($user, '%07');
        } else {
            $user = rtrim($user);
        }

        // Uso de la clase Database con sentencias preparadas para evitar Inyección SQL
        $stmt = Database::ExecuteQuery("SELECT COUNT(*) FROM usuarios WHERE usuario = ?", false, [$user]);
        
        // DEBUG 3: Verificar si la consulta de conteo funcionó
        if ($stmt === false) {
            error_log("[DEBUG LOGIN ERROR] Falló la consulta SQL de COUNT. Error DB: " . Database::$errorMessage);
        }

        $num = $stmt ? $stmt->fetchColumn() : 0;
        error_log("[DEBUG LOGIN] Usuarios encontrados con ese nombre: " . $num);

        if ($num == 1) {
            $stmt = Database::ExecuteQuery("SELECT id, clave, reset, idRol FROM usuarios WHERE usuario = ?", false, [$user]);
            $row = $stmt ? $stmt->fetch(PDO::FETCH_NUM) : null;

            // DEBUG 4: Verificar si se obtuvo la fila del usuario
            if (!$row) {
                error_log("[DEBUG LOGIN ERROR] El conteo dio 1 pero no se pudo recuperar la fila del usuario.");
            }

            if ($row) {
                $_SESSION['idUsuario'] = $row[0];
                $hash = $row[1];
                $reset = $row[2];
                $_SESSION['reset'] = $reset ? 1 : 0;
                $_SESSION['clave'] = $hash;
                $_SESSION['usuario'] = $user;
                $_SESSION['idRol'] = $row[3];

                $salt = substr($hash, 0, 29);
                $nkey = substr($hash, 44);
                $niv = substr($hash, 8, 16);
                
                // DEBUG 5: Comprobar si existe el token en sesión
                error_log("[DEBUG LOGIN] SESSION token existe: " . (isset($_SESSION['token']) ? "SÍ" : "NO"));
                $token = $_SESSION['token'] ?? '';

                $_SESSION['key'] = $nkey;
                $_SESSION['iv'] = $niv;

                // Cifrado con OpenSSL para el token
                $token = openssl_encrypt($token, 'aes-128-cbc', $nkey, OPENSSL_RAW_DATA, $niv);
                $token = bin2hex($token);
                $datos = $salt . $token;
                
                $dataEncrypted = openssl_encrypt($datos, 'aes-128-cbc', $key, OPENSSL_RAW_DATA, $iv);
                
                // DEBUG 6: Todo OK, imprimiendo respuesta cifrada
                error_log("[DEBUG LOGIN] Login exitoso, enviando respuesta cifrada.");
                echo bin2hex($dataEncrypted);
            }
        } else {
            error_log("[DEBUG LOGIN ERROR] El usuario no existe o hay más de un registro (num = " . $num . ").");
        }
        break;

    case "logginEnd":
        session_start();
        error_log("[DEBUG LOGINEND] Entrando en logginEnd.");
        
        $data = $_POST['data'] ?? '';
        error_log("[DEBUG LOGINEND] data recibida: " . (!empty($data) ? "SI (longitud: " . strlen($data) . ")" : "VACIA"));
        
        $data = hex2bin($data);
        $key = $_SESSION['key'];
        $iv = $_SESSION['iv'];
        
        $response = openssl_decrypt($data, 'aes-128-cbc', $key, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING, $iv);
        $response = rtrim($response);
        
        error_log("[DEBUG LOGINEND] Response desencriptado: '" . $response . "' | Token en SESSION: '" . ($_SESSION['token'] ?? 'NO SET') . "'");
        
        if ($response == $_SESSION['token']) {
            error_log("[DEBUG LOGINEND] El token coincide correctamente. Activando sesion.");
            $_SESSION['loggin'] = true;
            Session::init();
        } else {
            error_log("[DEBUG LOGINEND ERROR] El token NO coincide. No se activa la sesion.");
        }
        
        echo $_SESSION['token'];
        break;

    case "logout":
        session_start();
        Session::unsetUID();
        session_destroy();
        $JS .= "animatePanelLateralLogginOff();slide(3);$('#panelAdd').css('display','none');$('#panelAuxiliar').css('display','none');iqwerty.toast.Toast('Bye',{settings:{duration:1000}});";
        break;

    case "cambiarClave":
        session_start();
        $clave = $_POST['data'] ?? '';
        $clave = hex2bin($clave);
        $key = $_SESSION['key'];
        $iv = $_SESSION['iv'];
        
        $clave = openssl_decrypt($clave, 'aes-128-cbc', $key, OPENSSL_RAW_DATA, $iv);
        $clave = rtrim($clave);
        $clave = password_hash($clave, PASSWORD_DEFAULT);
        
        // Actualización segura utilizando Database::ExecuteQuery con parámetros
        Database::ExecuteQuery("UPDATE usuarios SET reset = 0, clave = ? WHERE id = ?", false, [$clave, $_SESSION['idUsuario']]);
        
        $JS .= "iqwerty.toast.Toast('Clave actualizada con éxito',{settings:{duration:1000}});";
        break;
}

echo $JS;
?>