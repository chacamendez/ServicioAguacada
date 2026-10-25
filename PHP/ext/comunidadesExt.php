<?php 
class comunidadesExt extends comunidades {
  public function toMailTo(){
    incluir('usuarios');
    $query = "SELECT id FROM usuarios WHERE idComunidad = ? AND estado <> 'eliminado'";
    $usuarios = usuarios::getAll($query, [$this->id]);
    $mailTo = "";
    $first = true;
    if ($usuarios){
      foreach ($usuarios as $usuario) {
        $usuarioExt = classInvoker::nc('usuarios', intval($usuario['id']), true);
        $arrayCorreos = $usuarioExt->arrayCorreos();
        if ($arrayCorreos){
          foreach ($arrayCorreos as $correo) {
            if ($first) {$mailTo = "?bcc={$correo['correo']},"; $first = false;}
            else $mailTo .= "{$correo['correo']},";
          }
        }
      }
    }

    return $mailTo;
  }

  public function tieneUsuarios(){
    incluir('usuarios');
    $query = "SELECT COUNT(*) FROM usuarios WHERE idComunidad = ? AND estado <> 'eliminado'";
    $numUsuarios = usuarios::getValue($query, [$this->id]);
    if ($numUsuarios == 0) return false;
    else return true;
  }

  public function deleteExt(){
    incluir('archivos');
    $query = "SELECT id FROM archivos WHERE idComunidad = ?";
    $archivos = archivos::getAll($query, [$this->id]);
    if ($archivos){
      foreach ($archivos as $archivo) {
        $archivoO = classInvoker::nc('archivos', intval($archivo['id']), true);
        $archivoO->deleteExt();
      }
    }
    $this->delete();
  }

  public static function perteneceAlaComunidad($idUsuario, $idComunidad){
    $query = "SELECT count(*) FROM usuarios WHERE id = ? AND idComunidad = ?";
    // Usamos getValue para obtener el conteo directamente de forma segura con PDO
    $num = usuarios::getValue($query, [$idUsuario, $idComunidad]);
    if ($num == 1) return true;
    else return false;
  }
}
?>