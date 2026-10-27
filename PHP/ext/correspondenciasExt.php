<?php 
class correspondenciasExt extends correspondencias {
  function destinatarios($option){
    $destinatarios = json_decode($this->destinatarios);
    $numDestinatarios = 0;
    $primera = true;
    $distinta = false;
    if ($destinatarios) {
      foreach ($destinatarios as $idUsuario => $destinatario) {
        $numDestinatarios += 1;
        $usuario = classInvoker::nc('usuarios', intval($idUsuario));
        if ($primera) {
          $idComunidad = $usuario->idComunidad;
          $primera = false;
        } else if ($idComunidad != $usuario->idComunidad) {
          $distinta = true;
        }
      }
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
    return json_decode($this->destinatarios,true);
  }

  function decodeText(){
    $this->texto = base64_decode($this->texto);
  }

  ///// Comprueba si una dirección está incluida en alguna correspondenciasExt
  static function checkDireccion($idDireccion){
    incluir('correspondencias');
    $check = false;
    $query = "SELECT destinatarios FROM correspondencias";
    // Usamos el método getAll heredado adaptado a PDO
    $correspondencias = correspondencias::getAll($query);
    if ($correspondencias) {
      foreach ($correspondencias as $key => $row) {
          $destinatarios = json_decode($row['destinatarios']);
          if ($destinatarios) {
            foreach ($destinatarios as $idUsuario => $destinatario) {
              if ($destinatario == $idDireccion) $check = true;
            }
          }
      }
    }
    return $check;
  }
}
?>