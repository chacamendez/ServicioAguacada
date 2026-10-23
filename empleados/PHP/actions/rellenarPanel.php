<?php
session_start();
if(Session::isLogged()) {
    $JS="";
    $destino=$_GET['destino'];
    switch ($destino){
    case "panelEntradasSalidas":
        $idEmpleado=$_GET['idEmpleado'];
        if(Session::allowedEmpleados()) {
            $JS.="$('#panelEntradasSalidas').html('');$('#idEmpleado').val('$idEmpleado');";
            incluir('entradasSalidas');
            $query="SELECT tipoEntrada,fechaHora FROM entradasSalidas WHERE idEmpleado=$idEmpleado ORDER BY id DESC LIMIT 50";
            $entradasSalidas=entradasSalidas::getAll($query);
            if ($entradasSalidas) {
                foreach ($entradasSalidas as $entradaSalida) {
                    $fechaHora=Strings::fechaHoraSQL($entradaSalida['fechaHora'],'d/m/Y H:i:s');
                    $JS.="$('#panelEntradasSalidas').append('<label>{$entradaSalida['tipoEntrada']} $fechaHora</label><br>');";
              
                }
            }

        }
    break;
} 
    echo $JS;
}
?>