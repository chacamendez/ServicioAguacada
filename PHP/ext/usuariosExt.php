<?php
class usuariosExt extends usuarios {

 public function toMailTo(){
    incluir('correos');
    $query="SELECT correo FROM correos WHERE origen='usuarios;{$this->id}';";
    $correos=correos::getAll($query);
    $mailTo="";
    $first=true;
    if ($correos){
      foreach ($correos as $correo) {
        if ($first) {$mailTo="?bcc={$correo['correo']},";$first=false;}
        else $mailTo.="{$correo['correo']},";

      }
    }

    return $mailTo;
 }
 public function arrayCorreos(){
   incluir('correos');
   $query="SELECT correo FROM correos WHERE origen='usuarios;{$this->id}';";
   $arrayCorreos=correos::getAll($query);
   return $arrayCorreos;
 }

 public function existe(){
    $query="SELECT COUNT(*) FROM usuarios WHERE usuario='{$this->usuario}' AND estado <> 'eliminado' AND id<>{$this->id}";
    $num=usuarios::getValue($query);
    if ($num !="0") return true;
    else return false;
  }

  public function setClave(){
    $this->clave=password_hash(trim($this->clave), PASSWORD_DEFAULT);
  }
  public function resetClave(){
    $this->clave=password_hash(trim($this->claveDefecto), PASSWORD_DEFAULT);

  }
  public function setTelefonoDefecto($prefijo,$telefono){
    $query="UPDATE telefonos SET prefijo='$prefijo',telefono='$telefono' WHERE origen='usuarios;{$this->id}' AND defecto=1;";
    Database::ExecuteQuery($query);
  }
  public function setCorreoDefecto($correo){
    $query="UPDATE correos SET correo='$correo' WHERE origen='usuarios;{$this->id}' AND defecto=1;";
    Database::ExecuteQuery($query);
  }
  public function getDireccionPostal(){
    incluir('comunidades');
    $query="SELECT direccionPostal FROM comunidades WHERE id={$this->idComunidad}";
    $direccion=comunidades::getValue($query);
    $query="SELECT direccionPostal2 FROM comunidades WHERE id={$this->idComunidad}";
    $direccion2=comunidades::getValue($query);
    $direccion.=" {$this->piso} $direccion2";
    return $direccion;
  }
  public function getDirecciones(){
    incluir('comunidades');
    $numChar=100;
    $direccion=$this->getDireccionPostal();
    $direcciones[0]=substr($direccion,0,$numChar);
    incluir('direccionesPostales');
    $query="SELECT id,direccionPostal FROM direccionesPostales WHERE origen='usuarios;{$this->id}'";
    $direccionesPostales=direccionesPostales::getAll($query);

    if ($direccionesPostales){
      foreach ($direccionesPostales as $id=>$direccionPostal) {
        $direcciones[$direccionPostal['id']]=substr($direccionPostal['direccionPostal'],0,$numChar);
      }
    }
    return $direcciones;
  }

  public function findPostalCode($direccion){
    $direccionArray=explode(" ",$direccion);
    $postalCode=0;
    foreach ($direccionArray as $word) {

      if (strlen($word)<>5) continue;
      if (is_numeric($word))  {$postalCode=$word;}
      else continue;
    }
    if ($postalCode<>0){
      return strpos($direccion,$postalCode);
    }
    else return false;
  }



  public function correoDefecto(){
    $query="SELECT correo FROM correos WHERE origen='usuarios;{$this->id}' AND defecto=1";
    $correoDefecto=Database::ExecuteQuery($query,true);
    return $correoDefecto;
  }
  public function fincaDefecto(){
    $query="SELECT id FROM fincas WHERE idUsuario={$this->id} AND defecto=1";
    $fincaDefecto=Database::ExecuteQuery($query,true);
    return $fincaDefecto;
  }
  public function telefonoDefecto($option=false){
    $query="SELECT prefijo,telefono FROM telefonos WHERE origen='usuarios;{$this->id}' AND defecto=1";
    $resultado=Database::ExecuteQuery($query);
    if ($resultado){
      $row=$resultado->fetch_row();
      if ($option){
        switch ($option) {
          case '1':
            $telefonoDefecto=$row[0];
            break;
          case '2':
            $telefonoDefecto=$row[1];
          break;

          default:
            // code...
            break;
        }
      }
      else{

        $telefonoDefecto=$row[0].$row[1];
      }

      return $telefonoDefecto;
    }

  }
  public function getComunidad(){
    $query="SELECT comunidad FROM comunidades WHERE id={$this->idComunidad}";
    $comunidad=Database::ExecuteQuery($query,true);
    return $comunidad;

  }
  public function eliminar(){
    $query="UPDATE usuarios SET estado='eliminado' WHERE id={$this->id}";
    Database::ExecuteQuery($query);

  }
  public function exportarUsuario(){
    $string="<label title=\"Exportar usuario\" id=labelExportarUsuario class=\"fa fa-arrow-circle-o-up buttons3\" style=\"background-color:#0c84a9;position:relative;top:0px\" onclick=\"executeAction(\'insertarPanel\',\'&destino=usuariosExportar&idUsuario={$this->id}\')\"></label>";
    return $string;
  }
  public function exportarUsuario2(){
    $string1="Name,Given Name,Additional Name,Family Name,Yomi Name,Given Name Yomi,Additional Name Yomi,Family Name Yomi,Name Prefix,Name Suffix,Initials,Nickname,Short Name,Maiden Name,Birthday,Gender,Location,Billing Information,Directory Server,Mileage,Occupation,Hobby,Sensitivity,Priority,Subject,Notes,Language,Photo,Group Membership,";
    $nombre=utf8_decode($this->nombre);
    $string2="$nombre,,,,,,,,,,,,,,,,,,,,,,,,,,,,* myContacts,";
    $query="SELECT correo,contacto FROM correos WHERE origen='usuarios;{$this->id}'";
    $correos=Database::ExecuteQuery($query);
    $num=1;
    $first=true;
    while ($row=$correos->fetch_row()){
      if ($first) {
        $string1.="E-mail $num - Type,E-mail $num - Value,";
        $string2.="* Home,{$row[0]},";
        $first=false;
      }
      else {
        $contacto=utf8_decode($row[1]);
        $string1.="E-mail $num - Type,E-mail $num - Value,";
        $string2.="$contacto,{$row[0]},";
      }
      $num+=1;
    }
    $query="SELECT prefijo,telefono,contacto FROM telefonos WHERE origen='usuarios;{$this->id}'";
    $num=1;
    $telefonos=Database::ExecuteQuery($query);
    while ($row=$telefonos->fetch_row()){
      $contacto=utf8_decode($row[2]);
      $string1.="Phone $num - Type,Phone $num - Value,";
      $string2.="$contacto,{$row[0]}{$row[1]},";
      $num+=1;
    }
    $string1=substr($string1, 0, -1);
    $string2=substr($string2, 0, -1);
    return "$string1 \n $string2 ";
  }

  public function toHtml(){
    $html='';
    incluir('telefonos');
    $query="SELECT * FROM telefonos WHERE defecto=0 AND origen='usuarios;{$this->id}'";
    $telefonos=telefonos::getAll($query);
    if ($telefonos){
      foreach ($telefonos as $telefono){
        $html.="<div class=\"divTelefonoUsuarios\"><div id=\"divElementoTelefono\">(+{$telefono['prefijo']}){$telefono['telefono']}</div>".
        "<div id=\"divElementoContacto\">{$telefono['contacto']}</div>".
        "<div id=\"divElementoTipo\">{$telefono['tipo']}</div>".
        "<div class=\"iconos2 \"><img src=\"./RESOURCES/phone.png\" onclick=\"window.location.href=\'tel:+{$telefono['prefijo']}{$telefono['telefono']}\'\">".
        "<label class=\"fa fa-trash buttons3\" style=\"background-color:#961e18\" onclick=\"executeAction(\'eliminar\',\'&destino=telefonos&idTelefono={$telefono['id']}\')\"></label></div></div>";
      }
    }
    return $html;
  }
  public function toHtml2(){
    $html='';
    incluir('correos');
    $query="SELECT * FROM correos WHERE defecto=0 AND origen='usuarios;{$this->id}'";
    $correos=correos::getAll($query);
    if ($correos){
      foreach ($correos as $correo){
        $html.="<div class=\"divCorreoUsuarios\"><div id=\"divElementoCorreo\">{$correo['correo']}</div>".
        "<div id=\"divElementoContacto\">{$correo['contacto']}</div>".
        "<div id=\"divElementoTipo\">{$correo['tipo']}</div>".
        "<div class=\"iconos2 \"><img src=\"./RESOURCES/mail.png\" onclick=\"window.open(\'mailto:{$correo['correo']}\');\">".
        "<label class=\"fa fa-trash buttons3\" style=\"background-color:#961e18\" onclick=\"executeAction(\'eliminar\',\'&destino=correos&idCorreo={$correo['id']}\')\"></label></div></div>";
      }
    }
    return $html;
  }
  public function toHtml3(){
    $html='';
    incluir('direccionesPostales');
    $query="SELECT * FROM direccionesPostales WHERE origen='usuarios;{$this->id}'";
    $direcciones=direccionesPostales::getAll($query);
    if ($direcciones){
      foreach ($direcciones as $direccion){
        $html.="<div class=\"divDireccionUsuarios\"><div id=\"divElementoDestinatario\">{$direccion['destinatario']}</div>".
        "<div class=\"iconos3 \"><label class=\"fa fa-pencil-square-o buttons3\" style=\"background-color:#e3600a\" onclick=\"executeAction(\'prepararPanel\',\'&destino=panelAuxiliarEditarDirecciones&idUsuario={$this->id}&idDireccion={$direccion['id']}\')\"></label>".
        "<label class=\"fa fa-trash buttons3\" style=\"background-color:#961e18\" onclick=\"executeAction(\'eliminar\',\'&destino=direccionesPostales&idDireccion={$direccion['id']}\')\"></label></div>".
        "<div id=\"divElementoDireccion\">{$direccion['direccionPostal']}</div></div>";
      }
    }
    return $html;
  }
  public function toHtml4(){
    $html='';
    incluir('fincas');
    $query="SELECT * FROM fincas  WHERE idUsuario={$this->id}";
    $fincas=fincas::getAll($query);
    if ($fincas){
      incluir('propietariosFincas');
      foreach ($fincas as $finca){
        $query="SELECT idPropietario,idFinca FROM propietariosFincas WHERE idFinca={$finca['id']}";
        $propietariosFincas=propietariosFincas::getAll($query);
        $stringPropietarios="";
        if ($propietariosFincas){
          $stringPropietarios="<table style=\"width:100%;font-size:18px\" >";
          foreach ($propietariosFincas as $propietarios) {
            
            $propietario=classInvoker::nc('propietarios',intVal($propietarios['idPropietario']));
            $stringPropietarios.="<tr><td>{$propietario->propietario}</td><td>{$propietario->dni}</td></tr>";
          }
          $stringPropietarios.="</table>";
          
        }
        if ($finca['defecto']==1){
          $html.="<div class=\"divFincaUsuarios\"><div id=\"divElementoFinca\">{$finca['finca']}        ({$finca['coeficiente']}) $stringPropietarios</div>".
          "<div class=\"iconos3 \"><label class=\"fa fa-trash buttons3\" style=\"background-color:#961e18\" onclick=\"executeAction(\'eliminar\',\'&destino=fincas&idFinca={$finca['id']}\')\"></label>".
          "<label class=\"fa fa-pencil-square-o buttons3\" style=\"background-color:#e3600a\" onclick=\"executeAction(\'prepararPanel\',\'&destino=panelAuxiliarEditarFincas&idUsuario={$this->id}&idFinca={$finca['id']}\')\"></label>".
          "<label class=\"fa fa fa-check-circle buttons3\" id={$this->id}idFinca{$finca['id']} style=\"background-color:#269905\" onclick=\"executeAction(\'editar\',\'&destino=fincasDefecto&idFinca={$finca['id']}&idUsuario={$this->id}\')\"></label></div></div>";

          //$html.="<div class=\"divFincaUsuarios\"><div id=\"divElementoFinca\">{$finca['finca']}</div>".
          //"<div class=\"iconos3 \"><label class=\"fa fa-trash buttons3\" style=\"background-color:#961e18\" onclick=\"executeAction(\'eliminar\',\'&destino=fincas&idFinca={$finca['id']}\')\"></label>".
          //"<label class=\"fa fa-check-circle buttons3\" id={$this->id}idFinca{$finca['id']} style=\"background-color:#269905\" onclick=\"executeAction(\'editar\',\'&destino=fincas&idFinca={$finca['id']}&idUsuario={$this->id}\')\"></label></div></div>";
        }
        else {
          $html.="<div class=\"divFincaUsuarios\"><div id=\"divElementoFinca\">{$finca['finca']}        ({$finca['coeficiente']}) $stringPropietarios</div>".
          "<div class=\"iconos3 \"><label class=\"fa fa-trash buttons3\" style=\"background-color:#961e18\" onclick=\"executeAction(\'eliminar\',\'&destino=fincas&idFinca={$finca['id']}\')\"></label>".
          "<label class=\"fa fa-pencil-square-o buttons3\" style=\"background-color:#e3600a\" onclick=\"executeAction(\'prepararPanel\',\'&destino=panelAuxiliarEditarFincas&idUsuario={$this->id}&idFinca={$finca['id']}\')\"></label>".
          "<label class=\"fa fa-times-circle buttons3\" id={$this->id}idFinca{$finca['id']} style=\"background-color:#961e18\" onclick=\"executeAction(\'editar\',\'&destino=fincasDefecto&idFinca={$finca['id']}&idUsuario={$this->id}\')\"></label></div></div>";
        }


      }
    }
    return $html;
  }
}
?>
