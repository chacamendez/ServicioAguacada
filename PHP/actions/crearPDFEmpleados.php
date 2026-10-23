<?php
error_reporting(-1);ini_set("display_errors", 1);
session_start();
$base=__DIR__;
$base=dirname($base);
$base=dirname($base);
include "$base/PHP/LoadCore.php";
$row=4.8;
if(Session::isLogged()) {
  if (Session::allowed()){
    $fechaInicio=$_GET['fechaInicio'];
    $fechaFin=$_GET['fechaFin'];
    
    $arrayFecha=preg_split("/-/",$fechaInicio);
    $dia=$arrayFecha[0];
    $dia-=0;
    $arrayFecha=preg_split("/-/",$fechaFin);
    $diaFinal=$arrayFecha[0];
    $dateInicio= $fechaInicio;

    $idEmpleado=$_GET["idEmpleado"];
    $arrayEmpleados=preg_split("/-/",$idEmpleado);
    incluirExt('justification');
    $pdf= new PDF('L','mm','A4');
    $pdf->AddPage();
    incluir('entradasSalidas');
    
    foreach ($arrayEmpleados as $idEmpleado){
      
      $empleado=classInvoker::nc('empleados',intval($idEmpleado));
      $TOTAL=0;
      $numErrores=0;
      $mayor=0;
       
      // Set font for the title
      $pdf->SetFont('Arial', 'B', 16);

      // Set the gray background color for the cell
      $pdf->SetFillColor(169, 169, 169); // RGB value for gray (169, 169, 169)

      // Get the page width to make the cell span the entire page width
      $pageWidth = $pdf->GetPageWidth()-20;
    
      // Add a gray cell that spans the entire width of the page (no border, height 10, centered text)
      $pdf->Cell($pageWidth, 10, $empleado->nombre, 1, 1, 'C', true);
      $pdf->SetFillColor(255, 255, 255); // RGB value for gray (169, 169, 169)
      $pdf->SetFont('Arial','',10);
      $anchoCelda=$pageWidth/($diaFinal-$dia+1);
      for ($i=$dia;$i<=$diaFinal;$i++){
        $pdf->Cell($anchoCelda, 10, $i, 1, 0, 'C');
      }
      $pdf->ln(10);
      $pdf->SetFont('Arial','',6);
      $SQLDateInicio=date("Y-m-d", strtotime($dateInicio));
      $SQLDateFin = date("Y-m-d", strtotime($fechaFin . " +1 day"));
      
      $query="SELECT COUNT(*) as row_count FROM entradasSalidas WHERE fechaHora BETWEEN '$SQLDateInicio' AND '$SQLDateFin' AND idEmpleado=$idEmpleado GROUP BY DATE(fechaHora) ORDER BY row_count DESC LIMIT 1";
      //echo $query;
      $altoRow=entradasSalidas::getValue($query);
      $altoRow*=$row;
	  $altoRow+=$row;
      for ($i=$dia;$i<=$diaFinal;$i++){
        $SQLDate=Strings::fechaHoraSQL($dateInicio);
        $query="SELECT * FROM entradasSalidas WHERE idEmpleado=$idEmpleado AND DATE(fechaHora)='$SQLDate'";
        $entradas=entradasSalidas::getAll($query);
        //print_r($entradas);
        if ($entradas){
          $numRows = count($entradas);
          $pdf->Cell($anchoCelda,$altoRow,"",1,0,'C');
          $x=$posicionX=$pdf->getX();
          $y=$posicionY=$pdf->getY();
          $lastEntrada = null;
          $totalMinutes=0;
          foreach ($entradas as $entrada){
            $cadena="";
            if ($entrada['tipoEntrada']=='Entrada') {$cadena.="E";$lastEntrada = strtotime($entrada['fechaHora']);}
            else {
              $cadena.="S";
              if ($lastEntrada !== null) {
                
                // Calculate the difference only if we have a corresponding Entrada (E)
                $salida = strtotime($entrada['fechaHora']);
                $differenceInSeconds = $salida - $lastEntrada;  // Time difference in seconds
    
                // Convert the difference to minutes
                $totalMinutes += $differenceInSeconds / 60;
                
                // RESET: Quitamos la acumulación errónea de aquí de $TOTAL
                
                // Reset the lastEntrada for the next cycle
                $lastEntrada = null;
              }
              else $numErrores+=1;
            }
            $time = date("H:i", strtotime($entrada['fechaHora']));
            $cadena.=$time;
            $pdf->SetXY($posicionX-$anchoCelda,$posicionY+1);
            $googleMapsUrl = "https://www.google.com/maps?q={$entrada['posicion']}";
            $pdf->Write(3,$cadena,$googleMapsUrl);
            $posicionY+=4;

          }
          
          // CAMBIO CLAVE: Sumamos el total neto del día una sola vez al terminar el foreach diario
          $TOTAL += $totalMinutes;

          $totalHours = floor($totalMinutes / 60);  // Get whole hours
          $totalMinutesRemaining = $totalMinutes % 60;  // Get remaining minutes
          $pdf->SetXY($posicionX-$anchoCelda,$posicionY+1);
          $pdf->Write(3,"H{$totalHours}M{$totalMinutesRemaining}");
          $posicionY+=3;
          

        }
        else{
          $pdf->Cell($anchoCelda,$altoRow,"",1,0,'C');
          $x=$posicionX=$pdf->getX();
          $y=$posicionY=$pdf->getY();
        }
        $pdf->SetXY($x,$y);
        $dateInicio = date("Y-m-d", strtotime($dateInicio . " +1 day"));


      }
      // Set font for the title
      $pdf->SetFont('Arial', 'B', 16);

      // Set the gray background color for the cell
      $pdf->SetFillColor(169, 169, 169); // RGB value for gray (169, 169, 169)

      // Get the page width to make the cell span the entire page width
      $pageWidth = $pdf->GetPageWidth()-20;
    
      // Add a gray cell that spans the entire width of the page (no border, height 10, centered text)
      $totalHours = floor($TOTAL / 60);  // Get whole hours
      $totalMinutesRemaining = $TOTAL % 60;  // Get remaining minutes
      $cadena="Total horas mensuales $totalHours y $totalMinutesRemaining minutos. $numErrores errores de fichaje detectados";
      $pdf->SetXY(10,$y+1);
      
      $pdf->ln(10);
      $dateInicio= $fechaInicio;
      $pdf->SetXY(10,$y+$altoRow);
      $pdf->Cell($pageWidth, 10, $cadena, 1, 1, 'C', true);
      $pdf->SetXY(10,$pdf->getY()+1);
    }
     
    $output=$pdf->Output('I',"PlantillaAsistencia.pdf",false);
  }
}