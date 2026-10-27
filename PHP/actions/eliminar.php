<?php
session_start();

if (Session::isLogged()) {
  $JS = "";
  $destino = $_GET['destino'] ?? '';

  switch ($destino) {
    case "usuario":
      if (Session::allowed()) {
        error_log("[DEBUG ELIMINAR USUARIO] --- INICIO eliminación de usuario ---");
        
        $confirm = isset($_GET['confirm']) && $_GET['confirm'] === 'true';
        $idUsuario = $_GET['idUsuario'] ?? 0;
        
        error_log("[DEBUG ELIMINAR USUARIO] confirm: " . ($confirm ? 'true' : 'false') . " | idUsuario recibido: " . var_export($idUsuario, true));

        if ($confirm) {
          // Eliminar usuario
          error_log("[DEBUG ELIMINAR USUARIO] Entrando en bloque de confirmación real para ID: " . intval($idUsuario));
          
          $usuario = classInvoker::nc('usuarios', intval($idUsuario), true);
          
          if ($usuario) {
            error_log("[DEBUG ELIMINAR USUARIO] Objeto usuario cargado correctamente. Llamando a ->eliminar()...");
            $usuario->eliminar();
            error_log("[DEBUG ELIMINAR USUARIO] ->eliminar() ejecutado con éxito.");
          } else {
            error_log("[DEBUG ELIMINAR USUARIO ERROR] No se pudo cargar el objeto usuario con ID: " . $idUsuario);
          }

          $JS .= "iqwerty.toast.Toast('Usuario eliminado con éxito',{settings:{duration:1000}});executeAction('rellenarPanel','&destino=usuarios','filtroUsuarios');";
        } else {
          error_log("[DEBUG ELIMINAR USUARIO] Mostrando ventana de confirmación Bootbox.");
          $JS = "bootbox.confirm({
            message: 'Va a eliminar un usuario, todos sus datos serán borrados ¿Desea continuar?',
            buttons: {
              confirm: { label: 'Continuar', className: 'btn-success' },
              cancel: { label: 'Cancelar', className: 'btn-danger' }
            },
            callback: function (result) {
              if (result) {
                executeAction('eliminar','&destino=usuario&idUsuario=$idUsuario&confirm=true');
              }
            }
          }).find('.modal-content').css('margin-top','300px');";
        }
        error_log("[DEBUG ELIMINAR USUARIO] --- FIN eliminación de usuario ---");
      }
      break;

    case "comunidades":
      if (Session::allowed()) {
        $confirm = isset($_GET['confirm']) && $_GET['confirm'] === 'true';
        $idComunidad = $_GET['idComunidad'] ?? 0;

        if ($confirm) {
          $comunidad = classInvoker::nc('comunidades', intval($idComunidad), true);
          if ($comunidad->tieneUsuarios()) {
            $JS .= "iqwerty.toast.Toast('No se puede borrar. Esta comunidad tiene usuarios',{settings:{duration:1000}});";
          } else {
            $JS .= "iqwerty.toast.Toast('Comunidad eliminada con éxito',{settings:{duration:1000}});executeAction('rellenarPanel','&destino=comunidades');";
            $comunidad->deleteExt();
          }
        } else {
          $JS = "bootbox.confirm({
            message: 'Va a eliminar una comunidad permanentemente ¿Desea continuar?',
            buttons: {
              confirm: { label: 'Continuar', className: 'btn-success' },
              cancel: { label: 'Cancelar', className: 'btn-danger' }
            },
            callback: function (result) {
              if (result) {
                executeAction('eliminar','&destino=comunidades&idComunidad=$idComunidad&confirm=true');
              }
            }
          }).find('.modal-content').css('margin-top','300px');";
        }
      }
      break;

    case "archivos":
      if (Session::allowed()) {
        $confirm = isset($_GET['confirm']) && $_GET['confirm'] === 'true';
        $idComunidad = $_GET['idComunidad'] ?? 0;
        $idUsuario = $_GET['idUsuario'] ?? 0;
        $idArchivo = $_GET['idArchivo'] ?? 0;
        $fromArchivos = !empty($_GET["fromArchivos"]);

        if ($confirm) {
          $archivo = classInvoker::nc('archivos', intval($idArchivo), true);
          $archivo->deleteExt();
          if ($fromArchivos) {
            $JS .= "iqwerty.toast.Toast('Archivo eliminado con éxito',{settings:{duration:1000}});executeAction('rellenarPanel','&destino=archivos&idComunidad=$idComunidad&idUsuario=$idUsuario');";
          } else {
            $JS .= "iqwerty.toast.Toast('Archivo eliminado con éxito',{settings:{duration:1000}});executeAction('rellenarPanel','&destino=panelAuxiliarArchivos&idComunidad=$idComunidad&idUsuario=$idUsuario');";
          }
        } else {
          $JS = "bootbox.confirm({
            message: 'Va a eliminar un archivo permanentemente ¿Desea continuar?',
            buttons: {
              confirm: { label: 'Continuar', className: 'btn-success' },
              cancel: { label: 'Cancelar', className: 'btn-danger' }
            },
            callback: function (result) {
              if (result) {
                executeAction('eliminar','&destino=archivos&idArchivo=$idArchivo&idComunidad=$idComunidad&idUsuario=$idUsuario&confirm=true&fromArchivos=$fromArchivos');
              }
            }
          }).find('.modal-content').css('margin-top','300px');";
        }
      }
      break;

    case "historial":
      if (Session::allowed()) {
        $confirm = isset($_GET['confirm']) && $_GET['confirm'] === 'true';
        $idComunidad = $_GET['idComunidad'] ?? 0;
        $idUsuario = $_GET['idUsuario'] ?? 0;
        $idEvento = $_GET['idEvento'] ?? 0;

        if ($confirm) {
          $evento = classInvoker::nc('historial', intval($idEvento));
          $evento->delete();
          $JS .= "iqwerty.toast.Toast('Evento eliminado con éxito',{settings:{duration:1000}});executeAction('rellenarPanel','&destino=panelAuxiliarHistorial&idComunidad=$idComunidad&idUsuario=$idUsuario');";
        } else {
          $JS = "bootbox.confirm({
            message: 'Va a eliminar un evento permanentemente ¿Desea continuar?',
            buttons: {
              confirm: { label: 'Continuar', className: 'btn-success' },
              cancel: { label: 'Cancelar', className: 'btn-danger' }
            },
            callback: function (result) {
              if (result) {
                executeAction('eliminar','&destino=historial&idEvento=$idEvento&idComunidad=$idComunidad&idUsuario=$idUsuario&confirm=true');
              }
            }
          }).find('.modal-content').css('margin-top','300px');";
        }
      }
      break;

    case "telefonos":
      if (Session::allowed()) {
        $confirm = isset($_GET['confirm']) && $_GET['confirm'] === 'true';
        $idTelefono = $_GET['idTelefono'] ?? 0;

        if ($confirm) {
          $telefono = classInvoker::nc('telefonos', intval($idTelefono));
          $telefono->delete();
          $JS .= "iqwerty.toast.Toast('Teléfono eliminado con éxito',{settings:{duration:1000}});executeAction('rellenarPanel','&destino=usuarios','filtroUsuarios');";
        } else {
          $JS = "bootbox.confirm({
            message: 'Va a eliminar un teléfono permanentemente ¿Desea continuar?',
            buttons: {
              confirm: { label: 'Continuar', className: 'btn-success' },
              cancel: { label: 'Cancelar', className: 'btn-danger' }
            },
            callback: function (result) {
              if (result) {
                executeAction('eliminar','&destino=telefonos&idTelefono=$idTelefono&confirm=true');
              }
            }
          }).find('.modal-content').css('margin-top','300px');";
        }
      }
      break;

    case "correos":
      if (Session::allowed()) {
        $confirm = isset($_GET['confirm']) && $_GET['confirm'] === 'true';
        $idCorreo = $_GET['idCorreo'] ?? 0;

        if ($confirm) {
          $correo = classInvoker::nc('correos', intval($idCorreo));
          $correo->delete();
          $JS .= "iqwerty.toast.Toast('Correo eliminado con éxito',{settings:{duration:1000}});executeAction('rellenarPanel','&destino=usuarios','filtroUsuarios');";
        } else {
          $JS = "bootbox.confirm({
            message: 'Va a eliminar un correo permanentemente ¿Desea continuar?',
            buttons: {
              confirm: { label: 'Continuar', className: 'btn-success' },
              cancel: { label: 'Cancelar', className: 'btn-danger' }
            },
            callback: function (result) {
              if (result) {
                executeAction('eliminar','&destino=correos&idCorreo=$idCorreo&confirm=true');
              }
            }
          }).find('.modal-content').css('margin-top','300px');";
        }
      }
      break;

    case "direccionesPostales":
      if (Session::allowed()) {
        $confirm = isset($_GET['confirm']) && $_GET['confirm'] === 'true';
        $idDireccion = $_GET['idDireccion'] ?? 0;

        if ($confirm) {
          incluir('correspondencias', true);
          if (correspondenciasExt::checkDireccion($idDireccion)) {
            $JS .= "iqwerty.toast.Toast('No se puede eliminar la dirección por estar incluida en una correspondencia',{settings:{duration:2000}});executeAction('rellenarPanel','&destino=usuarios','filtroUsuarios');";
          } else {
            $direccion = classInvoker::nc('direccionesPostales', intval($idDireccion));
            $direccion->delete();
            $JS .= "iqwerty.toast.Toast('Dirección postal eliminada con éxito',{settings:{duration:1000}});executeAction('rellenarPanel','&destino=usuarios','filtroUsuarios');";
          }
        } else {
          $JS = "bootbox.confirm({
            message: 'Va a eliminar una dirección postal permanentemente ¿Desea continuar?',
            buttons: {
              confirm: { label: 'Continuar', className: 'btn-success' },
              cancel: { label: 'Cancelar', className: 'btn-danger' }
            },
            callback: function (result) {
              if (result) {
                executeAction('eliminar','&destino=direccionesPostales&idDireccion=$idDireccion&confirm=true');
              }
            }
          }).find('.modal-content').css('margin-top','300px');";
        }
      }
      break;

    case "correspondencias":
      if (Session::allowed()) {
        $confirm = isset($_GET['confirm']) && $_GET['confirm'] === 'true';
        $idCorrespondencia = $_GET['idCorrespondencia'] ?? 0;

        if ($confirm) {
          $correspondencia = classInvoker::nc('correspondencias', intval($idCorrespondencia));
          $correspondencia->delete();
          $JS .= "iqwerty.toast.Toast('Correspondencia eliminada con éxito',{settings:{duration:1000}});executeAction('rellenarPanel','&destino=correspondencias');";
        } else {
          $JS = "bootbox.confirm({
            message: 'Va a eliminar una correspondencia permanentemente ¿Desea continuar?',
            buttons: {
              confirm: { label: 'Continuar', className: 'btn-success' },
              cancel: { label: 'Cancelar', className: 'btn-danger' }
            },
            callback: function (result) {
              if (result) {
                executeAction('eliminar','&destino=correspondencias&idCorrespondencia=$idCorrespondencia&confirm=true');
              }
            }
          }).find('.modal-content').css('margin-top','300px');";
        }
      }
      break;

    case "exportar":
      if (Session::allowed()) {
        $confirm = isset($_GET['confirm']) && $_GET['confirm'] === 'true';
        $idExportacion = $_GET['idExportacion'] ?? 0;

        if ($confirm) {
          $exportacion = classInvoker::nc('exportar', intval($idExportacion));
          $exportacion->delete();
          $JS .= "iqwerty.toast.Toast('Exportación eliminada con éxito',{settings:{duration:1000}});executeAction('rellenarPanel','&destino=exportar');";
        } else {
          $JS = "bootbox.confirm({
            message: 'Va a eliminar una exportación permanentemente ¿Desea continuar?',
            buttons: {
              confirm: { label: 'Continuar', className: 'btn-success' },
              cancel: { label: 'Cancelar', className: 'btn-danger' }
            },
            callback: function (result) {
              if (result) {
                executeAction('eliminar','&destino=exportar&idExportacion=$idExportacion&confirm=true');
              }
            }
          }).find('.modal-content').css('margin-top','300px');";
        }
      }
      break;

    case "fincas":
      if (Session::allowed()) {
        $confirm = isset($_GET['confirm']) && $_GET['confirm'] === 'true';
        $idFinca = $_GET['idFinca'] ?? 0;

        if ($confirm) {
          $finca = classInvoker::nc('fincas', intval($idFinca), true);
          $finca->deleteFinca();
          $JS .= "iqwerty.toast.Toast('Finca eliminada con éxito',{settings:{duration:1000}});executeAction('rellenarPanel','&destino=usuarios','filtroUsuarios');";
        } else {
          $JS = "bootbox.confirm({
            message: 'Va a eliminar una finca permanentemente ¿Desea continuar?',
            buttons: {
              confirm: { label: 'Continuar', className: 'btn-success' },
              cancel: { label: 'Cancelar', className: 'btn-danger' }
            },
            callback: function (result) {
              if (result) {
                executeAction('eliminar','&destino=fincas&idFinca=$idFinca&confirm=true');
              }
            }
          }).find('.modal-content').css('margin-top','300px');";
        }
      }
      break;

    case "propietarios":
      if (Session::allowed()) {
        $confirm = isset($_GET['confirm']) && $_GET['confirm'] === 'true';
        $idUsuario = $_GET['idUsuario'] ?? 0;
        $idPropietario = $_GET['idPropietario'] ?? 0;
        $idFinca = $_GET['idFinca'] ?? 0;

        if ($confirm) {
          $propietario = classInvoker::nc('propietarios', intval($idPropietario), true);
          $propietario->deleteFincas($idUsuario);
          $JS .= "iqwerty.toast.Toast('Propietario eliminado con éxito',{settings:{duration:1000}});executeAction('rellenarPanel','&destino=panelAuxiliarEditarFincas&idFinca=$idFinca');";
        } else {
          $JS = "bootbox.confirm({
            message: 'Va a eliminar este propietario de todas las fincas ¿Desea continuar?',
            buttons: {
              confirm: { label: 'Continuar', className: 'btn-success' },
              cancel: { label: 'Cancelar', className: 'btn-danger' }
            },
            callback: function (result) {
              if (result) {
                executeAction('eliminar','&destino=propietarios&idUsuario=$idUsuario&idPropietario=$idPropietario&idFinca=$idFinca&confirm=true');
              }
            }
          }).find('.modal-content').css('margin-top','300px');";
        }
      }
      break;

    case "recibos":
    case "panelAuxiliarRecibos":
      if (Session::allowed()) {
        $confirm = isset($_GET['confirm']) && $_GET['confirm'] === 'true';
        $idRecibo = $_GET['idRecibo'] ?? 0;

        if ($confirm) {
          $recibo = classInvoker::nc('recibos', intval($idRecibo));
          $recibo->delete();
          $JS .= "iqwerty.toast.Toast('Recibo eliminado con éxito',{settings:{duration:1000}});executeAction('rellenarPanel','&destino=$destino&idUsuario={$recibo->idUsuario}');";
        } else {
          $JS = "bootbox.confirm({
            message: 'Va a eliminar un recibo ¿Desea continuar?',
            buttons: {
              confirm: { label: 'Continuar', className: 'btn-success' },
              cancel: { label: 'Cancelar', className: 'btn-danger' }
            },
            callback: function (result) {
              if (result) {
                executeAction('eliminar','&destino=$destino&idRecibo=$idRecibo&confirm=true');
              }
            }
          }).find('.modal-content').css('margin-top','300px');";
        }
      }
      break;

    case "empleados":
      if (Session::allowed()) {
        $confirm = isset($_GET['confirm']) && $_GET['confirm'] === 'true';
        $idEmpleado = $_GET['idEmpleado'] ?? 0;

        if ($confirm) {
          $empleado = classInvoker::nc('empleados', intval($idEmpleado));
          $empleado->delete();
          $JS .= "iqwerty.toast.Toast('Empleado eliminado con éxito',{settings:{duration:1000}});executeAction('rellenarPanel','&destino=empleados');";
        } else {
          $JS = "bootbox.confirm({
            message: 'Va a eliminar un empleado ¿Desea continuar?',
            buttons: {
              confirm: { label: 'Continuar', className: 'btn-success' },
              cancel: { label: 'Cancelar', className: 'btn-danger' }
            },
            callback: function (result) {
              if (result) {
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