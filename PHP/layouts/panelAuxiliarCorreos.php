<div  class="panelAuxiliarInterior">
  <form id=formCorreo>
    <input id=idUsuario type=hidden>
  <label class="fa fa-sign-out" style="float:right;font-size:30px" onclick="$(this).parent().parent().parent().fadeOut();"></label>
  <table id=tablaUsuarios>
    <tr>
      <td style="text-align:right">Contacto:<td><td style="text-align:left"><input id=contacto size="40%" /><td>
    </tr>
    <tr>
      <td style="text-align:right">Tipo:<td><td style="text-align:left"><input id=tipo size="20%" /><td>
    </tr>
      <td style="text-align:right">Email:<td><td style="text-align:left"><input id=correo size="40%" /><td>
    </tr>
  </table>
</form>
  <div align=center><input id=addMail type=button value="Enviar datos" onclick="executeAction('insertarPanel','formCorreo','&destino=correos')"/></div>
</div>
