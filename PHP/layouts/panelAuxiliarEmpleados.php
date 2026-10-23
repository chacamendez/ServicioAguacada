<div  class="panelAuxiliarInterior">
  <form id=formEmpleados>
    <input id=idEmpleadoEditar type=hidden>
  <label class="fa fa-sign-out" style="float:right;font-size:30px" onclick="$(this).parent().parent().parent().fadeOut();"></label>
  <table id=tablaEmpleados>
    <tr>
      <td style="text-align:right">Nombre:<td><td style="text-align:left"><input id=idNombre size="40%" maxlength="50"  /></td>
    </tr>
    <tr>
      <td style="text-align:right">Empleado:<td><td style="text-align:left"><input id=idEmpleado size="30%" maxlength="20"  /></td>
    </tr>
    <tr>
      <td style="text-align:right">Clave:<td><td style="text-align:left"><input id=idClave size="30%" maxlength="20"  /><td>
    </tr>
    <tr>
      <td style="text-align:right">DNI:<td><td style="text-align:left"><input id=idDNI size="20%" maxlength="10" /></td>
    </tr>
    <tr>
      <td style="text-align:right">Reset:<td><td style="text-align:left"><input id=idCheckEmpleados type=checkbox value="Reset" /></td>
    </tr>
  </table>
</form>
  <div align=center><input id=crearEmpleado type=button value="Enviar datos" onclick="executeAction('insertarPanel','formEmpleados','&destino=panelAuxiliarEmpleados')"/></div>
</div>
