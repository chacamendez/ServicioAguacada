<div  class="panelAuxiliarInterior">

  <label class="fa fa-sign-out" style="float:right;font-size:30px" onclick="$(this).parent().parent().fadeOut();"></label>
  <form id=formEmpleadosPDF>
   Marca o desmarca empleados:
      <div id=divListadoEmpleados style="text-align:left;margin-left:10%;padding:10px;"></div>
      <hr>
   Elije el período:
   <table id=tablaEmpleadosPDF style="width:90%">
    <tr>
      <td style="text-align:right">Fecha inicio:</td><td style="text-align:left"><input disabled="disabled" id="inputFechaInicioPDF" style="width: 50%"><img src="./RESOURCES/cal.gif" onclick="NewCssCal('inputFechaInicioPDF')" style="cursor:pointer;"></td>
      </tr>
      <tr>
      <td style="text-align:right">Fecha fin:</td><td style="text-align:left"><input disabled="disabled" id="inputFechaFinPDF" style="width: 50%"><img src="./RESOURCES/cal.gif" onclick="NewCssCal('inputFechaFinPDF')" style="cursor:pointer;"></td>
       </tr>
  </table>
  </form>
  <div align=center><input id=insertarCorrespondencia type=button value="Enviar datos" onclick="executeAction('insertarPanel','formEmpleadosPDF','&destino=empleadosPDF');" /></div>
  <hr>
  <div id=resultadosEmpleadosPDF>
     
  </div>

</div>
