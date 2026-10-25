<?php
error_reporting(-1);
ini_set("display_errors", 1);
session_start();
$base = __DIR__;
$base = dirname($base);
$base = dirname($base);
include "$base/PHP/LoadCore.php";

function arrayToTableString($lines) {
  $cadena = "";
  foreach ($lines as $line) {
    $cadena .= "<tr>";
    foreach ($line as $column) {
      $cadena .= "<td>$column</td>";
    }
    $cadena .= "</tr>";
  }
  return $cadena;
}

if (Session::isLogged()) {
  if (Session::allowed()) {
    incluir('comunidades');
    incluir('usuarios');
    incluir('direccionesPostales');
    incluir('correos');
    incluir('telefonos');
    incluir('fincas');
    incluir('propietarios');
    
    $columnas = ['id', 'nombre', 'usuario', 'reset', 'estado', 'claveDefecto', 'correspondencia'];
    $stringCampos = implode($columnas, ',');
    $columnas = array_merge($columnas, ['Direcciones', 'Destinatarios', 'Correos', 'Correos_contacto', 'Prefijos', 'Telefonos', 'Telefonos_contacto', 'Fincas', 'Propietarios', 'Dni', 'Cuota']);
    $breakpoints = [7, 9, 11];

    $cadena = "<table>";
    
    // Obtener comunidades con PDO
    $stmtComunidades = Database::ExecuteQuery("SELECT * FROM comunidades", false);
    $comunidades = $stmtComunidades ? $stmtComunidades->fetchAll(PDO::FETCH_ASSOC) : [];
    
    foreach ($comunidades as $comunidad) {
      $idComunidad = $comunidad['id'];
      $cadena .= "<tr style='background-color:blue'><td>$idComunidad</td><td>{$comunidad['comunidad']}</td><td>{$comunidad['direccionPostal']}</td><td>{$comunidad['direccionPostal2']}</td></tr>";
      
      // Obtener usuarios con PDO y parámetros seguros
      $stmtUsuarios = Database::ExecuteQuery("SELECT $stringCampos FROM usuarios WHERE idComunidad = ? AND estado <> 'eliminado'", false, [$idComunidad]);
      $usuarios = $stmtUsuarios ? $stmtUsuarios->fetchAll(PDO::FETCH_ASSOC) : [];
      
      if ($usuarios) {
        $cadena .= "<tr><td>id</td><td>nombre</td><td>usuario</td><td>reset</td><td>estado</td>" .
        "<td>ClaveDefecto</td><td>correspondencia</td><td>Direcciones</td><td>Destinatarios</td>" .
        "<td>Correos</td><td>Correos_contacto</td><td>Prefijo</td><td>Telefonos</td><td>Telefonos_contacto</td>" .
        "<td>Fincas</td><td>Propietarios</td><td>DNI</td><td>Coeficiente</td></tr>";
        
        foreach ($usuarios as $usuario) {
            $lines = [];
            $idUsuario = $usuario['id'];

            $lines[0] = [$usuario['id'], $usuario['nombre'], $usuario['usuario'], $usuario['reset'], $usuario['estado'], $usuario['claveDefecto'], $usuario['correspondencia'], "", "", "", "", "", "", "", "", "", "", ""];
            
            // Direcciones postales
            $stmtDir = Database::ExecuteQuery("SELECT direccionPostal, destinatario FROM direccionesPostales WHERE origen = ?", false, ["usuarios;$idUsuario"]);
            $direccionesA = $stmtDir ? $stmtDir->fetchAll(PDO::FETCH_ASSOC) : [];
            
            // Correos
            $stmtCor = Database::ExecuteQuery("SELECT correo, contacto FROM correos WHERE origen = ?", false, ["usuarios;$idUsuario"]);
            $correosA = $stmtCor ? $stmtCor->fetchAll(PDO::FETCH_ASSOC) : [];
            
            // Teléfonos
            $stmtTel = Database::ExecuteQuery("SELECT telefono, contacto, prefijo FROM telefonos WHERE origen = ?", false, ["usuarios;$idUsuario"]);
            $telefonosA = $stmtTel ? $stmtTel->fetchAll(PDO::FETCH_ASSOC) : [];
            
            $numLines = max(count($direccionesA), count($correosA), count($telefonosA));
            for ($index = 0; $index < $numLines; $index++) {
              if ($index != 0) $lines[$index] = ["", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", ""];
              if ($direccionesA && isset($direccionesA[$index])) {
                $lines[$index][$breakpoints[0]] = $direccionesA[$index]['direccionPostal'];
                $lines[$index][$breakpoints[0] + 1] = $direccionesA[$index]['destinatario'];
              }
              if ($correosA && isset($correosA[$index])) {
                $lines[$index][9] = $correosA[$index]['correo'];
                $lines[$index][10] = $correosA[$index]['contacto'];
              }
              if ($telefonosA && isset($telefonosA[$index])) {
                $lines[$index][$breakpoints[2]] = $telefonosA[$index]['prefijo'];
                $lines[$index][$breakpoints[2] + 1] = $telefonosA[$index]['telefono'];
                $lines[$index][$breakpoints[2] + 2] = $telefonosA[$index]['contacto'];
              }
            }
            
            // Fincas
            $stmtFin = Database::ExecuteQuery("SELECT finca, propietarios, coeficiente FROM fincas WHERE idUsuario = ?", false, [$idUsuario]);
            $fincasA = $stmtFin ? $stmtFin->fetchAll(PDO::FETCH_ASSOC) : [];
            
            $indexRow = 0;
            $indexColumn = 14;
            $num2 = 0;
            if ($fincasA) {
              $numFincas = count($fincasA);
              if ($numFincas > $numLines) {
                $num = $num2 = $numFincas - $numLines;
                $lines[] = ["", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", ""];
                while ($num != 0) {
                  $lines[] = ["", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", ""];
                  $num--;
                }
              }
              foreach ($fincasA as $finca) {
                $lines[$indexRow][$indexColumn] = $finca['finca'];
                $lines[$indexRow][$indexColumn + 3] = number_format($finca['coeficiente'], 3, ',', '');
                $propietarios = explode(",", $finca['propietarios']);
                if (!empty($propietarios[0])) {
                  $num3 = $num2;  
                  foreach ($propietarios as $propietario) {
                    $indexPropietario = 1;
                    $propietarioData = explode(":", $propietario);
                    if (!isset($propietarioData[1])) continue;
          
                    $lines[] = ["", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", ""];
            
                    $idPropietario = $propietarioData[0];
                    $cuota = $propietarioData[1];
                    
                    // Consultas de propietario con Database::ExecuteQuery y parámetros
                    $nombrePropietario = Database::ExecuteQuery("SELECT propietario FROM propietarios WHERE id = ?", true, [$idPropietario]);
                    $dni = Database::ExecuteQuery("SELECT dni FROM propietarios WHERE id = ?", true, [$idPropietario]);
                    
                    $lines[$indexRow + 1][$indexColumn + $indexPropietario] = $nombrePropietario;
                    $indexPropietario++;
                    $lines[$indexRow + 1][$indexColumn + $indexPropietario] = $dni;
                    $indexPropietario++;
                    $indexRow++;
                  }
                }
                $indexRow++;
              }
            }
            $cadena .= arrayToTableString($lines);
         }
      }
    }
    $cadena .= "</table>";
    
    header("Content-type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=Aguacada.xls");
    echo utf8_decode($cadena);
  }
}