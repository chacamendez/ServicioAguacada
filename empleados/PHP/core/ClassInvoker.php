<?php
class ClassInvoker {
  static function incluir($class,$ext=false){
    include_once "./dbclasses/$class.php";
    if ($ext){
      include_once "./ext/{$class}Ext.php";
    }
  }

  static function nc ($class,$params='',$ext=false){
    $path=dirname(__DIR__);
    include_once "$path/dbclasses/$class.php";
    if ($ext){
     include_once "$path/ext/{$class}Ext.php";
     eval ("\$object=new {$class}Ext($params);");
     return $object;
    }
    else{
     eval ("\$object=new $class($params);");
     return $object;
    }
  }

  

}
?>
