<div  class="panelAuxiliarInterior">
  <form id=formUsuarios>
    <input id=idUsuarioEditar type=hidden>
  <label class="fa fa-sign-out labelOut" style="float:right;font-size:30px" onclick="$(this).parent().parent().parent().fadeOut();"></label>
  <label id=labelUsuarios> Introduzca un usuario y contraseña para usar el servicio. </label>
  <hr>
  <div class=divTableExt><div class=divTableIzq>Nombre:</div><div class=divTableDer><input id=idNombre /></div></div>
  <div class=divTableExt><div class=divTableIzq>Usuario:</div><div class=divTableDer><input id=idUsuario /></div></div>
  <div class=divTableExt><div class=divTableIzq>Clave:</div><div class=divTableDer><input type=password id=idClave value=""/></div></div>
  <div class=divTableExt><div class=divTableIzq>Clave:</div><div class=divTableDer><input type=password id=idClave2 value=""/></div></div>
  <div class=divTableExt><div class=divTableIzq>Telefono:</div><div class=divTableDer><input id=idPrefijo size="2" maxlength=5 value="+34" /><input id=idTelefono maxlength=9 /></div></div>
  <div class=divTableExt><div class=divTableIzq>Correo:</div><div class=divTableDer><input id=idCorreo /></div></div>
  <hr>
  <div align=center><input id=crearUsuario type=button value="Enviar datos" onclick="executeAction('editar','formUsuarios','&destino=usuarios')"/></div>
  </form>

</div>
