<?php
session_start();

if(Session::isLogged()) {
  $JS="";
  $destino=$_GET['destino'];
  switch ($destino){
    case "usuario":
      if (Session::allowed()){
        if (isset($_GET['confirm'])) $confirm=$_GET['confirm'];
        else $confirm=false;
        $idUsuario=$_GET['idUsuario'];
        if ($confirm){
          //eliminar usuario
          $usuario=classInvoker::nc('usuarios',intval($idUsuario),true);
          $usuario->eliminar();
          $JS.="iqwerty.toast.Toast('Usuario eliminado con éxito',{settings:{duration:1000}});executeAction('rellenarPanel','&destino=usuarios','filtroUsuarios');";
        }
        else {
          $JS="bootbox.confirm({
            message: 'Va a eliminar un usuario, todos sus datos serán borrados ¿Desea continuar?',
            buttons: {
              confirm: {
                label: 'Continuar',
                className: 'btn-success'
              },
              cancel: {
                label: 'Cancelar',
                className: 'btn-danger'
              }
            },
            callback: function (result) {
              if (result){
                executeAction('eliminar','&destino=usuario&idUsuario=$idUsuario&confirm=true');
              }
            }
          }).find('.modal-content').css('margin-top','300px');";
        }

      }
    break;
    case "comunidades":
    if (Session::allowed()){
      if (isset($_GET['confirm'])) $confirm=$_GET['confirm'];
      else $confirm=false;
      $idComunidad=$_GET['idComunidad'];
      if ($confirm){
        $comunidad=classInvoker::nc('comunidades',intval($idComunidad),true);
        if ($comunidad->tieneUsuarios()){
			$JS.="iqwerty.toast.Toast('No se puede borrar. Esta comunidad tiene usuarios',{settings:{duration:1000}});";
          }
          else {
            $JS.="iqwerty.toast.Toast('Comunidad eliminada con éxito',{settings:{duration:1000}});executeAction('rellenarPanel','&destino=comunidades')";
            $comunidad->deleteExt();
          }
        }
        else{
          $JS="bootbox.confirm({
            message: 'Va a eliminar una comunidad permanentemente ¿Desea continuar?',
            buttons: {
              confirm: {
                label: 'Continuar',
                className: 'btn-success'
              },
              cancel: {
                label: 'Cancelar',
                className: 'btn-danger'
              }
            },
            callback: function (result) {
              if (result){
                executeAction('eliminar','&destino=comunidades&idComunidad=$idComunidad&confirm=true');
              }
            }
          }).find('.modal-content').css('margin-top','300px');";
        }
    }
    break;
    case "archivos":
    if (Session::allowed()){
      if (isset($_GET['confirm'])) $confirm=$_GET['confirm'];
      else $confirm=false;
      $idComunidad=$_GET['idComunidad'];
      $idUsuario=$_GET['idUsuario'];
      $idArchivo=$_GET['idArchivo'];
      if (empty($_GET["fromArchivos"])) $fromArchivos=false;
      else $fromArchivos=true;
      if ($confirm){
        //eliminar usuario
        $archivo=classInvoker::nc('archivos',intval($idArchivo),true);
        $archivo->deleteExt();
        if ($fromArchivos){
          $JS.="iqwerty.toast.Toast('Archivo eliminado con éxito',{settings:{duration:1000}});executeAction('rellenarPanel','&destino=archivos&idComunidad=$idComunidad&idUsuario=$idUsuario');";
        }
        else $JS.="iqwerty.toast.Toast('Archivo eliminado con éxito',{settings:{duration:1000}});executeAction('rellenarPanel','&destino=panelAuxiliarArchivos&idComunidad=$idComunidad&idUsuario=$idUsuario');";
      }
      else {
        $JS="bootbox.confirm({
          message: 'Va a eliminar un archivo permanentemente ¿Desea continuar?',
          buttons: {
            confirm: {
              label: 'Continuar',
              className: 'btn-success'
            },
            cancel: {
              label: 'Cancelar',
              className: 'btn-danger'
            }
          },
          callback: function (result) {
            if (result){
              executeAction('eliminar','&destino=archivos&idArchivo=$idArchivo&idComunidad=$idComunidad&idUsuario=$idUsuario&confirm=true&fromArchivos=$fromArchivos');
            }
          }
        }).find('.modal-content').css('margin-top','300px');";
      }
    }
    break;
    case "historial":
    if (Session::allowed()){
      if (isset($_GET['confirm'])) $confirm=$_GET['confirm'];
      else $confirm=false;
      $idComunidad=$_GET['idComunidad'];
      $idUsuario=$_GET['idUsuario'];
      $idEvento=$_GET['idEvento'];
      if ($confirm){
        //eliminar usuario
        $evento=classInvoker::nc('historial',intval($idEvento));
        $evento->delete();
        $JS.="iqwerty.toast.Toast('Evento eliminado con éxito',{settings:{duration:1000}});executeAction('rellenarPanel','&destino=panelAuxiliarHistorial&idComunidad=$idComunidad&idUsuario=$idUsuario');";
      }
      else {
        $JS="bootbox.confirm({
          message: 'Va a eliminar un evento permanentemente ¿Desea continuar?',
          buttons: {
            confirm: {
              label: 'Continuar',
              className: 'btn-success'
            },
            cancel: {
              label: 'Cancelar',
              className: 'btn-danger'
            }
          },
          callback: function (result) {
            if (result){
              executeAction('eliminar','&destino=historial&idEvento=$idEvento&idComunidad=$idComunidad&idUsuario=$idUsuario&confirm=true');
            }
          }
        }).find('.modal-content').css('margin-top','300px');";
      }
    }
    break;
    case "telefonos":
    if (Session::allowed()){
      if (isset($_GET['confirm'])) $confirm=$_GET['confirm'];
      else $confirm=false;
      $idTelefono=$_GET['idTelefono'];
      if ($confirm){
        //eliminar telefono
        $telefono=classInvoker::nc('telefonos',intval($idTelefono));
        $telefono->delete();
        $JS.="iqwerty.toast.Toast('Teléfono eliminado con éxito',{settings:{duration:1000}});executeAction('rellenarPanel','&destino=usuarios','filtroUsuarios');";
      }
      else {
        $JS="bootbox.confirm({
          message: 'Va a eliminar un teléfono permanentemente ¿Desea continuar?',
          buttons: {
            confirm: {
              label: 'Continuar',
              className: 'btn-success'
            },
            cancel: {
              label: 'Cancelar',
              className: 'btn-danger'
            }
          },
          callback: function (result) {
            if (result){
              executeAction('eliminar','&destino=telefonos&idTelefono=$idTelefono&confirm=true');
            }
          }
        }).find('.modal-content').css('margin-top','300px');";
      }
    }
    break;
    case "correos":
    if (Session::allowed()){
      if (isset($_GET['confirm'])) $confirm=$_GET['confirm'];
      else $confirm=false;
      $idCorreo=$_GET['idCorreo'];
      if ($confirm){
        //eliminar correo
        $correo=classInvoker::nc('correos',intval($idCorreo));
        $correo->delete();
        $JS.="iqwerty.toast.Toast('Correo eliminado con éxito',{settings:{duration:1000}});executeAction('rellenarPanel','&destino=usuarios','filtroUsuarios');";
      }
      else {
        $JS="bootbox.confirm({
          message: 'Va a eliminar un correo permanentemente ¿Desea continuar?',
          buttons: {
            confirm: {
              label: 'Continuar',
              className: 'btn-success'
            },
            cancel: {
              label: 'Cancelar',
              className: 'btn-danger'
            }
          },
          callback: function (result) {
            if (result){
              executeAction('eliminar','&destino=correos&idCorreo=$idCorreo&confirm=true');
            }
          }
        }).find('.modal-content').css('margin-top','300px');";
      }
    }
    break;
    case "direccionesPostales":
    if (Session::allowed()){
      if (isset($_GET['confirm'])) $confirm=$_GET['confirm'];
      else $confirm=false;
      $idDireccion=$_GET['idDireccion'];
      if ($confirm){
        //comprueba que no este incluida en una correspondencias
        incluir('correspondencias',true);
        if (correspondenciasExt::checkDireccion($idDireccion)){
          $JS.="iqwerty.toast.Toast('No se puede eliminar la dirección por estar incluida en una correspondencia',{settings:{duration:2000}});executeAction('rellenarPanel','&destino=usuarios','filtroUsuarios');";
        }
        else{
          //eliminar direccion
          $direccion=classInvoker::nc('direccionesPostales',intval($idDireccion));
          $direccion->delete();
          $JS.="iqwerty.toast.Toast('Dirección postal eliminada con éxito',{settings:{duration:1000}});executeAction('rellenarPanel','&destino=usuarios','filtroUsuarios');";

        }

      }
      else {
        $JS="bootbox.confirm({
          message: 'Va a eliminar una dirección postal permanentemente ¿Desea continuar?',
          buttons: {
            confirm: {
              label: 'Continuar',
              className: 'btn-success'
            },
            cancel: {
              label: 'Cancelar',
              className: 'btn-danger'
            }
          },
          callback: function (result) {
            if (result){
              executeAction('eliminar','&destino=direccionesPostales&idDireccion=$idDireccion&confirm=true');
            }
          }
        }).find('.modal-content').css('margin-top','300px');";
      }
    }
    break;
    case "correspondencias":
    if (Session::allowed()){
      if (isset($_GET['confirm'])) $confirm=$_GET['confirm'];
      else $confirm=false;
      $idCorrespondencia=$_GET['idCorrespondencia'];
      if ($confirm){
        //eliminar correspondencia
        $correspondencia=classInvoker::nc('correspondencias',intval($idCorrespondencia));
        $correspondencia->delete();
        $JS.="iqwerty.toast.Toast('Correspondencia eliminada con éxito',{settings:{duration:1000}});executeAction('rellenarPanel','&destino=correspondencias');";
      }
      else {
        $JS="bootbox.confirm({
          message: 'Va a eliminar una correspondencia permanentemente ¿Desea continuar?',
          buttons: {
            confirm: {
              label: 'Continuar',
              className: 'btn-success'
            },
            cancel: {
              label: 'Cancelar',
              className: 'btn-danger'
            }
          },
          callback: function (result) {
            if (result){
              executeAction('eliminar','&destino=correspondencias&idCorrespondencia=$idCorrespondencia&confirm=true');
            }
          }
        }).find('.modal-content').css('margin-top','300px');";
      }
    }
    break;
    case "exportar":
    if (Session::allowed()){
      if (isset($_GET['confirm'])) $confirm=$_GET['confirm'];
      else $confirm=false;
      $idExportacion=$_GET['idExportacion'];
      if ($confirm){
        //eliminar exportacion
        $exportacion=classInvoker::nc('exportar',intval($idExportacion));
        $exportacion->delete();
        $JS.="iqwerty.toast.Toast('Exportación eliminada con éxito',{settings:{duration:1000}});executeAction('rellenarPanel','&destino=exportar');";
      }
      else {
        $JS="bootbox.confirm({
          message: 'Va a eliminar una exportación permanentemente ¿Desea continuar?',
          buttons: {
            confirm: {
              label: 'Continuar',
              className: 'btn-success'
            },
            cancel: {
              label: 'Cancelar',
              className: 'btn-danger'
            }
          },
          callback: function (result) {
            if (result){
              executeAction('eliminar','&destino=exportar&idExportacion=$idExportacion&confirm=true');
            }
          }
        }).find('.modal-content').css('margin-top','300px');";
      }
    }
    break;
    case "fincas":
    if (Session::allowed()){
      if (isset($_GET['confirm'])) $confirm=$_GET['confirm'];
      else $confirm=false;
      $idFinca=$_GET['idFinca'];
      if ($confirm){
        //eliminar finca
        $finca=classInvoker::nc('fincas',intval($idFinca),true);
        $finca->deleteFinca();
        $JS.="iqwerty.toast.Toast('Finca eliminada con éxito',{settings:{duration:1000}});executeAction('rellenarPanel','&destino=usuarios','filtroUsuarios');";
      }
      else {
        $JS="bootbox.confirm({
          message: 'Va a eliminar una finca permanentemente ¿Desea continuar?',
          buttons: {
            confirm: {
              label: 'Continuar',
              className: 'btn-success'
            },
            cancel: {
              label: 'Cancelar',
              className: 'btn-danger'
            }
          },
          callback: function (result) {
            if (result){
              executeAction('eliminar','&destino=fincas&idFinca=$idFinca&confirm=true');
            }
          }
        }).find('.modal-content').css('margin-top','300px');";
      }
    }
    break;
    case "propietarios":
    if (Session::allowed()){
      if (isset($_GET['confirm'])) $confirm=$_GET['confirm'];
      else $confirm=false;
      $idUsuario=$_GET['idUsuario'];
      $idPropietario=$_GET["idPropietario"];
      $idFinca=$_GET['idFinca'];
      if ($confirm){
        //eliminar propietario en todas las fincas
        $propietario=classInvoker::nc('propietarios',intval($idPropietario),true);
        $propietario->deleteFincas($idUsuario);
        $JS.="iqwerty.toast.Toast('Propietario eliminado con éxito',{settings:{duration:1000}});executeAction('rellenarPanel','&destino=panelAuxiliarEditarFincas&idFinca=$idFinca');";
      }
      else {
        $JS="bootbox.confirm({
          message: 'Va a eliminar este propietario de todas las fincas ¿Desea continuar?',
          buttons: {
            confirm: {
              label: 'Continuar',
              className: 'btn-success'
            },
            cancel: {
              label: 'Cancelar',
              className: 'btn-danger'
            }
          },
          callback: function (result) {
            if (result){
              executeAction('eliminar','&destino=propietarios&idUsuario=$idUsuario&idPropietario=$idPropietario&idFinca=$idFinca&confirm=true');
            }
          }
        }).find('.modal-content').css('margin-top','300px');";
      }
    }
    break;
    case "recibos":
    if (Session::allowed()){
      if (isset($_GET['confirm'])) $confirm=$_GET['confirm'];
      else $confirm=false;
      $idRecibo=$_GET['idRecibo'];
      if ($confirm){
        //eliminar finca
        $recibo=classInvoker::nc('recibos',intval($idRecibo));
        $recibo->delete();
          $JS.="iqwerty.toast.Toast('Recibo eliminado con éxito',{settings:{duration:1000}});executeAction('rellenarPanel','&destino=recibos&idUsuario={$recibo->idUsuario}');";
      }
      else {
        $JS="bootbox.confirm({
          message: 'Va a eliminar un recibo ¿Desea continuar?',
          buttons: {
            confirm: {
              label: 'Continuar',
              className: 'btn-success'
            },
            cancel: {
              label: 'Cancelar',
              className: 'btn-danger'
            }
          },
          callback: function (result) {
            if (result){
              executeAction('eliminar','&destino=recibos&idRecibo=$idRecibo&confirm=true');
            }
          }
        }).find('.modal-content').css('margin-top','300px');";
      }
    }
    break;

    case "panelAuxiliarRecibos":
    if (Session::allowed()){
      if (isset($_GET['confirm'])) $confirm=$_GET['confirm'];
      else $confirm=false;
      $idRecibo=$_GET['idRecibo'];
      if ($confirm){
        //eliminar finca
        $recibo=classInvoker::nc('recibos',intval($idRecibo));
        $recibo->delete();
          $JS.="iqwerty.toast.Toast('Recibo eliminado con éxito',{settings:{duration:1000}});executeAction('rellenarPanel','&destino=panelAuxiliarRecibos&idUsuario={$recibo->idUsuario}');";
      }
      else {
        $JS="bootbox.confirm({
          message: 'Va a eliminar un recibo ¿Desea continuar?',
          buttons: {
            confirm: {
              label: 'Continuar',
              className: 'btn-success'
            },
            cancel: {
              label: 'Cancelar',
              className: 'btn-danger'
            }
          },
          callback: function (result) {
            if (result){
              executeAction('eliminar','&destino=panelAuxiliarRecibos&idRecibo=$idRecibo&confirm=true');
            }
          }
        }).find('.modal-content').css('margin-top','300px');";
      }
    }
    break;
    case "empleados":
      if (Session::allowed()){
        if (isset($_GET['confirm'])) $confirm=$_GET['confirm'];
        else $confirm=false;
        $idEmpleado=$_GET['idEmpleado'];
        if ($confirm){
          //eliminar empleado
          $empleado=classInvoker::nc('empleados',intval($idEmpleado));
          $empleado->delete();
            $JS.="iqwerty.toast.Toast('Empleado eliminado con éxito',{settings:{duration:1000}});executeAction('rellenarPanel','&destino=empleados');";
        }
        else {
          $JS="bootbox.confirm({
            message: 'Va a eliminar un empleado ¿Desea continuar?',
            buttons: {
              confirm: {
                label: 'Continuar',
                className: 'btn-success'
              },
              cancel: {
                label: 'Cancelar',
                className: 'btn-danger'
              }
            },
            callback: function (result) {
              if (result){
                executeAction('eliminar','&destino=empleados&idEmpleado=$idEmpleado&confirm=true');
              }
            }
          }).find('.modal-content').css('margin-top','300px');";
        }
      }
    break;
  }
  echo $JS;
}
?>
