<?php
	include "funciones.php";
	$usuario=comprobar_cokkies();
	if ($_SESSION['Permisos']!='Administracion') {
		cerrar_sesion();
		header("Location:../clientes.php?mensaje=No+tiene+permisos+para+acceder+a+esta+pagina.");
		exit;
    }
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<script language="javascript" src="../recursos/aguajs.js"> </script>
	<script language="javascript" src="../recursos/aguacada.js"> </script>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	<meta name="Description" content="Area clientes de Aguacadagesfincas." >
	<meta name="Keywords" content="aguacadagesfincas,asesoria,gestion,comunidades,tenerife,lalaguna,administrador">
	<meta name="Author" content="Juan Alberto Méndez Alonso">
	<link rel="icon" type="image/x-icon" href="../recursos/agua.ico">
	<link rel="stylesheet" type="text/css" href="../recursos/agua.css">
	<title>Aguacadagesfincas</title>
</head>
<iframe name="uploadIFrame" style="display: none" ></iframe>
<div id=divDialogoSiNo style="display: none">
<div id=divPanelDialogoSiNo>
<hr>
<label id=textoPanelDialogoSiNo></label>
<hr>
<br>

<div class=boton id=botonAceptarDialogoSiNo align="right" onmouseover="encenderimagen(this,'url(../recursos/logginbotonove.png)')" onmouseout="apagarimagen(this,'url(../recursos/logginboton.png)')"  onmousedown="encenderimagen(this,'url(../recursos/logginbotonpul.png)')">
Aceptar
</div>
<div class=boton id=botonCancelarDialogoSiNo align="right" onmouseover="encenderimagen(this,'url(../recursos/logginbotonove.png)')" onmouseout="apagarimagen(this,'url(../recursos/logginboton.png)')"  onmousedown="encenderimagen(this,'url(../recursos/logginbotonpul.png)')">
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
<div class=boton id=botonAceptarDialogoAdvertencia align="right" onmouseover="encenderimagen(this,'url(../recursos/logginbotonove.png)')" onmouseout="apagarimagen(this,'url(../recursos/logginboton.png)')" onmousedown="encenderimagen(this,'url(../recursos/logginbotonpul.png)')">
Aceptar
</div>
</div>
</div>

<body id=cuerpo onload="alCargar()">
<audio id=alertSound src ="alerta.mp3" hidden="true" autostart="false"></audio>
<div id=cabecera align="center">
<div id=titulo>
<img id=imagentitulo src="../recursos/imagentitulo4.png"></div>
<div id=barra>
<table id=tablabarra>
<tr>
	<td align="center" width="178px" id=panelprincipal onmouseover="encenderimagen(this,'url(../recursos/barraseccionseleccionada.png)')" onmouseout="apagarimagen(this,'url(../recursos/barraseccion.png)')" onclick="location.replace('../index.htm')">Inicio</td>
	<td align="center" width="178px" id=panelprincipal onmouseover="encenderimagen(this,'url(../recursos/barraseccionseleccionada.png)')" onmouseout="apagarimagen(this,'url(../recursos/barraseccion.png)')" onclick="location.replace('../contacto2.php')"> Contactar </td>
<td align="center" width="178px" id=panelprincipalsel onmouseover="encenderimagen(this,'url(../recursos/barraseccionseleccionada.png)')" onmouseout="apagarimagen(this,'url(../recursos/barraseccion.png)','1')" onclick="location.reload()">Area clientes</td>
</tr>
</table>
</div>
</div>

<div id=opcionesControl>
<div class=opcionControl id=divOpcionUsuarios> 
Usuarios
</div>
<div class=opcionControlNS id=divOpcionComunidades onmouseover="cambiarEstilo(this,'opcionControlOver','class')" onmouseout="cambiarEstilo(this,'opcionControlNS','class')"  onclick="cambiarEstiloOpcionesPrincipales('opcionControlNS','class');cambiarEstilo(this,'opcionControl','class');cambiarEstilo(this,'','onmouseover');cambiarEstilo(this,'','onmouseout');ocultarTodosPrincipales();mostrarElemento('divComunidades');actualizarPanelComunidades();"> 
Comunidades
</div>
<div class=opcionControlNS id=divOpcionMensajes onmouseover="cambiarEstilo(this,'opcionControlOver','class')" onmouseout="cambiarEstilo(this,'opcionControlNS','class')" onclick="cambiarEstiloOpcionesPrincipales('opcionControlNS','class');cambiarEstilo(this,'opcionControl','class');cambiarEstilo(this,'','onmouseover');cambiarEstilo(this,'','onmouseout');ocultarTodosPrincipales();mostrarElemento('divMensajes');actualizarPanelMensajes()"> 
Mensajes
</div>
<div class=opcionControlNS id=divOpcionArchivos onmouseover="cambiarEstilo(this,'opcionControlOver','class')" onmouseout="cambiarEstilo(this,'opcionControlNS','class')" onclick="cambiarEstiloOpcionesPrincipales('opcionControlNS','class');cambiarEstilo(this,'opcionControl','class');cambiarEstilo(this,'','onmouseover');cambiarEstilo(this,'','onmouseout');ocultarTodosPrincipales();mostrarElemento('divArchivos');actualizarPanelArchivos()"> 
Archivos
</div>
<div class=opcionControlNS id=divOpcionIncidencias onmouseover="cambiarEstilo(this,'opcionControlOver','class')" onmouseout="cambiarEstilo(this,'opcionControlNS','class')" onclick="cambiarEstiloOpcionesPrincipales('opcionControlNS','class');cambiarEstilo(this,'opcionControl','class');cambiarEstilo(this,'','onmouseover');cambiarEstilo(this,'','onmouseout');ocultarTodosPrincipales();mostrarElemento('divIncidencias');actualizarPanelIncidencias()"> 
Incidencias
</div>
<div class=opcionControlNS id=divOpcionAlertas onmouseover="cambiarEstilo(this,'opcionControlOver','class')" onmouseout="cambiarEstilo(this,'opcionControlNS','class')" onclick="cambiarEstiloOpcionesPrincipales('opcionControlNS','class');cambiarEstilo(this,'opcionControl','class');cambiarEstilo(this,'','onmouseover');cambiarEstilo(this,'','onmouseout');ocultarTodosPrincipales();mostrarElemento('divAlertas')"> 
Alertas
</div>
<label class=websymbol align="right" style="float:right;margin-right:10px" onmouseover="cambiarEstilo(this,'websymbolOver','class')" onmouseout=cambiarEstilo(this,'websymbol','class') onclick="cerrarSesion()" title="Cerrar sesión">X<label>
</div>


<div id=divPanelPrincipal>

<div id=divUsuarios>
<div id=subPanelListadoUsuario>
<br>
<div class=botonNormal align="right" onmouseover="encenderimagen(this,'url(../recursos/logginbotonove.png)')" onmouseout="apagarimagen(this,'url(../recursos/logginboton.png)')" onmousedown="encenderimagen(this,'url(../recursos/logginbotonpul.png)');mostrarElemento('subPanelCrearUsuarios');ocultarElemento('subPanelListadoUsuario');ocultarElemento('subPanelEditarUsuarios')">
Crear
</div>
<br>
<label style="margin: 20px" >Filtrar por:</label><select onchange="cambioOpcionFiltroUsuarios(this)"><option></option><option>comunidad</option></select><select id=selectorFiltroUsuarios onchange="actualizarListadoUsuarios()" style="display: none"></select>
<table width="90%" border="1" style="margin-left: auto;margin-right: auto">
<tr id=cabeceratabla>
<td>Usuario</td>
<td>Nombre</td>
<td>Comunidad</td>
<td>Piso</td>
<td>Clave</td>
</tr>
<tbody id=tablaUsuarios></tbody>
</table>

<br>
<div class=botonNormal align=actualizarDatosComunidades"right" onmouseover="encenderimagen(this,'url(../recursos/logginbotonove.png)')" onmouseout="apagarimagen(this,'url(../recursos/logginboton.png)')" onmousedown="encenderimagen(this,'url(../recursos/logginbotonpul.png)');mostrarDialogoSiNo('Va usted a eliminar los usuarios seleccionados, ¿Desea Continuar?','eliminarUsuarios()')">
Eliminar
</div>
<div class=botonNormal align="right" onmouseover="encenderimagen(this,'url(../recursos/logginbotonove.png)')" onmouseout="apagarimagen(this,'url(../recursos/logginboton.png)')" onmousedown="encenderimagen(this,'url(../recursos/logginbotonpul.png)');mostrarDialogoSiNo('Va usted a editar el usuario seleccionado, ¿Desea Continuar?','editarUsuario()')">
Editar
</div>
<div class=botonNormal align="right" onmouseover="encenderimagen(this,'url(../recursos/logginbotonove.png)')" onmouseout="apagarimagen(this,'url(../recursos/logginboton.png)')" onmousedown="encenderimagen(this,'url(../recursos/logginbotonpul.png)');mostrarDialogoSiNo('Va usted a restablecer la clave de los usuarios seleccionados, ¿Desea Continuar?','restablecerClaveUsuarios()')">
Reset
</div>
<br>
<br>
</div>

<div id=subPanelCrearUsuarios style="display: none;width: 80%;padding: 40px">
Numero usuarios:
<input type="text" size="1" maxlength="2" name="numero" id=numeroUsuarios> 
Comunidad:
<select size="1" id=selectComunidadUsuarios>
</select>
<br>
<div id=botonCrearUsuarios align="right" onmouseover="encenderimagen(this,'url(../recursos/logginbotonove.png)')" onmouseout="apagarimagen(this,'url(../recursos/logginboton.png)')" onclick="mostrarElemento('subPanelListadoUsuario');ocultarElemento('subPanelCrearUsuarios');ocultarElemento('subPanelEditarUsuarios')" onmousedown="encenderimagen(this,'url(../recursos/logginbotonpul.png)')">
Volver
</div>
<div id=botonCrearUsuarios align="right" onmouseover="encenderimagen(this,'url(../recursos/logginbotonove.png)')" onmouseout="apagarimagen(this,'url(../recursos/logginboton.png)')" onclick="mostrarDialogoSiNo('Va usted a crear usuarios, ¿Desea Continuar?','insertarUsuarios()')" onmousedown="encenderimagen(this,'url(../recursos/logginbotonpul.png)')">
Crear
</div>

</div>

<div id=subPanelEditarUsuarios style="display: none;" >
<table width="90%" style="margin-left: auto;margin-right: auto">
<tr>
<td style="text-align: right;">Usuario:</td><td><input id=inputEditarUsuario></td>
</tr>
<tr>
<td style="text-align: right;">Nombre:</td><td><input id=inputEditarNombre></td>
</tr>
<tr>
<td style="text-align: right;">Piso:</td><td><input id=inputEditarPiso></td>
</tr>
</table>
<div class=botonNormal align="right" onmouseover="encenderimagen(this,'url(../recursos/logginbotonove.png)')" onmouseout="apagarimagen(this,'url(../recursos/logginboton.png)')" onmousedown="encenderimagen(this,'url(../recursos/logginbotonpul.png)');ocultarElemento('subPanelEditarUsuarios');mostrarElemento('subPanelListadoUsuario')">
Volver
</div>
<div class=botonNormal align="right" onmouseover="encenderimagen(this,'url(../recursos/logginbotonove.png)')" onmouseout="apagarimagen(this,'url(../recursos/logginboton.png)')" onmousedown="encenderimagen(this,'url(../recursos/logginbotonpul.png)');updateDatosUsuario()">
Cambiar
</div>
<br>
<br>
</div>


</div>



<div id=divComunidades style="display: none;">

<div id=subPanelListadoComunidades>

<br>
<div class=botonNormal align="right" onmouseover="encenderimagen(this,'url(../recursos/logginbotonove.png)')" onmouseout="apagarimagen(this,'url(../recursos/logginboton.png)')" onmousedown="encenderimagen(this,'url(../recursos/logginbotonpul.png)');mostrarElemento('subPanelCrearComunidades');ocultarElemento('subPanelListadoComunidades')">
Crear
</div>
<br>


<table width="60%" border="1" style="margin-left: auto;margin-right: auto">
<tr id=cabeceratabla>
<td colspan="2">Comunidades</td>
</tr>
<tbody id=tablaComunidades></tbody>
</table>
</div>

<div id=subPanelCrearComunidades style="display: none;width: 80%;padding: 40px;margin-left: auto;margin-right: auto;">
Nombre de la comunidad:<input type="text" align="LEFT" size="40" maxlength="50" id=inputComunidad>
<br>
<br>
<div class=botonNormal align="right" onmouseover="encenderimagen(this,'url(../recursos/logginbotonove.png)')" onmouseout="apagarimagen(this,'url(../recursos/logginboton.png)')" onmousedown="encenderimagen(this,'url(../recursos/logginbotonpul.png)');ocultarElemento('subPanelCrearComunidades');mostrarElemento('subPanelListadoComunidades')">
Volver
</div>
<div class=boton align="right" onmouseover="encenderimagen(this,'url(../recursos/logginbotonove.png)')" onmouseout="apagarimagen(this,'url(../recursos/logginboton.png)')" onmousedown="encenderimagen(this,'url(../recursos/logginbotonpul.png)');mostrarDialogoSiNo('Va usted a crear una comunidad, ¿Desea Continuar?','insertarComunidad()')">
Crear
</div>
</div>

</div>

<div id=divMensajes style="display: none;">
<div id=subPanelListadoMensajes>
<br>
<div class=botonNormal align="right" onmouseover="encenderimagen(this,'url(../recursos/logginbotonove.png)')" onmouseout="apagarimagen(this,'url(../recursos/logginboton.png)')" onmousedown="encenderimagen(this,'url(../recursos/logginbotonpul.png)');mostrarElemento('subPanelCrearMensajes');ocultarElemento('subPanelListadoMensajes');ocultarElemento('subPanelVerMensajes')">
Crear
</div>
<br>
<label style="margin: 20px" >Filtrar por:</label><select onchange="cambioOpcionFiltroMensajes(this)"><option></option><option>comunidad</option></select><select id=selectorFiltroMensajes onchange="actualizarDatosMensajes()" style="display: none"></select>
<table width="90%" border="1" style="margin-left: auto;margin-right: auto;text-align:center;">
<tr id=cabeceratabla>
<td>Asunto</td>
<td>Remitente</td>
<td>Destinatario</td>
<td>Comunidad</td>
<td>Fecha</td>
<td>Leido</td>
</tr>
<tbody id=tablaMensajes></tbody>
</table>

<div class=opcionesListado id=opcionesListadoMensajes limit=0> <label class=websymbol onclick="actualizarDatosMensajes(-20)">(</label>
<label id=labelOpcionesListado style="font-size: 16px"> 0 a 20</label>   <label class=websymbol onclick="actualizarDatosMensajes(+20)">)</label> 
</div>
<br>
<div class=botonNormal align="right" onmouseover="encenderimagen(this,'url(../recursos/logginbotonove.png)')" onmouseout="apagarimagen(this,'url(../recursos/logginboton.png)')" onmousedown="encenderimagen(this,'url(../recursos/logginbotonpul.png)');mostrarDialogoSiNo('Va usted a eliminar los mensajes seleccionados, ¿Desea Continuar?','eliminarMensajes()')">
Eliminar
</div>
<div class=botonNormal align="right" onmouseover="encenderimagen(this,'url(../recursos/logginbotonove.png)')" onmouseout="apagarimagen(this,'url(../recursos/logginboton.png)')" onmousedown="encenderimagen(this,'url(../recursos/logginbotonpul.png)');verMensaje()">
Ver
</div>
<br>
<br>
</div>

<div id=subPanelCrearMensajes style="display: none;width: 80%;padding: 40px;margin-left: auto;margin-right: auto;">
Destinatario: <select id=selectDestinatarioMensajesA onchange="cambioOpcionSelectDestinatarioMensajes()"><option>A todos</option><option>Comunidad</option></select>
<select onchange="cambioOpcionSelectDestinatarioMensajesB()" id=selectDestinatarioMensajesB style="display: none"></select>
<select id=selectDestinatarioMensajesC style="display: none"></select>
<br>
Asunto:<input type="text" align="LEFT" size="60" maxlength="50" id=inputAsuntoMensaje style="padding-left: 15px">
<br>
Mensaje:<br>
<textarea type="text" align="LEFT" style="width: 602px" rows="4" id=inputContenidoMensaje></textarea>
<br>
<div class=botonNormal align="right" onmouseover="encenderimagen(this,'url(../recursos/logginbotonove.png)')" onmouseout="apagarimagen(this,'url(../recursos/logginboton.png)')" onmousedown="encenderimagen(this,'url(../recursos/logginbotonpul.png)');ocultarElemento('subPanelCrearMensajes');mostrarElemento('subPanelListadoMensajes')">
Volver
</div>
<div class=boton align="right" onmouseover="encenderimagen(this,'url(../recursos/logginbotonove.png)')" onmouseout="apagarimagen(this,'url(../recursos/logginboton.png)')" onmousedown="encenderimagen(this,'url(../recursos/logginbotonpul.png)');mostrarDialogoSiNo('Va usted a crear un mensaje, ¿Desea Continuar?','insertarMensaje()')">
Crear
</div>
</div>

<div id=subPanelVerMensajes style="display: none;width: 80%;padding: 40px;margin-left: auto;margin-right: auto;">
<label id=labelContenidoVerMensajes></label>
<br>
<div class=botonNormal align="right" onmouseover="encenderimagen(this,'url(../recursos/logginbotonove.png)')" onmouseout="apagarimagen(this,'url(../recursos/logginboton.png)')" onmousedown="encenderimagen(this,'url(../recursos/logginbotonpul.png)');ocultarElemento('subPanelVerMensajes');mostrarElemento('subPanelListadoMensajes')">
Volver
</div>
<br>
</div>
</div>

<div id=divArchivos style="display: none;">

<div id=subPanelListadoArchivos>
<br>
<div class=botonNormal align="right" onmouseover="encenderimagen(this,'url(../recursos/logginbotonove.png)')" onmouseout="apagarimagen(this,'url(../recursos/logginboton.png)')" onmousedown="encenderimagen(this,'url(../recursos/logginbotonpul.png)');mostrarElemento('subPanelCrearArchivos');ocultarElemento('subPanelListadoArchivos')">
Crear
</div>
<br>
<label style="margin: 20px" >Filtrar por:</label><select onchange="cambioOpcionFiltroArchivos(this)"><option></option><option>comunidad</option></select><select id=selectorFiltroArchivos onchange="actualizarDatosArchivos()" style="display: none"></select>
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
<label id=labelOpcionesListadoArchivos style="font-size: 16px"> 0 a 20</label>   <label class=websymbol onclick="actualizarDatosArchivos(+20)">)</label> 
</div>
<br>


<div class=botonNormal align="right" onmouseover="encenderimagen(this,'url(../recursos/logginbotonove.png)')" onmouseout="apagarimagen(this,'url(../recursos/logginboton.png)')" onmousedown="encenderimagen(this,'url(../recursos/logginbotonpul.png)');mostrarDialogoSiNo('Va usted a eliminar las incidencias seleccionadas, ¿Desea Continuar?','eliminarArchivos()')">
Eliminar
</div>
<br>
<br>
</div>

<div id=subPanelCrearArchivos style="display: none;width: 80%;padding: 40px;margin-left: auto;margin-right: auto;">
<form  action="upload.php" target="uploadIFrame"  method="POST" enctype="multipart/form-data" id=formUploadFile>
Destinatario: <select id=selectDestinatarioArchivosA name="selectDestinatarioArchivosA" onchange="cambioOpcionSelectDestinatarioArchivos()"><option>A todos</option><option>Comunidad</option></select>
<select id=selectDestinatarioArchivosB name="selectDestinatarioArchivosB" onchange="cambioOpcionSelectDestinatarioArchivosB()" style="display: none">
</select><select id=selectDestinatarioArchivosC name="selectDestinatarioArchivosC" style="display: none"></select>
<br>
Nombre archivo:<input type="text" align="LEFT" size="60" maxlength="50" id=inputNombreArchivo name="inputNombreArchivo" style="padding-left: 15px">
<br>
<label>Seleccionar archivo:</label><input id="inputUploadFile" type="file" name="inputUploadFile"><br><br>
<button type="button" onclick="mostrarDialogoSiNo('Va usted a crear un archivo, ¿Desea continuar?','subirArchivo()')">Subir archivo</button>
</form>
<br>
<div class=botonNormal align="right" onmouseover="encenderimagen(this,'url(../recursos/logginbotonove.png)')" onmouseout="apagarimagen(this,'url(../recursos/logginboton.png)')" onmousedown="encenderimagen(this,'url(../recursos/logginbotonpul.png)');ocultarElemento('subPanelCrearArchivos');mostrarElemento('subPanelListadoArchivos')">
Volver
</div>
<br>
</div>
</div>
<div id=divIncidencias style="display: none;">
<div id=subPanelListadoIncidencias>
<br>
<div class=botonNormal align="right" onmouseover="encenderimagen(this,'url(../recursos/logginbotonove.png)')" onmouseout="apagarimagen(this,'url(../recursos/logginboton.png)')" onmousedown="encenderimagen(this,'url(../recursos/logginbotonpul.png)');mostrarElemento('subPanelCrearIncidencias');ocultarElemento('subPanelListadoIncidencias')">
Crear
</div>
<br>
<label style="margin: 20px" >Filtrar por:</label><select onchange="cambioOpcionFiltroIncidencias(this)"><option></option><option>comunidad</option></select><select id=selectorFiltroIncidencias onchange="actualizarDatosIncidencias()" style="display: none"></select>
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

<div class=botonNormal align="right" onmouseover="encenderimagen(this,'url(../recursos/logginbotonove.png)')" onmouseout="apagarimagen(this,'url(../recursos/logginboton.png)')" onmousedown="encenderimagen(this,'url(../recursos/logginbotonpul.png)');mostrarDialogoSiNo('Va usted a eliminar las incidencias seleccionadas, ¿Desea Continuar?','eliminarIncidencias()')">
Eliminar
</div>
<div class=botonNormal align="right" onmouseover="encenderimagen(this,'url(../recursos/logginbotonove.png)')" onmouseout="apagarimagen(this,'url(../recursos/logginboton.png)')" onmousedown="encenderimagen(this,'url(../recursos/logginbotonpul.png)');verIncidencias()">
Ver
</div>
<br>
<br>
</div>

<div id=subPanelCrearIncidencias style="display: none;width: 80%;padding: 40px;margin-left: auto;margin-right: auto;">
<br>
Destinatario: <select id=selectDestinatarioIncidencia></select>
<br>
<br>
Asunto:
<br>
<input type="text" align="LEFT" size="60" maxlength="50" id=inputAsuntoIncidencia style="padding-left: 15px">
<br>
<br>
Contenido:<br>
<textarea type="text" align="LEFT" style="width: 602px" rows="4" id=inputContenidoIncidencia></textarea>
<br>
<div id=botonCrearUsuarios align="right" onmouseover="encenderimagen(this,'url(../recursos/logginbotonove.png)')" onmouseout="apagarimagen(this,'url(../recursos/logginboton.png)')" onclick="mostrarElemento('subPanelListadoIncidencias');ocultarElemento('subPanelCrearIncidencias')" onmousedown="encenderimagen(this,'url(../recursos/logginbotonpul.png)')">
Volver
</div>
<div class=boton align="right" onmouseover="encenderimagen(this,'url(../recursos/logginbotonove.png)')" onmouseout="apagarimagen(this,'url(../recursos/logginboton.png)')" onmousedown="encenderimagen(this,'url(../recursos/logginbotonpul.png)');mostrarDialogoSiNo('Va usted a crear una incidencia, ¿Desea Continuar?','insertarIncidencia()')">
Crear
</div>
</div>

<div id=subPanelVerIncidencias style="display: none;width: 80%;padding: 40px;margin-left: auto;margin-right: auto;">
<div id=divContenidoIncidencias></div>
Añadir comentario: <select id=selectorIncidenciaactualizarDatosPersonalessEstado style="float: right;"> <option>En proceso</option> <option>Solucionado</option> </select>
<textarea type="text" align="LEFT" style="width: 602px" rows="4" id=inputAñadirContenidoIncidencia></textarea>
<br>
<div class=botonNormal align="right" onmouseover="encenderimagen(this,'url(../recursos/logginbotonove.png)')" onmouseout="apagarimagen(this,'url(../recursos/logginboton.png)')" onmousedown="encenderimagen(this,'url(../recursos/logginbotonpul.png)');ocultarElemento('subPanelVerIncidencias');mostrarElemento('subPanelListadoIncidencias')">
Volver
</div>
<div class=botonNormal align="right" onmouseover="encenderimagen(this,'url(../recursos/logginbotonove.png)')" onmouseout="apagarimagen(this,'url(../recursos/logginboton.png)')" onmousedown="encenderimagen(this,'url(../recursos/logginbotonpul.png)');mostrarDialogoSiNo('Va a añadir un mensaje a la incidencia, ¿Desea continuar?','añadirIncidencia()')">
Añadir
</div>
<br>
</div>
</div>

<div id=divAlertas style="display: none;">
<div id=subPanelListadoAlertas>
<br>
<div class=alertas id style="text-align:center">
<label>Remitente ha enviado un mensaje/incidencia</label>
<hr style="width:80%">
<label>Asunto del mensaje o resumen de la incidencia</label>
<hr style="width:80%">
<label>Aqui pondremos el contenido del mensaje o de la incidencia</label>
<div class=botonNormal align="right" onmouseover="encenderimagen(this,'url(../recursos/logginbotonove.png)')" onmouseout="apagarimagen(this,'url(../recursos/logginboton.png)')" onmousedown="encenderimagen(this,'url(../recursos/logginbotonpul.png)');mostrarDialogoSiNo('Va a añadir un mensaje a la incidencia, ¿Desea continuar?','verAlerta()')">
Ver
</div>
<br><br>
</div>

</div>
</div>


<br><br>
</div>



<div id=pie>
<div id=piecontenido align="center">
<div id=pietexto>AguacadaGesfincas.es</div>
</div>
</div>
</body>
</html>
