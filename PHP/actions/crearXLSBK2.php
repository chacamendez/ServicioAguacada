<?php
error_reporting(-1);ini_set("display_errors", 1);
session_start();
$base=__DIR__;
$base=dirname($base);
$base=dirname($base);
//include "$base/PHP/LoadCore.php";

if(Session::isLogged()) {
  if (Session::allowed()){
    //header ( "Content-type: application/vnd.ms-excel" );
    //header ( "Content-Disposition: attachment; filename=Aguacada.xls" );
    incluir('comunidades');
    incluir('usuarios');
    incluir('direccionesPostales');
    incluir('correos');
    incluir('telefonos');
    incluir('fincas');
    $columnas=['id','nombre','usuario','reset','estado','claveDefecto','correspondencia'];
    $stringCampos=implode($columnas,',');
    $columnas=array_merge($columnas,['Direcciones','Destinatarios','Correos','Correos_contacto','Telefonos','Telefonos_contacto','Fincas','Propietarios','Dni','Cuota']);
    $breakpoints=[6,8,10];

    $cadena="<table>";
    $query="SELECT * FROM comunidades";
    $comunidades=comunidades::getAll($query);
    foreach ($comunidades as $comunidad) {
      $idComunidad=$comunidad['id'];
      $cadena.= "<tr><td>$idComunidad</td><td>{$comunidad['comunidad']}</td><td>{$comunidad['direccionPostal']}</td><td>{$comunidad['direccionPostal2']}</td></tr>";
      $query="SELECT $stringCampos FROM usuarios WHERE idComunidad=$idComunidad";
      $usuarios=usuarios::getAll($query);
      if ($usuarios){
        $cadena.= "<tr><td>id</td><td>nombre</td><td>usuario</td><td>reset</td><td>estado</td>".
        "<td>ClaveDefecto</td><td>correspondencia</td><td>Direcciones</td><td>Destinatarios</td>".
        "<td>Correos</td><td>Correos_contacto</td><td>Telefonos</td><td>Telefonos_contacto</td>".
        "<td>Fincas</td><td>Propietarios</td><td>DNI</td><td>Cuota</td></tr>";
        foreach ($usuarios as $usuario) {
            $lines=[];
            $idUsuario=$usuario['id'];
            $cadena.="<tr><td>{$usuario['id']}</td><td>{$usuario['nombre']}</td><td>{$usuario['usuario']}</td>".
            "<td>{$usuario['reset']}</td><td>{$usuario['estado']}</td><td>{$usuario['claveDefecto']}</td>".
            "<td>{$usuario['correspondencia']}</td>";
            $query="SELECT direccionPostal,destinatario FROM direccionesPostales WHERE origen='usuarios;$idUsuario'";
            $direccionesA=direccionesPostales::getAll($query);
            if ($direccionesA){
              foreach ($direccionesA as $direccion) {
                  if ($direccionesA){
                    $direccionesA=false;
                    $cadena.="<td>{$direccion['direccionPostal']}</td><td>{$direccion['destinatario']}</td>";
                  }
                  else {
                    $lines[]="<tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td>{$direccion['direccionPostal']}</td><td>{$direccion['destinatario']}</td>";
                  }
              }
            }
            $numLines=count($lines)
            $query="SELECT correo,contacto FROM correos WHERE origen='usuarios;$idUsuario'";
            $correosA=correos::getAll($query);
            $index=0;
            if ($correosA){
            foreach ($correosA as $correo) {
              if ($correosA){
                $correosA=false;
                $cadena.="<td>{$correo['correo']}</td><td>{$correo['contacto']}</td>";
              }
              else {
                if ($numLines==0){
                  $lines[]="<tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>"."
                  <td>{$correo['correo']}</td><td>{$correo['contacto']}</td>";
                }
                else{
                  if ($numLines>$index){
                    $lines[$index].="<td>{$correo['correo']}</td><td>{$correo['contacto']}</td>;";
                  }
                  else {
                    $lines[]="<tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>"."
                    <td>{$correo['correo']}</td><td>{$correo['contacto']}</td>";
                  }

                }
              }
              $index++;
            }
          }
          $numLines=count($lines)
          $query="SELECT telefono,contacto FROM telefonos WHERE origen='usuarios;$idUsuario'";
          $index=0;
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
    }
    header ( "Content-type: application/vnd.ms-excel" );
    header ( "Content-Disposition: attachment; filename=Aguacada.xls" );
    echo utf8_decode($cadena);

  }
}
