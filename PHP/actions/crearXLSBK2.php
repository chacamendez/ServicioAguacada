<?php
error_reporting(E_ALL & ~E_DEPRECATED);
session_start();
$base = __DIR__;
$base = dirname($base);
$base = dirname($base);
include "$base/PHP/LoadCore.php";

if (Session::isLogged()) {
  if (Session::allowed()){
    $columnas = ['id', 'nombre', 'usuario', 'reset', 'estado', 'claveDefecto', 'correspondencia'];
    $stringCampos = implode(',', $columnas);
    $columnas = array_merge($columnas, ['Direcciones', 'Destinatarios', 'Correos', 'Correos_contacto', 'Telefonos', 'Telefonos_contacto', 'Fincas', 'Propietarios', 'Dni', 'Cuota']);
    $breakpoints = [6, 8, 10];

    $cadena = "<table>";
    
    // Obtener comunidades con PDO
    $stmtComunidades = Database::ExecuteQuery("SELECT * FROM comunidades", false);
    $comunidades = $stmtComunidades ? $stmtComunidades->fetchAll(PDO::FETCH_ASSOC) : [];

    foreach ($comunidades as $comunidad) {
      $idComunidad = $comunidad['id'];
      $cadena .= "<tr><td>$idComunidad</td><td>{$comunidad['comunidad']}</td><td>{$comunidad['direccionPostal']}</td><td>{$comunidad['direccionPostal2']}</td></tr>";
      
      // Obtener usuarios con PDO y parámetros seguros
      $stmtUsuarios = Database::ExecuteQuery("SELECT $stringCampos FROM usuarios WHERE idComunidad = ?", false, [$idComunidad]);
      $usuarios = $stmtUsuarios ? $stmtUsuarios->fetchAll(PDO::FETCH_ASSOC) : [];

      if ($usuarios) {
        $cadena .= "<tr><td>id</td><td>nombre</td><td>usuario</td><td>reset</td><td>estado</td>" .
        "<td>ClaveDefecto</td><td>correspondencia</td><td>Direcciones</td><td>Destinatarios</td>" .
        "<td>Correos</td><td>Correos_contacto</td><td>Telefonos</td><td>Telefonos_contacto</td>" .
        "<td>Fincas</td><td>Propietarios</td><td>DNI</td><td>Cuota</td></tr>";
        
        foreach ($usuarios as $usuario) {
            $lines = [];
            $idUsuario = $usuario['id'];
            $cadena .= "<tr><td>{$usuario['id']}</td><td>{$usuario['nombre']}</td><td>{$usuario['usuario']}</td>" .
            "<td>{$usuario['reset']}</td><td>{$usuario['estado']}</td><td>{$usuario['claveDefecto']}</td>" .
            "<td>{$usuario['correspondencia']}</td>";
            
            // Direcciones postales
            $stmtDir = Database::ExecuteQuery("SELECT direccionPostal, destinatario FROM direccionesPostales WHERE origen = ?", false, ["usuarios;$idUsuario"]);
            $direccionesA = $stmtDir ? $stmtDir->fetchAll(PDO::FETCH_ASSOC) : [];
            
            if ($direccionesA) {
              foreach ($direccionesA as $direccion) {
                  if ($direccionesA) {
                    $cadena .= "<td>{$direccion['direccionPostal']}</td><td>{$direccion['destinatario']}</td>";
                  } else {
                    $lines[] = "<tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td>{$direccion['direccionPostal']}</td><td>{$direccion['destinatario']}</td>";
                  }
              }
            }
            $numLines = count($lines);
            
            // Correos
            $stmtCor = Database::ExecuteQuery("SELECT correo, contacto FROM correos WHERE origen = ?", false, ["usuarios;$idUsuario"]);
            $correosA = $stmtCor ? $stmtCor->fetchAll(PDO::FETCH_ASSOC) : [];
            $index = 0;
            
            if ($correosA) {
              foreach ($correosA as $correo) {
                if ($index == 0 && count($correosA) > 0) {
                  $cadena .= "<td>{$correo['correo']}</td><td>{$correo['contacto']}</td>";
                } else {
                  if ($numLines == 0) {
                    $lines[] = "<tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>" .
                    "<td>{$correo['correo']}</td><td>{$correo['contacto']}</td>";
                  } else {
                    if ($numLines > $index) {
                      $lines[$index] .= "<td>{$correo['correo']}</td><td>{$correo['contacto']}</td>;";
                    } else {
                      $lines[] = "<tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>" .
                      "<td>{$correo['correo']}</td><td>{$correo['contacto']}</td>";
                    }
                  }
                }
                $index++;
              }
            }
            $numLines = count($lines);
            
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
        }
      }
    }
    
    header("Content-type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=Aguacada.xls");
    echo utf8_decode($cadena);
  }
}