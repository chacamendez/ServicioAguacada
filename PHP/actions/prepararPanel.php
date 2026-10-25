<?php
session_start();

// DIAGNÓSTICO INICIAL Y VALIDACIÓN DE SESIÓN
error_log("[DEBUG EXECUTEACTION] Entrando. isLogged: " . (Session::isLogged() ? 'SÍ' : 'NO') . " | GET: " . print_r($_GET, true) . " | SESSION: " . print_r($_SESSION, true));

if (Session::isLogged()) {
  $JS = "";
  $destino = $_GET['destino'] ?? '';
  
  switch ($destino) {
    case "panelLateral":
        error_log("[DEBUG PANEL] Entrando en panelLateral. idUsuario: " . ($_SESSION['idUsuario'] ?? 'NO SET') . ", rol: " . ($_SESSION['idRol'] ?? 'NO SET'));
      
        if (Session::allowed()) {
            error_log("[DEBUG PANEL] Session::allowed() es TRUE. Cargando paneles de Administrador.");
            
            $JS .= "loadLayout('panelHome','panelHome');executeAction('rellenarPanel','&destino=home');";
            $JS .= "Menu.collapse();loadLayout('panelLateral','panelLateralContenido',true,false,'animatePanelLateral();";
            $JS .= "slide(4);');loadLayout('panelUsuarios','panelUsuarios');loadLayout('panelComunidades','panelComunidades');";
            $JS .= "loadLayout('panelMensajes','panelMensajes');loadLayout('panelArchivos','panelArchivos');$('#panelAdd').css('display','block');";
            $JS .= "loadLayout('panelCorrespondencias','panelCorrespondencias');loadLayout('panelRecibos','panelRecibos');";
            $JS .= "loadLayout('panelEmpleados','panelEmpleados');";
            $JS .= "loadLayout('panelExportar','panelExportar');";
        } else {
            error_log("[DEBUG PANEL] Session::allowed() es FALSE. Cargando paneles de Cliente.");
            
            incluir('usuarios');
            $usuario = classInvoker::nc('usuarios', intval($_SESSION['idUsuario']));
            $_SESSION['idComunidad'] = $usuario->idComunidad;
            
            error_log("[DEBUG PANEL] Usuario reset status: " . $usuario->reset);

            if ($usuario->reset == 1) {
                error_log("[DEBUG PANEL] El usuario requiere reseteo de clave. Cargando panelAuxiliarUsuariosClientes.");
                $JS .= "loadLayout('panelAuxiliarUsuariosClientes','panelAuxiliar',true,false,'executeAction(\'rellenarPanel\',\'&destino=panelAuxiliarUsuariosClientes&idUsuario={$_SESSION['idUsuario']}\');');";
            } else {
                error_log("[DEBUG PANEL] Cargando panelHomeClientes y panelLateralClientes.");
                $JS .= "loadLayout('panelHomeClientes','panelHome',true,false,'executeAction(\"rellenarPanel\",\"&destino=home\");');";
                $JS .= "Menu.collapse();loadLayout('panelLateralClientes','panelLateralContenido',true,false,'animatePanelLateral();";
                $JS .= "slide(4);');";
                $JS .= "loadLayout('panelArchivosClientes','panelArchivos');";
            }
        }
        break;

    case "home":
        break;

    case "usuarios":
      if (Session::allowed()) {
          $JS .= "loadLayout('panelUsuarios','panelUsuarios')";
      } else {
        $JS .= "$('#panelAuxiliar').fadeOut('slow');loadLayout('panelAuxiliarUsuariosClientes','panelAuxiliar',true,false,'executeAction(\"rellenarPanel\",\"&destino=panelAuxiliarUsuariosClientes&idUsuario={$_SESSION['idUsuario']}\");');";
      }
      break;

    case "panelAuxiliarUsuarios":
     if (Session::allowed()) {
       $JS .= "loadLayout('panelAuxiliarUsuarios','panelAuxiliar',true,false,'executeAction(\"rellenarPanel\",\"&destino=panelAuxiliarUsuarios\");');";
     }
     break;

    case "panelAuxiliarUsuariosEditar":
     if (Session::allowed()) {
       $idUsuario = $_GET["idUsuario"] ?? 0;
       $JS .= "loadLayout('panelAuxiliarUsuarios','panelAuxiliar',true,false,'executeAction(\"rellenarPanel\",\"&destino=panelAuxiliarUsuarios\");executeAction(\"editar\",\"&destino=panelAuxiliarUsuarios&idUsuario=$idUsuario\");');";
     }
     break;

    case "panelAuxiliarMensajes":
     if (Session::allowed()) {
      $idUsuario = $_GET["idUsuario"] ?? 0;
      $JS .= "loadLayout('panelAuxiliarMensajes','panelAuxiliar',true,false,'executeAction(\"rellenarPanel\",\"&destino=panelAuxiliarMensajes&idUsuario=$idUsuario\");');";
    }
    break;

    case "panelAuxiliarTelefonos":
    if (Session::allowed()) {
     $idUsuario = $_GET["idUsuario"] ?? 0;
     $JS .= "loadLayout('panelAuxiliarTelefonos','panelAuxiliar',true,false,'executeAction(\"rellenarPanel\",\"&destino=panelAuxiliarTelefonos&idUsuario=$idUsuario\");');";
    }
    break;

    case "panelAuxiliarCorreos":
    if (Session::allowed()) {
     $idUsuario = $_GET["idUsuario"] ?? 0;
     $JS .= "loadLayout('panelAuxiliarCorreos','panelAuxiliar',true,false,'executeAction(\"rellenarPanel\",\"&destino=panelAuxiliarCorreos&idUsuario=$idUsuario\");');";
    }
    break;

    case "panelAuxiliarDirecciones":
    if (Session::allowed()) {
     $idUsuario = $_GET["idUsuario"] ?? 0;
     $JS .= "loadLayout('panelAuxiliarDirecciones','panelAuxiliar',true,false,'executeAction(\"rellenarPanel\",\"&destino=panelAuxiliarDirecciones&idUsuario=$idUsuario&idDireccion=false\");');";
    }
    break;

    case "panelAuxiliarEditarDirecciones":
    if (Session::allowed()) {
     $idUsuario = $_GET["idUsuario"] ?? 0;
     $idDireccion = $_GET["idDireccion"] ?? 0;
     $JS .= "loadLayout('panelAuxiliarDirecciones','panelAuxiliar',true,false,'executeAction(\"rellenarPanel\",\"&destino=panelAuxiliarDirecciones&idUsuario=$idUsuario&idDireccion=$idDireccion\");');";
    }
    break;

    case "panelAuxiliarArchivos":
      if (Session::allowed()) {
        $idComunidad = $_GET["idComunidad"] ?? 0;
        $idUsuario = $_GET["idUsuario"] ?? 0;
        if (empty($idComunidad)) $idComunidad = 0;
        if (empty($idUsuario)) $idUsuario = 0;
        $JS .= "loadLayout('panelAuxiliarArchivos','panelAuxiliar',true,false,'executeAction(\"rellenarPanel\",\"&destino=panelAuxiliarArchivos&idUsuario=$idUsuario&idComunidad=$idComunidad\");');";
      }
      break;

    case "panelAuxiliarHistorial":
    if (Session::allowed()) {
      $idComunidad = $_GET["idComunidad"] ?? 0;
      $idUsuario = $_GET["idUsuario"] ?? 0;
      if (empty($idComunidad)) $idComunidad = 0;
      if (empty($idUsuario)) $idUsuario = 0;
      $JS .= "loadLayout('panelAuxiliarHistorial','panelAuxiliar',true,false,'executeAction(\"rellenarPanel\",\"&destino=panelAuxiliarHistorial&idUsuario=$idUsuario&idComunidad=$idComunidad\");');";
    }
    break;

    case "panelAuxiliarComunidades":
      if (Session::allowed()) {
        $idComunidad = $_GET["idComunidad"] ?? '';
        $JS .= "loadLayout('panelAuxiliarComunidades','panelAuxiliar',true,false,'executeAction(\"rellenarPanel\",\"&destino=panelAuxiliarComunidades&idComunidad=$idComunidad\");');";
      }
      break;

    case "panelAuxiliarCorrespondencias":
    if (Session::allowed()) {
      $idCorrespondencia = $_GET["idCorrespondencia"] ?? '';
      $JS .= "loadLayout('panelAuxiliarCorrespondencias','panelAuxiliar',true,false,'executeAction(\"rellenarPanel\",\"&destino=panelAuxiliarCorrespondencias&idCorrespondencia=$idCorrespondencia\");');";
    }
    break;

    case "panelAuxiliarExportar":
    if (Session::allowed()) {
      $idExportacion = $_GET["idExportacion"] ?? '';
      $JS .= "loadLayout('panelAuxiliarExportar','panelAuxiliar',true,false,'executeAction(\"rellenarPanel\",\"&destino=panelAuxiliarExportar&idExportacion=$idExportacion\");');";
    }
    break;

    case "comunidades":
      if (!Session::allowed()) {
        $JS = "iqwerty.toast.Toast('No tiene permisos',{settings:{duration:1000}});";
      }
      break;

    case "mensajes":
       if (Session::allowed()) {
         $JS .= "loadLayout('panelMensajes','panelMensajes',true);";
       } else {
         $idUsuario = $_SESSION['idUsuario'] ?? 0;
         $JS .= "$('#panelAuxiliar').fadeOut('slow');loadLayout('panelAuxiliarMensajes','panelAuxiliar',true,false,'executeAction(\"rellenarPanel\",\"&destino=panelAuxiliarMensajes&idUsuario=$idUsuario\");');";
       }
       break;

    case "archivos":
       if (Session::allowed()) {
           $JS .= "loadLayout('panelArchivos','panelArchivos',true);";
       } else {
           $JS = "$('#panelAuxiliar').fadeOut('slow');iqwerty.toast.Toast('No tiene permisos',{settings:{duration:1000}});";
       }
       break;

    case "panelAuxiliarFincas":
    if (Session::allowed()) {
      $idUsuario = $_GET["idUsuario"] ?? 0;
      $JS .= "loadLayout('panelAuxiliarFincas','panelAuxiliar',true,false,'executeAction(\"rellenarPanel\",\"&destino=panelAuxiliarFincas&idUsuario=$idUsuario\");');";
    }
    break;

    case "panelAuxiliarEditarFincas":
    if (Session::allowed()) {
      $idUsuario = $_GET["idUsuario"] ?? 0;
      $idFinca = $_GET["idFinca"] ?? 0;
      $JS .= "loadLayout('panelAuxiliarEditarFincas','panelAuxiliar',true,false,'executeAction(\"rellenarPanel\",\"&destino=panelAuxiliarEditarFincas&idUsuario=$idUsuario&idFinca=$idFinca\");');";
    }
    break;

    case "panelAuxiliarRecibos":
    if (Session::allowed()) {
      $idUsuario = $_GET["idUsuario"] ?? 0;
      $JS .= "loadLayout('panelAuxiliarRecibos','panelAuxiliar',true,false,'executeAction(\"rellenarPanel\",\"&destino=panelAuxiliarRecibos&idUsuario=$idUsuario\");');";
    }
    break;

    case "panelAuxiliarEmpleados":
      if (Session::allowed()) {
        $idEmpleado = $_GET['idEmpleado'] ?? '';
        $JS .= "loadLayout('panelAuxiliarEmpleados','panelAuxiliar',true,false,'executeAction(\"rellenarPanel\",\"&destino=panelAuxiliarEmpleados&idEmpleado=$idEmpleado\");');";
      }
      break;

    case "panelAuxiliarEmpleadosPDF":
      if (Session::allowed()) {
        $idEmpleado = $_GET['idEmpleado'] ?? '';
        $JS .= "loadLayout('panelAuxiliarEmpleadosPDF','panelAuxiliar',true,false,'executeAction(\"rellenarPanel\",\"&destino=panelAuxiliarEmpleadosPDF\");');";
      }
      break;
  }
  
  echo $JS;
} else {
    error_log("[DEBUG EXECUTEACTION ERROR] Session::isLogged() ha devuelto FALSE. Peticion bloqueada.");
}
?>