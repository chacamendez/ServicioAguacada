<?php
session_start();

if(Session::isLogged()) {

  $JS="";
  $destino=$_GET['destino'];
  switch ($destino){
    case "panelLateral":
    if (Session::allowed()){
      $JS.="loadLayout('panelHome','panelHome');executeAction('rellenarPanel','&destino=home');";
      $JS.="Menu.collapse();loadLayout('panelLateral','panelLateralContenido',true,false,'animatePanelLateral();";
      $JS.="slide(4);');loadLayout('panelUsuarios','panelUsuarios');loadLayout('panelComunidades','panelComunidades');";
      $JS.="loadLayout('panelMensajes','panelMensajes');loadLayout('panelArchivos','panelArchivos');$('#panelAdd').css('display','block');";
      $JS.="loadLayout('panelCorrespondencias','panelCorrespondencias');loadLayout('panelRecibos','panelRecibos');";
      $JS.="loadLayout('panelEmpleados','panelEmpleados');";
      $JS.="loadLayout('panelExportar','panelExportar');";


    }
    else  {
        incluir('usuarios');
        $usuario=classInvoker::nc('usuarios',intval($_SESSION['idUsuario']));
        $_SESSION['idComunidad']=$usuario->idComunidad;
        if ($usuario->reset==1){
          $JS.="loadLayout('panelAuxiliarUsuariosClientes','panelAuxiliar',true,false,'executeAction(\'rellenarPanel\',\'&destino=panelAuxiliarUsuariosClientes&idUsuario={$_SESSION['idUsuario']}\');');";
        }
        else{
          $JS.="loadLayout('panelHomeClientes','panelHome',true,false,'executeAction(\"rellenarPanel\",\"&destino=home\");');";
          $JS.="Menu.collapse();loadLayout('panelLateralClientes','panelLateralContenido',true,false,'animatePanelLateral();";
          $JS.="slide(4);');";
          $JS.="loadLayout('panelArchivosClientes','panelArchivos');";
        }
    }

    break;
    case "home":
    break;
    case "usuarios":
      if (Session::allowed()){
          $JS.="loadLayout('panelUsuarios','panelUsuarios')";
      }
      else {
        $JS.="$('#panelAuxiliar').fadeOut('slow');loadLayout('panelAuxiliarUsuariosClientes','panelAuxiliar',true,false,'executeAction(\"rellenarPanel\",\"&destino=panelAuxiliarUsuariosClientes&idUsuario={$_SESSION['idUsuario']}\");');";
      }
    break;
    case "panelAuxiliarUsuarios":
     if (Session::allowed()){
       $JS.="loadLayout('panelAuxiliarUsuarios','panelAuxiliar',true,false,'executeAction(\"rellenarPanel\",\"&destino=panelAuxiliarUsuarios\");');";

     }
    break;
    case "panelAuxiliarUsuariosEditar":
     if (Session::allowed()){
       $idUsuario=$_GET["idUsuario"];
       $JS.="loadLayout('panelAuxiliarUsuarios','panelAuxiliar',true,false,'executeAction(\"rellenarPanel\",\"&destino=panelAuxiliarUsuarios\");executeAction(\"editar\",\"&destino=panelAuxiliarUsuarios&idUsuario=$idUsuario\");');";
     }
    break;
    case "panelAuxiliarMensajes":
     if (Session::allowed()){
      $idUsuario=$_GET["idUsuario"];
      $JS.="loadLayout('panelAuxiliarMensajes','panelAuxiliar',true,false,'executeAction(\"rellenarPanel\",\"&destino=panelAuxiliarMensajes&idUsuario=$idUsuario\");');";
    }
    break;
    case "panelAuxiliarTelefonos":
    if (Session::allowed()){
     $idUsuario=$_GET["idUsuario"];
     $JS.="loadLayout('panelAuxiliarTelefonos','panelAuxiliar',true,false,'executeAction(\"rellenarPanel\",\"&destino=panelAuxiliarTelefonos&idUsuario=$idUsuario\");');";
    }
    break;
    case "panelAuxiliarCorreos":
    if (Session::allowed()){
     $idUsuario=$_GET["idUsuario"];
     $JS.="loadLayout('panelAuxiliarCorreos','panelAuxiliar',true,false,'executeAction(\"rellenarPanel\",\"&destino=panelAuxiliarCorreos&idUsuario=$idUsuario\");');";
    }
    break;
    case "panelAuxiliarDirecciones":
    if (Session::allowed()){
     $idUsuario=$_GET["idUsuario"];
     $JS.="loadLayout('panelAuxiliarDirecciones','panelAuxiliar',true,false,'executeAction(\"rellenarPanel\",\"&destino=panelAuxiliarDirecciones&idUsuario=$idUsuario&idDireccion=false\");');";
    }
    break;
    case "panelAuxiliarEditarDirecciones":
    if (Session::allowed()){
     $idUsuario=$_GET["idUsuario"];
     $idDireccion=$_GET["idDireccion"];
     $JS.="loadLayout('panelAuxiliarDirecciones','panelAuxiliar',true,false,'executeAction(\"rellenarPanel\",\"&destino=panelAuxiliarDirecciones&idUsuario=$idUsuario&idDireccion=$idDireccion\");');";
    }
    break;
    case "panelAuxiliarArchivos":
      if (Session::allowed()){
        if (empty($_GET["idComunidad"])){
          $idComunidad=0;
          $idUsuario=$_GET["idUsuario"];
        }
        if (empty($_GET["idUsuario"])){
          $idUsuario=0;
          $idComunidad=$_GET["idComunidad"];
        }
        $JS.="loadLayout('panelAuxiliarArchivos','panelAuxiliar',true,false,'executeAction(\"rellenarPanel\",\"&destino=panelAuxiliarArchivos&idUsuario=$idUsuario&idComunidad=$idComunidad\");');";
      }
    break;
    case "panelAuxiliarHistorial":
    if (Session::allowed()){
      if (empty($_GET["idComunidad"])){
        $idComunidad=0;
        $idUsuario=$_GET["idUsuario"];
      }
      if (empty($_GET["idUsuario"])){
        $idUsuario=0;
        $idComunidad=$_GET["idComunidad"];
      }
      $JS.="loadLayout('panelAuxiliarHistorial','panelAuxiliar',true,false,'executeAction(\"rellenarPanel\",\"&destino=panelAuxiliarHistorial&idUsuario=$idUsuario&idComunidad=$idComunidad\");');";
    }
    break;
    case "panelAuxiliarComunidades":
      if (Session::allowed()){
        if (empty ($_GET["idComunidad"])) $idComunidad='';
        else $idComunidad=$_GET["idComunidad"];
         $JS.="loadLayout('panelAuxiliarComunidades','panelAuxiliar',true,false,'executeAction(\"rellenarPanel\",\"&destino=panelAuxiliarComunidades&idComunidad=$idComunidad\");');";
      }
    break;
    case "panelAuxiliarCorrespondencias":
    if (Session::allowed()){
      if (empty ($_GET["idCorrespondencia"])) $idCorrespondencia='';
      else $idCorrespondencia=$_GET["idCorrespondencia"];
      $JS.="loadLayout('panelAuxiliarCorrespondencias','panelAuxiliar',true,false,'executeAction(\"rellenarPanel\",\"&destino=panelAuxiliarCorrespondencias&idCorrespondencia=$idCorrespondencia\");');";
    }
    break;
    case "panelAuxiliarExportar":
    if (Session::allowed()){
      if (empty ($_GET["idExportacion"])) $idExportacion='';
      else $idExportacion=$_GET["idExportacion"];
      $JS.="loadLayout('panelAuxiliarExportar','panelAuxiliar',true,false,'executeAction(\"rellenarPanel\",\"&destino=panelAuxiliarExportar&idExportacion=$idExportacion\");');";
    }
    break;
    case "comunidades":
      if (Session::allowed()){

      }
      else $JS="iqwerty.toast.Toast('No tiene permisos',{settings:{duration:1000}});";
    break;
    case "mensajes":
       if (Session::allowed()){
         $JS.="loadLayout('panelMensajes','panelMensajes',true);";
       }
       else {
         $idUsuario=$_SESSION['idUsuario'];
         $JS.="$('#panelAuxiliar').fadeOut('slow');loadLayout('panelAuxiliarMensajes','panelAuxiliar',true,false,'executeAction(\"rellenarPanel\",\"&destino=panelAuxiliarMensajes&idUsuario=$idUsuario\");');";
       }
    break;
    case "archivos":
       if (Session::allowed()){
           $JS.="loadLayout('panelArchivos','panelArchivos',true);";
       }
      else $JS="$('#panelAuxiliar').fadeOut('slow');iqwerty.toast.Toast('No tiene permisos',{settings:{duration:1000}});";
    break;
    case "panelAuxiliarFincas":
    if (Session::allowed()){
      $idUsuario=$_GET["idUsuario"];
      $JS.="loadLayout('panelAuxiliarFincas','panelAuxiliar',true,false,'executeAction(\"rellenarPanel\",\"&destino=panelAuxiliarFincas&idUsuario=$idUsuario\");');";
    }
    break;
    case "panelAuxiliarEditarFincas":
    if (Session::allowed()){
      $idUsuario=$_GET["idUsuario"];
      $idFinca=$_GET["idFinca"];
      $JS.="loadLayout('panelAuxiliarEditarFincas','panelAuxiliar',true,false,'executeAction(\"rellenarPanel\",\"&destino=panelAuxiliarEditarFincas&idUsuario=$idUsuario&idFinca=$idFinca\");');";
    }
    break;
    case "panelAuxiliarRecibos":
    if (Session::allowed()){
      $idUsuario=$_GET["idUsuario"];
      $JS.="loadLayout('panelAuxiliarRecibos','panelAuxiliar',true,false,'executeAction(\"rellenarPanel\",\"&destino=panelAuxiliarRecibos&idUsuario=$idUsuario\");');";
    }
    break;
    case "panelAuxiliarEmpleados":
      if (Session::allowed()){
        if (isset($_GET['idEmpleado'])) $idEmpleado=$_GET["idEmpleado"];
        else $idEmpleado='';
        $JS.="loadLayout('panelAuxiliarEmpleados','panelAuxiliar',true,false,'executeAction(\"rellenarPanel\",\"&destino=panelAuxiliarEmpleados&idEmpleado=$idEmpleado\");');";
      }
    break;
    case "panelAuxiliarEmpleadosPDF":
      if (Session::allowed()){
        if (isset($_GET['idEmpleado'])) $idEmpleado=$_GET["idEmpleado"];
        else $idEmpleado='';
        $JS.="loadLayout('panelAuxiliarEmpleadosPDF','panelAuxiliar',true,false,'executeAction(\"rellenarPanel\",\"&destino=panelAuxiliarEmpleadosPDF\");');";
      }
    break;

  }
  echo $JS;
}
?>
