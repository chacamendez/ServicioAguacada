<BR>
Filtro:<SELECT id=selectFiltroArchivos onchange="executeAction('rellenarPanel','&destino=archivos',['selectFiltroArchivos','filtroArchivos'])">
  <OPTION value=1 >Nombre</OPTION>
  <OPTION  value=2 >Comunidad</OPTION>
  <OPTION  value=3 >Usuario</OPTION>
</SELECT>
 <input id=filtroArchivos type="text" oninput="executeAction('rellenarPanel','&destino=archivos',['selectFiltroArchivos','filtroArchivos'])">
<div id=listadoArchivos>
<div class=listadoArchivosItem>
  Nombre archivo.extension opciones del archivo
</div>
</div>
