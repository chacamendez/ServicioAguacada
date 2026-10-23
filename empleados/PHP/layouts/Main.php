<html>

<head>


<script src="./JS/jquery-3.3.1.min.js"></script>
<script src="RESOURCES/popper/umd/popper.js"></script>
<script src="RESOURCES/bootstrap/js/bootstrap.js"></script>
<script src="JS/bootbox.min.js"></script>
<script language="javascript" src="./JS/aguajs.js"></script>
<script language="javascript" src="./JS/principal.js"></script>
<script language="javascript" src="./JS/menus2.js"></script>
<script language="javascript" src="./JS/clientes.js"></script>
<script language="javascript" src="./JS/datetimepicker_css.js"></script>

<script type="text/javascript" src="JS/AES.js"></script>
<script type="text/javascript" src="JS/BCRYPT.js"></script>
<script type="text/javascript" src="JS/toast.js"></script>

<meta http-equiv="content-type" content="text/html; charset=UTF-8">
	<meta name="Description" content="Inicio de Aguacadagesfincas." >
	<meta name="Keywords" content="aguacadagesfincas,asesoria,gestion,comunidades,tenerife,lalaguna,administrador">
	<meta name="author" content="Juan Alberto Méndez Alonso">
	<meta name="verify-v1" content="1uD+8cRhb6tCL92xQo9W8K/kwo8xrRqpDFc8V7bsBIo=" />
	<meta name="HandhelpFriendy" content="true" />
	<meta name="MobileOptimized" content="320" />
	<meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0, width=device-width, user-scalable=no" />
  <link href="RESOURCES/bootstrap/css/bootstrap.css" rel="stylesheet">
	<link rel="icon"   type="image/x-icon" href="./RESOURCES/agua.ico">
	<link rel="stylesheet" href="RESOURCES/font-awesome/css/font-awesome.css">
	<link rel="stylesheet" type="text/css" href="./CSS/agua.css?v=1.0"  >
	<link rel="stylesheet" type="text/css" href="./CSS/oldPhones.css" media="all and (min-width:0px) and (max-width: 480px)" >
	<link rel="stylesheet" type="text/css" href="./CSS/phones.css" media="all and (min-width:481px) and (max-width: 768px)" >
	<link rel="stylesheet" type="text/css" href="./CSS/tablets.css" media="all and (min-width:769px) and (max-width: 1024px)" >
	<link rel="stylesheet" type="text/css" href="./CSS/laptops.css" media="all and (min-width:1025px) and (max-width: 1920px)" >
	<link rel="stylesheet" type="text/css" href="./CSS/desktops.css" media="all and (min-width:1921px)" >


	<title>Aguacadagesfincas</title>
</head>
<iframe name="uploadIFrame" style="display: none"></iframe>
<iframe name="downloadIFrame" style="display: none"></iframe>
<div id=panelLateral style="display:none">
	<div id=botonLateral onclick="animatePanelLateral()">h</div>
	<div id=panelLateralContenido>
  </div>
</div>
<div id=panelAuxiliar class="panelAuxiliar"></div>
<div id=panelAdd onclick="" style="display: none">
	<label class="fa fa-plus-circle"/>
</div>


<body id=cuerpo onload="alCargar();">

<div id=cabecera align="center">
<div id=titulo>
<img id=imagentitulo src="./RESOURCES/imagentitulo4.png"></div>
<div id=barra>
<table id=tablabarra>
<tr>
	<td id=barraInicio align="center" class="botonBarraSeleccionada" ind=0  onclick="slide(this)">Inicio</td>
	<td id=barraContactar align="center" class="botonBarra"  ind=1 onclick="slide(this)"> Contactar </td>
	<td id=barraSituacion align="center" class="botonBarra"  ind=2 onclick="slide(this)"> Situación </td>
	<td id=barraClientes align="center" class="botonBarra" ind=3 onclick="slide(this)">Area clientes</td>
</tr>
</table>
</div>
</div>

<div id=panelPrincipal  align="center">
<div id=contenido>
	<div id=panelInicio ind=0 class="paneles">
		<div id=panelInicioInt>
		<img src="./RESOURCES/imagentitulo.png" id="imagen" >
		<br>
		<div class=labelexplicacion>AguacadaGesfincas es una empresa joven fundada en el 2006, dedicada principalmente a la administración de fincas y a la gestión íntegra de comunidades. Dirigida por Carmen Teresa Méndez Alonso, Administradora de fincas colegiada y titulada en administración de empresas, con una amplia experiencia en el sector. </div>
		<table class="table" style="width:80%;text-align:center">
		   <tbody>
		    <tr>
		      <td><img class="icons" src="./RESOURCES/phone.png" onclick="window.location.href='tel:629077064';"></td>
					 <td><img class="icons" src="./RESOURCES/mail.png" onclick="window.location.href = 'mailto:info@aguacadagesfincas.es';"></td>
					  <td><img class="icons" src="./RESOURCES/whatsapp.png" onclick="window.location.href='whatsapp://send?phone=34629077064'"></td>
						<td><img class="icons" src="./RESOURCES/usuario.png" onclick="var contacto=document.getElementById('botonBarraContacto');slide(contacto);"></td>
		    </tr>

		  </tbody>
		</table>
		<div class=labelexplicacion>Ponemos a disposición de nuestros clientes diversas maneras para cumplir eficientemente con nuestros servicios. Presencialmente y por teléfono, en nuestro horario de oficina, y las 24 horas a través de la zona clientes de nuestra página web.</div>
		<div><img src="./RESOURCES/presupuesto.png" id=imagenPresupuesto></div>
		<div class=labelexplicacion>Elaboramos todos los presupuestos necesarios para el mantenimiento de su comunidad, con la ventaja que supone trabajar con profesionales que rebajan precios por volumen de contratación.</div>
		<div><img src="./RESOURCES/remesas.png" id=imagenRemesa></div>
		<div class=labelexplicacion>Elaboración de remesas mensuales para el cobro domiciliado de las cuotas de la comunidad, así como la posiblilidad de pagar en efectivo en nuestra oficina.</div>
		<div><img src="./RESOURCES/revision.png" id="imagenRevision" ></div>
		<div class=labelexplicacion>Revisión mensual de las instalaciones de la comunidad con el objetivo de detectar anomalías. También ponemos a disposición del propietario varias formas para comunicar incidencias, telefónicamente durante el horario de apertura de nuestra oficina y las 24 horas desde la zona de clientes de nuestra página web.</div>
		<div><img src="./RESOURCES/gestiones.png" id="imagenGestiones"></div>
		<div class=labelexplicacion>Realizamos todas las gestiones que la comunidad necesite con las administraciones públicas. Solicitud de vados, permisos de obra, etc.</div>
		<div><img src="./RESOURCES/documentacion.png" id="imagenDocumentacion" ></div>
		<div class=labelexplicacion>Custodiamos toda la documentación referente a la comunidad por un máximo de cinco años, periodo establecido por ley. Además tendrá la posiblidad de acceder a toda esta documentación a través de la zona de clientes de nuestra página web.</div>
		<div><img src="./RESOURCES/seguro.png" id="imagenSeguro" ></div>
		<div class=labelexplicacion>La empresa cuenta con un seguro de responsabilidad civil, para cualquier contigencia que pudiera surgir.</div>

	</div>


	</div>
	<div id=panelContactar ind=1 style="display:none"  class="paneles">
		<div id=panelContactarInt>
		<label id=labelContacto>Nuestra oficina está abierta de lunes a jueves en horario de 8:00-14:00 y 16:00-18:00 y viernes de 8:00-15:00. Contacte con nosotros en el teléfono/fax (922)15 68 49, envíenos un correo a info@aguacadagesfincas.es o rellene el siguiente formulario y nos pondremos en contacto con usted lo antes posible.
	</label>
			<div align="center">
		<form id=contacto method="post">
		<table id=contactoTabla cellpadding="5px">
		<tr>
		<td align="right">Nombre:
		</td>
		<td >
			<input name="contactoNombre" id="contactoNombre" type="text"  align="LEFT" maxlength="50"  value="">
		</td>
		</tr>
		<tr>
		<td align="right">Email:
		</td>

		<td>
			<input  name="contactoCorreo" id="contactoCorreo" type="text" maxlength="50" align="LEFT" value="">
		</td>
		</tr>
		<tr>
		<td align="right"><p id=pconsulta>Consulta:</p>
		</td>

		<td>
			<textarea id=contactoConsulta  name="contactoConsulta"></textarea>
			</td>
		</tr>
		</table>

		<div id=logginboton onclick="enviarcomprobar()">
		  <label class=labelLoggin>Enviar</label>
		</div>
			</form>
		</div>
		<div align="center">

		<p id=loggincontactomensaje> El correo introducido es incorrecto. Por favor revíselo</p>
		</div>
	</div>
	</div>
	<div id=panelSituacion ind=2 style="display:none" class="paneles">
		<div id=panelSituacionInt>
		<label id=labelSituacion>Estamos en Punta del Hidalgo, calle Francisco Alonso Martín nº 7, La Laguna.</label>
		<br>
		<iframe id="mapIframe"   frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://maps.google.com/maps/ms?msa=0&amp;msid=200473717597826143596.000455e9d2359954bd93a&amp;hl=es&amp;ie=UTF8&amp;t=m&amp;ll=28.568043,-16.327497&amp;spn=0.003298,0.00456&amp;z=17&amp;output=embed"></iframe>
		<br /><small>Ver <a href="https://maps.google.com/maps/ms?msa=0&amp;msid=200473717597826143596.000455e9d2359954bd93a&amp;hl=es&amp;ie=UTF8&amp;t=m&amp;ll=28.568043,-16.327497&amp;spn=0.003298,0.00456&amp;z=17&amp;source=embed" style="color:#0000FF;text-align:left">Aguacadagesfincas</a> en un mapa más grande</small>
		</div>
		</div>
	<div id=panelAreaClientes ind=3 style="display:none" class="paneles">
		<form id=loggin method="get">
		<table id=clientesTabla cellpadding="5px">
		<tr>
		<td align="right">Usuario:</td>
		<td ><input name="username" id="username" type="text"  align="LEFT" maxlength="20"  >
		</td>
		</tr>
		<tr>
		<td align="right">Clave:
		</td>
		<br>
		<td><input name="password" id="password" type="password" maxlength="20" align="LEFT" onkeydown="enter(event)">
		</td>
		</tr>
		</table>
		<br>

		<div id=logginboton onclick="loggin()">
				<label class=labelLoggin>Enviar</label>
		</div>
		</form>
		</div>
		<div id=panelHome ind=4 style="display:none" class="paneles"></div>
		<div id=panelUsuarios ind=5 style="display:none" class="paneles"></div>
		<div id=panelComunidades ind=6 style="display:none" class="paneles"></div>
		<div id=panelMensajes ind=7 style="display:none" class="paneles"></div>
		<div id=panelArchivos ind=8 style="display:none" class="paneles"></div>
		<div id=panelCorrespondencias ind=9 style="display:none" class="paneles"></div>
		<div id=panelRecibos ind=10 style="display:none" class="paneles"></div>
		<div id=panelEmpleados ind=11 style="display:none" class="paneles"></div>




	</div>

</div>


<div id=pie>
<div id=piecontenido align="center">
<div id=pietexto>Aguacadagesfincas.es</div>
</div>
</div>
</body>
</html>
