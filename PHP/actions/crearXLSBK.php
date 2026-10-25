<?php
error_reporting(-1);
ini_set("display_errors", 1);
session_start();
$base = __DIR__;
$base = dirname($base);
$base = dirname($base);
include "$base/PHP/LoadCore.php";

if (Session::isLogged()) {
  if (Session::allowed()) {
    $columnas = ['id', 'nombre', 'usuario', 'reset', 'estado', 'claveDefecto', 'correspondencia'];
    $stringCampos = implode(',', $columnas);
    $columnas = array_merge($columnas, ['Direcciones', 'Destinatarios', 'Correos', 'Correos_contacto', 'Telefonos', 'Telefonos_contacto', 'Fincas', 'Propietarios', 'Dni', 'Cuota']);
    
    incluirExt('xls');
    incluir('comunidades');
    incluir('usuarios');
    incluir('direccionesPostales');
    incluir('correos');
    incluir('telefonos');
    incluir('fincas');

    $contenidoXLS = [];
    $breakpoints = [6, 8, 10];

    // Obtener comunidades con PDO
    $stmtComunidades = Database::ExecuteQuery("SELECT * FROM comunidades", false);
    $comunidades = $stmtComunidades ? $stmtComunidades->fetchAll(PDO::FETCH_ASSOC) : [];

    foreach ($comunidades as $comunidad) {
        $idComunidad = $comunidad['id'];
        $contenidoXLS[] = array_values($comunidad);
        $contenidoXLS[] = $columnas;
        
        // Obtener usuarios con PDO y parámetros seguros
        $stmtUsuarios = Database::ExecuteQuery("SELECT $stringCampos FROM usuarios WHERE idComunidad = ?", false, [$idComunidad]);
        $usuarios = $stmtUsuarios ? $stmtUsuarios->fetchAll(PDO::FETCH_ASSOC) : [];

        if ($usuarios) {
            foreach ($usuarios as $usuario) {
              $idUsuario = $usuario['id'];
              
              // Direcciones postales
              $stmtDir = Database::ExecuteQuery("SELECT direccionPostal, destinatario FROM direccionesPostales WHERE origen = ?", false, ["usuarios;$idUsuario"]);
              $direccionesA = $stmtDir ? $stmtDir->fetchAll(PDO::FETCH_ASSOC) : [];
              if ($direccionesA) {
                foreach ($direccionesA as $direccion) {
                    $usuario[] = $direccion['direccionPostal'];
                    $usuario[] = $direccion['destinatario'];
                }
              }
              
              // Correos
              $stmtCor = Database::ExecuteQuery("SELECT correo, contacto FROM correos WHERE origen = ?", false, ["usuarios;$idUsuario"]);
              $correosA = $stmtCor ? $stmtCor->fetchAll(PDO::FETCH_ASSOC) : [];
              if ($correosA) {
                foreach ($correosA as $correo) {
                  $usuario[] = $correo['correo'];
                  $usuario[] = $correo['contacto'];
                }
              }
              
              // Teléfonos
              $stmtTel = Database::ExecuteQuery("SELECT telefono, contacto FROM telefonos WHERE origen = ?", false, ["usuarios;$idUsuario"]);
              $telefonosA = $stmtTel ? $stmtTel->fetchAll(PDO::FETCH_ASSOC) : [];
              if ($telefonosA) {
                foreach ($telefonosA as $telefono) {
                  $usuario[] = $telefono['telefono'];
                  $usuario[] = $telefono['contacto'];
                }
              }
              
              // Fincas
              $stmtFin = Database::ExecuteQuery("SELECT finca FROM fincas WHERE idUsuario = ?", false, [$idUsuario]);
              $fincasA = $stmtFin ? $stmtFin->fetchAll(PDO::FETCH_ASSOC) : [];
              if ($fincasA) {
                 foreach ($fincasA as $finca) {
                    $usuario[] = $finca['finca'];
                 }
              }
              
              $contenidoXLS[] = array_values($usuario);
            }
        }
    }
    
    // Generar archivo Excel con SimpleXLSXGen
    $xlsx = Shuchkin\SimpleXLSXGen::fromArray($contenidoXLS);
    $xlsx->downloadAs('Aguacada.xlsx');
  }
}