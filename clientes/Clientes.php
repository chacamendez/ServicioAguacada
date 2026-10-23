<?php
	include "funciones.php";
	$usuario=comprobar_cokkies();
	if ($_SESSION['Permisos']!='Cliente') {
		cerrar_sesion();
		header("Location:../clientes.php?mensaje=No+tiene+permisos+para+acceder+a+esta+página.");
		exit;
    }
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<script language="javascript" src="../recursos/aguajs.js"></script>
	<script language="javascript" src="../recursos/aguacadaClientes.js"></script>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<meta name="Description" content="Area clientes de Aguacadagesfincas." >
	<meta name="Keywords" content="aguacadagesfincas,asesoria,gestion,comunidades,tenerife,lalaguna,administrador">
	<meta name="Author" content="Juan Alberto Méndez Alonso">
	<link rel="icon" type="image/x-icon" href="../recursos/agua.ico">
	<link rel="stylesheet" type="text/css" href="../recursos/agua.css">
	<title>Aguacadagesfincas</title>
</head>
<label id=idUsuario mysqlId=<?php echo $_SESSION['idUsuario']?>></label>
<div id=divDialogoPrimera style="display: none">
<div id=divPanelPrimera>
<label>Introduzca sus datos personales</label>
<hr>
<table style="margin-left: auto;margin-right: auto">
<tr>
<td style="text-align: right;">Nombre Completo:</td><td><input id=inputPrimeraNombreCompleto size="30"></td>
</tr>
<tr>
<td style="text-align: right;">Usuario:</td><td><input id=inputPrimeraUsuario size="30"></td>
</tr>
<tr>
<td style="text-align: right;">Clave:</td><td><input type="password" id=inputPrimeraClave size="30"></td>
</tr>
<tr>
<td style="text-align: right;">Repetir clave:</td><td><input type="password" id=inputPrimeraClave2 size="30"></td>
</tr>
</table>
<br>
<div class=boton id=botonAceptarDialogoAdvertencia align="right" onmouseover="encenderimagen(this,'url(../recursos/logginbotonove.png)')" onmouseout="apagarimagen(this,'url(../recursos/logginboton.png)')" onmousedown="encenderimagen(this,'url(../recursos/logginbotonpul.png)');comprobarDatosPrimera()">
Aceptar
</div>
<br>
<br>
</div>
</div>
<div id=divDialogoSiNo style="display: none">
<div id=divPanelDialogoSiNo>
<hr>
<label id=textoPanelDialogoSiNo></label>
<hr>
<br>

<div class=boton id=botonAceptarDialogoSiNo align="right" onmouseover="encenderimagen(this,'url(../recursos/logginbotonove.png)')" onmouseout="apagarimagen(this,'url(../recursos/logginboton.png)')" onmousedown="encenderimagen(this,'url(../recursos/logginbotonpul.png)')">
Aceptar
</div>
<div class=boton id=botonCancelarDialogoSiNo align="right" onmouseover="encenderimagen(this,'url(../recursos/logginbotonove.png)')" onmouseout="apagarimagen(this,'url(../recursos/logginboton.png)')" onmousedown="encenderimagen(this,'url(../recursos/logginbotonpul.png)')">
Cancelar
</div>
</div>
</div>

<div id=divDialogoAdvertencia style="display: none">
<div id=divPanelDialogoAdvertencia>
<hr>
<label id=textoPanelDialogoAdvertencia></label>
<hr>
<br>
<div class=boton id=botonAceptarDialogoAdvertencia align="right" onmouseover="encenderimagen(this,'url(../recursos/logginbotonove.png)')" onmouseout="apagarimagen(this,'url(../recursos/logginboton.png)')" onmousedown="encenderimagen(this,'url(../recursos/logginbotonpul.png)');ocultarElemento('divDialogoAdvertencia')">
Aceptar
</div>
</div>
</div>



<body id=cuerpo onload="alCargar()">

<div id=cabecera align="center">
<div id=titulo>
<img id=imagentitulo src="../recursos/imagentitulo4.png"></div>
<div id=barra>
<table id=tablabarra>
<tr>
	<td align="center" width="178px" id=panelprincipal onmouseover="encenderimagen(this,'url(../recursos/barraseccionseleccionada.png)')" onmouseout="apagarimagen(this,'url(../recursos/barraseccion.png)')" onclick="location.replace('../index.htm')">Inicio</td>
	<td align="center" width="178px" id=panelprincipal onmouseover="encenderimagen(this,'url(../recursos/barraseccionseleccionada.png)')" onmouseout="apagarimagen(this,'url(../recursos/barraseccion.png)')" onclick="location.replace('../contacto2.php')"> Contactar </td>
	<td align="center" width="178px" id=panelprincipal onmouseover="encenderimagen(this,'url(../recursos/barraseccionseleccionada.png)')" onmouseout="apagarimagen(this,'url(../recursos/barraseccion.png)')" onclick="location.replace('../situacion.htm')"> Situación </td>
<td align="center" width="178px" id=panelprincipalsel onmouseover="encenderimagen(this,'url(../recursos/barraseccionseleccionada.png)')" onmouseout="apagarimagen(this,'url(../recursos/barraseccion.png)','1')" onclick="location.reload()">Area clientes</td>
</tr>
</table>
</div>
</div>

<div id=opcionesControl>
<div class=opcionControl id=divOpcionDatosPersonales> 
Datos Personales
</div>
<div class=opcionControlNS id=divOpcionMensajes onmouseover="cambiarEstilo(this,'opcionControlOver','class')" onmouseout="cambiarEstilo(this,'opcionControlNS','class')" onclick="cambiarEstiloOpcionesPrincipales('opcionControlNS','class');cambiarEstilo(this,'opcionControl','class');cambiarEstilo(this,'','onmouseover');cambiarEstilo(this,'','onmouseout');ocultarTodosPrincipales();mostrarElemento('divMensajes');actualizarDatosMensajes()"> 
Mensajes
</div>
<div class=opcionControlNS id=divOpcionArchivos onmouseover="cambiarEstilo(this,'opcionControlOver','class')" onmouseout="cambiarEstilo(this,'opcionControlNS','class')" onclick="cambiarEstiloOpcionesPrincipales('opcionControlNS','class');cambiarEstilo(this,'opcionControl','class');cambiarEstilo(this,'','onmouseover');cambiarEstilo(this,'','onmouseout');ocultarTodosPrincipales();mostrarElemento('divArchivos');actualizarDatosArchivos()"> 
Archivos
</div>
<div class=opcionControlNS id=divOpcionIncidencias onmouseover="cambiarEstilo(this,'opcionControlOver','class')" onmouseout="cambiarEstilo(this,'opcionControlNS','class')" onclick="cambiarEstiloOpcionesPrincipales('opcionControlNS','class');cambiarEstilo(this,'opcionControl','class');cambiarEstilo(this,'','onmouseover');cambiarEstilo(this,'','onmouseout');ocultarTodosPrincipales();mostrarElemento('divIncidencias');actualizarDatosIncidencias()"> 
Incidencias
</div>
<label class=websymbol align="right" style="float:right;margin-right:10px" onmouseover="cambiarEstilo(this,'websymbolOver','class')" onmouseout=cambiarEstilo(this,'websymbol','class') onclick="cerrarSesion()" title="Cerrar sesión">X<label>
</div>


<div id=divPanelPrincipal>
<br>
<br>
<div id=divDatosPersonales>
<div id=subPanelDatosPersonales>
<label  style="margin-left: 40%;margin-right: auto;text-align: center;width:600px">Datos personales</label>

<hr style="width: 80%">
<table style="margin-left: auto;margin-right: auto">
<tr>
<td style="text-align: right;">Nombre Completo:</td><td><input id=inputNombreCompleto size="30"></td>
</tr>
<tr>
<td style="text-align: right;">Usuario:</td><td><input id=inputUsuario size="30"></td>
</tr>
<tr>
<td style="text-align: right;">Clave:</td><td><input type="password" id=inputClave size="30"></td>
</tr>
<tr>
<td style="text-align: right;">Repetir clave:</td><td><input type="password" id=inputClave2 size="30"></td>
</tr>
</table>
<br>
<br>
<div class=botonNormal align="right" onmouseover="encenderimagen(this,'url(../recursos/logginbotonove.png)')" onmouseout="apagarimagen(this,'url(../recursos/logginboton.png)')" onmousedown="encenderimagen(this,'url(../recursos/logginbotonpul.png)');comprobarDatosPersonales()">
Guardar
</div>
<br>
<br>
</div>
<br>
<br>
</div>

<div id=divMensajes style="display: none;">
<div id=subPanelListadoMensajes>
<div class=botonNormal align="right" onmouseover="encenderimagen(this,'url(../recursos/logginbotonove.png)')" onmouseout="apagarimagen(this,'url(../recursos/logginboton.png)')" onmousedown="encenderimagen(this,'url(../recursos/logginbotonpul.png)');mostrarElemento('subPanelCrearMensajes');ocultarElemento('subPanelListadoMensajes')">
Crear
</div>
<br>
<table width="90%" style="margin-left: auto;margin-right: auto">
<tr id=cabeceratabla>
<td>Asunto</td>
<td>Fecha</td>
<td>Leido</td>
</tr>
<tbody id=tablaMensajes></tbody>
</table>

<div class=opcionesListado id=opcionesListadoMensajes limit=0> <label class=websymbol onclick="actualizarDatosMensajes(-20)">(</label>
<label id=labelOpcionesListado style="font-size: 16px"> 0 a 20</label>   <label class=websymbol onclick="actualizarDatosMensajes(+20)">)</label> 
</div>

<br>

</div>

<div id=subPanelVerMensajes style="display: none;width: 80%;padding: 40px;margin-left: auto;margin-right: auto;">
<label id=labelContenidoVerMensajes></label>
<br>
<div class=botonNormal align="right" onmouseover="encenderimagen(this,'url(../recursos/logginbotonove.png)')" onmouseout="apagarimagen(this,'url(../recursos/logginboton.png)')" onmousedown="encenderimagen(this,'url(../recursos/logginbotonpul.png)');ocultarElemento('subPanelVerMensajes');mostrarElemento('subPanelListadoMensajes');actualizarDatosMensajes()">
Volver
</div>
<br>
</div>
<div id=subPanelCrearMensajes style="display: none;width: 80%;padding: 40px;margin-left: auto;margin-right: auto;">
<br>
Asunto:<input type="text" align="LEFT" size="60" maxlength="50" id=inputAsuntoMensaje style="padding-left: 15px">
<br>
Mensaje:<br>
<textarea type="text" align="LEFT" style="width: 663px" rows="4" id=inputContenidoMensaje></textarea>
<br>
<div class=botonNormal align="right" onmouseover="encenderimagen(this,'url(../recursos/logginbotonove.png)')" onmouseout="apagarimagen(this,'url(../recursos/logginboton.png)')" onmousedown="encenderimagen(this,'url(../recursos/logginbotonpul.png)');ocultarElemento('subPanelCrearMensajes');mostrarElemento('subPanelListadoMensajes')">
Volver
</div>
<div class=boton align="right" onmouseover="encenderimagen(this,'url(../recursos/logginbotonove.png)')" onmouseout="apagarimagen(this,'url(../recursos/logginboton.png)')" onmousedown="encenderimagen(this,'url(../recursos/logginbotonpul.png)');mostrarDialogoSiNo('Va usted a crear un mensaje, ¿Desea Continuar?','insertarMensaje()')">
Crear
</div>
</div>
<br>
<br>
</div>



<div id=divArchivos style="display: none;">
<br>
<br>
<div id=subPanelListadoArchivos>
<table width="90%" border="1" style="margin-left: auto;margin-right: auto;text-align: center;">
<tr id=cabeceratabla>
<td>Nombre</td>
<td>Destinatario</td>
<td>Fecha</td>
<td></td>
</tr>
<tbody id=tablaArchivos></tbody>
</table>
<div class=opcionesListado id=opcionesListadoArchivos limit=0> <label class=websymbol onclick="actualizarDatosArchivos(-20)">(</label>
<label id=labelOpcionesListadoArchcrearMensajeivos style="font-size: 16px"> 0 a 20</label>   <label class=websymbol onclick="actualizarDatosArchivos(+20)">)</label> 
</div>
<br>
<br>

</div>
<br>
<br>
</div>

<div id=divIncidencias style="display: none;">
<div id=subPanelListadoIncidencias>
<div class=botonNormal align="right" onmouseover="encenderimagen(this,'url(../recursos/logginbotonove.png)')" onmouseout="apagarimagen(this,'url(../recursos/logginboton.png)')" onmousedown="encenderimagen(this,'url(../recursos/logginbotonpul.png)');mostrarElemento('subPanelCrearIncidencias');ocultarElemento('subPanelListadoIncidencias')">
Crear
</div>
<br>
<table width="90%" border="1" style="margin-left: auto;margin-right: auto">
<tr id=cabeceratabla>
<td>Incidencia</td>
<td>Comunidad</td>
<td>Estado</td>
<td>Fecha</td>
</tr>
<tbody id=tablaIncidencias></tbody>
</table>

<div class=opcionesListado id=opcionesListadoIncidencias limit=0> <label class=websymbol onclick="actualizarDatosIncidencias(-20)">(</label>
<label id=labelOpcionesListadoIncidencias style="font-size: 16px"> 0 a 20</label>   <label class=websymbol onclick="actualizarDatosIncidencias(+20)">)</label> 
</div>

<br>


</div>
<div id=subPanelCrearIncidencias style="display: none;width: 80%;padding: 40px;margin-left: auto;margin-right: auto;">
Asunto: <input type="text" align="LEFT" size="60" maxlength="50" id=inputAsuntoIncidencia style="padding-left: 15px">
<br>
<br>
Contenido: <br>
<textarea type="text" align="LEFT" style="width: 665px" rows="4" id=inputContenidoIncidencia></textarea>
<br>
<div class=botonNormal align="right" onmouseover="encenderimagen(this,'url(../recursos/logginbotonove.png)')" onmouseout="apagarimagen(this,'url(../recursos/logginboton.png)')" onmousedown="encenderimagen(this,'url(../recursos/logginbotonpul.png)');ocultarElemento('subPanelCrearIncidencias');mostrarElemento('subPanelListadoIncidencias')">
Volver
</div>
<div class=boton align="right" onmouseover="encenderimagen(this,'url(../recursos/logginbotonove.png)')" onmouseout="apagarimagen(this,'url(../recursos/logginboton.png)')" onmousedown="encenderimagen(this,'url(../recursos/logginbotonpul.png)');mostrarDialogoSiNo('Va usted a crear una incidencia, ¿Desea Continuar?','insertarIncidencia()')">
Crear
</div>
<br>

</div>

<div id=subPanelVerIncidencias style="display: none;width: 80%;padding: 40px;margin-left: auto;margin-right: auto;">
<div id=divContenidoIncidencias></div>
Añadir comentario: <br>
<textarea type="text" align="LEFT" style="width: 602px" rows="4" id=inputAñadirContenidoIncidencia></textarea>
<br>
<div class=botonNormal align="right" onmouseover="encenderimagen(this,'url(../recursos/logginbotonove.png)')" onmouseout="apagarimagen(this,'url(../recursos/logginboton.png)')" onmousedown="encenderimagen(this,'url(../recursos/logginbotonpul.png)');ocultarElemento('subPanelVerIncidencias');mostrarElemento('subPanelListadoIncidencias')">
Volver
</div>
<div class=botonNormal align="right" onmouseover="encenderimagen(this,'url(../recursos/logginbotonove.png)')" onmouseout="apagarimagen(this,'url(../recursos/logginboton.png)')" onmousedown="encenderimagen(this,'url(../recursos/logginbotonpul.png)');mostrarDialogoSiNo('Va a añadir un mensaje a la incidencia, ¿Desea continuar?','añadirIncidencia()')">
Añadir
</div>
<br>
<br>
</div>
</div>

</div>



<div id=pie>
<div id=piecontenido align="center">
<div id=pietexto>AguacadaGesfincas.es</div>
</div>
</div>
</body>
</html>
