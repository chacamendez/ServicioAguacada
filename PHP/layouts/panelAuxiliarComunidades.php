
<div  class="panelAuxiliarInterior">
  <form id=formComunidades>
    <input id=idComunidad type=hidden>
  <label class="fa fa-sign-out" style="float:right;font-size:30px" onclick="$(this).parent().parent().parent().fadeOut();"></label>
  <table id=tablaComunidades>
    <tr>
      <td style="text-align:right">Comunidad:<td><td style="text-align:left"><input id=comunidad size=40 /><td>
    </tr>
    <tr>
      <td style="text-align:right">Dirección:<td><td style="text-align:left"><input id=direccion size=40 /><td>
    </tr>
    <tr>
      <td style="text-align:right">Dirección:<td><td style="text-align:left"><input id=direccion2 size=40 /><td>
    </tr>

  </table>
  </form>
  <div align=center><input id=crearComunidad type=button value="Enviar datos" onclick="executeAction('insertarPanel','formComunidades','&destino=comunidades')"/></div>
</div>
