<?php
error_reporting(-1);
ini_set("display_errors", 1);

$nombre = isset($_GET['contactoNombre']) ? trim($_GET['contactoNombre']) : '';
$email = isset($_GET['contactoCorreo']) ? trim($_GET['contactoCorreo']) : '';
$consulta = isset($_GET['contactoConsulta']) ? trim($_GET['contactoConsulta']) : '';
$JS = "";

if ($nombre === '' || $email === '' || $consulta === '') {
    $JS .= "iqwerty.toast.Toast('Por favor, completa todos los campos.',{settings:{duration:2000}});";
    echo $JS;
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $JS .= "iqwerty.toast.Toast('Dirección de correo electrónico no válida.',{settings:{duration:2000}});";
    echo $JS;
    exit;
}

require("PHPMailer/class.phpmailer.php");
require("PHPMailer/class.smtp.php");

$mail = new PHPMailer();

$mail->IsSMTP();
$mail->SMTPAuth = true;
$mail->Host = "mail.aguacadagesfincas.es";
$mail->Username = "info@aguacadagesfincas.es";
$mail->Password = "Persefon@"; 
$mail->Port = 25;
$mail->SMTPOptions = array(
    'ssl' => array(
        'verify_peer' => false,
        'verify_peer_name' => false,
        'allow_self_signed' => true
    )
);

$mail->From = "info@aguacadagesfincas.es";
$mail->FromName = "Web Aguacada Gesfincas";

$mail->AddAddress("info@aguacadagesfincas.es");
$mail->addReplyTo($email, $nombre);

$mail->IsHTML(true);
$mail->CharSet = 'UTF-8';
$mail->Subject = "Nueva consulta desde la web de Aguacada Gesfincas";

$body = "Has recibido una nueva consulta desde el formulario web:<br><br>";
$body .= "Nombre: " . htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8') . "<br>";
$body .= "Correo del cliente: " . htmlspecialchars($email, ENT_QUOTES, 'UTF-8') . "<br><br>";
$body .= "Consulta:<br>" . nl2br(htmlspecialchars($consulta, ENT_QUOTES, 'UTF-8')) . "";

$mail->Body = $body;
$exito = $mail->Send();

if ($exito) {
    $JS .= "iqwerty.toast.Toast('Se ha enviado su consulta, le responderemos a la mayor brevedad posible.',{settings:{duration:2000}});";
} else {
    $errorDetalle = addslashes($mail->ErrorInfo);
    $JS .= "iqwerty.toast.Toast('Error al enviar el mensaje. Inténtelo más tarde.',{settings:{duration:4000}});";
}

echo $JS;
?>