<div  class="panelAuxiliarInterior">
  <form id=formDirecciones>
    <input id=idUsuario type=hidden>
    <input id=idDireccion type=hidden>
  <label class="fa fa-sign-out" style="float:right;font-size:30px" onclick="$(this).parent().parent().parent().fadeOut();"></label>
  <BR>Destinatario:<input id=destinatario />
  <BR>Dirección postal:<BR>
  <textarea id=direccion style="width: 90%;" size=40></textarea>
  </form>
  <div align=center><input id=insertarDireccion type=button value="Enviar datos" onclick="executeAction('insertarPanel','formDirecciones','&destino=direcciones')"/></div>
</div>
