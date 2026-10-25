<?php 
class archivosExt extends archivos {
  public function existe(){
     $query = "SELECT COUNT(*) FROM archivos WHERE idhash = ?";
     // Pasamos el valor como un array en el segundo argumento
     $num = archivos::getValue($query, [$this->idhash]);
     if ($num != 0) return true;
     else return false;
   }

   public function hasPermissions(){
       if (Session::allowed()) return true;
       else {
         if ($this->idComunidad != 0){
           incluir('comunidades', true);
           $allowed = comunidadesExt::perteneceAlaComunidad($_SESSION['idUsuario'], $this->idComunidad);
           if ($allowed) return true;
           else return false;
         }
         else{
           if ($_SESSION['idUsuario'] == $this->idUsuario) return true;
           else return false;
         }
       }
   }

   public function deleteExt(){
     $dirFiles = dirname(__DIR__);
     $dirFiles = "$dirFiles/files";
     $file = "$dirFiles/{$this->idhash}";
     if (file_exists($file)) {
         unlink($file);
     }
     $this->delete();
   }

   public function generarHash(){
     $dirFiles = dirname(__DIR__);
     require_once("$dirFiles/core/ext/challengeclass.php");
     $this->idhash = ChallengeGenerator::staticGetRandomString(10);
   }

   public function saveExt(){
     $dirFiles = dirname(__DIR__);
     $dirFiles = "$dirFiles/files";
     rename("$dirFiles/archivoDestino", "$dirFiles/{$this->idhash}");
     $this->insert();
   }

   public function addHistorial(){
     $now = Strings::fechaHora();
     if (strlen($this->historialDescargas) > 450) $replace = true;
     else $replace = false;
     $json = json_decode($this->historialDescargas, TRUE);
     $json[] = ['id' => $_SESSION['idUsuario'], 'DT' => "$now"];
     if ($replace){
       array_shift($json);
     }
     $json = json_encode($json);
     $this->historialDescargas = $json;
   }

   public function historialToHtml(){
     $json = json_decode($this->historialDescargas, TRUE);
     $html = '';
     incluir('usuarios');
     if ($json){
       foreach ($json as $evento) {
         $query = "SELECT usuario FROM usuarios WHERE id = ?";
         // Utilizando parámetros seguros en la llamada a getValue
         $usuario = usuarios::getValue($query, [$evento['id']]);
         $html .= "Descargado por $usuario el {$evento['DT']}<br>";
       }
     }
     return $html;
   }
}
?>