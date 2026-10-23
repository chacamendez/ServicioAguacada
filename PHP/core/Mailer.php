<?php

class Mailer{
	public $email;
	public $asunto;
	public $contenido;
	public $cabeceras;
	public $error;


	public function __construct($email='none',$asunto='none',$contenido='none',$cabeceras='none'){
		$Strings=new Strings();
		$this->error=new Errores();
		if ($Strings->validEmail($email)) $this->email=$email;
		else {$this->error->setError($Strings->error->getError());return;}
		$this->asunto=$asunto;
		$this->contenido=$contenido;
		$this->cabeceras=$cabeceras;
	}

	public function sendNewPasswordMail (){
		$query="SELECT email FROM usuarios WHERE email='".$this->email."'";
		$email=Database::ExecuteQuery($query,true);
		if (empty($email)) {$this->error->setError("Este correo no está asociado con ninguna de las cuentas en activo");return;}

		$query="SELECT estado FROM usuarios WHERE email='".$this->email."'";
		$estado=Database::ExecuteQuery($query,true);
		if ($estado=='forget') {$this->error->setError("Ya ha sido enviado un correo a esta dirección");return;}

		$query="SELECT lpad(conv(floor(rand()*pow(36,6)), 10, 36), 6, 0)";
		$hash=Database::ExecuteQuery($query,true);

	  require('./PHPMailer/class.smtp.php');
		require('./PHPMailer/class.phpmailer.php');
		$mail = new PHPMailer;
	  $mail->IsSMTP();
		$mail->SMTPAuth = true;
    $mail->Host = 'mail.restauranteelabogado.es';
    $mail->Username = 'info@restauranteelabogado.es';
    $mail->Password = "Oipy49#3";
    $mail->Port = 25;
    $mail->From = 'info@restauranteelabogado.es';
    $mail->FromName = 'Restaurante el abogado';
		$mail->AddAddress($this->email);
		$mail->IsHTML(true);
    $mail->Subject =$this->asunto;
		$contenido="Para restablecer su clave siga las instrucciones de este link <a href=\"http://restauranteelabogado.es/Clientes/index.php?resetPassword=$hash\">Restablecer clave</a>";
		$mail->Body = 	$contenido;
		$exito = $mail->Send();
		if (!$exito) $this->error->setError("Ha ocurrido un error enviando el mensaje");
		else{
			$query="UPDATE usuarios SET hash='$hash',status='forget' WHERE email='$email'";
			Database::ExecuteQuery($query);
		}
		return $hash;


	}
	public function sendEmailNewAccount (){
		$query="SELECT lpad(conv(floor(rand()*pow(36,6)), 10, 36), 6, 0)";
		$hash=Database::ExecuteQuery($query,true);
		require('./PHPMailer/class.smtp.php');
		require('./PHPMailer/class.phpmailer.php');
		$mail = new PHPMailer;
		$mail->IsSMTP();
		$mail->SMTPAuth = true;
		$mail->Host = 'mail.restauranteelabogado.es';
		$mail->Username = 'info@restauranteelabogado.es';
		$mail->Password = "Oipy49#3";
		$mail->Port = 25;
		$mail->From = 'info@restauranteelabogado.es';
		$mail->FromName = 'Restaurante el abogado';
		$mail->AddAddress($this->email);
		$mail->IsHTML(true);
		$mail->Subject =$this->asunto;
		$contenido="Para activar la cuenta visitar el siguiente link <a href=\"http://restauranteelabogado.es/Clientes/index.php?activarCuenta=$hash\">Activar cuenta</a>";
		$mail->Body = $contenido;
		$exito = $mail->Send();
		if (!$exito) $this->error->setError("Ha ocurrido un error enviando el mensaje al correo suministrado");
		return $hash;
	}

}




?>
