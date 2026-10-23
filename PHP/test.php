<?php
$data = array('Ds_SignatureVersion' => 'HMAC_SHA256_V1','Ds_MerchantParameters' =>'eyJEc19EYXRlIjoiMjFcLzEyXC8yMDE4IiwiRHNfSG91ciI6IjEwOjQyIiwiRHNfU2VjdXJlUGF5bWVudCI6IjEiLCJEc19DYXJkX0NvdW50cnkiOiI3MjQiLCJEc19BbW91bnQiOiIzNzUiLCJEc19DdXJyZW5jeSI6Ijk3OCIsIkRzX09yZGVyIjoiMzk5MzkiLCJEc19NZXJjaGFudENvZGUiOiIzNDgzMDI5OTMiLCJEc19UZXJtaW5hbCI6IjAwMSIsIkRzX1Jlc3BvbnNlIjoiMDAwMCIsIkRzX01lcmNoYW50RGF0YSI6IiIsIkRzX1RyYW5zYWN0aW9uVHlwZSI6IjAiLCJEc19Db25zdW1lckxhbmd1YWdlIjoiMSIsIkRzX0F1dGhvcmlzYXRpb25Db2RlIjoiMjEzMjQ0IiwiRHNfQ2FyZF9CcmFuZCI6IjEifQ==','Ds_Signature'=>'3md8YvHKhdtiypH9AGCTjNDbSHH_tht0rXu9OdvnWb4=');
	$options = array(
    		'http' => array(
        	'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
        	'method'  => 'POST',
        	'content' => http_build_query($data)
    		)
		);
	$context  = stream_context_create($options);
	//$link="http://casandra/webClientes/PHP/core/ext/servicioWeb.php";
	$link="http://restauranteelabogado.es/webClientes/PHP/core/ext/Notificacion.php";
	$respuesta= file_get_contents($link,false,$context);

  echo $respuesta;


 ?>
