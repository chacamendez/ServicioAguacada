<?php
session_start();
if(Session::isLogged()) {
    $JS="";
    $destino=$_GET['destino'];
    switch ($destino){
    case "entradasSalidas":
            if (Session::allowedEmpleados()){
                if (isset($_GET['confirm'])) $confirm=$_GET['confirm'];
                else $confirm=false;
                $idEmpleado=$_GET['idEmpleado'];
                if ($confirm){
                    //insertar entrada salida
                    $tipoEntrada=$_GET['tipoEntrada'];
                    $entradaSalida=classInvoker::nc('entradasSalidas');
                    $entradaSalida->fechaHora= Strings::fechaHoraSQL();
                    $entradaSalida->idEmpleado=$idEmpleado;
                    $entradaSalida->tipoEntrada=$tipoEntrada;
                    $entradaSalida->posicion="{$_GET['latitud']},{$_GET['longitud']}";
                    $entradaSalida->insert();
                    $JS.="iqwerty.toast.Toast('{$tipoEntrada} introducida con éxito',{settings:{duration:1000}});executeAction('rellenarPanel','&destino=panelEntradasSalidas&idEmpleado=$idEmpleado');";
                }
                else {
                    $tipoEntrada=$_GET['tipoEntrada'];
                    $idEmpleado=$_GET['idEmpleado'];
                    $latidud=$_GET['latitud'];
                    $longitud=$_GET['longitud'];
                    $query="SELECT * FROM entradasSalidas WHERE idEmpleado=$idEmpleado ORDER BY id DESC LIMIT 1";
                    incluir('entradasSalidas');
                    $entradasSalidas=entradasSalidas::getAll($query);
                    $cadena="";
                    if ($entradasSalidas){
                        $entradaSalida=$entradasSalidas[0];
                        $fechaHora=Strings::fechaHoraSQL($entradaSalida['fechaHora'],'d/m/Y H:i:s');
                        $cadena="El último registro es {$entradaSalida['tipoEntrada']} {$fechaHora}. ";

                    }
                    $cadena.="Va a introducir una nueva {$tipoEntrada} ¿Desea continuar?";


                    $JS="bootbox.confirm({
                        message: '$cadena',
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
                        if (result){
                            executeAction('insertar','&destino=entradasSalidas&tipoEntrada=$tipoEntrada&idEmpleado=$idEmpleado&latitud=$latidud&longitud=$longitud&confirm=true');
                        }
                        }
                    }).find('.modal-content').css('margin-top','300px');";
                }
            }
    break;
    }
    echo $JS;
}
?>