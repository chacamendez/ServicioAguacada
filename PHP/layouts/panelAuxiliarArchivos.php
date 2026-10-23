<div  class="panelAuxiliarInterior">
  <form id=formArchivos  action="./PHP/actions/upload.php" target=uploadIFrame method="POST" enctype="multipart/form-data">
  <input id=idUsuario name=idUsuario type=hidden value=0>
  <input id=idComunidad name=idComunidad type=hidden value=0>
  <label class="fa fa-sign-out" style="float:right;font-size:30px" onclick="$(this).parent().parent().parent().fadeOut();"></label>
  <table id=tablaUsuarios>
    <tr>
      <td style="text-align:right">Nombre:<td><td style="text-align:left"><input id=idNombreArchivo name=idNombreArchivo size="40%" /><td>
    </tr>
      <td style="text-align:right">Archivo:<td><td style="text-align:left"><input id="inputSubirArchivo" type="file" name="inputSubirArchivo" onchange="executeAction('rellenarPanel','formArchivos','&destino=panelAuxiliarArchivosNombre')"><td>
    </tr>
  </table>
</form>
  <div align=center><input id=uploadFile type=button value="Enviar datos" onclick="executeAction('insertarPanel','formArchivos','&destino=archivos')"/></div>
  <hr>
  <div id=divListadoArchivos style="overflow:auto;height:50%"></div>
</div>
