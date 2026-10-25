<?php
session_start();
if(Session::isLogged()) {
  $JS="";
  $destino=$_GET['destino'];
  switch ($destino){
  case "home":
    if (Session::allowed()){
      $html='';
      incluir('mensajes');
      $query="SELECT * FROM mensajes,usuarios WHERE mensajes.idUsuario=usuarios.id AND leido=0 AND usuarios.idRol=0 ORDER BY fechaHora DESC";
      $mensajes=mensajes::getAll($query);
      if ($mensajes){
        foreach ($mensajes as $mensaje) {
          $html.="<div class=eventosItem onClick=\"executeAction(\'prepararPanel\',\'&destino=panelAuxiliarMensajes&idUsuario={$mensaje['idUsuario']}\')\"> Tienes un nuevo mensaje de {$mensaje['nombre']}</div>";
        }
      }
      incluir('historial');
      $fecha=Strings::fechaSQL();
      $query="SELECT * FROM historial WHERE activo=1 AND alerta<='$fecha'";
      $eventos=historial::getAll($query);
      if ($eventos){
        foreach ($eventos as $evento) {
          if ($evento['idUsuario']!=0){
            incluir('usuarios');
            $usuario=classInvoker::nc('usuarios',intVal($evento['idUsuario']));
            $origen="{$usuario->nombre}({$usuario->usuario})";
          }
          if ($evento['idComunidad']!=0){
            incluir('comunidades');
            if (idExiste('comunidades',$evento['idComunidad'])){
              $comunidad=classInvoker::nc('comunidades',intVal($evento['idComunidad']));
              $origen="{$comunidad->comunidad}";
            }
            else {
              $historial=classInvoker::nc('historial',intval($evento['id']));
              $historial->delete();
            }
          }
          $html.="<div class=eventosItem onClick=\"executeAction(\'prepararPanel\',\'&destino=panelAuxiliarHistorial&idComunidad={$evento['idComunidad']}&idUsuario={$evento['idUsuario']}\')\"> Tienes una nueva alerta de historial de $origen</div>";
        }
      }
      $JS.="$('#listadoEventos').html('$html');";
      $JS.="$('#panelAdd').off('click');$('#panelAdd').click(function () {executeAction('insertarPanel','&destino=home')});slide(4);";

    }
    else{
      $html='';
      incluir('mensajes');
      $query="SELECT nombre,usuario,idUsuario FROM mensajes,usuarios WHERE usuarios.id=mensajes.idusuario AND leido=0 AND destino={$_SESSION['idUsuario']} ORDER BY fechaHora DESC";
      $mensajes=mensajes::getAll($query);
      if ($mensajes){
        foreach ($mensajes as $mensaje) {
          $html.="<div class=listado>{$mensaje['nombre']}({$mensaje['usuario']})".
          "<div class=iconos><label class=\"fa fa-comment buttons\" style=\"background-color:#e8be16\" onclick=\"executeAction(\'prepararPanel\',\'&destino=mensajes\')\"></label></div></div>";
        }
      }
      $JS.="$('#listadoEventos').html('$html');";
      $JS.="$('#panelAdd').off('click');$('#panelAdd').click(function () {executeAction('insertarPanel','&destino=home')});slide(4);";

    }
  break;
  case "usuarios":
  if (Session::allowed()){
    if (!empty($_GET['filtroUsuarios'])) $filtro=$_GET['filtroUsuarios'];
    else $filtro='';
    if (!empty($_GET['searchId'])) {
      $searchId=$_GET['searchId'];
      if ($searchId!=$_SESSION['searchId']) return;
      else $search=new Search($searchId);
    }
    else $search= new Search();
    incluir('usuarios');
     if (isset($_GET["limit"])) {
       $limit=$_GET["limit"];
       $offset=$_GET["offset"];
       if ($filtro) $query="SELECT * FROM usuarios WHERE nombre like '%$filtro%' AND idRol=0 AND estado <> 'eliminado' ORDER BY nombre DESC LIMIT $offset,$limit";
       else $query="SELECT * FROM usuarios WHERE idRol=0 AND estado <> 'eliminado' ORDER BY nombre DESC LIMIT $offset,$limit";
     }
     else {
       $JS.="$('#listadoUsuarios').html('');";
       $limit=50;
       if ($filtro) $query="SELECT * FROM usuarios WHERE nombre like '%$filtro%' AND idRol=0 AND estado <> 'eliminado' ORDER BY nombre DESC LIMIT $limit";
       else $query="SELECT * FROM usuarios WHERE idRol=0 AND estado <> 'eliminado' ORDER BY nombre DESC LIMIT $limit";
     }
     $usuarios=usuarios::getAll($query);
     if ($usuarios) {
       incluir('correos');
       incluir('telefonos');
       foreach ($usuarios as $usuario) {
         $usuarioExt=classInvoker::nc('usuarios',intval($usuario['id']),true);
         $correo=$usuarioExt->correoDefecto();
         $telefono=$usuarioExt->telefonoDefecto();
         $comunidad=$usuarioExt->getComunidad();
         $direccionPostal=$usuarioExt->getDireccionPostal();
         $fincaDefecto=$usuarioExt->fincaDefecto();
         $stringExportartUsuario=$usuarioExt->exportarUsuario();

         $html="<hr> <label class=\"fa fa-plus-circle buttons2\" onclick=\"executeAction(\'prepararPanel\',\'&destino=panelAuxiliarFincas&idUsuario={$usuario['id']}\')\">".
         "</label> {$usuarioExt->toHtml4()}".
         "<br>$telefono<label class=\"fa fa-plus-circle buttons2\" onclick=\"executeAction(\'prepararPanel\',\'&destino=panelAuxiliarTelefonos&idUsuario={$usuario['id']}\')\"></label>{$usuarioExt->toHtml()}".
         "<br>$correo<label class=\"fa fa-plus-circle buttons2\" onclick=\"executeAction(\'prepararPanel\',\'&destino=panelAuxiliarCorreos&idUsuario={$usuario['id']}\')\"></label>{$usuarioExt->toHtml2()}".
         "<br>$direccionPostal<label class=\"fa fa-plus-circle buttons2\" onclick=\"executeAction(\'prepararPanel\',\'&destino=panelAuxiliarDirecciones&idUsuario={$usuario['id']}\')\"></label>{$usuarioExt->toHtml3()}";
         $JS.="$('#listadoUsuarios').append('<div  class=listado><div class=nombrePropietarios>{$usuario['nombre']}</div> <div class=\"iconos\"> <img  src=\"./RESOURCES/phone.png\" onclick=\"window.location.href=\'tel:{$telefono}\'\" /> <img  src=\"./RESOURCES/whatsapp.png\" onclick=\"window.open(\'https://web.whatsapp.com/send?phone={$telefono}\',\'whatsApp\')\" /> <img  src=\"./RESOURCES/mail.png\" onclick=\"window.open(\'mailto:{$usuarioExt->toMailTo()}\');\" /><label class=\"fa fa-comment buttons\" style=\"background-color:#e8be16\" onclick=\"executeAction(\'prepararPanel\',\'&destino=panelAuxiliarMensajes&idUsuario={$usuario['id']}\')\" ></label><label class=\"fa fa-pencil buttons\" style=\"background-color:#e3600a\" onclick=\"executeAction(\'prepararPanel\',\'&destino=panelAuxiliarUsuariosEditar&idUsuario={$usuario['id']}\')\" ></label><label class=\"fa fa-upload buttons\" style=\"background-color:#0ca2a9\" onclick=\"executeAction(\'prepararPanel\',\'&destino=panelAuxiliarArchivos&idUsuario={$usuario['id']}\')\"></label><label class=\"fa fa-ticket buttons\" style=\"background-color:#1f7124\" onclick=\"executeAction(\'prepararPanel\',\'&destino=panelAuxiliarRecibos&idUsuario={$usuario['id']}&idComunidad={$usuario['idComunidad']}\')\"></label><label class=\"fa fa-history buttons\" style=\"background-color:#a90c51\" onclick=\"executeAction(\'prepararPanel\',\'&destino=panelAuxiliarHistorial&idUsuario={$usuario['id']}&idComunidad=0\')\"></label><label class=\"fa fa-trash buttons\" style=\"background-color:#961e18\" onclick=\"executeAction(\'eliminar\',\'&destino=usuario&idUsuario={$usuario['id']}\')\"></label><label class=\"fa fa-compress buttons\" target=usuario{$usuario['id']} style=\"background-color:#274e73\" onclick=\"expande(this);\" ></label></div><div id=usuario{$usuario['id']}  class=\"compress\" ><br>Comunidad: $comunidad <br>Usuario: {$usuario['usuario']} $stringExportartUsuario <br>Clave defecto: {$usuario['claveDefecto']}<br>Estado cuenta: {$usuario['estado']}<br><input id=inputReset onclick=\"executeAction(\'editar\',\'&destino=usuariosReset&idUsuario={$usuario['id']}\')\" type=button value=\"Reset clave\">".
         "$html</div></div> ');";
       }

       if (isset($offset)) $offset+=$limit;
       else $offset=$limit;
       if ($filtro) {
         $JS.="executeAction('rellenarPanel','&destino=usuarios&offset=$offset&limit=$limit&filtroUsuarios=$filtro&searchId={$search->getId()}');";
       }
       else $JS.="executeAction('rellenarPanel','&destino=usuarios&offset=$offset&limit=$limit&searchId={$search->getId()}');";
       if ($search->isState('started')){
         $search->setState('processing');
         $JS.="$('#panelAdd').off('click');$('#panelAdd').click(function () {executeAction('prepararPanel','&destino=panelAuxiliarUsuarios')});slide(5);";
       }

     }
     else {
       $search->setState('completed');
     }
  }

  break;
  case "mensajes":
  $html='';
  incluir('mensajes');
  $query="select idUsuario,usuario,nombre from mensajes,usuarios where usuarios.id=mensajes.idUsuario AND idRol=0 GROUP BY idUsuario";
  $mensajes=mensajes::getAll($query);
  $idUsuarios=[];
  if ($mensajes){
    foreach ($mensajes as $mensaje) {
      if (idNotPresent($idUsuarios,$mensaje['idUsuario'])){
        $idUsuarios[]=$mensaje['idUsuario'];
        $html.="<div class=listado>{$mensaje['nombre']}({$mensaje['usuario']})".
        "<div class=iconos><label class=\"fa fa-comment buttons\" style=\"background-color:#e8be16\" onclick=\"executeAction(\'prepararPanel\',\'&destino=panelAuxiliarMensajes&idUsuario={$mensaje['idUsuario']}\')\"></label></div></div>";
      }
    }
  }

  $query="select distinct idUsuario,usuario,nombre,destino from mensajes,usuarios where usuarios.id=mensajes.idUsuario AND idRol=1;";
  $mensajes=mensajes::getAll($query);

  if ($mensajes){
    foreach ($mensajes as $mensaje) {
      $usuario=classInvoker::nc('usuarios',intval($mensaje['destino']));
        if (idNotPresent($idUsuarios,$mensaje['destino'])){
          $idUsuarios[]=$mensaje['idUsuario'];
          $html.="<div class=listado>{$usuario->nombre}({$usuario->usuario})".
          "<div class=iconos><label class=\"fa fa-comment buttons\" style=\"background-color:#e8be16\" onclick=\"executeAction(\'prepararPanel\',\'&destino=panelAuxiliarMensajes&idUsuario={$usuario->id}\')\"></label></div></div>";
        }
    }
  }
  $JS.="$('#listadoMensajes').html('$html');";
  $JS.="$('#panelAdd').off('click');slide(7);";
  break;
  case "archivos":
  incluir('archivos');
  if (Session::allowed()){
    if (!empty($_GET['filtroArchivos'])) $filtro=$_GET['filtroArchivos'];
    else $filtro=false;
    if (!empty($_GET['selectFiltroArchivos'])) $option=$_GET['selectFiltroArchivos'];
    else $option="1";
   if (isset($_GET["limit"])) {
       $limit=$_GET["limit"];
       $offset=$_GET["offset"];
       if ($filtro) {
         switch ($option){
           case "1":
            $query="SELECT idUsuario,archivos.idComunidad,archivo,extension,archivos.id,numDescargas,fecha FROM archivos WHERE archivo like '%$filtro%' ORDER BY fecha DESC LIMIT $offset,$limit";
           break;

           case "2":
            $query="SELECT idUsuario,archivos.idComunidad,archivo,extension,archivos.id,numDescargas,fecha FROM archivos,comunidades  WHERE archivos.idComunidad=comunidades.id AND comunidad like '%$filtro%' ORDER BY comunidad,fecha DESC LIMIT $offset,$limit";

           break;
           case "3":
            $query="SELECT idUsuario,archivos.idComunidad,archivo,extension,archivos.id,numDescargas,fecha FROM archivos,usuarios WHERE archivos.idUsuario=usuarios.id AND usuarios.nombre like '%$filtro%' ORDER BY usuarios.nombre,fecha DESC LIMIT $offset,$limit";
           break;
         }
       }
       else {
         switch ($option){
           case "1":
            $query="SELECT idUsuario,archivos.idComunidad,archivo,extension,archivos.id,numDescargas,fecha FROM archivos ORDER BY fecha DESC LIMIT $offset,$limit";
           break;

           case "2":
            $query="SELECT idUsuario,archivos.idComunidad,archivo,extension,archivos.id,numDescargas,fecha FROM archivos,comunidades  WHERE archivos.idComunidad=comunidades.id ORDER BY comunidad,fecha DESC LIMIT $offset,$limit";

           break;
           case "3":
            $query="SELECT idUsuario,archivos.idComunidad,archivo,extension,archivos.id,numDescargas,fecha FROM archivos,usuarios WHERE archivos.idUsuario=usuarios.id ORDER BY usuarios.nombre,fecha DESC LIMIT $offset,$limit";
           break;
         }
       }
     }
     else {
       $JS.="$('#listadoArchivos').html('');";
       $limit=5;
      if ($filtro) {
         switch ($option){
           case "1":
            $query="SELECT idUsuario,archivos.idComunidad,archivo,extension,archivos.id,numDescargas,fecha FROM archivos WHERE archivo like '%$filtro%' ORDER BY fecha DESC LIMIT $limit";
           break;

           case "2":
            $query="SELECT idUsuario,archivos.idComunidad,archivo,extension,archivos.id,numDescargas,fecha FROM archivos,comunidades  WHERE archivos.idComunidad=comunidades.id AND comunidad like '%$filtro%' ORDER BY comunidad,fecha DESC LIMIT $limit";

           break;
           case "3":
            $query="SELECT idUsuario,archivos.idComunidad,archivo,extension,archivos.id,numDescargas,fecha FROM archivos,usuarios WHERE archivos.idUsuario=usuarios.id AND usuarios.nombre like '%$filtro%' ORDER BY usuarios.nombre,fecha DESC LIMIT $limit";
           break;
         }
       }
       else {
         switch ($option){
           case "1":
            $query="SELECT idUsuario,archivos.idComunidad,archivo,extension,archivos.id,numDescargas,fecha FROM archivos ORDER BY fecha DESC LIMIT $limit";
           break;

           case "2":
            $query="SELECT idUsuario,archivos.idComunidad,archivo,extension,archivos.id,numDescargas,fecha FROM archivos,comunidades  WHERE archivos.idComunidad=comunidades.id ORDER BY comunidad,fecha DESC LIMIT $limit";

           break;
           case "3":
           $query="SELECT idUsuario,archivos.idComunidad,archivo,extension,archivos.id,numDescargas,fecha FROM archivos,usuarios WHERE archivos.idUsuario=usuarios.id ORDER BY usuarios.nombre,fecha DESC LIMIT $limit";
           break;
         }

       }
     }
     $archivos=archivos::getAll($query);
     if ($archivos) {
		
       foreach ($archivos as $archivo) {
         $fecha=Strings::fecha($archivo['fecha']);
         $archivoO=classInvoker::nc('archivos',intval($archivo['id']),true);
         if ($archivo['idUsuario']!=0) {
           incluir('usuarios');
           $usuario=classInvoker::nc('usuarios',intVal($archivo['idUsuario']));
           incluir('comunidades');
           $comunidad=classInvoker::nc('comunidades',intVal($usuario->idComunidad));
           $destino="|{$comunidad->comunidad}| |{$usuario->nombre}($usuario->usuario)|";

         }
         else {
           incluir('comunidades');
           $comunidad=classInvoker::nc('comunidades',intVal($archivo['idComunidad']));
           $destino="|{$comunidad->comunidad}|";
         }
         $htmlHitorial=$archivoO->historialToHtml();
         $JS.="$('#listadoArchivos').append('<div  class=listado>{$archivo['archivo']}.{$archivo['extension']} $destino <div class=\"iconos\">".
         "<label class=\"fa fa-download buttons\" style=\"background-color:#0ca2a9\" onclick=\"document.getElementById(\'formDownloadFile{$archivo['id']}\').submit()\"></label>".
         "<label class=\"fa fa-trash buttons\" style=\"background-color:#961e18\" onclick=\"executeAction(\'eliminar\',\'&destino=archivos&idArchivo={$archivo['id']}&idComunidad=0&idUsuario=0&fromArchivos=true\')\"></label>".
         "<label class=\"fa fa-compress buttons\" target=archivo{$archivo['id']} style=\"background-color:#274e73\" onclick=\"expande(this);\" ></label>".
         "</div><div id=archivo{$archivo['id']}  class=\"compress\" ><br>Subido para $destino el $fecha<br>Descargado {$archivo['numDescargas']} veces.<br>$htmlHitorial <form id=formDownloadFile{$archivo['id']} target=downloadIFrame action=\"./PHP/actions/download.php\" method=GET><input id=idArchivo name=idArchivo value={$archivo['id']} type=hidden></form></div></div> ');";
       }
       if (isset($offset)) $offset+=$limit;
       else $offset=$limit;
       if ($filtro) $JS.="executeAction('rellenarPanel','&destino=archivos&offset=$offset&limit=$limit&filtroArchivos=$filtro&selectFiltroArchivos=$option');";
       else $JS.="executeAction('rellenarPanel','&destino=archivos&offset=$offset&limit=$limit&selectFiltroArchivos=$option&selectFiltroArchivos=$option');";
     }
     else {
       $JS.="$('#panelAdd').off('click');slide(8);";

     }
  }
  else{
    $JS.="$('#panelAuxiliar').fadeOut('slow');";
    if (!empty($_GET['filtroArchivos'])) $filtro=$_GET['filtroArchivos'];
    else $filtro=false;
    incluir('archivos');
   if (isset($_GET["limit"])) {
       $limit=$_GET["limit"];
       $offset=$_GET["offset"];

       if ($filtro) $query="SELECT * FROM archivos WHERE archivo like '%$filtro%' AND (idComunidad={$_SESSION['idComunidad']} OR idUsuario={$_SESSION['idUsuario']}) ORDER BY fecha DESC LIMIT $offset,$limit";
       else $query="SELECT * FROM archivos WHERE (idComunidad={$_SESSION['idComunidad']} OR idUsuario={$_SESSION['idUsuario']}) ORDER BY fecha DESC LIMIT $offset,$limit";
     }
     else {
       $JS.="$('#listadoArchivos').html('');";
       $limit=5;
       if ($filtro) $query="SELECT * FROM archivos WHERE archivo like '%$filtro%' AND (idComunidad={$_SESSION['idComunidad']} OR idUsuario={$_SESSION['idUsuario']}) ORDER BY fecha DESC LIMIT $limit";
       else $query="SELECT * FROM archivos WHERE (idComunidad={$_SESSION['idComunidad']} OR idUsuario={$_SESSION['idUsuario']}) ORDER BY fecha DESC LIMIT $limit";
     }
     $archivos=archivos::getAll($query);
     if ($archivos) {
       foreach ($archivos as $archivo) {
         $fecha=Strings::fecha($archivo['fecha']);
         if ($archivo['idUsuario']!=0) {
           incluir('usuarios');
           $usuario=classInvoker::nc('usuarios',intVal($archivo['idUsuario']));
           $destino="{$usuario->nombre}($usuario->usuario)";
         }
         else {
           incluir('comunidades');
           $comunidad=classInvoker::nc('comunidades',intVal($archivo['idComunidad']));
           $destino="{$comunidad->comunidad}";
         }
         $JS.="$('#listadoArchivos').append('<div  class=listado>{$archivo['archivo']}.{$archivo['extension']} <div class=\"iconos\">".
         "<label class=\"fa fa-download buttons\" style=\"background-color:#0ca2a9\" onclick=\"document.getElementById(\'formDownloadFile{$archivo['id']}\').submit()\"></label>".
         "</div><div id=archivo{$archivo['id']}  class=\"compress\" ><br>Subido para $destino el $fecha<br>Descargado {$archivo['numDescargas']} veces.<form id=formDownloadFile{$archivo['id']} target=downloadIFrame action=\"./PHP/actions/download.php\" method=GET><input id=idArchivo name=idArchivo value={$archivo['id']} type=hidden></form></div></div> ');";
       }
       if (isset($offset)) $offset+=$limit;
       else $offset=$limit;
       if ($filtro) $JS.="executeAction('rellenarPanel','&destino=archivos&offset=$offset&limit=$limit&filtroArchivos=$filtro');";
       else $JS.="executeAction('rellenarPanel','&destino=archivos&offset=$offset&limit=$limit');";
     }
     else {
       $JS.="$('#panelAdd').off('click');slide(8);";

     }

  }
  break;
  case "panelArchivosUsuarios":
  $JS.="$('#panelAuxiliar').fadeOut('slow');";
  if (!empty($_GET['filtroArchivos'])) $filtro=$_GET['filtroArchivos'];
  else $filtro=false;
  incluir('archivos');
 if (isset($_GET["limit"])) {
     $limit=$_GET["limit"];
     $offset=$_GET["offset"];

     if ($filtro) $query="SELECT * FROM archivos WHERE archivo like '%$filtro%' AND (idComunidad={$_SESSION['idComunidad']} OR idUsuario={$_SESSION['idUsuario']}) ORDER BY fecha DESC LIMIT $offset,$limit";
     else $query="SELECT * FROM archivos WHERE (idComunidad={$_SESSION['idComunidad']} OR idUsuario={$_SESSION['idUsuario']}) ORDER BY fecha DESC LIMIT $offset,$limit";
   }
   else {
     $JS.="$('#listadoArchivos').html('');";
     $limit=5;
     if ($filtro) $query="SELECT * FROM archivos WHERE archivo like '%$filtro%' AND (idComunidad={$_SESSION['idComunidad']} OR idUsuario={$_SESSION['idUsuario']}) ORDER BY fecha DESC LIMIT $limit";
     else $query="SELECT * FROM archivos WHERE (idComunidad={$_SESSION['idComunidad']} OR idUsuario={$_SESSION['idUsuario']}) ORDER BY fecha DESC LIMIT $limit";
   }
   $archivos=archivos::getAll($query);
   if ($archivos) {
     foreach ($archivos as $archivo) {
       $fecha=Strings::fecha($archivo['fecha']);
       if ($archivo['idUsuario']!=0) {
         incluir('usuarios');
         $usuario=classInvoker::nc('usuarios',intVal($archivo['idUsuario']));
         $destino="{$usuario->nombre}($usuario->usuario)";
       }
       else {
         incluir('comunidades');
         $comunidad=classInvoker::nc('comunidades',intVal($archivo['idComunidad']));
         $destino="{$comunidad->comunidad}";
       }
       $JS.="$('#listadoArchivos').append('<div  class=listado>{$archivo['archivo']}.{$archivo['extension']} <div class=\"iconos\">".
       "<label class=\"fa fa-download buttons\" style=\"background-color:#0ca2a9\" onclick=\"document.getElementById(\'formDownloadFile{$archivo['id']}\').submit()\"></label>".
       "</div><div id=archivo{$archivo['id']}  class=\"compress\" ><br>Subido para $destino el $fecha<br>Descargado {$archivo['numDescargas']} veces.<form id=formDownloadFile{$archivo['id']} target=downloadIFrame action=\"./PHP/actions/download.php\" method=GET><input id=idArchivo name=idArchivo value={$archivo['id']} type=hidden></form></div></div> ');";
     }
     if (isset($offset)) $offset+=$limit;
     else $offset=$limit;
     if ($filtro) $JS.="executeAction('rellenarPanel','&destino=archivos&offset=$offset&limit=$limit&filtroArchivos=$filtro');";
     else $JS.="executeAction('rellenarPanel','&destino=archivos&offset=$offset&limit=$limit');";
   }
   else {
     $JS.="$('#panelAdd').off('click');slide(8);";

   }
  break;

  case "panelAuxiliarUsuarios":
    if (Session::allowed()){
      incluir('comunidades');
      $comunidades=comunidades::getAll("SELECT id,comunidad FROM comunidades ORDER BY comunidad DESC");
      if ($comunidades){
        foreach ($comunidades as $comunidad) {
          $JS.="$('#selectorComunidades').append('<option value={$comunidad['id']}>{$comunidad['comunidad']}</option>');";
        }
      }
      $JS.="$('#idUsuarioEditar').val('');$('#panelAuxiliar').fadeIn('slow');";
    }
  break;
  case "panelAuxiliarUsuariosClientes":
    $idUsuario=$_GET["idUsuario"];
    $usuarioExt=classInvoker::nc('usuarios',intval($idUsuario),true);
    $prefijo=$usuarioExt->telefonoDefecto(1);
    $telefono=$usuarioExt->telefonoDefecto(2);
    $correo=$usuarioExt->correoDefecto();
    $JS.="$('#idUsuarioEditar').val('$idUsuario');$('#idNombre').val('{$usuarioExt->nombre}');$('#idPrefijo').val('$prefijo');".
    "$('#idUsuario').val('{$usuarioExt->usuario}');".
    "$('#idTelefono').val('$telefono');$('#idCorreo').val('$correo');$('#labelUsuarios').css('display','none');".
    "$('#checkboxCorrespondencia').prop('checked', '{$usuarioExt->correspondencia}');$('#panelAuxiliar').fadeIn('slow');";
  break;
  case 'panelAuxiliarMensajes':
  if (Session::allowed()){
      $idUsuario=$_GET["idUsuario"];
      $query="SELECT * from mensajes WHERE idUsuario=$idUsuario OR destino=$idUsuario ORDER BY fechaHora";
      incluir('mensajes');
      $mensajes=mensajes::getAll($query);
      
      // Actualización de lectura migrada a PDO con parámetros seguros
      Database::ExecuteQuery("UPDATE mensajes SET leido = 1 WHERE idUsuario = ?", false, [$idUsuario]);
      
      $JS.="$('#divMensajesVisor').html('');";
      $i=0;
      if ($mensajes){
        foreach ($mensajes as $mensaje) {
          $i++;
          if ($mensaje['idUsuario']==$_SESSION['idUsuario'])
           $JS.="$('#divMensajesVisor').append('<div class=\'misMensajes\'><div class=divTopMensajes>{$_SESSION['usuario']}</div><div class=divBodyMensajes>{$mensaje['mensaje']}</div><div class=divBottomMensajes>{$mensaje['fechaHora']}</div></div');";
          else
           $JS.="$('#divMensajesVisor').append('<div class=\'tusMensajes\'><div class=divTopMensajes>{$mensaje['remitente']}</div><div class=divBodyMensajes>{$mensaje['mensaje']}</div><div class=divBottomMensajes>{$mensaje['fechaHora']}</div></div');";
        }
      }
      $JS.="$('#panelAuxiliar').fadeIn('slow');$('#inputMensajesEnviar').focus();$('#idUsuario').val('$idUsuario');";
      $_SESSION['mensajes']['cuenta']=$i;
      $_SESSION['mensajes']['chat']=1;
      $_SESSION['mensajes']['idUsuario']=$idUsuario;
      $JS.="setTimeout(function(){executeAction('rellenarPanel','&destino=panelAuxiliarMensajesUpdate')},1000);";
  }
  else{
    $idUsuario=$_SESSION['idUsuario'];
    $query="SELECT * from mensajes WHERE idUsuario=$idUsuario OR destino=$idUsuario ORDER BY fechaHora";
    incluir('mensajes');
    $mensajes=mensajes::getAll($query);
    
    // Actualización de lectura migrada a PDO con parámetros seguros
    Database::ExecuteQuery("UPDATE mensajes SET leido = 1 WHERE destino = ?", false, [$idUsuario]);
    
    $JS.="$('#divMensajesVisor').html('');";
    $i=0;
    if ($mensajes){
      foreach ($mensajes as $mensaje) {
        $i++;
        if ($mensaje['idUsuario']==$_SESSION['idUsuario'])
         $JS.="$('#divMensajesVisor').append('<div class=\'misMensajes\'><div class=divTopMensajes>{$_SESSION['usuario']}</div><div class=divBodyMensajes>{$mensaje['mensaje']}</div><div class=divBottomMensajes>{$mensaje['fechaHora']}</div></div');";
        else
         $JS.="$('#divMensajesVisor').append('<div class=\'tusMensajes\'><div class=divTopMensajes>{$mensaje['remitente']}</div><div class=divBodyMensajes>{$mensaje['mensaje']}</div><div class=divBottomMensajes>{$mensaje['fechaHora']}</div></div');";
      }
    }
    $JS.="$('#panelAuxiliar').fadeIn('slow');$('#inputMensajesEnviar').focus();$('#idUsuario').val('$idUsuario');";
    $_SESSION['mensajes']['cuenta']=$i;
    $_SESSION['mensajes']['chat']=1;
    $_SESSION['mensajes']['idUsuario']=$idUsuario;
    $JS.="setTimeout(function(){executeAction('rellenarPanel','&destino=panelAuxiliarMensajesUpdate')},1000);";
  }
  break;
  case "panelAuxiliarMensajesUpdate":
    if ($_SESSION['mensajes']['chat']==0) exit;
    else{
      $offset=$_SESSION['mensajes']['cuenta'];
      $idUsuario=$_SESSION['mensajes']['idUsuario'];
      $query="SELECT * from mensajes WHERE idUsuario=$idUsuario OR destino=$idUsuario ORDER BY fechaHora LIMIT $offset,18446744073709551615;";
      incluir('mensajes');
      $mensajes=mensajes::getAll($query);
      if ($mensajes){
        foreach ($mensajes as $mensaje) {
          $offset++;

          if ($mensaje['idUsuario']==$_SESSION['idUsuario'])
            $JS.="$('#divMensajesVisor').append('<div class=\'misMensajes\'><div class=divTopMensajes>{$_SESSION['usuario']}</div><div class=divBodyMensajes>{$mensaje['mensaje']}</div><div class=divBottomMensajes>{$mensaje['fechaHora']}</div></div');";
          else
            $JS.="$('#divMensajesVisor').append('<div class=\'tusMensajes\'><div class=divTopMensajes>{$_SESSION['usuario']}</div><div class=divBodyMensajes>{$mensaje['mensaje']}</div><div class=divBottomMensajes>{$mensaje['fechaHora']}</div></div');";
          $JS.="$('#divMensajesVisor').scrollTop($('#divMensajesVisor')[0].scrollHeight);";
        }
      }
      $_SESSION['mensajes']['cuenta']=$offset;
      $JS.="setTimeout(function(){executeAction('rellenarPanel','&destino=panelAuxiliarMensajesUpdate')},1000);";

    }
  break;
  case "panelAuxiliarMensajesStop":
      $_SESSION['mensajes']['chat']=0;
  break;
  case 'panelAuxiliarGenerarClaves':
    require_once('./core/ext/challengeclass.php');
    $challenge=new ChallengeGenerator(false);
    $usuario=$challenge->getRandomString(8);
    $clave=$challenge->getRandomString(8);
    $JS.="$('#idUsuario').val('$usuario');$('#idClave').val('$clave')";
  break;
  case "panelAuxiliarArchivos":
    $idUsuario=$_GET["idUsuario"];
    $idComunidad=$_GET["idComunidad"];
    $html="";
    if ($idUsuario!=0){
      incluir('archivos');
      $archivos=archivos::getAll("SELECT * FROM archivos WHERE idUsuario=$idUsuario");
      if ($archivos){
        foreach ($archivos as $archivo) {
          $html.="<div>{$archivo['archivo']}.{$archivo['extension']}  <label class=\'fa fa-trash\' onclick=\'executeAction(&quot;eliminar&quot;,&quot;&destino=archivos&idArchivo={$archivo['id']}&idComunidad=$idComunidad&idUsuario=$idUsuario&quot;)\'></label></div>";
        }
      }

    }
    if ($idComunidad!=0){
      incluir('archivos');
      $archivos=archivos::getAll("SELECT * FROM archivos WHERE idComunidad=$idComunidad");
      if ($archivos){
        foreach ($archivos as $archivo) {
          $html.="<div>{$archivo['archivo']}.{$archivo['extension']}  ".
          "<label class=\"fa fa-download\" onclick=\"document.getElementById(\'formDownloadFile{$archivo['id']}\').submit()\"></label> ".
          "<label class=\"fa fa-trash\" onclick=\"executeAction(\'eliminar\',\'&destino=archivos&idArchivo={$archivo['id']}&idComunidad=$idComunidad&idUsuario=$idUsuario\')\"></label>".
          "<form id=formDownloadFile{$archivo['id']} target=downloadIFrame action=\"./PHP/actions/download.php\" method=GET><input id=idArchivo name=idArchivo value={$archivo['id']} type=hidden></form>".
          "</div>";
        }
      }
    }

    $JS.="$('#divListadoArchivos').html('$html');";
    $JS.="$('#idUsuario').val('$idUsuario');$('#idComunidad').val('$idComunidad');$('#panelAuxiliar').fadeIn('slow');";
  break;
  case 'panelAuxiliarArchivosNombre':
    $filePath=$_GET['inputSubirArchivo'];
    $fileName = pathinfo($filePath,  PATHINFO_FILENAME);
    if(strpos($filePath, "\\") !== false){
      $fileName=explode("\\",$fileName);
      $fileName=$fileName[sizeof($fileName)-1];
    }
    $JS="$('#idNombreArchivo').val('$fileName')";
  break;
  case 'panelAuxiliarHistorial':
    $idUsuario=$_GET["idUsuario"];
    $idComunidad=$_GET["idComunidad"];
    $html="";
    if ($idUsuario!=0){
      incluir('historial');
      incluir('usuarios');
      $eventos=historial::getAll("SELECT * FROM historial WHERE idUsuario=$idUsuario ORDER BY fechaHora DESC");
      if ($eventos){
        foreach ($eventos as $evento) {
          $usuario=usuarios::getValue("SELECT usuario FROM usuarios WHERE id={$evento['idOrigen']}");
          $fechaAlerta=strings::fecha($evento['alerta']);
          $alertString="";
          if ($evento['alerta']!='1977-10-10'){
            if ($evento['activo']==1) {$check="checked=checked";$inputAlerta=true;}
            else {$check="";$inputAlerta=false;}
            $alertString="<input id=idAlerta{$evento['id']} type=checkbox $check onchange=\"executeAction(\'editar\',\'&destino=historial&idEvento={$evento['id']}&alerta=$inputAlerta\')\"><label style=\'font-size:120%\'>$fechaAlerta</label></input>";
          }
          $html.="<div class=historialItem><div class=divTopHistorialItem>$usuario<div class=divAlertaHistorialItem>$alertString<i class=\'fa fa-trash \' style=\'font-size:150%;margin-left:10px\'  onclick=\'executeAction(&quot;eliminar&quot;,&quot;&destino=historial&idEvento={$evento['id']}&idComunidad=$idComunidad&idUsuario=$idUsuario&quot;)\'></i></div> </div>";
          $html.="<div class=divBodyHistorialItem>{$evento['evento']}</div>";
          $html.="<div class=divBottomHistorialItem >{$evento['fechaHora']}</div></div>";
        }
      }

    }
    if ($idComunidad!=0){
      incluir('historial');
      incluir('usuarios');
      $eventos=historial::getAll("SELECT * FROM historial WHERE idComunidad=$idComunidad ORDER BY fechaHora DESC");
      if ($eventos){
        foreach ($eventos as $evento) {
          $usuario=usuarios::getValue("SELECT usuario FROM usuarios WHERE id={$evento['idOrigen']}");
          $fechaAlerta=strings::fecha($evento['alerta']);
          $alertString="";
          if ($evento['alerta']!='1977-10-10'){
            if ($evento['activo']==1) {$check="checked=checked";$inputAlerta=true;}
            else {$check="";$inputAlerta=false;}
            $alertString="<input id=idAlerta{$evento['id']} type=checkbox $check onchange=\"executeAction(\'editar\',\'&destino=historial&idEvento={$evento['id']}&alerta=$inputAlerta\')\"><label style=\'font-size:120%\'>$fechaAlerta</label></input>";
          }
          $html.="<div class=historialItem><div class=divTopHistorialItem>$usuario<div class=divAlertaHistorialItem>$alertString<i class=\'fa fa-trash \' style=\'font-size:150%;margin-left:10px\'  onclick=\'executeAction(&quot;eliminar&quot;,&quot;&destino=historial&idEvento={$evento['id']}&idComunidad=$idComunidad&idUsuario=$idUsuario&quot;)\'></i></div> </div>";
          $html.="<div class=divBodyHistorialItem>{$evento['evento']}</div>";
          $html.="<div class=divBottomHistorialItem >{$evento['fechaHora']}</div></div>";
      }
    }
  }
  $JS.="$('#divListadoHistorial').html('$html');";
  $JS.="$('#idUsuario').val('$idUsuario');$('#idComunidad').val('$idComunidad');$('#panelAuxiliar').fadeIn('slow');";
  break;
  case "comunidades":
  if (Session::allowed()){
   incluir('comunidades');
   if (!empty($_GET['filtroComunidades'])) $filtro=$_GET['filtroComunidades'];
   else $filtro=false;
   if (!empty($_GET['searchId'])) {
     $searchId=$_GET['searchId'];
     if ($searchId!=$_SESSION['searchId']) return;
     else $search=new Search($searchId);
   }
   else $search= new Search();

   if (isset($_GET["limit"])) {
     $limit=$_GET["limit"];
     $offset=$_GET["offset"];

     if ($filtro) $query="SELECT * FROM comunidades WHERE comunidad like '%$filtro%' ORDER BY comunidad DESC LIMIT $offset,$limit";
     else $query="SELECT * FROM comunidades ORDER BY comunidad DESC LIMIT $offset,$limit";
   }
   else {
     $JS.="$('#listadoComunidades').html('');";
     $limit=5;
     if ($filtro) $query="SELECT * FROM comunidades WHERE comunidad like '%$filtro%' ORDER BY comunidad DESC LIMIT $limit";
     else $query="SELECT * FROM comunidades  ORDER BY comunidad DESC LIMIT $limit";
   }


   $comunidades=comunidades::getAll($query);
   if ($comunidades) {
     incluir('correos');
     incluir('telefonos');
     incluir('usuarios');

     foreach ($comunidades as $comunidad) {
       $comunidadExt=classInvoker::nc('comunidades',intval($comunidad['id']),true);
       $JS.="$('#listadoComunidades').append('<div  class=listado>{$comunidad['comunidad']} <div class=\"iconos\"> ".
       "<img  src=\"./RESOURCES/mail.png\" onclick=\"window.open(\'mailto:{$comunidadExt->toMailTo()}\');\" />".
       "<label class=\"fa fa-pencil-square-o buttons\" style=\"background-color:#e3600a\" onclick=\"executeAction(\'prepararPanel\',\'&destino=panelAuxiliarComunidades&idComunidad={$comunidad['id']}\')\" ></label>".
       "<label class=\"fa fa-upload buttons\" style=\"background-color:#0ca2a9\" onclick=\"executeAction(\'prepararPanel\',\'&destino=panelAuxiliarArchivos&idComunidad={$comunidad['id']}\')\"></label>".
       "<label class=\"fa fa-history buttons\" style=\"background-color:#a90c51\" onclick=\"executeAction(\'prepararPanel\',\'&destino=panelAuxiliarHistorial&idUsuario=0&idComunidad={$comunidad['id']}\')\"></label>".
       "<label class=\"fa fa-trash buttons\" style=\"background-color:#961e18\" onclick=\"executeAction(\'eliminar\',\'&destino=comunidades&idComunidad={$comunidad['id']}\')\"></label>".
       "<label class=\"fa fa-compress buttons\" target=comunidad{$comunidad['id']} style=\"background-color:#274e73\" onclick=\"expande(this);\"></label></div>".
       "<div id=comunidad{$comunidad['id']}  class=\"compress\" style=\"width:100%\" ><br>Dirección: {$comunidad['direccionPostal']} {$comunidad['direccionPostal2']}<br><br>".
       "<div id=listadoUsuarios{$comunidad['id']} ></div></div></div> ');";

       $query="SELECT * FROM usuarios WHERE idComunidad={$comunidad['id']} AND idRol=0 AND estado <> 'eliminado' ORDER BY nombre";
       $usuarios=usuarios::getAll($query);
       if ($usuarios) {
         foreach ($usuarios as $usuario) {
           $usuarioExt=classInvoker::nc('usuarios',intval($usuario['id']),true);
           $correo=$usuarioExt->correoDefecto();
           $telefono=$usuarioExt->telefonoDefecto();
           $Scomunidad=$usuarioExt->getComunidad();
           $JS.="$('#listadoUsuarios{$comunidad['id']}').append('<div  class=listado><div class=nombrePropietarios>{$usuario['nombre']}</div> ".
           "<div class=\"iconos\"> <img  src=\"./RESOURCES/phone.png\" onclick=\"window.location.href=\'tel:{$telefono}\'\" />".
           " <img  src=\"./RESOURCES/whatsapp.png\" onclick=\"window.open(\'https://web.whatsapp.com/send?phone={$telefono}\',\'whatsApp\')\" />".
           " <img  src=\"./RESOURCES/mail.png\" onclick=\"window.location.href =\'mailto:{$correo}\';\" />".
           "<label class=\"fa fa-comment buttons\" onclick=\"executeAction(\'prepararPanel\',\'&destino=panelAuxiliarMensajes&idUsuario={$usuario['id']}\')\" style=\"background-color:#e8be16\"></label>".
           "<label class=\"fa fa-pencil-square-o buttons\" style=\"background-color:#e3600a\" onclick=\"executeAction(\'prepararPanel\',\'&destino=panelAuxiliarUsuariosEditar&idUsuario={$usuario['id']}\')\" ></label>".
           "<label class=\"fa fa-upload buttons\" style=\"background-color:#0ca2a9\" onclick=\"executeAction(\'prepararPanel\',\'&destino=panelAuxiliarArchivos&idUsuario={$usuario['id']}\')\"></label>".
           "<label class=\"fa fa-ticket buttons\" style=\"background-color:#1f7124\" onclick=\"executeAction(\'prepararPanel\',\'&destino=panelAuxiliarRecibos&idUsuario={$usuario['id']}&idComunidad={$comunidad['id']}\')\"></label>".
           "<label class=\"fa fa-history buttons\" style=\"background-color:#a90c51\" onclick=\"executeAction(\'prepararPanel\',\'&destino=panelAuxiliarHistorial&idUsuario={$usuario['id']}&idComunidad=0\')\"></label>".
           "<label class=\"fa fa-trash buttons\" style=\"background-color:#961e18\" onclick=\"executeAction(\'eliminar\',\'&destino=usuario&idUsuario={$usuario['id']}\')\"></label>".
           "<label class=\"fa fa-compress buttons\" target=usuarioComunidades{$usuario['id']} style=\"background-color:#274e73\" onclick=\"expande(this,false,true)\" ></label>".
           "</div><div id=usuarioComunidades{$usuario['id']}  class=\"compress\" ><br>Comunidad: $Scomunidad<br> Piso: {$usuario['piso']} <br>Usuario: {$usuario['usuario']} <br>Clave defecto: {$usuario['claveDefecto']}<br>Estado cuenta: {$usuario['estado']}</div></div> ');";
         }
       }

     }
     if (isset($offset)) $offset+=$limit;
     else $offset=$limit;
     if ($filtro) $JS.="executeAction('rellenarPanel','&destino=comunidades&offset=$offset&limit=$limit&filtroComunidades=$filtro&searchId={$search->getId()}');";
     else $JS.="executeAction('rellenarPanel','&destino=comunidades&offset=$offset&limit=$limit&searchId={$search->getId()}');";
     if ($search->isState('started')){
       $search->setState('processing');
       $JS.="$('#panelAdd').off('click');$('#panelAdd').click(function () {executeAction('prepararPanel','&destino=panelAuxiliarComunidades')});slide(6);";
     }
   }
   else {
     $search->setState('completed');
   }
  }
  break;
  case "correspondencias":
  if (Session::allowed()){
    incluir('correspondencias');
    if (!empty($_GET['filtroCorrespondencias'])) $filtro=$_GET['filtroCorrespondencias'];
    else $filtro=false;

     if (isset($_GET["limit"])) {
       $limit=$_GET["limit"];
       $offset=$_GET["offset"];

       if ($filtro) $query="SELECT id,correspondencia FROM correspondencias WHERE correspondencia like '%$filtro%' ORDER BY correspondencia DESC LIMIT $offset,$limit";
       else $query="SELECT id,correspondencia FROM correspondencias ORDER BY correspondencia DESC LIMIT $offset,$limit";
     }
     else {
       $JS.="$('#listadoCorrespondencias').html('');";
       $limit=5;
       if ($filtro) $query="SELECT id,correspondencia FROM correspondencias WHERE correspondencia like '%$filtro%'  ORDER BY correspondencia DESC LIMIT $limit";
       else $query="SELECT id,correspondencia FROM correspondencias ORDER BY correspondencia DESC LIMIT $limit";
     }
     $correspondencias=correspondencias::getAll($query);
     if ($correspondencias) {
       foreach ($correspondencias as $correspondencia) {
         $JS.="$('#listadoCorrespondencias').append('<div  class=listado>{$correspondencia['correspondencia']}".
         "<div class=\"iconos\"><label class=\"fa fa-file-text-o buttons\" style=\"background-color:#326699\"  onclick=\"window.open(\'./PHP/actions/crearPDF.php?idCorrespondencia={$correspondencia['id']}\')\" ></label>".
         "<label class=\"fa fa-pencil-square-o buttons\"  onclick=\"executeAction(\'prepararPanel\',\'&destino=panelAuxiliarCorrespondencias&idCorrespondencia={$correspondencia['id']}\')\" style=\"background-color:#e36110\"/></label>".
         "<label class=\"fa fa-trash buttons\" style=\"background-color:#961e18\" onclick=\"executeAction(\'eliminar\',\'&destino=correspondencias&idCorrespondencia={$correspondencia['id']}\')\"/></label></div>');";
       }
       if (isset($offset)) $offset+=$limit;
       else $offset=$limit;
       if ($filtro) $JS.="executeAction('rellenarPanel','&destino=correspondencias&offset=$offset&limit=$limit&filtroCorrespondencias=$filtro');";
       else $JS.="executeAction('rellenarPanel','&destino=correspondencias&offset=$offset&limit=$limit');";
     }
     else {
       $JS.="$('#panelAdd').off('click');$('#panelAdd').click(function () {executeAction('prepararPanel','&destino=panelAuxiliarCorrespondencias')});slide(9);";

     }
  }
  break;

  case "exportar":
  if (Session::allowed()){
    incluir('exportar');
    if (!empty($_GET['filtroExportar'])) $filtro=$_GET['filtroExportar'];
    else $filtro=false;

     if (isset($_GET["limit"])) {
       $limit=$_GET["limit"];
       $offset=$_GET["offset"];

       if ($filtro) $query="SELECT id,exportacion FROM exportar WHERE exportacion like '%$filtro%' ORDER BY exportacion DESC LIMIT $offset,$limit";
       else $query="SELECT id,exportacion FROM exportar ORDER BY exportacion DESC LIMIT $offset,$limit";
     }
     else {
       $JS.="$('#listadoExportar').html('');";
       $limit=5;
       if ($filtro) $query="SELECT id,exportacion FROM exportar WHERE exportacion like '%$filtro%'  ORDER BY exportacion DESC LIMIT $limit";
       else $query="SELECT id,exportacion FROM exportar ORDER BY exportacion DESC LIMIT $limit";
     }
     $exportar=exportar::getAll($query);
     if ($exportar) {
       foreach ($exportar as $exportacion) {
         $JS.="$('#listadoExportar').append('<div  class=listado>{$exportacion['exportacion']}".
         "<div class=\"iconos\"><label class=\"fa fa-file-text-o buttons\" style=\"background-color:#326699\"  onclick=\"executeAction(\'crearXLS\',\'&idExportacion={$exportacion['id']}\')\" ></label>".
         "<label class=\"fa fa-pencil-square-o buttons\"  onclick=\"executeAction(\'prepararPanel\',\'&destino=panelAuxiliarExportar&idExportar={$exportacion['id']}\')\" style=\"background-color:#e36110\"/></label>".
         "<label class=\"fa fa-trash buttons\" style=\"background-color:#961e18\" onclick=\"executeAction(\'eliminar\',\'&destino=exportar&idExportacion={$exportacion['id']}\')\"/></label></div>');";
       }
       if (isset($offset)) $offset+=$limit;
       else $offset=$limit;
       if ($filtro) $JS.="executeAction('rellenarPanel','&destino=exportar&offset=$offset&limit=$limit&filtroExportar=$filtro');";
       else $JS.="executeAction('rellenarPanel','&destino=exportar&offset=$offset&limit=$limit');";
     }
     else {
       $JS.="$('#panelAdd').off('click');$('#panelAdd').click(function () {executeAction('prepararPanel','&destino=panelAuxiliarExportar')});slide(10);";

     }
  }
  break;

  case "panelAuxiliarTelefonos":
    $idUsuario=$_GET["idUsuario"];
    $JS.="$('#idUsuario').val('$idUsuario');$('#contacto').val('');$('#idTelefono').val('');$('#panelAuxiliar').fadeIn('slow');";
  break;
  case "panelAuxiliarFincas":
    $idUsuario=$_GET["idUsuario"];
    $JS.="$('#idUsuario').val('$idUsuario');$('#finca').val('');$('#panelAuxiliar').fadeIn('slow');";
  break;
  case "panelAuxiliarCorreos":
    $idUsuario=$_GET["idUsuario"];
    $JS.="$('#idUsuario').val('$idUsuario');$('#destinatario').val('');$('#correos').val('');$('#panelAuxiliar').fadeIn('slow');";
  break;
  case "panelAuxiliarDirecciones":
    $idUsuario=$_GET["idUsuario"];
    $idDireccion=$_GET["idDireccion"];
    incluir("direccionesPostales");
    $direccion=classInvoker::nc('direccionesPostales',$idDireccion);
    $JS.="$('#idUsuario').val('$idUsuario');$('#idDireccion').val('$idDireccion');$('#direccion').val('{$direccion->direccionPostal}');$('#destinatario').val('{$direccion->destinatario}');$('#panelAuxiliar').fadeIn('slow');";
  break;
  case "panelAuxiliarComunidades":
    $idComunidad=$_GET["idComunidad"];
    if (empty($idComunidad)){
      $JS.="$('#idComunidad').val('');$('#comunidad').val('');$('#direccion').val('');$('#direccion2').val('');$('#panelAuxiliar').fadeIn('slow');";
    }
    else{
      $comunidad=classInvoker::nc('comunidades',intval($idComunidad));
      $JS.="$('#idComunidad').val('{$comunidad->id}');$('#comunidad').val('{$comunidad->comunidad}');$('#direccion').val('{$comunidad->direccionPostal}');$('#direccion2').val('{$comunidad->direccionPostal2}');$('#panelAuxiliar').fadeIn('slow');";
    }
  break;
  case "panelAuxiliarCorrespondencias":
    $idCorrespondencia=$_GET["idCorrespondencia"];
    if (empty($idCorrespondencia)){
      $_SESSION['correspondencia']='';
      $JS.="$('#idCorrespondencia').val('');$('#panelAuxiliar').fadeIn('slow');";
      $JS.="executeAction('rellenarPanel','&destino=panelAuxiliarCorrespondenciasSelect',['selectDestinatarios']);";
    }
    else{
      $correspondencia=classInvoker::nc('correspondencias',intval($idCorrespondencia),true);
      $correspondencia->destinatarios=$correspondencia->jsonDestinatarios();
      $correspondencia->destinatarios=(Array) $correspondencia->destinatarios;
      foreach ($correspondencia->destinatarios as $idUsuario=>$idDireccion) {
        $usuarioExt=classInvoker::nc('usuarios',intval($idUsuario),true);
        $JS.="$('#divListadoUsuariosCorrespondencias').append('<input type=checkbox checked onchange=\"executeAction(\'editar\',\'&destino=check&checkbox=checkbox{$usuarioExt->id}\',\'checkbox{$usuarioExt->id}\')\" id=checkbox{$usuarioExt->id} /><label class=checkboxLabel>{$usuarioExt->nombre}({$usuarioExt->usuario})</label><BR>');";
        $direcciones=$usuarioExt->getDirecciones();
        foreach ($direcciones as $id=>$direccion) {
          if ($id==$idDireccion) $JS.="$('#divListadoUsuariosCorrespondencias').append('<input type=radio checked class=radio id={$id}radiocheckbox{$usuarioExt->id} name=radiocheckbox{$usuarioExt->id} /><label class=radioLabel>$direccion</label><BR>');";
          else $JS.="$('#divListadoUsuariosCorrespondencias').append('<input type=radio class=radio id={$id}radiocheckbox{$usuarioExt->id} name=radiocheckbox{$usuarioExt->id} /><label class=radioLabel>$direccion</label><BR>');";

        }
      }
      $JS.="$('#idCorrespondencia').val('$idCorrespondencia');$('#correspondencia').val('{$correspondencia->correspondencia}');";
      $JS.="$('#textoCorrespondencia').val(atob('{$correspondencia->texto}'));$('#panelAuxiliar').fadeIn('slow');";

  }
  break;
  case "panelAuxiliarCorrespondenciasSelect":
  if (Session::allowed()){
    $selectDestinatarios=$_GET["selectDestinatarios"];
    switch ($selectDestinatarios) {
      case '1':
        $JS.="$('#divListadoUsuariosCorrespondencias').html('');$('#divComunidadesCorrespondencias').css('display','none');$('#divUsuariosCorrespondencias').css('display','none');";
        incluir('usuarios');
        $query="SELECT id,usuario,nombre FROM usuarios WHERE idRol=0 AND estado<>'eliminado'   AND correspondencia=1 ORDER BY nombre";
        $usuarios=usuarios::getAll($query);
        if ($usuarios){
          foreach ($usuarios as $usuario) {
            $usuarioExt=classInvoker::nc('usuarios',intval($usuario['id']),true);
            $direcciones=$usuarioExt->getDirecciones();
            $JS.="$('#divListadoUsuariosCorrespondencias').append('<input type=checkbox onchange=\"executeAction(\'editar\',\'&destino=check&checkbox=checkbox{$usuario['id']}\',\'checkbox{$usuario['id']}\')\" id=checkbox{$usuario['id']} /><label class=checkboxLabel>{$usuario['nombre']}({$usuario['usuario']})</label><BR>');";
            foreach ($direcciones as $id=>$direccion) {
              $JS.="$('#divListadoUsuariosCorrespondencias').append('<input type=radio class=radio id={$id}radiocheckbox{$usuario['id']} name=radiocheckbox{$usuario['id']} /><label class=radioLabel>$direccion</label><BR>');";
            }

          }
        }
      break;
      case '2':
        $JS.="$('#divListadoUsuariosCorrespondencias').html('');$('#divUsuariosCorrespondencias').css('display','none');";
        incluir ('comunidades');
        $query="SELECT id,comunidad FROM comunidades ORDER BY comunidad";
        $comunidades=comunidades::getAll($query);
        if ($comunidades){
          foreach ($comunidades as $comunidad) {
              $JS.="$('#selectComunidadesCorrespondencias').append('<OPTION value={$comunidad['id']}>{$comunidad['comunidad']}</OPTION>');";
          }
          $JS.="$('#divComunidadesCorrespondencias').css('display','block');";
        }
        $JS.="executeAction('rellenarPanel','&destino=panelAuxiliarCorrespondenciasSelectComunidades',['selectComunidadesCorrespondencias']);";
      break;
      case '3':
      $JS.="$('#divListadoUsuariosCorrespondencias').html('');$('#divComunidadesCorrespondencias').css('display','none');";
      incluir('usuarios');
      $query="SELECT id,usuario,nombre FROM usuarios WHERE idRol=0 AND estado<>'eliminado'   AND correspondencia=1 ORDER BY nombre";
      $usuarios=usuarios::getAll($query);
      if ($usuarios){
        foreach ($usuarios as $usuario) {
            $JS.="$('#selectUsuariosCorrespondencias').append('<OPTION value={$usuario['id']}>{$usuario['nombre']}</OPTION>');";
        }
        $JS.="$('#divUsuariosCorrespondencias').css('display','block');";
      }
      $JS.="executeAction('rellenarPanel','&destino=panelAuxiliarCorrespondenciasSelectUsuarios',['selectUsuariosCorrespondencias']);";
      break;
    }
  }
  break;
  case "panelAuxiliarCorrespondenciasSelectComunidades":
  if (Session::allowed()){
    $idComunidad=$_GET["selectComunidadesCorrespondencias"];
    incluir('usuarios');
    $JS.="$('#divListadoUsuariosCorrespondencias').html('');";
    $query="SELECT id,usuario,nombre FROM usuarios WHERE idRol=0 AND estado<>'eliminado' AND idComunidad=$idComunidad  AND correspondencia=1 ORDER BY nombre";
    $usuarios=usuarios::getAll($query);
    if ($usuarios){
      foreach ($usuarios as $usuario) {
        $usuarioExt=classInvoker::nc('usuarios',intval($usuario['id']),true);
        $direcciones=$usuarioExt->getDirecciones();
        $JS.="$('#divListadoUsuariosCorrespondencias').append('<input type=checkbox onchange=\"executeAction(\'editar\',\'&destino=check&checkbox=checkbox{$usuario['id']}\',\'checkbox{$usuario['id']}\')\" id=checkbox{$usuario['id']} /><label class=checkboxLabel>{$usuario['nombre']}({$usuario['usuario']})</label><BR>');";
        foreach ($direcciones as $id=>$direccion) {
          $JS.="$('#divListadoUsuariosCorrespondencias').append('<input type=radio class=radio id={$id}radiocheckbox{$usuario['id']} name=radiocheckbox{$usuario['id']} /><label class=radioLabel>$direccion</label><BR>');";
        }

      }
    }

  }

  break;
  case "panelAuxiliarCorrespondenciasSelectUsuarios":
  if (Session::allowed()){
    $idUsuario=$_GET["selectUsuariosCorrespondencias"];
    incluir('usuarios');
    $JS.="$('#divListadoUsuariosCorrespondencias').html('');";
    $usuarioExt=classInvoker::nc('usuarios',intval($idUsuario),true);
    $direcciones=$usuarioExt->getDirecciones();
    $JS.="$('#divListadoUsuariosCorrespondencias').append('<input type=checkbox onchange=\"executeAction(\'editar\',\'&destino=check&checkbox=checkbox{$usuarioExt->id}\',\'checkbox{$usuarioExt->id}\')\" id=checkbox{$usuarioExt->id} /><label class=checkboxLabel>{$usuarioExt->nombre}({$usuarioExt->usuario})</label><BR>');";
    foreach ($direcciones as $id=>$direccion) {
        $JS.="$('#divListadoUsuariosCorrespondencias').append('<input type=radio class=radio id={$id}radiocheckbox{$usuarioExt->id} name=radiocheckbox{$usuarioExt->id} /><label class=radioLabel>$direccion</label><BR>');";
    }
  }
  break;
  case "panelAuxiliarEditarFincas":
    $idFinca =$_GET["idFinca"];
    $fincaExt=classInvoker::nc('fincas',intval($idFinca),true);
    if (isset($_GET["idUsuario"])){
      $idUsuario=$_GET["idUsuario"];
      $JS.="$('#idUsuario').val('$idUsuario');$('#idUsuario2').val('$idUsuario');$('#panelAuxiliar').fadeIn('slow');";
    }
    $JS.="$('#idFinca').val('$idFinca');$('#coeficiente').val('{$fincaExt->coeficiente}');$('#idFinca2').val('$idFinca');$('#finca').val('{$fincaExt->finca}');$('#tablaPropietarios').html('');{$fincaExt->html()};";
  break;
  case "panelAuxiliarRecibos":
    $idUsuario =$_GET["idUsuario"];
    incluir('comunidades');
    incluir('recibos');
    incluir('usuarios');
    incluir('fincas');
    $comunidad=usuarios::getValue("SELECT comunidad FROM usuarios,comunidades  WHERE usuarios.id=$idUsuario AND usuarios.idComunidad=comunidades.id");
    $nombre=usuarios::getValue("SELECT nombre FROM usuarios WHERE id=$idUsuario");
    $query="SELECT id,finca FROM fincas WHERE idUsuario=$idUsuario";
    $fincas=fincas::getAll($query);
    $query="SELECT * FROM recibos WHERE idUsuario=$idUsuario";
    $JS.="$('#idUsuario').val('$idUsuario');$('#tablaListadoRecibos').html('');";
    $recibos=recibos::getAll($query);
    if ($recibos){
      foreach ($recibos as $recibo) {
        $fecha=Strings::fecha($recibo['fecha']);
        $JS.="$('#tablaListadoRecibos').append('<tr><td>{$recibo['concepto']}</td><td>{$recibo['importe']}</td>".
        "<td>$fecha</td><td><label class=\"fa fa-pencil buttons2\" onclick=\"executeAction(\'editar\',\'&destino=panelAuxiliarRecibos&idRecibo={$recibo['id']}\')\" ></label></td>".
        "<td><label class=\"fa fa-print buttons2\" onclick=\"window.open(\'./PHP/actions/crearPDFRecibos.php?idRecibo={$recibo['id']}\')\" ></label></td>".
        "<td><label class=\"fa fa-trash buttons2\" onclick=\"executeAction(\'eliminar\',\'&destino=panelAuxiliarRecibos&idRecibo={$recibo['id']}\')\" ></label></td></tr>');";
      }
    }
    if ($fincas){
      foreach ($fincas as $key => $finca) {
        $JS.="$('#fincaRecibo').append('<option id=$key value={$finca['id']}>{$finca['finca']}</option>');";
      }
    }
    $JS.="$('#nombreRecibo').val('$nombre');$('#idRecibo').val('');$('#panelAuxiliar').fadeIn('slow');";

  break;
  case "panelAuxiliarExportar":
    $idExportacion=$_GET["idExportacion"];
    if (empty($idCorrespondencia)){
      $_SESSION['exportar']='';
      $JS.="$('#idExportacion').val('');$('#panelAuxiliar').fadeIn('slow');";
      $JS.="executeAction('rellenarPanel','&destino=panelAuxiliarCorrespondenciasSelect',['selectDestinatarios']);";
    }
    else{
      $correspondencia=classInvoker::nc('correspondencias',intval($idExportacion),true);
      $correspondencia->destinatarios=$correspondencia->jsonDestinatarios();
      $correspondencia->destinatarios=(Array) $correspondencia->destinatarios;
      foreach ($correspondencia->destinatarios as $idUsuario=>$idDireccion) {
        $usuarioExt=classInvoker::nc('usuarios',intval($idUsuario),true);
        $JS.="$('#divListadoUsuariosCorrespondencias').append('<input type=checkbox checked onchange=\"executeAction(\'editar\',\'&destino=check&checkbox=checkbox{$usuarioExt->id}\',\'checkbox{$usuarioExt->id}\')\" id=checkbox{$usuarioExt->id} /><label class=checkboxLabel>{$usuarioExt->nombre}({$usuarioExt->usuario})</label><BR>');";
        $direcciones=$usuarioExt->getDirecciones();
        foreach ($direcciones as $id=>$direccion) {
          if ($id==$idDireccion) $JS.="$('#divListadoUsuariosCorrespondencias').append('<input type=radio checked class=radio id={$id}radiocheckbox{$usuarioExt->id} name=radiocheckbox{$usuarioExt->id} /><label class=radioLabel>$direccion</label><BR>');";
          else $JS.="$('#divListadoUsuariosCorrespondencias').append('<input type=radio class=radio id={$id}radiocheckbox{$usuarioExt->id} name=radiocheckbox{$usuarioExt->id} /><label class=radioLabel>$direccion</label><BR>');";

        }
      }
      $JS.="$('#idCorrespondencia').val('$idCorrespondencia');$('#correspondencia').val('{$correspondencia->correspondencia}');";
      $JS.="$('#textoCorrespondencia').val(atob('{$correspondencia->texto}'));$('#panelAuxiliar').fadeIn('slow');";

  }
  break;
  case "panelAuxiliarExportarSelect":
  if (Session::allowed()){
    $selectExportar=$_GET["selectExportar"];
    switch ($selectExportar) {
      case '1':
        $JS.="$('#divComunidadesExportar').css('display','none');";
      break;
      case '2':
        incluir ('comunidades');
        $query="SELECT id,comunidad FROM comunidades ORDER BY comunidad";
        $comunidades=comunidades::getAll($query);
        if ($comunidades){
          foreach ($comunidades as $comunidad) {
              $JS.="$('#selectComunidadesExportar').append('<OPTION value={$comunidad['id']}>{$comunidad['comunidad']}</OPTION>');";
          }
          $JS.="$('#divComunidadesExportar').css('display','block');";
        }
      break;
    }
  }
  break;
  case "panelAuxiliarEmpleados":
    if (Session::allowed()){
      if (isset($_GET['idEmpleado'])){
        $idEmpleado=$_GET['idEmpleado'];
        $empleado=classInvoker::nc('empleados',intval($idEmpleado));
        $cadena;
        if ($empleado->reset=="1") $cadena="$('#idCheckEmpleados').prop('checked', true)";
        else $cadena="$('#idCheckEmpleados').prop('checked', false)";
        $JS.="$('#idEmpleadoEditar').val('{$empleado->id}');$('#idNombre').val('{$empleado->nombre}');$('#idEmpleado').val('{$empleado->empleado}');";
        $JS.="$('#idClave').val('{$empleado->claveDefecto}');$('#idDNI').val('{$empleado->dni}');$cadena;$('#panelAuxiliar').fadeIn('slow');";
      }
      else $JS.="$('#idEmpleadoEditar').val('');$('#panelAuxiliar').fadeIn('slow');";
 
    } 
  break;
  case "recibos":
  incluir('recibos');
  if (Session::allowed()){
    if (!empty($_GET['filtroRecibos'])) $filtro=$_GET['filtroRecibos'];
    else $filtro=false;
    if (!empty($_GET['selectFiltroRecibos'])) $option=$_GET['selectFiltroRecibos'];
    else $option="1";
   if (isset($_GET["limit"])) {
       $limit=$_GET["limit"];
       $offset=$_GET["offset"];
       if ($filtro) {
         switch ($option){
           case "1":
            $query="SELECT * FROM recibos WHERE nombre like '%$filtro%' ORDER BY fecha DESC LIMIT $offset,$limit";
           break;

           case "2":
            $query="SELECT recibos.idUsuario,comunidades.id,recibos.nombre,recibos.id,recibos.fecha FROM recibos,comunidades,usuarios  WHERE recibos.idUsuario=usuarios.id AND usuarios.idComunidad=comunidades.id AND comunidad like '%$filtro%' ORDER BY recibos.id DESC LIMIT $offset,$limit";

           break;
         }
       }
       else {
         switch ($option){
           case "1":
            $query="SELECT * FROM recibos WHERE nombre like '%$filtro%' ORDER BY fecha DESC LIMIT $offset,$limit";
           break;
           case "2":
            $query="SELECT recibos.idUsuario,comunidades.id,recibos.nombre,recibos.id,recibos.fecha FROM recibos,comunidades,usuarios  WHERE recibos.idUsuario=usuarios.id AND usuarios.idComunidad=comunidades.id ORDER BY recibos.id DESC LIMIT $offset,$limit";
           break;

         }
       }
     }
     else {
       $JS.="$('#listadoRecibos').html('');";
       $limit=5;
      if ($filtro) {
        switch ($option){
          case "1":
           $query="SELECT * FROM recibos WHERE nombre like '%$filtro%' ORDER BY fecha DESC LIMIT $limit";
          break;

          case "2":
           $query="SELECT recibos.idUsuario,comunidades.id,recibos.nombre,recibos.id,recibos.fecha FROM recibos,comunidades,usuarios  WHERE recibos.idUsuario=usuarios.id AND usuarios.idComunidad=comunidades.id AND comunidad like '%$filtro%' ORDER BY recibos.id  DESC LIMIT $limit";

          break;
        }
       }
       else {
         switch ($option){
           case "1":
            $query="SELECT * FROM recibos WHERE nombre like '%$filtro%' ORDER BY fecha DESC LIMIT $limit";
           break;
           case "2":
            $query="SELECT recibos.idUsuario,comunidades.id,recibos.nombre,recibos.id,recibos.fecha FROM recibos,comunidades,usuarios  WHERE recibos.idUsuario=usuarios.id AND usuarios.idComunidad=comunidades.id ORDER BY recibos.id  DESC LIMIT $limit";
           break;

         }

       }
     }
     $recibos=recibos::getAll($query);
     if ($recibos) {
       foreach ($recibos as $recibo) {
         $fecha=Strings::fecha($recibo['fecha']);
         $reciboO=classInvoker::nc('recibos',intval($recibo['id']));
         incluir('usuarios');
         $usuario=classInvoker::nc('usuarios',intVal($recibo['idUsuario']));
         incluir('comunidades');
         $comunidad=classInvoker::nc('comunidades',intVal($usuario->idComunidad));
         $destino="|{$comunidad->comunidad}| |{$recibo['nombre']}|";


         $JS.="$('#listadoRecibos').append('<div  class=listado>Recibo{$recibo['id']} $destino <div class=\"iconos\">".
         "<label class=\"fa fa-pencil buttons\" style=\"background-color:#0ca2a9\" onclick=\"executeAction(\'prepararPanel\',\'&destino=panelAuxiliarRecibos&idUsuario={$recibo['idUsuario']}\')\"></label>".
         "<label class=\"fa fa-print buttons\" style=\"background-color:#961e18\" onclick=\"window.open(\'./PHP/actions/crearPDFRecibos.php?idRecibo={$recibo['id']}\')\"></label>".
         "<label class=\"fa fa-trash buttons\" style=\"background-color:#274e73\" onclick=\"executeAction(\'eliminar\',\'&destino=recibos&idRecibo={$recibo['id']}&idComunidad=0&idUsuario=0&fromArchivos=true\')\" ></label>".
         "</div></div>');";
       }
       if (isset($offset)) $offset+=$limit;
       else $offset=$limit;
       if ($filtro) $JS.="executeAction('rellenarPanel','&destino=recibos&offset=$offset&limit=$limit&filtroRecibos=$filtro&selectFiltroRecibos=$option');";
       else $JS.="executeAction('rellenarPanel','&destino=recibos&offset=$offset&limit=$limit&selectFiltroRecibos=$option&selectFiltroRecibos=$option');";
     }
     else {
       $JS.="$('#panelAdd').off('click');slide(10);";

     }
  }
  else{
    $JS.="$('#panelAuxiliar').fadeOut('slow');";
    if (!empty($_GET['filtroArchivos'])) $filtro=$_GET['filtroArchivos'];
    else $filtro=false;
    incluir('archivos');
   if (isset($_GET["limit"])) {
       $limit=$_GET["limit"];
       $offset=$_GET["offset"];

       if ($filtro) $query="SELECT * FROM archivos WHERE archivo like '%$filtro%' AND (idComunidad={$_SESSION['idComunidad']} OR idUsuario={$_SESSION['idUsuario']}) ORDER BY fecha DESC LIMIT $offset,$limit";
       else $query="SELECT * FROM archivos WHERE (idComunidad={$_SESSION['idComunidad']} OR idUsuario={$_SESSION['idUsuario']}) ORDER BY fecha DESC LIMIT $offset,$limit";
     }
     else {
       $JS.="$('#listadoArchivos').html('');";
       $limit=5;
       if ($filtro) $query="SELECT * FROM archivos WHERE archivo like '%$filtro%' AND (idComunidad={$_SESSION['idComunidad']} OR idUsuario={$_SESSION['idUsuario']}) ORDER BY fecha DESC LIMIT $limit";
       else $query="SELECT * FROM archivos WHERE (idComunidad={$_SESSION['idComunidad']} OR idUsuario={$_SESSION['idUsuario']}) ORDER BY fecha DESC LIMIT $limit";
     }
     $archivos=archivos::getAll($query);
     if ($archivos) {
       foreach ($archivos as $archivo) {
         $fecha=Strings::fecha($archivo['fecha']);
         if ($archivo['idUsuario']!=0) {
           incluir('usuarios');
           $usuario=classInvoker::nc('usuarios',intVal($archivo['idUsuario']));
           $destino="{$usuario->nombre}($usuario->usuario)";
         }
         else {
           incluir('comunidades');
           $comunidad=classInvoker::nc('comunidades',intVal($archivo['idComunidad']));
           $destino="{$comunidad->comunidad}";
         }
         $JS.="$('#listadoArchivos').append('<div  class=listado>{$archivo['archivo']}.{$archivo['extension']} <div class=\"iconos\">".
         "<label class=\"fa fa-download buttons\" style=\"background-color:#0ca2a9\" onclick=\"document.getElementById(\'formDownloadFile{$archivo['id']}\').submit()\"></label>".
         "</div><div id=archivo{$archivo['id']}  class=\"compress\" ><br>Subido para $destino el $fecha<br>Descargado {$archivo['numDescargas']} veces.<form id=formDownloadFile{$archivo['id']} target=downloadIFrame action=\"./PHP/actions/download.php\" method=GET><input id=idArchivo name=idArchivo value={$archivo['id']} type=hidden></form></div></div> ');";
       }
       if (isset($offset)) $offset+=$limit;
       else $offset=$limit;
       if ($filtro) $JS.="executeAction('rellenarPanel','&destino=archivos&offset=$offset&limit=$limit&filtroArchivos=$filtro');";
       else $JS.="executeAction('rellenarPanel','&destino=archivos&offset=$offset&limit=$limit');";
     }
     else {
       $JS.="$('#panelAdd').off('click');slide(8);";

     }

  }
  break;
  case "empleados":
   
    if (Session::allowed()){
      incluir('empleados');
      $JS.="$('#listadoEmpleados').html('');";
      $query="SELECT * from empleados";
       $empleados=empleados::getAll($query);
       if ($empleados) {
        foreach ($empleados as $empleado) {
            $JS.="$('#listadoEmpleados').append('<div  class=listado> {$empleado['nombre']} <div class=\"iconos\">".
         "<label class=\"fa fa-pencil buttons\" style=\"background-color:#0ca2a9\" onclick=\"executeAction(\'prepararPanel\',\'&destino=panelAuxiliarEmpleados&idEmpleado={$empleado['id']}\')\"></label>".
         "<label class=\"fa fa-print buttons\" style=\"background-color:#961e18\" onclick=\"executeAction(\'prepararPanel\',\'&destino=panelAuxiliarEmpleadosPDF\')\"></label>".
         "<label class=\"fa fa-trash buttons\" style=\"background-color:#274e73\" onclick=\"executeAction(\'eliminar\',\'&destino=empleados&idEmpleado={$empleado['id']}\')\" ></label>".
         "</div></div>');";

        }
      }
      $JS.="$('#panelAdd').off('click');$('#panelAdd').click(function () {executeAction('prepararPanel','&destino=panelAuxiliarEmpleados')});slide(11);";
    }
  break;
  case "panelAuxiliarEmpleadosPDF":
    if (Session::allowed()){
      incluir('empleados');
      $query="SELECT * from empleados";
      $empleados=empleados::getAll($query);
      if ($empleados) {
        foreach ($empleados as $empleado) {
          $JS.="$('#divListadoEmpleados').append('<label><input style=\'\' type=checkbox id=checkBox{$empleado['id']} value={$empleado['empleado']} checked>{$empleado['empleado']}</label><br>');";
        
      }
      $firstDayLastMonth = new DateTime('first day of last month');
      $lastDayLastMonth = new DateTime('last day of last month');
      $SfirstDay=$firstDayLastMonth->format('d/m/Y');
      $SlastDay=$lastDayLastMonth->format('d/m/Y');
      $JS.="$('#inputFechaInicioPDF').val('{$SfirstDay}');$('#inputFechaFinPDF').val('{$SlastDay}');$('#panelAuxiliar').fadeIn('slow');";

    }
  }  
  break;
}
  echo $JS;
}
?>