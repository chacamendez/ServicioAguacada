<?php
session_start();

error_log("[DEBUG INSERCION] Petición recibida. GET: " . print_r($_GET, true));

if(Session::isLogged()) {
    $JS = "";
    $destino = $_GET['destino'] ?? '';

    switch ($destino) {
        case "entradasSalidas":
            if (Session::allowedEmpleados()) {
                // Comprobación flexible de confirmación (tanto si llega como booleano o string 'true')
                $confirm = isset($_GET['confirm']) && ($_GET['confirm'] === 'true' || $_GET['confirm'] === '1' || $_GET['confirm'] === true);
                $idEmpleado = $_GET['idEmpleado'] ?? '';
                $tipoEntrada = $_GET['tipoEntrada'] ?? '';
                $latitud = $_GET['latitud'] ?? '';
                $longitud = $_GET['longitud'] ?? '';

                error_log("[DEBUG ENTRADAS/SALIDAS] idEmpleado: $idEmpleado | tipoEntrada: $tipoEntrada | confirm: " . ($confirm ? "SÍ" : "NO"));

                if ($confirm) {
                    error_log("[DEBUG ENTRADAS/SALIDAS] Ejecutando inserción en base de datos...");
                    
                    // Insertar entrada/salida
                    $entradaSalida = classInvoker::nc('entradasSalidas');
                    $entradaSalida->fechaHora = Strings::fechaHoraSQL();
                    $entradaSalida->idEmpleado = $idEmpleado;
                    $entradaSalida->tipoEntrada = $tipoEntrada;
                    $entradaSalida->posicion = "{$latitud},{$longitud}";
                    
                    $resultado = $entradaSalida->insert();
                    error_log("[DEBUG ENTRADAS/SALIDAS] Resultado del insert(): " . var_export($resultado, true));

                    $JS .= "iqwerty.toast.Toast('{$tipoEntrada} introducida con éxito', {settings: {duration: 1000}});executeAction('rellenarPanel', '&destino=panelEntradasSalidas&idEmpleado={$idEmpleado}');";
                } else {
                    error_log("[DEBUG ENTRADAS/SALIDAS] Mostrando diálogo de confirmación (Bootbox)...");
                    
                    $query = "SELECT * FROM entradasSalidas WHERE idEmpleado = " . intval($idEmpleado) . " ORDER BY id DESC LIMIT 1";
                    incluir('entradasSalidas');
                    $entradasSalidas = entradasSalidas::getAll($query);
                    $cadena = "";

                    if ($entradasSalidas) {
                        $entradaSalida = $entradasSalidas[0];
                        $fechaHora = Strings::fechaHoraSQL($entradaSalida['fechaHora'], 'd/m/Y H:i:s');
                        $cadena = "El último registro es {$entradaSalida['tipoEntrada']} {$fechaHora}. ";
                    }

                    $cadena .= "Va a introducir una nueva {$tipoEntrada} ¿Desea continuar?";

                    // Mantenemos action=insertar como me indicas que funciona en tu router
                    $JS = "bootbox.confirm({
                        message: '" . addslashes($cadena) . "',
                        buttons: {
                            confirm: {
                                label: 'Continuar',
                                className: 'btn-success'
                            },
                            cancel: {
                                label: 'Cancelar',
                                className: 'btn-danger'
                            }
                        },
                        callback: function (result) {
                            if (result) {
                                executeAction('insertar', '&destino=entradasSalidas&tipoEntrada={$tipoEntrada}&idEmpleado={$idEmpleado}&latitud={$latitud}&longitud={$longitud}&confirm=true');
                            }
                        }
                    }).find('.modal-content').css('margin-top', '300px');";
                }
            } else {
                error_log("[DEBUG ENTRADAS/SALIDAS ERROR] Session::allowedEmpleados() devolvió FALSE.");
            }
            break;
        default:
            error_log("[DEBUG INSERCION ERROR] Destino desconocido: '$destino'");
            break;
    }

    echo $JS;
} else {
    error_log("[DEBUG INSERCION ERROR] Session::isLogged() devolvió FALSE.");
}
?>