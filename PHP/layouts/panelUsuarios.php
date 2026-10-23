
Filtro: <input id=filtroUsuarios type="text" oninput="executeAction('rellenarPanel','&destino=usuarios',['filtroUsuarios'])">
<form id=formExportar  action="./PHP/actions/exportar.php" target=downloadIFrame method="GET" enctype="multipart/form-data">
  <input id=idUsuarioExportar name=idUsuarioExportar type=hidden>
</form>
<div id=listadoUsuarios>
<div class=listado>
  Juan Alberto Mendez Alonso
  <div class="iconos">
  <img  src="./RESOURCES/phone.png" onclick="window.location.href='tel:629077064';" />
  <img  src="./RESOURCES/whatsapp.png" onclick="window.location.href='whatsapp://send?phone=34629077064'" />
  <img  src="./RESOURCES/mail.png" onclick="window.location.href = 'mailto:info@aguacadagesfincas.es';" />
  <label class="fa fa-pencil-square-o buttons" style="background-color:#e3600a"></label>
  <label class="fa fa-history buttons" style="background-color:#a90c51"></label>
  <label class="fa fa-upload buttons" style="background-color:#a90c51"></label>
  <label class="fa fa-compress buttons" target=usuarios1 style="background-color:#274e73" onclick="expande(this);" ></label>


  </div>
  <div class="compress" id=usuarios1>

  <br>Comunidad: Pues esa que tu sabes
    <br> Piso: 2B
  <br>Usuario:pues uno
  <br>Clave defecto: pues username
  <br>Estado de la cuenta:
 </div>
</div>
</listadoUsuarios>
