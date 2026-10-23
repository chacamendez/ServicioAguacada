<div  class="panelAuxiliarInterior">
  <label class="fa fa-sign-out" style="float:right;font-size:30px" onclick="$(this).parent().parent().fadeOut();"></label>
  <table id=tablaListadoRecibos style="margin-left:auto;margin-right:auto;width:80%;font-size:18px;"></table>


    <br>
    <br>
  <hr>
  <form id=formRecibos>
    <input id=idUsuario type=hidden>
    <input id=idRecibo type=hidden>
  <table id=tablaRecibos>
    <tr>
      <td style="text-align:right">Nombre:</td><td style="text-align:left"><input id=nombreRecibo size="70%" /></td>
    </tr>
    <tr>
      <td style="text-align:right">Concepto:</td><td style="text-align:left"><input id=conceptoRecibo size="70%" /></td>
    </tr>
    <tr>
      <td style="text-align:right">Cantidad:</td><td style="text-align:left"><input id=cantidadRecibo size=5 maxlength=7 /></td>
    </tr>
    <tr>
      <td style="text-align:right">Fecha:</td><td style="text-align:left"><input disabled="disabled" id="fechaRecibo" style="width: 25%"><img src="./RESOURCES/cal.gif" onclick="NewCssCal('fechaRecibo')" style="cursor:pointer;"></td>
    </tr>
    <tr>
      <td style="text-align:right">Finca:</td><td style="text-align:left"><select id=fincaRecibo></secect></td>
    </tr>
  </table>

</form>
  <div align=center><input id=addRecibo type=button value="Enviar datos" onclick="executeAction('insertarPanel','formRecibos','&destino=panelAuxiliarRecibos')"/></div>
</div>
