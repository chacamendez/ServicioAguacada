<div id=panelAreaClientes style="display:none" class="paneles">
  <form id=loggin method="get">
  <table id=clientesTabla cellpadding="5px">
  <tr>
  <td align="right">Usuario:</td>
  <td ><input name="username" id="username" type="text"  align="LEFT" maxlength="20"  >
  </td>
  </tr>
  <tr>
  <td align="right">Clave:
  </td>
  <br>
  <td><input name="password" id="password" type="password" maxlength="20" align="LEFT" onkeydown="enter(event)">
  </td>
  </tr>
  </table>
  <br>

  <div id=logginboton onclick="loggin()">
      <label class=labelLoggin>Enviar</label>
  </div>
  </form>
  </div>
