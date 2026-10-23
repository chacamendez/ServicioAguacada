<div  class="panelAuxiliarInterior">

  <label class="fa fa-sign-out" style="float:right;font-size:30px" onclick="$(this).parent().parent().fadeOut();"></label>
  <form id=formDestinatarios>
  <input id=idCorrespondencia type=hidden>  
  <label>Correspondencia</label><input id=correspondencia>
  <BR>Destinatarios:
  <SELECT id=selectDestinatarios onchange="executeAction('rellenarPanel','&destino=panelAuxiliarCorrespondenciasSelect',['selectDestinatarios'])">
    <OPTION value=1 >A todos</OPTION>
    <OPTION  value=2 >Comunidad</OPTION>
    <OPTION  value=3 >Usuarios</OPTION>
  </SELECT>
  <div id=divComunidadesCorrespondencias style="display:none">
    <SELECT id=selectComunidadesCorrespondencias onchange="executeAction('rellenarPanel','&destino=panelAuxiliarCorrespondenciasSelectComunidades',['selectComunidadesCorrespondencias'])"></SELECT>
  </div>
  <div id=divUsuariosCorrespondencias style="display:none">
    <SELECT id=selectUsuariosCorrespondencias onchange="executeAction('rellenarPanel','&destino=panelAuxiliarCorrespondenciasSelectUsuarios',['selectUsuariosCorrespondencias'])"> </SELECT>
  </div>
    <div id=divListadoUsuariosCorrespondencias></div>
  </form>
  <form id=formTexto action="./PHP/actions/correspondencias.php" target=uploadIFrame  method="POST">
    <div id=divAreaTextoCorrespondencias><textarea id=textoCorrespondencia name="textoCorrespondencia"></textarea></div>
  </form>
  <div align=center><input id=insertarCorrespondencia type=button value="Enviar datos" onclick="executeAction('insertarPanel','formDestinatarios','&destino=correspondencias');" /></div>

</div>
