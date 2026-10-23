<?php
error_reporting(-1);ini_set("display_errors", 1);
session_start();
$base=__DIR__;
$base=dirname($base);
$base=dirname($base);
//include "$base/PHP/LoadCore.php";

if(Session::isLogged()) {
  if (Session::allowed()){
    $columnas=['id','nombre','usuario','reset','estado','claveDefecto','correspondencia'];
    $stringCampos=implode($columnas,',');
    $columnas=  array_merge($columnas,['Direcciones','Destinatarios','Correos','Correos_contacto','Telefonos','Telefonos_contacto','Fincas','Propietarios','Dni','Cuota']);
    incluirExt('xls');
    incluir('comunidades');
    incluir('usuarios');
    incluir('direccionesPostales');
    incluir('correos');
    incluir('telefonos');
    incluir('fincas');

    $contenidoXLS = "";
    $breakpoints=[6,8,10];

    $query="SELECT * FROM comunidades";
    $comunidades=comunidades::getAll($query);
    foreach ($comunidades as $comunidad) {
        echo "paso";
        $idComunidad=$comunidad['id'];
        $contenidoXLS[]=$comunidad;
        $contenidoXLS[]=$columnas;
        $query="SELECT $stringCampos FROM usuarios WHERE idComunidad=$idComunidad";
          //'<style bgcolor="#FFFF00" color="#0000FF">12345.67</style>']
        $usuarios=usuarios::getAll($query);
        if ($usuarios){
            foreach ($usuarios as $usuario) {
              $idUsuario=$usuario['id'];
              $query="SELECT direccionPostal,destinatario FROM direccionesPostales WHERE origen='usuarios;$idUsuario'";
              $direccionesA=direccionesPostales::getAll($query);
              if ($direccionesA){
                foreach ($direccionesA as $direccion) {
                    $direccion = array_values($direccion);
                    $usuario[]=$direccion[0];
                    $usuario[]=$direccion[1];
                }
              }
            $query="SELECT correo,contacto FROM correos WHERE origen='usuarios;$idUsuario'";
            $correosA=correos::getAll($query);
            if ($correosA){
              foreach ($correosA as $correo) {
                $correo = array_values($correo);
                $usuario[]=$correo[0];
                $usuario[]=$correo[1];
              }
            }
            $query="SELECT telefono,contacto FROM telefonos WHERE origen='usuarios;$idUsuario'";
                $telefonosA=telefonos::getAll($query);
                if ($telefonosA){
                  foreach ($telefonosA as $telefono) {
                    $telefono = array_values($telefono);
                    $usuario[]=$telefono[0];
                    $usuario[]=$telefono[1];
                }
              }
            $query="SELECT finca FROM fincas WHERE idUsuario=$idUsuario";
            $fincasA=fincas::getAll($query);
            if ($fincasA){
               foreach ($fincasA as $finca) {
                    $finca = array_values($finca);
                    $usuario[]=$finca[0];
               }
            }
          }
           $contenidoXLS[]=$usuario;
        }
        //$contenidoXLS[]=[];
   }
  print_r($contenidoXLS);
   $xlsx = Shuchkin\SimpleXLSXGen::fromArray( $contenidoXLS );
   //$xlsx->downloadAs('Aguacada.xlsx');
  }
}
