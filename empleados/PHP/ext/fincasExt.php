<?php
class fincasExt extends fincas {
  public function addPropietario($property){
    if (empty($this->propietarios)) $this->propietarios="$property,";
    else $this->propietarios.="$property,";
    $this->update();
  }

  Public function html(){
    $html="";
    if (empty($this->propietarios)) return;
      $arrayPropietarios=explode(",",$this->propietarios);
      $html.="$('#tablaPropietarios').append('<tr><td>Propietario</td><td>Dni/Nie</td><td>Coeficiente</td><td></td></tr>');";

      foreach ($arrayPropietarios as $propietarios) {
        if (empty($propietarios)) continue;
        $arrayPropietario=explode(":",$propietarios);
        $propietario=classInvoker::nc('propietarios',intval($arrayPropietario[0]));
        if ($propietario->id==0){$this->deletePropietario($arrayPropietario[0]);continue;}
        $html.="$('#tablaPropietarios').append('<tr><td>{$propietario->propietario}</td><td>{$propietario->dni}</td>".
        "<td>{$arrayPropietario[1]}</td><td><label class=\"fa fa-trash buttons2\" onclick=\"executeAction(\'eliminar\',\'&destino=propietarios&idFinca={$this->id}&idPropietario={$propietario->id}\')\" ></label></td></tr>');";
    }
    return $html;
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
    $query="SELECT * FROM fincas WHERE idUsuario={$this->idUsuario}";
    incluir('fincas');
    $fincas=fincas::getAll($query);
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
