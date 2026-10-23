<?php
// Usar POST en lugar de GET para mayor seguridad con datos sensibles
$destino = $_POST['destino'] ?? $_GET['destino'] ?? '';
$JS = "";
error_reporting(-1);
ini_set("display_errors", 1);

// Se asume que $pdo es tu conexión instanciada mediante PDO
// Ejemplo: $pdo = new PDO('mysql:host=localhost;dbname=tu_base', 'usuario', 'contraseña');

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
        $android = isset($_POST['android']);
        
        $data = hex2bin($data);
        $key = $_SESSION['key'];
        $iv = $_SESSION['iv'];
        
        // Sustitución de mcrypt por OpenSSL (AES-128-CBC)
        $user = openssl_decrypt($data, 'aes-128-cbc', $key, OPENSSL_RAW_DATA, $iv);
        
        if ($android) {
            $user = urlencode($user);
            $user = rtrim($user, '%07');
        } else {
            $user = rtrim($user);
        }

        // Uso de sentencias preparadas (PDO) para evitar Inyección SQL
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE usuario = ?");
        $stmt->execute([$user]);
        $num = $stmt->fetchColumn();

        if ($num == 1) {
            $stmt = $pdo->prepare("SELECT id, clave, reset, idRol FROM usuarios WHERE usuario = ?");
            $stmt->execute([$user]);
            $row = $stmt->fetch(PDO::FETCH_NUM);

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
            $token = $_SESSION['token'];

            $_SESSION['key'] = $nkey;
            $_SESSION['iv'] = $niv;

            // Cifrado con OpenSSL para el token
            $token = openssl_encrypt($token, 'aes-128-cbc', $nkey, OPENSSL_RAW_DATA, $niv);
            $token = bin2hex($token);
            $datos = $salt . $token;
            
            $dataEncrypted = openssl_encrypt($datos, 'aes-128-cbc', $key, OPENSSL_RAW_DATA, $iv);
            echo bin2hex($dataEncrypted);
        }
        break;

    case "logginEnd":
        session_start();
        $data = $_POST['data'] ?? '';
        $data = hex2bin($data);
        $key = $_SESSION['key'];
        $iv = $_SESSION['iv'];
        
        $response = openssl_decrypt($data, 'aes-128-cbc', $key, OPENSSL_RAW_DATA, $iv);
        $response = rtrim($response);
        
        if ($response == $_SESSION['token']) {
            $_SESSION['loggin'] = true;
            Session::init();
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
        
        // Uso de sentencia preparada para evitar SQLi en la actualización
        $stmt = $pdo->prepare("UPDATE usuarios SET reset = 0, clave = ? WHERE id = ?");
        $stmt->execute([$clave, $_SESSION['idUsuario']]);
        
        $JS .= "iqwerty.toast.Toast('Clave actualizada con éxito',{settings:{duration:1000}});";
        break;
}

echo $JS;
?>