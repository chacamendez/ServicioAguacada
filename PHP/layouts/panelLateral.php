<div class="panelLateralElement" ind=4 onclick="executeAction('rellenarPanel','&destino=home');" ><div class="panelLateralSymbol"><i class="fa fa-home"></i></div><label class="panelLateralLabel">Home</label></div>
<div class="panelLateralElement" ind=5 onclick="executeAction('rellenarPanel','&destino=usuarios')" ><div class="panelLateralSymbol"><i class="fa fa-user"></i></div><label class="panelLateralLabel">Usuarios</label></div>
<div class="panelLateralElement" ind=6 onclick="executeAction('rellenarPanel','&destino=comunidades');" ><div class="panelLateralSymbol"><i class="fa fa-users"></i></div><label class="panelLateralLabel">Comunidades</label></div>
<div class="panelLateralElement" ind=7 onclick="executeAction('rellenarPanel','&destino=mensajes');" ><div class="panelLateralSymbol"><i class="fa fa-comment"></i></div><label class="panelLateralLabel">Mensajes</label></div>
<div class="panelLateralElement" ind=8 onclick="executeAction('rellenarPanel','&destino=archivos');" ><div class="panelLateralSymbol"><i class="fa fa-file "></i></div><label class="panelLateralLabel">Archivos</label></div>
<div class="panelLateralElement" ind=9 onclick="executeAction('rellenarPanel','&destino=correspondencias');" ><div class="panelLateralSymbol"><i class="fa fa-envelope "></i></div><label class="panelLateralLabel">Correspondencias</label></div>
<div class="panelLateralElement" ind=10 onclick="executeAction('rellenarPanel','&destino=recibos');" ><div class="panelLateralSymbol"><i class="fa fa-ticket "></i></div><label class="panelLateralLabel">Recibos</label></div>
<div class="panelLateralElement" ind=10 onclick="executeAction('rellenarPanel','&destino=empleados');" ><div class="panelLateralSymbol"><i class="fa fa-address-book"></i></div><label class="panelLateralLabel">Empleados</label></div>
<div class="panelLateralElement" ind=11 onclick="executeAction('insertarPanel','&destino=exportarXLS');" >
  <div class="panelLateralSymbol">
    <form id=formExportarXLS  action="./PHP/actions/crearXLS.php" target=downloadIFrame method="GET" enctype="multipart/form-data">
    </form>
  <i class="fa fa-arrow-circle-right"></i></div><label class="panelLateralLabel">Exportar</label>
  </div>
<div class="panelLateralElement" onclick="executeAction('loggin','&destino=logout');" ><div class="panelLateralSymbol"><i class="fa fa-sign-out "></i></div><label class="panelLateralLabel">Salir</label></div>
