<?php
session_start();

error_log("[DEBUG PANEL ENTRADAS] Entrando. isLogged: " . (Session::isLogged() ? "SÍ" : "NO") . " | GET: " . print_r($_GET, true) . " | SESSION: " . print_r($_SESSION, true));

if(Session::isLogged()) {
    $JS="";
    $destino=$_GET['destino'] ?? '';
    
    switch ($destino){
        case "panelEntradasSalidas":
            $idEmpleado=$_GET['idEmpleado'] ?? '';
            
            // Comprobamos también si allowedEmpleados() existe y da true
            $allowed = method_exists('Session', 'allowedEmpleados') ? (Session::allowedEmpleados() ? "SÍ" : "NO") : "MÉTODO NO EXISTE";
            error_log("[DEBUG PANEL ENTRADAS] allowedEmpleados: " . $allowed);

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
            } else {
                error_log("[DEBUG PANEL ENTRADAS ERROR] Session::allowedEmpleados() devolvió FALSE. Petición bloqueada.");
            }
        break;
    } 
    echo $JS;
} else {
    error_log("[DEBUG PANEL ENTRADAS ERROR] Session::isLogged() devolvió FALSE. El usuario no está logueado en la sesión.");
}
?>