<?php
error_reporting(-1);ini_set("display_errors", 1);
session_start();
$base=__DIR__;
$base=dirname($base);
$base=dirname($base);
include "$base/PHP/LoadCore.php";

if(Session::isLogged()) {
  if (Session::allowed()){
    $idCorrespondencia=$_GET["idCorrespondencia"];
	$correspondencia=classInvoker::nc('correspondencias',intval($idCorrespondencia),true);
    $correspondencia->destinatarios=$correspondencia->jsonDestinatarios();
    incluirExt('fpdf');
    $pdf= new FPDF('L','mm',array(225,115));
    $pdf->SetAutoPageBreak(true,1);
    foreach ($correspondencia->destinatarios as $idUsuario => $idDireccion) {
	  $usuario=classInvoker::nc('usuarios',intval($idUsuario),true);
      $direcciones2=$usuario->getDirecciones();
      $pdf->AddPage();
      $pdf->SetFont('Arial','',10);
      $direccionFinal=$direcciones2[$idDireccion];
      $fileImage=getDir('resources')."aguacadaCarta.png";
      $pdf->Image($fileImage,22,5,40,30);
      $pdf->SetXY(15,45);
      $pdf->Write(4,utf8_decode('C/ Francisco Alonso Martín, 7'));
      $pdf->SetXY(17,35);
      $pdf->Write(4,'38240 Punta del Hidalgo');
      $pdf->SetXY(15,40);
      $pdf->Write(4,'Tel. 922156849 / 629077064');

      $pdf->SetFont('Arial','',12);
      $destinatario=$usuario->nombre;
      $pos=strpos($destinatario,"/ ");
      if ($pos===false){
        $pdf->SetXY(100,75);
        $pdf->Write(4,utf8_decode($destinatario));
      }
      else {
        $pdf->SetXY(100,70);
        $strings=explode("/ ",$destinatario);
        $pdf->Write(4,utf8_decode($strings[0]));
        $pdf->SetXY(100,75);
        $pdf->Write(4,utf8_decode($strings[1]));
      }


      $usuario->findPostalCode($direccionFinal);
      if ($pos=$usuario->findPostalCode($direccionFinal)){
        $direccionPrimera= substr($direccionFinal, 0, $pos);
        $pdf->SetXY(100,80);
        $pdf->Write(4,utf8_decode($direccionPrimera));
        //echo $direccionFinal;
        $direccionSegunda= substr($direccionFinal,$pos);
        $pdf->SetXY(100,85);
        $pdf->Write(4, utf8_decode($direccionSegunda));
      }
      else {
        $pdf->SetXY(140,80);
        $pdf->Write(4,utf8_decode($direccionFinal));
      }

    }
    $output=$pdf->Output('I','correspondencias.pdf',false);
  }
}
