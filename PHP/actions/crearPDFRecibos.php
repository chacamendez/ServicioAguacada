<?php
error_reporting(E_ALL & ~E_DEPRECATED);
session_start();
$base = __DIR__;
$base = dirname($base);
$base = dirname($base);
include "$base/PHP/LoadCore.php";

if (Session::isLogged()) {
  if (Session::allowed()) {
    $idRecibo = $_GET["idRecibo"];
    $recibo = classInvoker::nc('recibos', intval($idRecibo));
    
    // Consultas migradas a la clase Database con PDO y parámetros seguros
    $comunidad = Database::ExecuteQuery(
        "SELECT comunidades.comunidad FROM usuarios JOIN comunidades ON usuarios.idComunidad = comunidades.id WHERE usuarios.id = ?", 
        true, 
        [$recibo->idUsuario]
    );
    
    $nombre = Database::ExecuteQuery(
        "SELECT nombre FROM recibos WHERE id = ?", 
        true, 
        [$recibo->id]
    );
    
    $finca = Database::ExecuteQuery(
        "SELECT finca FROM fincas WHERE id = ?", 
        true, 
        [$recibo->idFinca]
    );

    $fecha = Strings::fecha($recibo->fecha);
    incluirExt('justification');
    $pdf = new PDF('P', 'mm', 'A4');
    $pdf->AddPage();
    $pdf->SetFont('Arial', '', 10);
    $fileImage = getDir('resources') . "aguacadaRecibos.png";
    $pdf->Image($fileImage, 13, 7, 35, 35);
    $pdf->SetXY(6, 42);
    $pdf->Write(4, 'C/ Francisco Alonso Martín, 7');
    $pdf->SetXY(9, 46);
    $pdf->Write(4, '38240 Punta del Hidalgo');
    $pdf->SetXY(6, 50);
    $pdf->Write(4, 'Tel. 922156849 / 629077064');
    $pdf->SetXY(6, 54);
    $pdf->Write(4, 'info@aguacadagesfincas.es');
    $pdf->SetXY(6, 58);
    $pdf->Write(4, 'www.aguacadagesfincas.es');
    
    // logo dibujado
    $pdf->SetXY(10, 70);
    $pdf->SetFont('Arial', 'b', 18);
    $pdf->Write(4, "Recibo nº: $idRecibo");
    $pdf->SetXY(10, 76);
    $pdf->SetFont('Arial', '', 15);
    $texto = "He recibido de $nombre la cantidad de {$recibo->importe} € en concepto de {$recibo->concepto}. \n  Fecha: $fecha  ";

    $pdf->SetLeftMargin(10);
    $pdf->Justify($texto, 180, 8);

    $pdf->SetXY(10, 120);
    $pdf->Write(4, "$comunidad $finca ");

    $output = $pdf->Output('I', "recibo$idRecibo.pdf", false);
  }
}