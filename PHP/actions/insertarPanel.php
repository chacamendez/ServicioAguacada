<?php
session_start();

if(Session::isLogged()) {
  $JS="";
  $destino=$_GET['destino'];
  switch ($destino){
  case "home":
    if (Session::allowed()){
      $JS.="document.getElementById('formBackup').submit();iqwerty.toast.Toast('Backup realizado con éxito',{settings:{duration:1000}});";
    }
  break;
  case "usuarios":
    if (Session::allowed()){
      $Snombre=$_GET['idNombre'];
      $idComunidad=$_GET['selectorComunidades'];
      $Spiso=$_GET['idPiso'];
      $Susuario=$_GET['idUsuario'];
      $Sclave=$_GET['idClave'];
      $Sprefijo=$_GET['idPrefijo'];
      $Stelefono=$_GET['idTelefono'];
      $Semail=$_GET['idCorreo'];
      $idUsuario=$_GET['idUsuarioEditar'];
      $checkboxCorrespondencia=$_GET['checkboxCorrespondencia'];
      $Strings=new Strings();
      $errores=new Errores();

      if (!empty($idUsuario)){
        $usuario=classInvoker::nc('usuarios',intval($idUsuario),true);
        if (!empty($Susuario)){
          $usuario->usuario=$Strings->chop($Susuario);
          $usuario->claveDefecto=$Strings->chop($Sclave);
          $usuario->resetClave();
          if ($usuario->existe()) $errores->setError('Usuario repetido. Pruebe con otro.');
        }

      }
      else {
        $usuario=classInvoker::nc('usuarios',false,true);
        $usuario->usuario=$Strings->chop($Susuario);
        $usuario->claveDefecto=$Strings->chop($Sclave);
        $usuario->resetClave();
        if ($usuario->existe()) $errores->setError('Usuario repetido. Pruebe con otro.');

      }

      $usuario->nombre=$Strings->chop($Snombre);
      $usuario->idComunidad=$idComunidad;

      $Spiso=$Strings->chop($Spiso);
      $usuario->piso=$Spiso;

      $Semail=$Strings->chop($Semail);
      $Sprefijo=$Strings->chop($Sprefijo);
      $Stelefono=$Strings->chop($Stelefono);

      if (empty($usuario->nombre)) $errores->setError("Debe introducir nombre");
      if (empty($Spiso)) $errores->setError("Debe introducir un piso");
      if (empty($usuario->usuario)) $errores->setError("Debe introducir un usuario");
      if (empty($usuario->clave)) $errores->setError("Debe introducir una clave");

      if (strlen($usuario->usuario)<5) $errores->setError("El usuario debe tener al menos 5 caracteres");
      if (strlen($usuario->clave)<5) $errores->setError("La clave debe tener al menos 5 caracteres");

      if (!empty($Semail)) {
        if (!Strings::SvalidEmail($Semail)) $errores->setError("Debe introducir un email válido");
      }
      if (empty($Sprefijo)) $errores->setError("Debe introducir un prefijo");
      if (empty($Stelefono)) $errores->setError("Debe introducir un telefono");
      if ($checkboxCorrespondencia=='true') $usuario->correspondencia=1;
      else $usuario->correspondencia=0;

      if ($errores->success){

        if (!empty($idUsuario)){
                $usuario->update();
                
                // Consultas migradas de manera segura a Database::ExecuteQuery con PDO
                Database::ExecuteQuery("UPDATE fincas SET finca = ? WHERE idUsuario = ? AND defecto = 1", false, [$usuario->piso, $usuario->id]);
                
                $idTelefono = Database::ExecuteQuery("SELECT id FROM telefonos WHERE origen = ? AND defecto = 1", true, ["usuarios;{$usuario->id}"]);
                if ($idTelefono) {
                  $telefono = classInvoker::nc('telefonos', intval($idTelefono));
                  $telefono->telefono = $Stelefono;
                  $telefono->prefijo = $Sprefijo;
                  $telefono->update();
                }

                $idCorreo = Database::ExecuteQuery("SELECT id FROM correos WHERE origen = ? AND defecto = 1", true, ["usuarios;{$usuario->id}"]);
                if ($idCorreo) {
                  $correo = classInvoker::nc('correos', intval($idCorreo));
                  $correo->correo = $Semail;
                  $correo->update();
                }

                $JS.="iqwerty.toast.Toast('Usuario actualizado con éxito',{settings:{duration:1000}});";
        }
        else{
                $usuario->insert();
                $telefono=classInvoker::nc('telefonos');
                $telefono->origen="usuarios;{$usuario->id}";
                $telefono->telefono=$Stelefono;
                $telefono->prefijo=$Sprefijo;
                $telefono->defecto=1;
                $telefono->insert();
                
                $correo=classInvoker::nc('correos');
                $correo->origen="usuarios;{$usuario->id}";
                $correo->correo=$Semail;
                $correo->defecto=1;
                $correo->insert();
                
                $finca=classInvoker::nc('fincas');
                $finca->finca=$Spiso;
                $finca->defecto=1;
                $finca->idUsuario=$usuario->id;
                $finca->insert();
                
                $JS.="iqwerty.toast.Toast('Usuario insertado con éxito',{settings:{duration:1000}});";
        }
        $JS.="$('#panelAuxiliar').fadeOut('slow');executeAction('rellenarPanel','&destino=usuarios','filtroUsuarios');";

      }
      else{
        $JS.="iqwerty.toast.Toast('{$errores->error},{settings:{duration:2000}});";
      }
    }
  break;
  case "usuariosExportar":
    if (Session::allowed()){
      $idUsuario=$_GET["idUsuario"];
      $JS.="$('#idUsuarioExportar').val('$idUsuario');document.getElementById('formExportar').submit();iqwerty.toast.Toast('Usuario exportado con éxito',{settings:{duration:1000}});";
    }
  break;
  case "exportarXLS":
    if (Session::allowed()){
      $JS.="document.getElementById('formExportarXLS').submit();";
    }
  break;
  case 'comunidades':
    if (Session::allowed()){
      $Scomunidad=$_GET['comunidad'];
      $direccion=$_GET['direccion'];
      $direccion2=$_GET["direccion2"];
      $idComunidad=$_GET["idComunidad"];
      $errores=new Errores();
      if (empty($Scomunidad)) $errores->setError('Debe introducir un nombre para la comunidad');
      if (empty($direccion)) $errores->setError('Debe introducir una dirección');
      if (empty($direccion2)) $errores->setError('Debe introducir direccion2 para la comunidad');
      if ($errores->success){
        if (empty($idComunidad)){
          $comunidad=classInvoker::nc('comunidades');
          $comunidad->comunidad=$Scomunidad;
          $comunidad->direccionPostal=$direccion;
          $comunidad->direccionPostal2=$direccion2;
          $comunidad->insert();
          $JS.="iqwerty.toast.Toast('Comunidad introducida con éxito',{settings:{duration:1000}});";
        }
        else {
          $comunidad=classInvoker::nc('comunidades',intval($idComunidad));
          $comunidad->comunidad=$Scomunidad;
          $comunidad->direccionPostal=$direccion;
          $comunidad->direccionPostal2=$direccion2;
          $comunidad->update();
          $JS.="iqwerty.toast.Toast('Comunidad actualizada con éxito',{settings:{duration:1000}});";
        }
        $JS.="executeAction('rellenarPanel','&destino=comunidades');$('#panelAuxiliar').fadeOut('slow');";
      }
      else {
        $JS.="iqwerty.toast.Toast('{$errores->error}',{settings:{duration:1000}});";
      }
    }
  break;
  case 'mensajes':
    if (Session::allowed()){
      $mensaje=$_GET["inputMensajesEnviar"];
      if (trim($mensaje)=='') exit;
      $destino=$_GET['idUsuario'];
      $idUsuario=Session::getUID();
      $fechaHora=Session::fechaHoraActual();
      incluir('mensajes');
      $Omensaje=new mensajes();
      $Omensaje->idUsuario=$idUsuario;
      $Omensaje->fechaHora=$fechaHora;
      $Omensaje->destino=$destino;
      $Omensaje->mensaje=$mensaje;
      $Omensaje->remitente=$_SESSION['usuario'];
      $Omensaje->insert();
    }
    else {
      $mensaje=$_GET["inputMensajesEnviar"];
      if (trim($mensaje)=='') exit;
      $destino=0;
      $idUsuario=Session::getUID();
      $fechaHora=Session::fechaHoraActual();
      incluir('mensajes');
      $Omensaje=new mensajes();
      $Omensaje->idUsuario=$idUsuario;
      $Omensaje->fechaHora=$fechaHora;
      $Omensaje->destino=$destino;
      $Omensaje->mensaje=$mensaje;
      $Omensaje->remitente=$_SESSION['usuario'];
      $Omensaje->insert();
    }
  break;
  case 'archivos':
    if (Session::allowed()){
      $idUsuario=$_GET["idUsuario"];
      $idComunidad=$_GET["idComunidad"];
      $errores=new Errores();
      $archivo=$_GET["idNombreArchivo"];
      if (empty($archivo)) $errores->setError("Debe introducir un nombre para el archivo");
      $uploadFile=$_GET["inputSubirArchivo"];
      if (empty($uploadFile)) $errores->setError("Debe introducir un archivo");
      if ($errores->success){
        if ($idUsuario!=0) $JS.="executeAction('prepararPanel','&destino=panelAuxiliarArchivos&idUsuario=$idUsuario');";
        if ($idComunidad!=0) $JS.="executeAction('prepararPanel','&destino=panelAuxiliarArchivos&idComunidad=$idComunidad');";
        $JS.="document.getElementById('formArchivos').submit();iqwerty.toast.Toast('Archivo subido con éxito',{settings:{duration:1000}});";
      }
      else {
          $JS.="iqwerty.toast.Toast('{$errores->error}',{settings:{duration:1000}});";
      }
    }
  break;
  case "historial":
    if (Session::allowed()){
      $idUsuario=$_GET["idUsuario"];
      $idComunidad=$_GET["idComunidad"];
      $errores=new Errores();
      $evento=$_GET["inputEvento"];
      $alerta=$_GET["inputAlerta"];
      $fechaAlerta=$_GET["inputFechaAlerta"];
      if (empty($evento)) $errores->setError("Debe introducir un evento para el historial");
      if ($alerta=='true'){
        if (empty($fechaAlerta)) $errores->setError("Debe introducir una fecha para la alarma");
          $fechaAlerta=Strings::fechaSQL($fechaAlerta);
      }

      if ($errores->success){
        $historial=classInvoker::nc('historial');
        $historial->evento=$evento;
        $historial->fechaHora=Strings::fechaHoraSQL();
        if ($alerta=='true'){
          $historial->alerta=$fechaAlerta;
          $historial->activo=1;
        }
        $historial->idComunidad=$idComunidad;
        $historial->idUsuario=$idUsuario;
        $historial->idOrigen=$_SESSION['idUsuario'];
        $historial->insert();

        if ($idUsuario!=0) $JS.="executeAction('prepararPanel','&destino=panelAuxiliarHistorial&idUsuario=$idUsuario');";
        if ($idComunidad!=0) $JS.="executeAction('prepararPanel','&destino=panelAuxiliarHistorial&idComunidad=$idComunidad');";
        $JS.="iqwerty.toast.Toast('Historial actualizado con éxito',{settings:{duration:1000}});";
      }
      else {
          $JS.="iqwerty.toast.Toast('{$errores->error}',{settings:{duration:1000}});";
      }
    }
  break;
  case "telefonos":
    if (Session::allowed()){
      $idUsuario=$_GET["idUsuario"];
      $contacto=$_GET["contacto"];
      $tipo=$_GET["tipo"];
      $prefijo=$_GET["prefijo"];
      $telefono=$_GET["telefono"];
      $errores=new Errores();
      if (empty($prefijo)) $errores->setError('Debe introducir un prefijo');
      if (empty($telefono)) $errores->setError('Debe introducir un teléfono');
      if ($errores->success){
        $telefonoO=classInvoker::nc('telefonos');
        $telefonoO->telefono=$telefono;
        $telefonoO->tipo=$tipo;
        $telefonoO->prefijo=$prefijo;
        $telefonoO->contacto=$contacto;
        $telefonoO->origen="usuarios;$idUsuario";
        $telefonoO->insert();
        $JS.="iqwerty.toast.Toast('Teléfono introducido con éxito',{settings:{duration:1000}});executeAction('rellenarPanel','&destino=usuarios','filtroUsuarios');$('#panelAuxiliar').fadeOut('slow');";
      }
      else {
        $JS.="iqwerty.toast.Toast('{$errores->error}',{settings:{duration:1000}});";
      }
    }
  break;
  case "correos":
    if (Session::allowed()){
      $idUsuario=$_GET["idUsuario"];
      $contacto=$_GET["contacto"] ?? '';
      $tipo=$_GET["tipo"] ?? '';
      $correo=$_GET["correo"];
      $errores=new Errores();
      $Strings=new Strings();

      if (!$Strings->validEmail($correo)) $errores->setError('Debe introducir un correo válido');
      if ($errores->success){
        $correoO=classInvoker::nc('correos');
        $correoO->contacto=$contacto;
        $correoO->correo=$correo;
        $correoO->tipo=$tipo;
        $correoO->origen="usuarios;$idUsuario";
        $correoO->insert();
        $JS.="iqwerty.toast.Toast('Correo introducido con éxito',{settings:{duration:1000}});executeAction('rellenarPanel','&destino=usuarios','filtroUsuarios');$('#panelAuxiliar').fadeOut('slow');";
      }
      else {
        $JS.="iqwerty.toast.Toast('{$errores->error}',{settings:{duration:1000}});";
      }
    }
  break;
  case "direcciones":
    if (Session::allowed()){
      $idDireccion=$_GET["idDireccion"];
      $idUsuario=$_GET["idUsuario"];
      $destinatario=$_GET["destinatario"];
      $direccion=$_GET["direccion"];
      $errores=new Errores();
      if (empty ($_GET["destinatario"])) $errores->setError('Debe introducir un destinatario');
      if (empty ($_GET["direccion"])) $errores->setError('Debe introducir una dirección postal');

      if ($errores->success){
        if ($idDireccion!="false"){
          $direccionObj=classInvoker::nc('direccionesPostales',intval($idDireccion));
          $direccionObj->direccionPostal=$_GET["direccion"];
          $direccionObj->origen="usuarios;$idUsuario";
          $direccionObj->destinatario=$destinatario;
          $direccionObj->update();
          $JS.="iqwerty.toast.Toast('Dirección postal editada con éxito',{settings:{duration:1000}});executeAction('rellenarPanel','&destino=usuarios','filtroUsuarios');$('#panelAuxiliar').fadeOut('slow');";
        }
        else {
          $direccionObj=classInvoker::nc('direccionesPostales');
          $direccionObj->direccionPostal=$_GET["direccion"];
          $direccionObj->origen="usuarios;$idUsuario";
          $direccionObj->destinatario=$destinatario;
          $direccionObj->insert();
          $JS.="iqwerty.toast.Toast('Dirección postal introducida con éxito',{settings:{duration:1000}});executeAction('rellenarPanel','&destino=usuarios','filtroUsuarios');$('#panelAuxiliar').fadeOut('slow');";
        }
      }
      else {
        $JS.="iqwerty.toast.Toast('{$errores->error}',{settings:{duration:1000}});";
      }
    }
  break;
  case "correspondencias":
    $correspondencia=$_GET["correspondencia"];
    $hayDirecciones=false;
    $idCorrespondencia=!empty($_GET["idCorrespondencia"]) ? $_GET["idCorrespondencia"] : '';
    $destinatarios = [];
    foreach ($_GET as $key => $value) {
      if (strpos($key, "radiocheckbox")){
        if ($value=='true') {
          $hayDirecciones=true;
          $idDirecciones=explode("radiocheckbox",$key);
          $destinatarios[$idDirecciones[1]]=$idDirecciones[0];
        }
      }
    }
    $errores=new Errores();
    if (empty($correspondencia)) $errores->setError('Debe introducir un nombre para la correspondencia');
    if (!$hayDirecciones) $errores->setError('Debe seleccionar algun destinatario');
    if ($errores->success){
      if (!(empty($idCorrespondencia))){
        $JS.="if ($('#textoCorrespondencia').val()=='') iqwerty.toast.Toast('Debe introducir el texto de la correspondencia',{settings:{duration:1000}});".
        "else { $('#formTexto').submit();$('#panelAuxiliar').fadeOut('slow');executeAction('rellenarPanel','&destino=correspondencias');".
        "iqwerty.toast.Toast('Correspondencia actualizada con éxito',{settings:{duration:1000}}); }";
        $_SESSION['idCorrespondencia']=$idCorrespondencia;
      }
      else {
        $JS.="if ($('#textoCorrespondencia').val()=='') iqwerty.toast.Toast('Debe introducir el texto de la correspondencia',{settings:{duration:1000}});".
        "else { $('#formTexto').submit();$('#panelAuxiliar').fadeOut('slow');executeAction('rellenarPanel','&destino=correspondencias');".
        "iqwerty.toast.Toast('Correspondencia introducida con éxito',{settings:{duration:1000}}); }";
        $_SESSION['idCorrespondencia']='';
      }
      $_SESSION['correspondencia']=$correspondencia;
      $_SESSION['destinatarios']=$destinatarios;
    }
    else $JS.="iqwerty.toast.Toast('{$errores->error}',{settings:{duration:1000}});";
  break;
  case "exportar":
    $exportacion=$_GET["exportacion"];
    $idExportacion=!empty($_GET["idExportacion"]) ? $_GET["idExportacion"] : '';
    $filtros='';
    foreach ($_GET as $key => $value) {
      if (strpos($key, "Checkbox")){
        if ($value=='true') {
          $filtro=explode("Checkbox",$key);
          $filtros.="{$filtro[0]};";
        }
      }
    }
    $errores=new Errores();
    if (empty($exportacion)) $errores->setError('Debe introducir un nombre para exportar');
    if ($errores->success){
      if (!(empty($idExportacion))){
        // Actualización si aplica
      }
      else {
        $exportacionO=classInvoker::nc('exportar');
        $exportacionO->exportacion=$exportacion;
        $exportacionO->filtros=$filtros;
        $exportacionO->insert();
        $JS.="$('#panelAuxiliar').fadeOut('slow');iqwerty.toast.Toast('Nuevo exportar introducido con éxito',{settings:{duration:1000}});";
      }
    }
    else $JS.="iqwerty.toast.Toast('{$errores->error}',{settings:{duration:1000}});";
  break;
  case "fincas":
    if (Session::allowed()){
      $idUsuario=$_GET["idUsuario"];
      $finca=$_GET["finca"];
      $coeficiente=$_GET['coeficiente'];
      $errores=new Errores();
      if (empty ($finca)) $errores->setError('Debe introducir una finca');
      if (!(is_numeric($coeficiente))) $errores->setError('Debe introducir un número para el coeficiente');
      else {
        if ($coeficiente > 100) $errores->setError('El número debe estar comprendido entre 0 y 100');
        $coeficiente = number_format($coeficiente, 3, '.', '');
      }
      if ($errores->success){
        $fincas=classInvoker::nc('fincas',false,true);
        $fincas->finca=$finca;
        $fincas->coeficiente=$coeficiente;
        $fincas->idUsuario=$idUsuario;
        $fincas->insert();
        $fincas->addPropietarios();
        $JS.="iqwerty.toast.Toast('Finca añadida con éxito',{settings:{duration:1000}});executeAction('rellenarPanel','&destino=usuarios','filtroUsuarios');$('#panelAuxiliar').fadeOut('slow');";
      }
      else {
        $JS.="iqwerty.toast.Toast('{$errores->error}',{settings:{duration:1000}});";
      }
    }
  break;
  case "propietarios":
    if (Session::allowed()){
      $idUsuario=$_GET["idUsuario2"];
      $idFinca=$_GET["idFinca2"];
      $nombrePropietario=$_GET["nombrePropietario"];
      $dniPropietario=$_GET["dniPropietario"];
      $idPropietario=$_GET['idPropietario2'];
      $errores=new Errores();
      if (empty($nombrePropietario)) $errores->setError('Debe introducir un nombre completo de propietario');
      if ($errores->success){
        incluir('propietarios',true);
        if ($idPropietario!=0){
            $propietario=classInvoker::nc('propietarios',intval($idPropietario));
            $propietario->propietario=$nombrePropietario;
            $propietario->dni=strtoupper($dniPropietario);
            $propietario->update();
        }
        else{
            $idPropietario=propietariosExt::devolverId($dniPropietario);
            if ($idPropietario){
              $propietario=classInvoker::nc('propietarios',intval($idPropietario),true);
              $propietario->update();
            }
            else {
              $propietario=classInvoker::nc('propietarios',false,true);
              $propietario->propietario=$nombrePropietario;
              $propietario->dni=strtoupper($dniPropietario);
              $propietario->insert();
            }
            $propietario->addFincas($idUsuario);
        }
        $JS.="iqwerty.toast.Toast('Propietario añadido con éxito',{settings:{duration:1000}});executeAction('rellenarPanel','&destino=panelAuxiliarEditarFincas&idUsuario=$idUsuario&idFinca=$idFinca');";
      }
      else {
        $JS.="iqwerty.toast.Toast('{$errores->error}',{settings:{duration:1000}});";
      }
    }
  break;
  case "panelAuxiliarEmpleados":
    $idEmpleado=$_GET["idEmpleado"];
    $idNombre=$_GET["idNombre"];
    $idClave=$_GET["idClave"];
    $idDNI=$_GET["idDNI"];
    $idCheckEmpleados=$_GET['idCheckEmpleados'];
    $idEmpleadoEditar = isset($_GET['idEmpleadoEditar']) ? $_GET["idEmpleadoEditar"] : 0;
    $errores=new Errores();
    if (empty($idNombre)) $errores->setError("Debe introducir nombre completo para el empleado");
    if (empty($idEmpleado)) $errores->setError("Debe introducir un nombre de empleado");
    if (empty($idClave)) $errores->setError("Debe introducir una clave por defecto");
    if (empty($idDNI)) $errores->setError("Debe introducir un DNI");

    if ($errores->success){
      if ($idEmpleadoEditar!=0){
        $empleado=classInvoker::nc('empleados',intval($idEmpleadoEditar));
        $empleado->nombre=$idNombre;
        $empleado->empleado=$idEmpleado;
        $empleado->claveDefecto=$idClave;
        $empleado->clave=password_hash(trim($empleado->claveDefecto), PASSWORD_DEFAULT);
        $empleado->dni=$idDNI;
        if ($idCheckEmpleados=="true") $empleado->reset=1;
        else $empleado->reset=0;
        $empleado->update();
        $JS.="iqwerty.toast.Toast('Empleado editado con éxito',{settings:{duration:1000}});$('#panelAuxiliar').fadeOut('slow');";
      }
      else {
        $empleado=classInvoker::nc('empleados');
        $empleado->nombre=$idNombre;
        $empleado->empleado=$idEmpleado;
        $empleado->claveDefecto=$idClave;
        $empleado->clave=password_hash(trim($empleado->claveDefecto), PASSWORD_DEFAULT);
        $empleado->dni=$idDNI;
        $empleado->insert();
        $JS.="iqwerty.toast.Toast('Empleado añadido con éxito',{settings:{duration:1000}});$('#panelAuxiliar').fadeOut('slow');";
        $JS.="executeAction('rellenarPanel','&destino=empleados');";
      }
    }
    else {
      $JS.="iqwerty.toast.Toast('{$errores->error}',{settings:{duration:2000}});";
    }
  break;
  case "panelAuxiliarRecibos":
    $idUsuario=$_GET["idUsuario"];
    $nombreRecibo=$_GET["nombreRecibo"];
    $conceptoRecibo=$_GET["conceptoRecibo"];
    $cantidadRecibo=$_GET["cantidadRecibo"];
    $fechaRecibo=$_GET["fechaRecibo"];
    $fincaRecibo=$_GET["fincaRecibo"];
    $idRecibo=$_GET["idRecibo"];
    $errores=new Errores();
    if (empty($nombreRecibo)) $errores->setError("Debe introducir un nombre para el recibo");
    if (empty($conceptoRecibo)) $errores->setError("Debe introducir un concepto para el recibo");
    if (empty($cantidadRecibo)) $errores->setError("Debe introducir una cantidad para el recibo");
    if (empty($fechaRecibo)) $errores->setError("Debe introducir una fecha para el recibo");
    else $fechaRecibo=Strings::fechaSQL($fechaRecibo);
    if (empty($fincaRecibo)) $errores->setError("Debe introducir una finca para el recibo");
    if (!is_numeric($cantidadRecibo)) $errores->setError("Debe introducir una cantidad válida para el recibo");

    if ($errores->success){
      if ($idRecibo!=0){
        $recibo=classInvoker::nc('recibos',intval($idRecibo));
        $recibo->nombre=$nombreRecibo;
        $recibo->concepto=$conceptoRecibo;
        $recibo->idUsuario=$idUsuario;
        $recibo->importe=$cantidadRecibo;
        $recibo->fecha=$fechaRecibo;
        $recibo->idFinca=$fincaRecibo;
        $recibo->update();
        $JS.="iqwerty.toast.Toast('Recibo editado con éxito',{settings:{duration:1000}});executeAction('rellenarPanel','&destino=panelAuxiliarRecibos&idUsuario=$idUsuario');$('#idRecibo').val('');";
      }
      else {
        $recibo=classInvoker::nc('recibos');
        $recibo->nombre=$nombreRecibo;
        $recibo->concepto=$conceptoRecibo;
        $recibo->idUsuario=$idUsuario;
        $recibo->importe=$cantidadRecibo;
        $recibo->fecha=$fechaRecibo;
        $recibo->idFinca=$fincaRecibo;
        $recibo->insert();
        $JS.="iqwerty.toast.Toast('Recibo añadido con éxito',{settings:{duration:1000}});executeAction('rellenarPanel','&destino=panelAuxiliarRecibos&idUsuario=$idUsuario');";
      }
    }
    else {
      $JS.="iqwerty.toast.Toast('{$errores->error}',{settings:{duration:2000}});";
    }
  break;
  case "empleadosPDF":
    $JS.="$('#resultadosEmpleadosPDF').html('');";
    $valoresCheckbox = array();
    $idEmpleados="";
    foreach ($_GET as $key => $value) {
        if (strpos($key, 'checkBox') === 0) {
            if ($value=="true"){
              $nuevoKey = substr($key, strlen('checkBox'));
              $idEmpleados.="$nuevoKey-";
            }
            $valoresCheckbox[$key] = $value;
        }
    }
    $idEmpleados = rtrim($idEmpleados, $idEmpleados[strlen($idEmpleados)-1]);
    $empleados=false;
    foreach($valoresCheckbox as $valor){
        if ($valor=='true') $empleados=true;
    }
    if (!$empleados){
      $JS.="iqwerty.toast.Toast('Debe seleccionar al menos un empleado',{settings:{duration:1000}});";
    }
    else {
      if (!isset($_GET['inputFechaInicioPDF']) || !isset($_GET['inputFechaFinPDF'])){
        $JS.="iqwerty.toast.Toast('Debe seleccionar fecha de inicio y fin',{settings:{duration:1000}});";
      }
      else {
        $fechaInicio=DateTime::createFromFormat('d/m/Y', $_GET['inputFechaInicioPDF']);
        $fechaFin=DateTime::createFromFormat('d/m/Y', $_GET['inputFechaFinPDF']);
        $intervalo = $fechaInicio->diff($fechaFin);
                  
        if ($intervalo->days > 30) {
          $JS.="iqwerty.toast.Toast('No se permiten periodos mayores a 31 días',{settings:{duration:1000}});";
        }
        else{
          $fechaInicioFormatted = $fechaInicio->format('d-m-Y');
          $fechaFinFormatted = $fechaFin->format('d-m-Y'); 
          $JS.="$('#resultadosEmpleadosPDF').append('<div onclick=\"window.open(\'./PHP/actions/crearPDFEmpleados.php?idEmpleado=$idEmpleados&fechaInicio=$fechaInicioFormatted&fechaFin=$fechaFinFormatted\')\"><div class=\"panelLateralSymbol\"><i class=\"fa fa-file \"></i></div><label class=\"panelLateralLabel\">Crear PDF</label></div>');";
        } 
      }
    }
  break;
  }
}
echo $JS;
?>