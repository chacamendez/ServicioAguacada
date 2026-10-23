<?php

$base=__DIR__;
$base=pathinfo($base);
$base=$base['dirname'];
foreach (glob("$base/PHP/core/*.php") as $filename){
  include $filename;
}
?>
