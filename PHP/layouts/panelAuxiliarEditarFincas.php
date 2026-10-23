<div  class="panelAuxiliarInterior">
  <form id=formFinca>
    <input id=idUsuario type=hidden>
    <input id=idFinca type=hidden>
  <label class="fa fa-sign-out" style="float:right;font-size:30px" onclick="executeAction('rellenarPanel','&destino=usuarios','filtroUsuarios');$(this).parent().parent().parent().fadeOut();"></label>
  <table id=tablaUsuarios>
    <tr>
      <td style="text-align:right">Finca:<td><td style="text-align:left"><input id=finca size="40%" /><td>
    </tr>
     <tr>
      <td style="text-align:right">Coeficiente:<td><td style="text-align:left"><input id=coeficiente size="5%" type="text" pattern="\d{1,3}(\.\d{1,3})?" inputmode="decimal" maxlength="6" /><td>
    </tr>
  </table>
  <br>
  <table id=tablaPropietarios style="margin-left:auto;margin-right:auto;width:80%;font-size:20px;"></table>
</form>
  <div align=center><input id=addFinca type=button value="Enviar datos" onclick="executeAction('editar','formFinca','&destino=fincas')"/></div>
  <hr>
  <div id=divPropietarios style="display:flex;flex-direction:column;">
  Añadir propietarios<br>
  <form id=formPropietarios>
    <input id=idUsuario2 type=hidden>
    <input id=idFinca2 type=hidden>
    <input id=idPropietario2 type=hidden>
  <table id=tablaInsertarPropietarios style="margin-left:auto;margin-right:auto;">
    <tr>
      <td style="text-align:right">Propietario:<td><td style="text-align:left"><input id=nombrePropietario size="40%" maxlength=300 /></td>
    </tr>

    <tr>
      <td style="text-align:right">Dni/Nie:<td><td style="text-align:left"><input id=dniPropietario size="15%" maxlength=9 /></td>
    </tr>
    <tr style="visibility:hidden">
      <td style="text-align:right">Coeficiente:<td><td style="text-align:left"><input id=coeficientePropietario size="2%" maxlength=3 /></td>
    </tr>
  </table>
</form>
<div align=center><input id=addPropietario type=button value="Añadir propietario" onclick="executeAction('insertarPanel','formPropietarios','&destino=propietarios')"/></div>
</div>

</div>
