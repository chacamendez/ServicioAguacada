<div  class="panelAuxiliarInterior">
  <input id=idUsuario type=hidden>
  <label class="labelOut fa fa-sign-out" style="float:right;font-size:30px" onclick="$(this).parent().parent().fadeOut();executeAction('rellenarPanel','&destino=panelAuxiliarMensajesStop')"></label>
  
  <div id=divMensajesVisor>
<div class="misMensajes">
<div class=divTopMensajes >Chaca</div>
<div class=divBodyMensajes></div>
Mensaje aqui
<div class=divBottomMensajes >10/10/1977 15:03</div>
</div>
  </div>
  <input id=inputMensajesEnviar onkeydown="if (event.key == 'Enter') {$('#buttonMensajesEnviar').click();}">
  <button id=buttonMensajesEnviar value="Enviar" onClick="executeAction('insertarPanel','&destino=mensajes',['inputMensajesEnviar','idUsuario']);$('#inputMensajesEnviar').val('')">Enviar</button>
</div>
