<div  class="panelAuxiliarInterior">
  <form id=formTelefono>
    <input id=idUsuario type=hidden>
  <label class="fa fa-sign-out" style="float:right;font-size:30px" onclick="$(this).parent().parent().parent().fadeOut();"></label>
  <table id=tablaUsuarios>
    <tr>
      <td style="text-align:right">Contacto:<td><td style="text-align:left"><input id=contacto size="40%" /><td>
    </tr>
    <tr>
      <td style="text-align:right">Tipo:<td><td style="text-align:left"><input id=tipo size="20%" /><td>
    </tr>
    <tr>
    <td style="text-align:right">Telefono:<td><td style="text-align:left"><input id=prefijo size="3" maxlength=5 value="34" /><input id=telefono size="13%"  maxlength=11 /><td>
    </tr>
  </table>
</form>
  <div align=center><input id=addPhone type=button value="Enviar datos" onclick="executeAction('insertarPanel','formTelefono','&destino=telefonos')"/></div>
</div>
