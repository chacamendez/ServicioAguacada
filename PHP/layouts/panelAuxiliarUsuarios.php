<div  class="panelAuxiliarInterior">
  <form id=formUsuarios>
    <input id=idUsuarioEditar type=hidden>
  <label class="fa fa-sign-out" style="float:right;font-size:30px" onclick="$(this).parent().parent().parent().fadeOut();"></label>
  <table id=tablaUsuarios>
    <tr>
      <td style="text-align:right">Nombre:<td><td style="text-align:left"><input id=idNombre size="40%" /></td>
    </tr>
    <tr>
      <td style="text-align:right">Comunidad:<td><td style="text-align:left"><select id=selectorComunidades></td>
    </tr>
    <tr>
      <td style="text-align:right">Finca:<td><td style="text-align:left"><input id=idPiso maxlength=50  size="40%" /></td>
    </tr>
    <tr>
      <td style="text-align:right">Usuario:<td><td style="text-align:left"><input id=idUsuario size="30%" /><input type=button value="Generar" onclick="executeAction('rellenarPanel','&destino=panelAuxiliarGenerarClaves')"></td>
    </tr>
    <tr>
      <td style="text-align:right">Clave:<td><td style="text-align:left"><input id=idClave size="40%" /><td>
    </tr>
    <tr>
      <td style="text-align:right">Telefono:<td><td style="text-align:left"><input id=idPrefijo size="2" maxlength=5 value="+34" /><input id=idTelefono size="33%"  maxlength=11 /></td>
    </tr>
    <tr>
      <td style="text-align:right">Email:<td><td style="text-align:left"><input id=idCorreo size="40%" /></td>
    </tr>
    <tr>
      <td style="text-align:right">Correspondencia:<td><td style="text-align:left"><input type=checkbox id=checkboxCorrespondencia value="Correspondencia"></td>
    </tr>
  </table>
</form>
  <div align=center><input id=crearUsuario type=button value="Enviar datos" onclick="executeAction('insertarPanel','formUsuarios','&destino=usuarios')"/></div>
</div>
