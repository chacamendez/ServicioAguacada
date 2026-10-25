<?php
session_start();

if(Session::isLogged()) {
  $JS="";
  $destino=$_GET['destino'];
  switch ($destino){
    case "panelAuxiliarUsuarios":
      if (Session::allowed()){
        $idUsuario=$_GET['idUsuario'];
        $usuario=classInvoker::nc('usuarios',intval($idUsuario),true);
        $prefijo=$usuario->telefonoDefecto(1);
        $telefono=$usuario->telefonoDefecto(2);
        $correo=$usuario->correoDefecto();
        $JS.="$('#idUsuarioEditar').val('$idUsuario');$('#idNombre').val('{$usuario->nombre}');".
        "$('#selectorComunidades').val('{$usuario->idComunidad}');$('#idPiso').val('{$usuario->piso}');".
        "$('#idUsuario').val('');$('#idClave').val('');$('#idNombre').val('{$usuario->nombre}');".
        "$('#idPrefijo').val('$prefijo');$('#idTelefono').val('$telefono');$('#idCorreo').val('$correo');".
        "$('#checkboxCorrespondencia').prop('checked',{$usuario->correspondencia});";
      }
    break;

    case "historial":
      if (Session::allowed()){
        $idEvento=$_GET["idEvento"];
        $alerta=$_GET["alerta"];
        $evento=classInvoker::nc('historial',intval($idEvento));
        if ($alerta==1){
          $evento->activo=0;
          $JS.="$('#idAlerta$idEvento').removeAttr('onchange');$('#idAlerta$idEvento').off('change');$('#idAlerta$idEvento').on('change',function () {executeAction('editar','&destino=historial&idEvento=$idEvento&alerta=0')});";
        }
        else{
          $evento->activo=1;
          $JS.="$('#idAlerta$idEvento').removeAttr('onchange');$('#idAlerta$idEvento').off('change');$('#idAlerta$idEvento').on('change',function () {executeAction('editar','&destino=historial&idEvento=$idEvento&alerta=1')});";
        }
        $evento->update();
      }
    break;

    case "usuarios":
      $nombre=$_GET["idNombre"];
      $idUsuarioEditar=$_GET["idUsuarioEditar"];
      $Susuario=$_GET["idUsuario"];
      $clave=$_GET["idClave"];
      $clave2=$_GET["idClave2"];
      $prefijo=$_GET["idPrefijo"];
      $telefono=$_GET["idTelefono"];
      $correo=$_GET["idCorreo"];
      $usuarioExt=classInvoker::nc('usuarios',intval($idUsuarioEditar),true);
      $error=new Errores();
      if (empty($nombre)) $error->setError('Debe rellenar el campo nombre');
      $usuarioExt->nombre=$nombre;

      $usuarioExt->usuario=$Susuario;
      if (empty($Susuario)) $error->setError('Debe rellenar el campo usuario');
      if (strlen($Susuario)<5) $error->setError('El campo usuario debe tener al menos 5 caracteres');
      if ($usuarioExt->existe()) $error->setError('Usario en uso. Pruebe con otro');

      if (empty($clave)) $error->setError('Debe rellenar el campo clave');
      if (strlen($clave)<8) $error->setError('El campo clave debe tener al menos 8 caracteres');

      if (empty($clave2)) $error->setError('Debe rellenar el campo clave');
      if ($clave2 != $clave) $error->setError('Las claves no coinciden');
      $usuarioExt->clave=$clave;

      if (empty($prefijo)) $error->setError('Debe rellenar el campo prefijo');
      if (empty($telefono)) $error->setError('Debe rellenar el campo telefono');
      if (!empty($correo)) {
        if (!(Strings::SvalidEmail($correo))) $error->setError('Dirección de correo inválida');
        $usuarioExt->setCorreoDefecto($correo);
      }

      if ($error->success){
        $usuarioExt->reset=0;
        $usuarioExt->estado='activa';
        $usuarioExt->setClave();
        $usuarioExt->update();
        $usuarioExt->setTelefonoDefecto($prefijo,$telefono);
        $JS.="iqwerty.toast.Toast('Datos actualizados con éxito',{settings:{duration:1000}});";
        $JS.="executeAction('prepararPanel','&destino=panelLateral');$('#panelAuxiliar').fadeOut();";
      }
      else{
          $JS.="iqwerty.toast.Toast('{$error->error}',{settings:{duration:2500}});";
      }
    break;

    case 'usuariosReset':
      if (Session::allowed()){
        $idUsuario=$_GET["idUsuario"];
        $usuarioExt=classInvoker::nc('usuarios',intval($idUsuario),true);
        $usuarioExt->estado='creada';
        $usuarioExt->resetClave();
        $usuarioExt->reset=1;
        $usuarioExt->update();
        $JS.="iqwerty.toast.Toast('cuenta reseteada con éxito',{settings:{duration:1000}});";
      }
    break;

    case "check":
      if (Session::allowed()){
        $checkbox=$_GET["checkbox"];
        $value=$_GET[$checkbox];
        if ($value=='true'){
          $JS.="$('#0radio$checkbox').prop('checked','true')";
        }
        else{
          $JS.="$('[name=\"radio$checkbox\"]').prop('checked','');";
        }
      }
    break;

    case "fincasDefecto":
      if (Session::allowed()){
        $idFinca=$_GET["idFinca"];
        $idUsuario=$_GET["idUsuario"];
        incluir('fincas');
        
        // Consultas migradas a Database::ExecuteQuery / ExecuteMultiQuery con parámetros seguros
        Database::ExecuteMultiQuery("UPDATE fincas SET defecto = 0 WHERE idUsuario = ?; UPDATE fincas SET defecto = 1 WHERE id = ?;", true, [$idUsuario, $idFinca]);
        Database::ExecuteQuery("UPDATE usuarios SET piso = (SELECT finca FROM fincas WHERE idUsuario = ? AND defecto = 1) WHERE id = ?", false, [$idUsuario, $idUsuario]);
        
        $stmtFincas = Database::ExecuteQuery("SELECT id, defecto FROM fincas WHERE idUsuario = ?", false, [$idUsuario]);
        $fincas = $stmtFincas ? $stmtFincas->fetchAll(PDO::FETCH_ASSOC) : [];

        if ($fincas){
          foreach ($fincas as $finca) {
            if ($finca['defecto']==1){
              $JS.="$('#{$idUsuario}idFinca{$finca['id']}').css('background-color','#269905');";
              $JS.="$('#{$idUsuario}idFinca{$finca['id']}').removeClass('fa fa-times-circle');";
              $JS.="$('#{$idUsuario}idFinca{$finca['id']}').addClass('fa fa-check-circle');";
            }
            else{
              $JS.="$('#{$idUsuario}idFinca{$finca['id']}').css('background-color','#961e18');";
              $JS.="$('#{$idUsuario}idFinca{$finca['id']}').removeClass('fa fa-check-circle');";
              $JS.="$('#{$idUsuario}idFinca{$finca['id']}').addClass('fa fa-times-circle');";
            }
          }
        }
        $JS.="iqwerty.toast.Toast('Finca por defecto actualizada',{settings:{duration:1000}});";
      }
    break;

    case "fincas":
      if (Session::allowed()){
        $idUsuario=$_GET["idUsuario"];
        $finca=$_GET["finca"];
        $idFinca=$_GET["idFinca"];
        $coeficiente=$_GET['coeficiente'];
        
        $errores=new Errores();
        if (empty ($finca)) $errores->setError('Debe introducir una finca');
        if (!(is_numeric($coeficiente))) $errores->setError('Debe introducir un número para el coeficiente');
        else {
          if ($coeficiente > 100) $errores->setError('El número debe estar comprendido entre 0 y 100');
          $coeficiente = number_format($coeficiente, 3, '.', '');
        }
        if ($errores->success){
          $fincasExt=classInvoker::nc('fincas',intVal($idFinca),true);
          $fincasExt->finca=$finca;
          if ($fincasExt->defecto==1){
            Database::ExecuteQuery("UPDATE usuarios SET piso = ? WHERE id = ?", false, [$fincasExt->finca, $idUsuario]);
          }
          $fincasExt->idUsuario=$idUsuario;
          $fincasExt->coeficiente=$coeficiente;
          $fincasExt->update();
          $JS.="iqwerty.toast.Toast('Finca editada con éxito',{settings:{duration:1000}});executeAction('rellenarPanel','&destino=usuarios','filtroUsuarios');$('#panelAuxiliar').fadeOut('slow');";
        }
        else {
          $JS.="iqwerty.toast.Toast('{$errores->error}',{settings:{duration:1000}});";
        }
      }
    break;

    case "panelAuxiliarRecibos":
      if (Session::allowed()){
        $idRecibo=$_GET["idRecibo"];
        $recibo=classInvoker::nc('recibos',intVal($idRecibo));
        $fecha=Strings::fecha($recibo->fecha);
        $JS.="$('#idRecibo').val('$idRecibo');$('#nombreRecibo').val('{$recibo->nombre}');".
        "$('#fincaRecibo').val('{$recibo->idFinca}');$('#conceptoRecibo').val('{$recibo->concepto}');".
        "$('#cantidadRecibo').val({$recibo->importe});$('#fechaRecibo').val('$fecha');";
        $JS.="iqwerty.toast.Toast('Recibo editado con éxito',{settings:{duration:1000}});";
      }
    break;

    case "propietarios":
      if (Session::allowed()){
        $idPropietario=$_GET['idPropietario'];
        $propietario=classInvoker::nc('propietarios',intVal($idPropietario));
        $JS.="$('#idPropietario2').val('{$propietario->id}');$('#nombrePropietario').val('{$propietario->propietario}');".
        "$('#dniPropietario').val('{$propietario->dni}');";
      }
    break;  
  }
  echo $JS;
}
?>