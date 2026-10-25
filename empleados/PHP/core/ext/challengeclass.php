<?php
/*
 * Begin of server-side processing
 */
class ChallengeGenerator{
  // constructor
  function __Construct($clearSession=true){
    if($clearSession){
      $this->clearVars();

    }
    if (session_status() == PHP_SESSION_NONE) {
  	session_start();
    }
    $this->setRmdK();


  }
  // public method clearVars()
  function clearVars(){
    // destroy existing session
    session_start();
    session_unset();
    session_destroy();

  }
  function setRmdK(){
      $_SESSION['key']=$this->getRandomString(16);
      $_SESSION['iv']=$this->getRandomString(16);
      $_SESSION['token']=$_SESSION['key'];
  }

  function setHashVar($name='hash'){
    if(!is_string($name)||!$name){
      trigger_error('Invalid variable name');
      exit();
    }
    // register session variable
    $_SESSION[$name]=$this->getRandomString(10,true);
  }

  function getVar($name){
    if(!$_SESSION[$name]){
      trigger_error('Nombre de variable no valida');
      exit();
    }
    return $_SESSION[$name];
  }

  function deleteChallengeVar($name){
      if(!$_SESSION[$name]){
      trigger_error('Invalid variable name');
      exit();
    }
            unset($_SESSION[$name]);
  }
  // private method "getRandomString()"
  function getRandomString($length=256,$distinct=false){
    if(!is_int($length)||$length<1){
      trigger_error('Invalid length for random string');
      exit();
    }
    $chars="abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNOPQRSTUVWXYZ0123456789";
    $randstring='';
    $maxvalue=strlen($chars)-1;
    for($i=0;$i<$length;$i++){
      $rand=substr($chars,rand(0,$maxvalue),1);
      if ($distinct) $chars = str_replace($rand, '', $chars);
      $randstring.=$rand;
    }
    return $randstring;
  }
  static function staticGetRandomString($length=256,$distinct=false){
    if(!is_int($length)||$length<1){
      trigger_error('Invalid length for random string');
      exit();
    }
    $chars="abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNOPQRSTUVWXYZ0123456789";
    $randstring='';
    $maxvalue=strlen($chars)-1;
    for($i=0;$i<$length;$i++){
      $rand=substr($chars,rand(0,$maxvalue),1);
      if ($distinct) $chars = str_replace($rand, '', $chars);
      $randstring.=$rand;
    }
    return $randstring;
  }

}
