<?php
class propietariosExt extends propietarios {
  public function existe(){
     $query = "SELECT COUNT(*) FROM propietarios WHERE dni = ?";
     $num = propietarios::getValue($query, [$this->dni]);
     if ($num != 0) return true;
     else return false;
   }

   public static function devolverId($dni){
     if ($dni == '') return false;
     $query = "SELECT id FROM propietarios WHERE dni = ?";
     $idPropietario = propietarios::getValue($query, [$dni]);
     if ($idPropietario) return $idPropietario;
     else return false;
   }

   // Funcion para añadir el propietario a todas las fincas del usuario dado un usuario concreto
   public function addFincas($idUsuario){
      incluir('fincas');
      incluir('propietariosFincas');
      $query = "SELECT id FROM fincas WHERE idUsuario = ?";
      $idFincas = fincas::getAll($query, [$idUsuario]);
      if ($idFincas){
        foreach($idFincas as $idFinca){
          $propietarioFinca = classInvoker::nc('propietariosFincas');
          $propietarioFinca->idPropietario = $this->id;
          $propietarioFinca->idFinca = $idFinca['id'];
          $propietarioFinca->insert();
        }
      }
   }

   // Funcion para borrar el propietario de todas las fincas del usuario dado un usuario concreto
   public function deleteFincas($idUsuario){
      incluir('fincas');
      incluir('propietariosFincas');
      $query = "SELECT id FROM fincas WHERE idUsuario = ?";
      $idFincas = fincas::getAll($query, [$idUsuario]);
      if ($idFincas){
        foreach($idFincas as $idFinca){
          $propietarioFinca = classInvoker::nc('propietariosFincas');
          $propietarioFinca->idPropietario = $this->id;
          $propietarioFinca->idFinca = $idFinca['id'];
          $propietarioFinca->delete();
        }
      }
   }
}
?>