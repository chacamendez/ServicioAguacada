<div  class="panelAuxiliarInterior">
  <form id=formHistorial>
  <input id=idUsuario name=idUsuario type=hidden value=0>
  <input id=idComunidad name=idComunidad type=hidden value=0>
  <label class="fa fa-sign-out" style="float:right;font-size:30px" onclick="$(this).parent().parent().parent().fadeOut();"></label>
  <table id=tablaHistorial style="width:90%">
    <tr>
      <td style="text-align:right">Evento:<td><td style="text-align:left"><textarea  id=inputEvento name=inputEvento style="width:90%"></textarea><td>
    </tr>
      <td style="text-align:right"><input id=inputAlerta type=checkbox>Alerta</input><td><td style="text-align:left"><input disabled="disabled" id="inputFechaAlerta" style="width: 25%"><img src="./RESOURCES/cal.gif" onclick="NewCssCal('inputFechaAlerta')" style="cursor:pointer;"><td>
    </tr>
  </table>
</form>
  <div align=center><input id=insertEvent type=button value="Enviar datos" onclick="executeAction('insertarPanel','formHistorial','&destino=historial')"/></div>
  <hr>
  <div id=divListadoHistorial style="overflow:auto;height:70%">
    <div class="historialItem">
    <div class=divTopHistorialItem >Chaca <div class=divAlertaHistorialItem> <input id=idAlerta type=checkbox><label>fechaAlerta<label></input></div></div>
    <div class=divBodyHistorialItem>Mensaje aqui</div>
    <div class=divBottomHistorialItem >10/10/1977 15:03</div>
    </div>

  </div>
</div>
