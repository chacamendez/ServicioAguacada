<?php
class propietariosExt extends propietarios {
  public function existe(){
     $query="SELECT COUNT(*) FROM propietarios WHERE dni={$this->dni}";
     $num=propietarios::getValue($query);
     if ($num !=0) return true;
     else return false;
   }
   public static function devolverId($dni){
     if ($dni=='') return false;
     $query="SELECT id FROM propietarios WHERE dni='$dni'";
     $idPropietario=propietarios::getValue($query);
     if ($idPropietario) return $idPropietario;
     else return false;
   }
   public function insertExt($property=false){
     if ($idPropietario=$this->devolverId()){
       $this->id=$idPropietario;
       if ($property) addProperty($property);
       $this->update();
     }
     else {
       if ($property) addProperty($property);
       $this->insert();
     }

   }
   public function isOwner($property){
     $propiedad=explode(":",$property);
     $arrayFincas=explode(",",$this->listadoFincas);
     foreach ($arrayFincas as $key=>$arrayFinca) {
       $finca=explode(":",$arrayFinca);
       if (!isset($finca[1])) continue;
       if ($finca[0]==$property[0]) return true;
     }
     return false;

   }
   public function addProperty($property){
     if (empty($this->listadoFincas)) {$this->listadoFincas.="$property,";return;}
     if ($this->isOwner($property)) return;
     else $this->listadoFincas.="$property,";
   }
   public function deleteFinca($idFinca){
     $arrayFincas=explode(",",$this->listadoFincas);
     $stringFincas="";
     foreach ($arrayFincas as $key=>$arrayFinca) {
       $finca=explode(":",$arrayFinca);
       if (!isset($propietario[1])) continue;
       if ($arrayFinca[0]==$idFinca) continue;
       else $stringFincas.="$key:{$arrayFinca[$key]},";
     }
     if (empty($stringFincas)) $this->delete();
     else $this->update();
   }

}
