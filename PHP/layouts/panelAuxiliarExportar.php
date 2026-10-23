<div  class="panelAuxiliarInterior">

  <label class="fa fa-sign-out" style="float:right;font-size:30px" onclick="$(this).parent().parent().fadeOut();"></label>
  <form id=filtroCamporExportar>
  <input id=idExportacion type=hidden>
  <label>Nombre: </label><input id=exportacion>
  <BR>Selección exportar:
  <SELECT id=selectExportar onchange="executeAction('rellenarPanel','&destino=panelAuxiliarExportarSelect',['selectExportar'])">
    <OPTION value=1 >Todo</OPTION>
    <OPTION  value=2 >Comunidad</OPTION>
  </SELECT>
  <div id=divComunidadesExportar style="display:none">
    <SELECT id=selectComunidadesExportar onchange="executeAction('rellenarPanel','&destino=panelAuxiliarExportarSelectComunidades',['selectComunidadesExportar'])"></SELECT>
  </div>
    <div id=divfiltrosExpotar style="width:80%;margin-left:auto;margin-right:auto;text-align:left">
    <input type=checkbox id=nombreCheckboxExportar name="nombreCheckExportar" value="Nombre"> <label for="html">Nombre</label>
    <input type=checkbox id=usuarioCheckboxExportar name="usuarioCheckExportar" value="Usuario"> <label for="html" style="text-align:left">Usuario</label>
    <input type=checkbox id=resetCheckboxExportar name="resetCheckExportar" value="Reset"> <label for="html">Reset</label><br>
    <input type=checkbox id=estadoCheckboxExportar name="estadoCheckExportar" value="Estado"> <label for="html">Estado</label>
    <input type=checkbox id=claveDefectoCheckboxExportar name="claveDefectoCheckExportar" value="Clave defecto"> <label for="html">Clave defecto</label><br>
    <input type=checkbox id=correspondenciaCheckboxExportar name="correspondenciaCheckExportar" value="Correspondencia"> <label for="html">Correspondencia</label>
    <input type=checkbox id=fincaCheckboxExportar name="fincaCheckExportar" value="Fincas"> <label for="html">Fincas</label><br>
    <input type=checkbox id=direccionesCheckboxExportar name="direccionesCheckExportar" value="Direcciones"> <label for="html">Direcciones</label><br>
    <input type=checkbox id=mailCheckboxExportar name="mailCheckExportar" value="Emails"> <label for="html">Emails</label>
    <input type=checkbox id=telefonosCheckboxExportar name="telefonosCheckExportar" value="Teléfonos"> <label for="html">Teléfonos</label><br>
  </div>
  </form>

  <div align=center><input id=insertarExportar type=button value="Enviar datos" onclick="executeAction('insertarPanel','filtroCamporExportar','&destino=exportar');" /></div>

</div>
