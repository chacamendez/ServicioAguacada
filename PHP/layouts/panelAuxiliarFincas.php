<div  class="panelAuxiliarInterior">
  <form id=formFinca>
    <input id=idUsuario type=hidden>
  <label class="fa fa-sign-out" style="float:right;font-size:30px" onclick="$(this).parent().parent().parent().fadeOut();"></label>
  <table id=tablaUsuarios>
    <tr>
      <td style="text-align:right">Finca:<td><td style="text-align:left"><input id=finca size="40%" /><td>
    </tr>
    <tr>
      <td style="text-align:right">Coeficiente:<td><td style="text-align:left"><input id=coeficiente size="5%" type="text" pattern="\d{1,3}(\.\d{1,3})?" inputmode="decimal" maxlength="6" /><td>
    </tr>
  </table>
  <table id=tablaPropietarios><table>
</form>
  <div align=center><input id=addFinca type=button value="Enviar datos" onclick="executeAction('insertarPanel','formFinca','&destino=fincas')"/></div>
</div>
