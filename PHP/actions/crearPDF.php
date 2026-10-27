<?php
// Ocultar deprecations y notices en producción para evitar romper FPDF
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
ini_set("display_errors", 0);

session_start();
$base = __DIR__;
$base = dirname($base);
$base = dirname($base);
include "$base/PHP/LoadCore.php";

if (Session::isLogged()) {
  if (Session::allowed()) {
    $idCorrespondencia = $_GET["idCorrespondencia"] ?? 0;
    $correspondencia = classInvoker::nc('correspondencias', intval($idCorrespondencia), true);
    
    if ($correspondencia && method_exists($correspondencia, 'jsonDestinatarios')) {
      $correspondencia->destinatarios = $correspondencia->jsonDestinatarios();
      incluirExt('fpdf');
      
      $pdf = new FPDF('L', 'mm', array(225, 115));
      $pdf->SetAutoPageBreak(true, 1);
      
      if ($correspondencia->destinatarios && is_iterable($correspondencia->destinatarios)) {
        foreach ($correspondencia->destinatarios as $idUsuario => $idDireccion) {
          $usuario = classInvoker::nc('usuarios', intval($idUsuario), true);
          if (!$usuario) continue;
          
          $direcciones2 = $usuario->getDirecciones();
          
          // Verificamos si la clave de dirección existe; si no, usamos la dirección por defecto [0] o vacío
          $direccionFinal = isset($direcciones2[$idDireccion]) ? $direcciones2[$idDireccion] : ($direcciones2[0] ?? '');
          
          $pdf->AddPage();
          $pdf->SetFont('Arial', '', 10);
          
          $fileImage = getDir('resources') . "aguacadaCarta.png";
          if (file_exists($fileImage)) {
            $pdf->Image($fileImage, 22, 5, 40, 30);
          }
          
          $pdf->SetXY(15, 45);
          $pdf->Write(4, mb_convert_encoding('C/ Francisco Alonso Martín, 7', 'ISO-8859-1', 'UTF-8'));
          $pdf->SetXY(17, 35);
          $pdf->Write(4, '38240 Punta del Hidalgo');
          $pdf->SetXY(15, 40);
          $pdf->Write(4, 'Tel. 922156849 / 629077064');

          $pdf->SetFont('Arial', '', 12);
          $destinatario = $usuario->nombre ?? '';
          $pos = strpos($destinatario, "/ ");
          
          if ($pos === false) {
            $pdf->SetXY(100, 75);
            $pdf->Write(4, mb_convert_encoding($destinatario, 'ISO-8859-1', 'UTF-8'));
          } else {
            $pdf->SetXY(100, 70);
            $strings = explode("/ ", $destinatario);
            $pdf->Write(4, mb_convert_encoding($strings[0] ?? '', 'ISO-8859-1', 'UTF-8'));
            $pdf->SetXY(100, 75);
            $pdf->Write(4, mb_convert_encoding($strings[1] ?? '', 'ISO-8859-1', 'UTF-8'));
          }

          if (!empty($direccionFinal)) {
            $posPostal = $usuario->findPostalCode($direccionFinal);
            if ($posPostal !== false) {
              $direccionPrimera = substr($direccionFinal, 0, $posPostal);
              $pdf->SetXY(100, 80);
              $pdf->Write(4, mb_convert_encoding($direccionPrimera, 'ISO-8859-1', 'UTF-8'));
              
              $direccionSegunda = substr($direccionFinal, $posPostal);
              $pdf->SetXY(100, 85);
              $pdf->Write(4, mb_convert_encoding($direccionSegunda, 'ISO-8859-1', 'UTF-8'));
            } else {
              $pdf->SetXY(140, 80);
              $pdf->Write(4, mb_convert_encoding($direccionFinal, 'ISO-8859-1', 'UTF-8'));
            }
          }
        }
      }
      
      $pdf->Output('I', 'correspondencias.pdf', false);
    }
  }
}