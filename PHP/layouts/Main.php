<!DOCTYPE html>
<html lang="es">

<head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta name="Description" content="Inicio de Aguacadagesfincas.">
    <meta name="Keywords" content="aguacadagesfincas,asesoria,gestion,comunidades,tenerife,lalaguna,administrador">
    <meta name="author" content="Juan Alberto Méndez Alonso">
    <meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0, width=device-width, user-scalable=no" />

    <title>Aguacadagesfincas</title>

    <!-- CSS -->
    <link href="./RESOURCES/bootstrap/css/bootstrap.css?v=1.0" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="./RESOURCES/agua.ico?v=1.0">
    <link rel="stylesheet" href="./RESOURCES/font-awesome/css/font-awesome.css?v=1.0">
    <link rel="stylesheet" type="text/css" href="./CSS/agua.css?v=1.2">
    <link rel="stylesheet" type="text/css" href="./CSS/main.css?v=1.1">

    <!-- JavaScripts -->
    <script src="./JS/jquery-3.3.1.min.js?v=1.0"></script>
    <script src="./RESOURCES/popper/umd/popper.js?v=1.0"></script>
    <script src="./RESOURCES/bootstrap/js/bootstrap.js?v=1.0"></script>
    <script src="./JS/bootbox.min.js?v=1.0"></script>
    <script src="./JS/clientes.js?v=1.1"></script>
    <script src="./JS/datetimepicker_css.js?v=1.0"></script>
    <script src="./JS/AES.js?v=1.0"></script>
    <script src="./JS/BCRYPT.js?v=1.0"></script>
    <script src="./JS/toast.js?v=1.0"></script>
    <script src="./JS/main.js?v=1.0"></script>

</head>

<!-- Aviso de Cookies -->
<div id="avisoCookies" class="cookie-banner">
    <div class="cookie-contenido">
        <p>
            En <strong>Aguacadagesfincas</strong> solo utilizamos cookies estrictamente necesarias para el correcto funcionamiento de la página web. Al continuar navegando, consideramos que acepta su uso.
        </p>
        <button type="button" id="aceptarCookies" onclick="aceptarCookies()">Aceptar</button>
    </div>
</div>

<body id="cuerpo">

    <iframe name="uploadIFrame" style="display: none"></iframe>
    <iframe name="downloadIFrame" style="display: none"></iframe>

    <div id="panelLateral" style="display:none">
        <div id="panelLateralContenido"></div>
    </div>

    <div id=panelAuxiliar class="panelAuxiliar"></div>
   

    <div id="panelAdd" onclick="" style="display: none">
        <!-- CORREGIDO: Etiqueta label cerrada correctamente -->
        <label class="fa fa-plus-circle"></label>
    </div>

    <div id="cabecera" align="center">
        <div id="titulo">
            <img id="imagentitulo" src="./RESOURCES/imagentitulo4.png?v=1.0" alt="Logotipo">
            <span class="texto-con-skin">AguacadaGesfincas</span>
        </div>
        <div id="barra">
            <table id="tablabarra">
                <tr>
                    <td id="barraInicio" align="center" class="botonBarraSeleccionada" ind="0" target="contenido" onclick="slide(this)">
                        <i class="fa fa-home"></i><span class="texto-boton"> Inicio</span>
                    </td>
                    <td id="barraContactar" align="center" class="botonBarra" ind="1" target="contenido" onclick="slide(this)">
                        <i class="fa fa-envelope"></i><span class="texto-boton"> Contactar</span>
                    </td>
                    <td id="barraSituacion" align="center" class="botonBarra" ind="2" target="contenido" onclick="slide(this)">
                        <i class="fa fa-map-marker"></i><span class="texto-boton"> Situación</span>
                    </td>
                    <td id="barraClientes" align="center" class="botonBarra" ind="3" target="contenido" onclick="slide(this)">
                        <i class="fa fa-user"></i><span class="texto-boton"> Área clientes</span>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <div id="panelPrincipal" align="center">
        <div id="contenido">

            <!-- Panel Inicio -->
        <div id="panelInicio" ind="0" status="active" class="paneles">
            <div id="panelInicioInt">
                 <div class="bloque-servicio fincas-animar">
                    <img src="./RESOURCES/imagentitulo.png?v=1.0" id="imagen" class="fincas-animar">
                    <br>
                    <div class="labelexplicacion fincas-animar">
                        AguacadaGesfincas dedicada desde 2006,  a la administración de fincas y a la gestión íntegra de comunidades.
                    </div>
                 </div>   
                
                 <div class="bloque-servicio fincas-animar">
                    <table class="table fincas-animar" style="width:80%;text-align:center">
                        <tbody>
                            <tr>
                                <td><img class="icons" src="./RESOURCES/phone.png?v=1.0" onclick="window.location.href='tel:629077064';"></td>
                                <td><img class="icons" src="./RESOURCES/mail.png?v=1.0" onclick="window.location.href = 'mailto:info@aguacadagesfincas.es';"></td>
                                 <td><img class="icons" id="btnWhatsapp" src="./RESOURCES/whatsapp.png?v=1.0"></td>
                                    <td><img class="icons" src="./RESOURCES/usuario.png?v=1.0" onclick="var clientes=document.getElementById('barraClientes');slide(clientes);"></td>
                            </tr>
                        </tbody>
                    </table>
                     <div class="labelexplicacion fincas-animar">
                    Ponemos a disposición de nuestros clientes diversas maneras para cumplir eficientemente con nuestros servicios. Presencialmente y por teléfono, en nuestro horario de oficina, y las 24 horas a través de la zona clientes de nuestra página web.
                    </div>
                </div>

               

                <div class="bloque-servicio fincas-animar">
                    <img src="./RESOURCES/presupuesto.png?v=1.0" id="imagenPresupuesto">
                    <div class="labelexplicacion">Elaboramos todos los presupuestos necesarios para el mantenimiento de su comunidad, con la ventaja que supone trabajar con profesionales a precios competitivos por volumen de contratación.</div>
                </div>

                <div class="bloque-servicio fincas-animar">
                    <img src="./RESOURCES/remesas.png?v=1.0" id="imagenRemesa">
                    <div class="labelexplicacion">Elaboración de remesas mensuales para el cobro domiciliado de las cuotas de la comunidad, así como la posibilidad de pagar en efectivo en nuestra oficina.</div>
                </div>

                <div class="bloque-servicio fincas-animar">
                    <img src="./RESOURCES/revision.png?v=1.0" id="imagenRevision">
                    <div class="labelexplicacion">Revisión mensual de las instalaciones de la comunidad con el objetivo de detectar anomalías. También ponemos a disposición del propietario varias formas para comunicar incidencias, telefónicamente durante el horario de apertura de nuestra oficina y las 24 horas desde la zona de clientes de nuestra página web.</div>
                </div>

                <div class="bloque-servicio fincas-animar">
                    <img src="./RESOURCES/gestiones.png?v=1.0" id="imagenGestiones">
                    <div class="labelexplicacion">Realizamos todas las gestiones que la comunidad necesite con las administraciones públicas. Solicitud de vados, permisos de obra, etc.</div>
                </div>

                <div class="bloque-servicio fincas-animar">
                    <img src="./RESOURCES/documentacion.png?v=1.0" id="imagenDocumentacion">
                    <div class="labelexplicacion">Custodiamos toda la documentación referente a la comunidad por un máximo de cinco años, periodo establecido por ley. Además tendrá la posibilidad de acceder a toda esta documentación a través de la zona de clientes de nuestra página web.</div>
                </div>

                <div class="bloque-servicio fincas-animar">
                    <img src="./RESOURCES/seguro.png?v=1.0" id="imagenSeguro">
                    <div class="labelexplicacion">La empresa cuenta con un seguro de responsabilidad civil, para cualquier contingencia que pudiera surgir.</div>
                </div>
            </div>
        </div>
    

             <!-- Panel Contactar -->
            <div id="panelContactar" ind="1" class="paneles">
                <div id="panelContactarInt">
                    <p class="labelexplicacion">
                        Nuestra oficina está abierta de lunes a jueves en horario de <strong>8:00 a 15:00</strong> y <strong>16:00 a 18:00</strong>, y los viernes de <strong>8:00 a 16:00</strong>. Horario de verano: del 15/07 al 15/09, de lunes a viernes en horario de 8:00 a 15:00.<br>
                        Contacte con nosotros en el teléfono <strong>922156849</strong> o móvil <strong>629077064</strong>, envíenos un correo a 
                        <a href="mailto:info@aguacadagesfincas.es">info@aguacadagesfincas.es</a> o complete el siguiente formulario y nos pondremos en contacto a la mayor brevedad.
                    </p>

                    <div class="contenedor-formulario">
                        <form id="contacto" method="post" onsubmit="return false;">
                            
                            <div class="grupo-campo">
                                <label for="contactoNombre">Nombre completo</label>
                                <input name="contactoNombre" id="contactoNombre" type="text" maxlength="50" placeholder="Ej. María García">
                            </div>

                            <div class="grupo-campo">
                                <label for="contactoCorreo">Correo electrónico</label>
                                <input name="contactoCorreo" id="contactoCorreo" type="email" maxlength="50" placeholder="ejemplo@correo.com">
                            </div>

                            <div class="grupo-campo">
                                <label for="contactoConsulta">Consulta</label>
                                <textarea id="contactoConsulta" name="contactoConsulta" rows="5" placeholder="Escriba aquí su mensaje..."></textarea>
                            </div>

                            <div class="contenedor-boton">
                                <button type="button" id="logginboton" onclick="enviarConsulta()">
                                    <span class="labelLoggin">Enviar consulta</span>
                                </button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>

            <!-- Panel Situación -->
            <div id="panelSituacion" ind="2" class="paneles" style="display: none;">
                <div id="panelSituacionInt">
                    
                    <!-- Tarjeta de Información Directa -->
                    <div class="tarjeta-situacion">
                        <h2>Dónde estamos</h2>
                        <p>
                            Visítenos en nuestra oficina. Estaremos encantados de atenderle en persona para cualquier consulta relacionada con la gestión de su comunidad.
                        </p>

                        <div class="grid-informacion">
                            <div class="info-bloque">
                                <strong>Dirección</strong>
                                <p>Calle Francisco Alonso Martín 7 <br>38240 San Cristóbal de La Laguna, Santa Cruz de Tenerife</p>
                            </div>

                            <div class="info-bloque">
                                <strong>Horarios de Atención</strong>
                                <p>Lunes a Jueves: 8:00 - 15:00 | 16:00 - 18:00<br>Viernes: 8:00 - 16:00<br>Horario de verano (del 15/07 al 15/09): Lunes a Viernes de 8:00 a 15:00</p>
                            </div>

                            <div class="info-bloque">
                                <strong>Contacto Directo</strong>
                                <p>Teléfono: 922156849<br>Móvil: 629077064<br>Email: <a href="mailto:info@aguacadagesfincas.es">info@aguacadagesfincas.es</a></p>
                            </div>
                        </div>
                    </div>

                    <!-- Contenedor del Mapa interactivo -->
                    <div class="contenedor-mapa">
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d851.0710932015276!2d-16.328339320352743!3d28.56864112364312!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0xc41cfc60e172f31%3A0x44f88569ae735993!2sAguacada%20Gesfincas%20S%20L!5e0!3m2!1ses!2ses!4v1786374352438!5m2!1ses!2ses" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="strict-origin-when-cross-origin"></iframe>
                    </div>

                </div>
            </div>

            <!-- Panel Área Clientes -->
            <div id="panelAreaClientes" ind="3" class="paneles" style="display: none;">
                <div id="panelAreaClientesInt">

                    <div class="tarjeta-login">
                        <div class="login-header">
                            <h2>Acceso a Clientes</h2>
                            <p>Introduzca sus credenciales para acceder a la gestión de su comunidad.</p>
                        </div>

                        <form id="loggin" method="post" onsubmit="return false;">
                            
                            <div class="grupo-campo">
                                <label for="username">Usuario</label>
                                <input name="username" id="username" type="text" maxlength="20" placeholder="Su usuario" autocomplete="username">
                            </div>

                            <div class="grupo-campo">
                                <label for="password">Clave de acceso</label>
                                <input name="password" id="password" type="password" maxlength="20" placeholder="••••••••" autocomplete="current-password" onkeydown="enter(event)">
                            </div>

                            <div class="contenedor-boton">
                                <button type="button" id="logginboton" onclick="loggin()">
                                    <span class="labelLoggin">Entrar</span>
                                </button>
                            </div>

                        </form>
                    </div>

                </div>
            </div>

            <!-- Paneles Secundarios -->
            <div id="panelHome" ind="4" class="paneles"></div>
            <div id="panelUsuarios" ind="5" class="paneles"></div>
            <div id="panelComunidades" ind="6" class="paneles"></div>
            <div id="panelMensajes" ind="7" class="paneles"></div>
            <div id="panelArchivos" ind="8" class="paneles"></div>
            <div id="panelCorrespondencias" ind="9" class="paneles"></div>
            <div id="panelRecibos" ind="10" class="paneles"></div>
            <div id="panelEmpleados" ind="11" class="paneles"></div>

        </div>
    </div>

   <div id="pie">
        <div id="piecontenido" style="display: flex; justify-content: space-between; align-items: center; width: 95%; max-width: 1200px; height: 100%;">
            <div id="pietexto" class="texto-con-skin-2" style="flex: 1; text-align: left; font-size: 14px; text-shadow: none; display: flex; align-items: center; height: 100%;">Aguacadagesfincas.es</div>
            <div id="pielegal" onclick="loadLayout('panelAvisoLegal', 'panelAuxiliar'); document.getElementById('panelAuxiliar').style.display = 'flex';">
                Aviso Legal
            </div>
        </div>
    </div>

</body>
</html>