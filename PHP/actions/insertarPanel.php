<?php
session_start();

if(Session::isLogged()) {
  $JS="";
  // Unificamos la obtención del destino tanto por GET como por POST
  $destino = $_POST['destino'] ?? $_GET['destino'] ?? '';
  
  switch ($destino){
  case "home":
    if (Session::allowed()){
      $JS.="document.getElementById('formBackup').submit();iqwerty.toast.Toast('Backup realizado con éxito',{settings:{duration:1000}});";
    }
  break;
  case "usuarios":
    if (Session::allowed()){
      error_log("[DEBUG USUARIOS] --- INICIO panel usuarios ---");
      
      $Snombre = $_POST['idNombre'] ?? $_GET['idNombre'] ?? '';
      $idComunidad = $_POST['selectorComunidades'] ?? $_GET['selectorComunidades'] ?? '';
      $Spiso = $_POST['idPiso'] ?? $_GET['idPiso'] ?? '';
      $Susuario = $_POST['idUsuario'] ?? $_GET['idUsuario'] ?? '';
      $Sclave = $_POST['idClave'] ?? $_GET['idClave'] ?? '';
      $Sprefijo = $_POST['idPrefijo'] ?? $_GET['idPrefijo'] ?? '';
      $Stelefono = $_POST['idTelefono'] ?? $_GET['idTelefono'] ?? '';
      $Semail = $_POST['idCorreo'] ?? $_GET['idCorreo'] ?? '';
      $idUsuario = $_POST['idUsuarioEditar'] ?? $_GET['idUsuarioEditar'] ?? '';
      $checkboxCorrespondencia = $_POST['checkboxCorrespondencia'] ?? $_GET['checkboxCorrespondencia'] ?? '';
      
      $Strings = new Strings();
      $errores = new Errores();

      if (!empty($idUsuario)){
        error_log("[DEBUG USUARIOS] Modo EDICIÓN para ID: " . $idUsuario);
        $usuario = classInvoker::nc('usuarios', intval($idUsuario), true);
        
        if (!empty($Susuario)){
          $usuario->usuario = $Strings->chop($Susuario);
        }
        
        if (!empty($Sclave)){
          $usuario->claveDefecto = $Strings->chop($Sclave);
          $usuario->resetClave();
        }
        
        if ($usuario->existe()) {
          $errores->setError('Usuario repetido. Pruebe con otro.');
        }
      }
      else {
        error_log("[DEBUG USUARIOS] Modo INSERCIÓN de nuevo usuario.");
        $usuario = classInvoker::nc('usuarios', false, true);
        $usuario->usuario = $Strings->chop($Susuario);
        $usuario->claveDefecto = $Strings->chop($Sclave);
        $usuario->resetClave();
        
        if ($usuario->existe()) {
          $errores->setError('Usuario repetido. Pruebe con otro.');
        }
      }

      $usuario->nombre = $Strings->chop($Snombre);
      $usuario->idComunidad = $idComunidad;

      $Spiso = $Strings->chop($Spiso);
      $usuario->piso = $Spiso;

      $Semail = $Strings->chop($Semail);
      $Sprefijo = $Strings->chop($Sprefijo);
      $Stelefono = $Strings->chop($Stelefono);

      if (empty($usuario->nombre)) $errores->setError("Debe introducir nombre");
      if (empty($Spiso)) $errores->setError("Debe introducir un piso");
      if (empty($usuario->usuario)) $errores->setError("Debe introducir un usuario");
      if (empty($idUsuario) && empty($Sclave)) {
        $errores->setError("Debe introducir una clave");
      }

      if (strlen($usuario->usuario) < 5) $errores->setError("El usuario debe tener al menos 5 caracteres");
      if (!empty($Sclave) && strlen($Sclave) < 5) {
        $errores->setError("La clave debe tener al menos 5 caracteres");
      }

      if (!empty($Semail)) {
        if (!Strings::SvalidEmail($Semail)) $errores->setError("Debe introducir un email válido");
      }
      if (empty($Sprefijo)) $errores->setError("Debe introducir un prefijo");
      if (empty($Stelefono)) $errores->setError("Debe introducir un telefono");
      
      if ($checkboxCorrespondencia == 'true') $usuario->correspondencia = 1;
      else $usuario->correspondencia = 0;

      if ($errores->success){
        error_log("[DEBUG USUARIOS] Validación superada con éxito.");

        if (!empty($idUsuario)){
                $usuario->update();
                error_log("[DEBUG USUARIOS] Usuario actualizado ID: " . $usuario->id);
                
                Database::ExecuteQuery("UPDATE fincas SET finca = ? WHERE idUsuario = ? AND defecto = 1", false, [$usuario->piso, $usuario->id]);
                incluir('telefonos');
                incluir('correos');
                $idTelefono = telefonos::getValue("SELECT id FROM telefonos WHERE origen = ? AND defecto = 1", ["usuarios;{$usuario->id}"]);
                if ($idTelefono) {
                  $telefono = classInvoker::nc('telefonos', intval($idTelefono));
                  $telefono->telefono = $Stelefono;
                  $telefono->prefijo = $Sprefijo;
                  $telefono->update();
                }

                $idCorreo = correos::getValue("SELECT id FROM correos WHERE origen = ? AND defecto = 1", ["usuarios;{$usuario->id}"]);
                if ($idCorreo) {
                  $correo = classInvoker::nc('correos', intval($idCorreo));
                  $correo->correo = $Semail;
                  $correo->update();
                }

                $JS .= "iqwerty.toast.Toast('Usuario actualizado con éxito',{settings:{duration:1000}});";
        }
        else{
                error_log("[DEBUG USUARIOS] Intentando llamar a \$usuario->insert()...");
                
                // Llamamos al insert del modelo
                $usuario->insert();
                
                error_log("[DEBUG USUARIOS] Tras \$usuario->insert(), \$usuario->id es: " . var_export($usuario->id, true));

                // PARCHE DE SEGURIDAD EXTRA: Si por lo que sea el modelo devuelve 0 o vacío, lo rescatamos al vuelo
                if (empty($usuario->id) || $usuario->id == 0) {
                    $lastId = Database::lastInsertId();
                    error_log("[DEBUG USUARIOS] Database::lastInsertId() devolvió: " . var_export($lastId, true));
                    if (!empty($lastId) && $lastId != 0) {
                        $usuario->id = $lastId;
                    } else {
                        // Último recurso: buscar el id por nombre de usuario único
                        $buscado = usuarios::getValue("SELECT id FROM usuarios WHERE usuario = ? ORDER BY id DESC LIMIT 1", [$usuario->usuario]);
                        error_log("[DEBUG USUARIOS] Búsqueda manual por nombre de usuario devolvió ID: " . var_export($buscado, true));
                        if (!empty($buscado)) {
                            $usuario->id = $buscado;
                        }
                    }
                }

                error_log("[DEBUG USUARIOS] ID definitivo asignado para insertar relaciones: " . $usuario->id);

                $telefono = classInvoker::nc('telefonos');
                $telefono->origen = "usuarios;{$usuario->id}";
                $telefono->telefono = $Stelefono;
                $telefono->prefijo = $Sprefijo;
                $telefono->defecto = 1;
                $telefono->insert();
                
                $correo = classInvoker::nc('correos');
                $correo->origen = "usuarios;{$usuario->id}";
                $correo->correo = $Semail;
                $correo->defecto = 1;
                $correo->insert();
                
                $finca = classInvoker::nc('fincas');
                $finca->finca = $Spiso;
                $finca->defecto = 1;
                $finca->idUsuario = $usuario->id;
                $finca->insert();
                
                $JS .= "iqwerty.toast.Toast('Usuario insertado con éxito',{settings:{duration:1000}});";
        }
        $JS .= "$('#panelAuxiliar').fadeOut('slow');executeAction('rellenarPanel','&destino=usuarios','filtroUsuarios');";

      }
      else{
        error_log("[DEBUG USUARIOS ERROR] Falló la validación: " . $errores->error);
        $JS .= "iqwerty.toast.Toast('{$errores->error}',{settings:{duration:2000}});";
      }
      error_log("[DEBUG USUARIOS] --- FIN panel usuarios ---");
    }
  break;
  case "usuariosExportar":
    if (Session::allowed()){
      $idUsuario = $_POST["idUsuario"] ?? $_GET["idUsuario"] ?? '';
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
      $Scomunidad = $_POST['comunidad'] ?? $_GET['comunidad'] ?? '';
      $direccion = $_POST['direccion'] ?? $_GET['direccion'] ?? '';
      $direccion2 = $_POST["direccion2"] ?? $_GET["direccion2"] ?? '';
      $idComunidad = $_POST["idComunidad"] ?? $_GET["idComunidad"] ?? '';
      
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
    $inputMensaje = $_POST["inputMensajesEnviar"] ?? $_GET["inputMensajesEnviar"] ?? '';
    $idDestino = $_POST['idUsuario'] ?? $_GET['idUsuario'] ?? 0;
    
    if (Session::allowed()){
      $mensaje=$inputMensaje;
      if (trim($mensaje)=='') exit;
      $destino=$idDestino;
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
      $mensaje=$inputMensaje;
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
      $idUsuario = $_POST["idUsuario"] ?? $_GET["idUsuario"] ?? 0;
      $idComunidad = $_POST["idComunidad"] ?? $_GET["idComunidad"] ?? 0;
      $archivo = $_POST["idNombreArchivo"] ?? $_GET["idNombreArchivo"] ?? '';
      $uploadFile = $_POST["inputSubirArchivo"] ?? $_GET["inputSubirArchivo"] ?? '';
      
      $errores=new Errores();
      if (empty($archivo)) $errores->setError("Debe introducir un nombre para el archivo");
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
      $idUsuario = $_POST["idUsuario"] ?? $_GET["idUsuario"] ?? 0;
      $idComunidad = $_POST["idComunidad"] ?? $_GET["idComunidad"] ?? 0;
      $evento = $_POST["inputEvento"] ?? $_GET["inputEvento"] ?? '';
      $alerta = $_POST["inputAlerta"] ?? $_GET["inputAlerta"] ?? '';
      $fechaAlerta = $_POST["inputFechaAlerta"] ?? $_GET["inputFechaAlerta"] ?? '';
      
      $errores=new Errores();
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
      $idUsuario = $_POST["idUsuario"] ?? $_GET["idUsuario"] ?? '';
      $contacto = $_POST["contacto"] ?? $_GET["contacto"] ?? '';
      $tipo = $_POST["tipo"] ?? $_GET["tipo"] ?? '';
      $prefijo = $_POST["prefijo"] ?? $_GET["prefijo"] ?? '';
      $telefono = $_POST["telefono"] ?? $_GET["telefono"] ?? '';
      
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
      $idUsuario = $_POST["idUsuario"] ?? $_GET["idUsuario"] ?? '';
      $contacto = $_POST["contacto"] ?? $_GET["contacto"] ?? '';
      $tipo = $_POST["tipo"] ?? $_GET["tipo"] ?? '';
      $correo = $_POST["correo"] ?? $_GET["correo"] ?? '';
      
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
      $idDireccion = $_POST["idDireccion"] ?? $_GET["idDireccion"] ?? '';
      $idUsuario = $_POST["idUsuario"] ?? $_GET["idUsuario"] ?? '';
      $destinatario = $_POST["destinatario"] ?? $_GET["destinatario"] ?? '';
      $direccion = $_POST["direccion"] ?? $_GET["direccion"] ?? '';
      
      $errores=new Errores();
      if (empty ($destinatario)) $errores->setError('Debe introducir un destinatario');
      if (empty ($direccion)) $errores->setError('Debe introducir una dirección postal');

      if ($errores->success){
        if ($idDireccion!="false"){
          $direccionObj=classInvoker::nc('direccionesPostales',intval($idDireccion));
          $direccionObj->direccionPostal=$direccion;
          $direccionObj->origen="usuarios;$idUsuario";
          $direccionObj->destinatario=$destinatario;
          $direccionObj->update();
          $JS.="iqwerty.toast.Toast('Dirección postal editada con éxito',{settings:{duration:1000}});executeAction('rellenarPanel','&destino=usuarios','filtroUsuarios');$('#panelAuxiliar').fadeOut('slow');";
        }
        else {
          $direccionObj=classInvoker::nc('direccionesPostales');
          $direccionObj->direccionPostal=$direccion;
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
    $correspondencia = $_POST["correspondencia"] ?? $_GET["correspondencia"] ?? '';
    $idCorrespondencia = $_POST["idCorrespondencia"] ?? $_GET["idCorrespondencia"] ?? '';
    $hayDirecciones=false;
    $destinatarios = [];
    
    $fuenteDatos = !empty($_POST) ? $_POST : $_GET;
    foreach ($fuenteDatos as $key => $value) {
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
    $exportacion = $_POST["exportacion"] ?? $_GET["exportacion"] ?? '';
    $idExportacion = $_POST["idExportacion"] ?? $_GET["idExportacion"] ?? '';
    $filtros='';
    
    $fuenteDatos = !empty($_POST) ? $_POST : $_GET;
    foreach ($fuenteDatos as $key => $value) {
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
      $idUsuario = $_POST["idUsuario"] ?? $_GET["idUsuario"] ?? '';
      $finca = $_POST["finca"] ?? $_GET["finca"] ?? '';
      $coeficiente = $_POST['coeficiente'] ?? $_GET['coeficiente'] ?? '';
      
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
      $idUsuario = $_POST["idUsuario2"] ?? $_GET["idUsuario2"] ?? '';
      $idFinca = $_POST["idFinca2"] ?? $_GET["idFinca2"] ?? '';
      $nombrePropietario = $_POST["nombrePropietario"] ?? $_GET["nombrePropietario"] ?? '';
      $dniPropietario = $_POST["dniPropietario"] ?? $_GET["dniPropietario"] ?? '';
      $idPropietario = $_POST['idPropietario2'] ?? $_GET['idPropietario2'] ?? 0;
      
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
    error_log("[DEBUG EMPLEADOS] --- INICIO panelAuxiliarEmpleados ---");
    error_log("[DEBUG EMPLEADOS] _GET recibido: " . print_r($_GET, true));
    error_log("[DEBUG EMPLEADOS] _POST recibido: " . print_r($_POST, true));

    $idEmpleado = $_POST["idEmpleado"] ?? $_GET["idEmpleado"] ?? '';
    $idNombre = $_POST["idNombre"] ?? $_GET["idNombre"] ?? '';
    $idClave = $_POST["idClave"] ?? $_GET["idClave"] ?? '';
    $idDNI = $_POST["idDNI"] ?? $_GET["idDNI"] ?? '';
    $idCheckEmpleados = $_POST['idCheckEmpleados'] ?? $_GET['idCheckEmpleados'] ?? '';
    $idEmpleadoEditar = isset($_POST['idEmpleadoEditar']) ? intval($_POST["idEmpleadoEditar"]) : (isset($_GET['idEmpleadoEditar']) ? intval($_GET['idEmpleadoEditar']) : 0);
    
    incluir('empleados');
    $errores = new Errores();
    if (empty($idNombre)) $errores->setError("Debe introducir nombre completo para el empleado");
    if (empty($idEmpleado)) $errores->setError("Debe introducir un nombre de empleado");
    if (empty($idClave)) $errores->setError("Debe introducir una clave por defecto");
    if (empty($idDNI)) $errores->setError("Debe introducir un DNI");

    // Comprobación de empleado repetido
    if (!empty($idEmpleado)) {
      $queryExiste = "SELECT id FROM empleados WHERE empleado = ?";
      $paramsExiste = [$idEmpleado];
      if ($idEmpleadoEditar != 0) {
        $queryExiste .= " AND id != ?";
        $paramsExiste[] = $idEmpleadoEditar;
      }
      if (empleados::getValue($queryExiste, $paramsExiste)) {
        $errores->setError("El nombre de empleado ya existe. Pruebe con otro.");
      }
    }

    if ($errores->success){
      error_log("[DEBUG EMPLEADOS] Validación superada con éxito.");
      
      if ($idEmpleadoEditar != 0){
        error_log("[DEBUG EMPLEADOS] Intentando actualizar empleado ID: " . $idEmpleadoEditar);
        $empleado = classInvoker::nc('empleados', $idEmpleadoEditar);
        $empleado->nombre = $idNombre;
        $empleado->empleado = $idEmpleado;
        $empleado->claveDefecto = $idClave;
        $empleado->clave = password_hash(trim($idClave), PASSWORD_DEFAULT);
        $empleado->dni = $idDNI;
        $empleado->reset = ($idCheckEmpleados == "true" || $idCheckEmpleados == "1") ? 1 : 0;
        
        try {
            $empleado->update();
            error_log("[DEBUG EMPLEADOS] Empleado actualizado correctamente.");
        } catch (Exception $e) {
            error_log("[DEBUG EMPLEADOS ERROR SQL UPDATE] " . $e->getMessage());
        }

        $JS .= "iqwerty.toast.Toast('Empleado editado con éxito',{settings:{duration:1000}});$('#panelAuxiliar').fadeOut('slow');executeAction('rellenarPanel','&destino=empleados');";
      }
      else {
        error_log("[DEBUG EMPLEADOS] Intentando insertar nuevo empleado.");
        $empleado = classInvoker::nc('empleados');
        $empleado->nombre = $idNombre;
        $empleado->empleado = $idEmpleado;
        $empleado->claveDefecto = $idClave;
        $empleado->clave = password_hash(trim($idClave), PASSWORD_DEFAULT);
        $empleado->dni = $idDNI;
        $empleado->reset = ($idCheckEmpleados == "true" || $idCheckEmpleados == "1") ? 1 : 0;
        
        try {
            $empleado->insert();
            error_log("[DEBUG EMPLEADOS] Empleado insertado correctamente. ID asignado: " . $empleado->id);
        } catch (Exception $e) {
            error_log("[DEBUG EMPLEADOS ERROR SQL INSERT] " . $e->getMessage());
        }

        $JS .= "iqwerty.toast.Toast('Empleado añadido con éxito',{settings:{duration:1000}});$('#panelAuxiliar').fadeOut('slow');executeAction('rellenarPanel','&destino=empleados');";
      }
    }
    else {
      error_log("[DEBUG EMPLEADOS ERROR] Falló la validación: " . $errores->error);
      $JS .= "iqwerty.toast.Toast('{$errores->error}',{settings:{duration:2000}});";
    }
    error_log("[DEBUG EMPLEADOS] --- FIN panelAuxiliarEmpleados ---");
  break;
  case "panelAuxiliarRecibos":
      $idUsuario = $_GET["idUsuario"] ?? '';
      $nombreRecibo = $_GET["nombreRecibo"] ?? '';
      $conceptoRecibo = $_GET["conceptoRecibo"] ?? '';
      $cantidadRecibo = $_GET["cantidadRecibo"] ?? '';
      $fechaRecibo = $_GET["fechaRecibo"] ?? '';
      $fincaRecibo = $_GET["fincaRecibo"] ?? '';

      $idReciboRaw = $_GET["idRecibo"] ?? 'NO LLEGÓ';
      $idRecibo = trim($idReciboRaw);

      // LOG DE DEPURACIÓN
      error_log("[DEBUG RECIBOS GET] idRecibo crudo: " . var_export($idReciboRaw, true) . " | idRecibo limpio: " . var_export($idRecibo, true));

      $errores = new Errores();
      if (empty($nombreRecibo)) $errores->setError("Debe introducir un nombre para el recibo");
      if (empty($conceptoRecibo)) $errores->setError("Debe introducir un concepto para el recibo");
      if (empty($cantidadRecibo)) $errores->setError("Debe introducir una cantidad para el recibo");
      if (empty($fechaRecibo)) $errores->setError("Debe introducir una fecha para el recibo");
      else $fechaRecibo = Strings::fechaSQL($fechaRecibo);
      if (empty($fincaRecibo)) $errores->setError("Debe introducir una finca para el recibo");
      if (!is_numeric($cantidadRecibo)) $errores->setError("Debe introducir una cantidad válida para el recibo");

      if ($errores->success){
        if ($idRecibo !== '' && is_numeric($idRecibo) && intval($idRecibo) > 0){
          error_log("[DEBUG RECIBOS GET] Entró por EDICIÓN con ID: " . $idRecibo);
          $recibo = classInvoker::nc('recibos', intval($idRecibo));
          $recibo->nombre = $nombreRecibo;
          $recibo->concepto = $conceptoRecibo;
          $recibo->idUsuario = $idUsuario;
          $recibo->importe = $cantidadRecibo;
          $recibo->fecha = $fechaRecibo;
          $recibo->idFinca = $fincaRecibo;
          $recibo->update();

          $JS .= "iqwerty.toast.Toast('Recibo editado con éxito',{settings:{duration:1000}});executeAction('rellenarPanel','&destino=panelAuxiliarRecibos&idUsuario=$idUsuario');$('#idRecibo').val('');";
        }
        else {
          error_log("[DEBUG RECIBOS GET] Entró por INSERCIÓN limpia.");
          $recibo = classInvoker::nc('recibos');
          $recibo->nombre = $nombreRecibo;
          $recibo->concepto = $conceptoRecibo;
          $recibo->idUsuario = $idUsuario;
          $recibo->importe = $cantidadRecibo;
          $recibo->fecha = $fechaRecibo;
          $recibo->idFinca = $fincaRecibo;
          $recibo->insert();

          $JS .= "iqwerty.toast.Toast('Recibo insertado con éxito',{settings:{duration:1000}});executeAction('rellenarPanel','&destino=panelAuxiliarRecibos&idUsuario=$idUsuario');$('#idRecibo').val('');";
        }
      }
      else {
        $JS .= "iqwerty.toast.Toast('{$errores->error}',{settings:{duration:2000}});";
      }
    break;
  case "empleadosPDF":
    $JS.="$('#resultadosEmpleadosPDF').html('');";
    $valoresCheckbox = array();
    $idEmpleados="";
    
    $fuenteDatos = !empty($_POST) ? $_POST : $_GET;
    foreach ($fuenteDatos as $key => $value) {
        if (strpos($key, 'checkBox') === 0) {
            if ($value=="true"){
              $nuevoKey = substr($key, strlen('checkBox'));
              $idEmpleados.="$nuevoKey-";
            }
            $valoresCheckbox[$key] = $value;
        }
    }
    if (!empty($idEmpleados)) {
      $idEmpleados = rtrim($idEmpleados, '-');
    }
    $empleados=false;
    foreach($valoresCheckbox as $valor){
        if ($valor=='true') $empleados=true;
    }
    if (!$empleados){
      $JS.="iqwerty.toast.Toast('Debe seleccionar al menos un empleado',{settings:{duration:1000}});";
    }
    else {
      $fechaInicioStr = $_POST['inputFechaInicioPDF'] ?? $_GET['inputFechaInicioPDF'] ?? '';
      $fechaFinStr = $_POST['inputFechaFinPDF'] ?? $_GET['inputFechaFinPDF'] ?? '';
      
      if (empty($fechaInicioStr) || empty($fechaFinStr)){
        $JS.="iqwerty.toast.Toast('Debe seleccionar fecha de inicio y fin',{settings:{duration:1000}});";
      }
      else {
        $fechaInicio=DateTime::createFromFormat('d/m/Y', $fechaInicioStr);
        $fechaFin=DateTime::createFromFormat('d/m/Y', $fechaFinStr);
        
        if (!$fechaInicio || !$fechaFin) {
          $JS.="iqwerty.toast.Toast('Formato de fecha inválido',{settings:{duration:1000}});";
        } else {
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
    }
  break;
  }
}
echo $JS;
?>