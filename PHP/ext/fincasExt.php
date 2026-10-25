<?php
class fincasExt extends fincas {
  

  Public function html(){
    
    $html="";
    incluir('propietariosFincas');
    $query="SELECT idPropietario FROM propietariosFincas WHERE idFinca = ?";
    $idPropietarios=propietariosFincas::getAll($query, [$this->id]);
    if ($idPropietarios){
      
      $html.="$('#tablaPropietarios').append('<tr><td>Propietario</td><td>Dni/Nie</td><td></td><td></td></tr>');";
      foreach ($idPropietarios as $idPropietario){
        $propietario=classInvoker::nc('propietarios',intval($idPropietario['idPropietario']));
        $html.="$('#tablaPropietarios').append('<tr><td>{$propietario->propietario}</td><td>{$propietario->dni}</td>".
        "<td><label class=\"fa fa-pencil buttons2\" onclick=\"executeAction(\'editar\',\'&destino=propietarios&idPropietario={$propietario->id}\')\" ></label></td>".
        "<td><label class=\"fa fa-trash buttons2\" onclick=\"executeAction(\'eliminar\',\'&destino=propietarios&idUsuario={$this->idUsuario}&idPropietario={$propietario->id}&idFinca={$this->id}\')\" ></label></td></tr>');";
    
      }
    }
   
    return $html;
  }

   //Funcion para añadir a la finca todos los propietarios relacionados con las otras fincas
   
   public function addPropietarios(){
      incluir('propietarios');
      incluir('propietariosFincas');
      $query="SELECT id FROM fincas WHERE idUsuario = ? AND id <> ? LIMIT 1";
      $idFinca=fincas::getValue($query, [$this->idUsuario, $this->id]);
      if ($idFinca){
       $query="SELECT idPropietario FROM propietariosFincas WHERE idFinca = ?";
       $idPropietarios=propietarios::getAll($query, [$idFinca]);
       if ($idPropietarios){
        foreach ($idPropietarios as $idPropietario){
          $propietarioFinca=classInvoker::nc('propietariosFincas');
          $propietarioFinca->idPropietario=$idPropietario['idPropietario'];
          $propietarioFinca->idFinca=$this->id;
          $propietarioFinca->insert();
        }
       }
        
      }

   }
   //Funcion para eliminar la finca y los registros propietariosFincas relacionadas con ella
   public function deleteFinca (){
      $query="DELETE FROM propietariosFincas WHERE idFinca = ?";
      Database::ExecuteQuery($query, [$this->id]);
      $this->delete();
   }



  //Todas estas funciones son obsoletas comprobar y borrar

  public function addPropietario($property){
    if (empty($this->propietarios)) $this->propietarios="$property,";
    else $this->propietarios.="$property,";
    $this->update();
  }
  public function deletePropietario($id){
    $arrayPropietarios=explode(",",$this->propietarios);
    $stringPropietarios="";
    foreach ($arrayPropietarios as $key=>$arrayPropietario) {
      $propietario=explode(":",$arrayPropietario);
      if (!isset($propietario[1])) continue;
      if ($propietario[0]==$id) continue;
      else $stringPropietarios.="$propietario[0]:$propietario[1],";
    }
    $this->propietarios=$stringPropietarios;
    $this->update();
  }
  public function updatePropietarios(){
    $query="SELECT * FROM fincas WHERE idUsuario = ?";
    incluir('fincas');
    $fincas=fincas::getAll($query, [$this->idUsuario]);
    if ($fincas){
      foreach ($fincas as $finca) {
        if ($finca['propietarios']==""){
          $fincaO=classInvoker::nc('fincas',intval($finca['id']));
          $fincaO->propietarios=$this->propietarios;
          $fincaO->update();
        }
      }
    }
  }

}
?>