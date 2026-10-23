<?php
class correspondenciasExt extends correspondencias {
  function destinatarios($option){
    $destinatarios=json_decode($this->destinatarios);
    $numDestinatarios=0;
    $primera=true;
    $distinta=false;
    foreach ($destinatarios as $idUsuario => $destinatario) {
      $numDestinatarios+=1;
      $usuario=classInvoker::nc('usuarios',intval($idUsuario));
      if ($primera) {$idComunidad=$usuario->idComunidad;$primera=false;}
      else if ($idComunidad!=$usuario->idComunidad) $distinta=true;

    }
    switch ($option) {
      case 'numDestinatarios':
        return $numDestinatarios;
      break;
      case 'comunidadDistinta':
      return $distinta;
      break;
    }
  }
  function jsonDestinatarios(){
    return json_decode($this->destinatarios);
  }
  function decodeText(){
    $this->texto=base64_decode($this->texto);
  }
  /////comprueba si una dirección esta incluida en alguna correspondenciasExt
  static function checkDireccion ($idDireccion){
    incluir('correspondencias');
    $check=false;
    $correspondencias=classInvoker::nc('correspondencias');
    $query="SELECT destinatarios FROM correspondencias";
    $correspondencias=$correspondencias->getAll($query);
    foreach ($correspondencias as $key=>$destinatarios) {
        $destinatarios=json_decode($destinatarios['destinatarios']);
            foreach ($destinatarios as $idUsuario => $destinatario) {
            if ($destinatario==$idDireccion) $check=true;
        }
      }
    return $check;
  }
}
