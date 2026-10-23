<div id=panelContactar style="display:none"  class="paneles">
  <div id=panelContactarInt>
  <label id=labelContacto>Nuestra oficina está abierta de lunes a viernes en horario de 8:00 a 14:00. Y además, lunes a jueves, de 4 a 6 de la tarde, exceptuando los meses de julio y agosto.
    Contacte con nosotros en el teléfono/fax (922)15 68 49, envíenos un correo a info@aguacadagesfincas.es o rellene el siguiente formulario y nos pondremos en contacto con usted lo antes posible.
</label>
    <div align="center">
  <form id=contacto method="post">
  <table id=contactoTabla cellpadding="5px">
  <tr>
  <td align="right">Nombre:
  </td>
  <td >
    <input name="contactoNombre" id="contactoNombre" type="text"  align="LEFT" maxlength="50"  value="">
  </td>
  </tr>
  <tr>
  <td align="right">Email:
  </td>

  <td>
    <input  name="contactoCorreo" id="contactoCorreo" type="text" maxlength="50" align="LEFT" value="">
  </td>
  </tr>
  <tr>
  <td align="right"><p id=pconsulta>Consulta:</p>
  </td>

  <td>
    <textarea id=contactoConsulta  name="contactoConsulta"></textarea>
    </td>
  </tr>
  </table>

  <div id=logginboton onclick="enviarcomprobar()">
    <label class=labelLoggin>Enviar</label>
  </div>
    </form>
  </div>
  <div align="center">

  <p id=loggincontactomensaje> El correo introducido es incorrecto. Por favor revíselo</p>
  </div>
</div>
</div>
