
Filtro:<SELECT id=selectFiltroRecibos onchange="executeAction('rellenarPanel','&destino=recibos',['selectFiltroRecibos','filtroRecibos'])">
  <OPTION value=1 >Nombre</OPTION>
  <OPTION  value=2 >Comunidad</OPTION>
  </SELECT>
 <input id=filtroRecibos type="text" oninput="executeAction('rellenarPanel','&destino=recibos',['selectFiltroRecibos','filtroRecibos'])">
<div id=listadoRecibos>
<div class=listadoRecibosItem>
</div>
</div>
