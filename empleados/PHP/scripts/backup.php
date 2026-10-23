<?php
 function deleteDir($dirPath) {
    if (! is_dir($dirPath)) {
        throw new InvalidArgumentException("$dirPath must be a directory");
    }
    if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
        $dirPath .= '/';
    }
    $files = glob($dirPath . '*', GLOB_MARK);
    foreach ($files as $file) {
        if (is_dir($file)) {
            deleteDir($file);
        } else {
            unlink($file);
        }
    }
    rmdir($dirPath);
 }
 $dir=dirname(__DIR__);
 date_default_timezone_set ("Europe/London" );
 $db=$argv[1];
 $mysql=new mysqli('localhost','root', 'Polifem@1',$db);
 $pathBackups="/home/chaca/Dropbox/backups/$db";
 $year=date("Y");
 $pathYear="$pathBackups/$year";
 if (!file_exists($pathYear)){
   mkdir($pathYear,0777);
   $lastYear=date("Y",strtotime("-1 year"));
   $pathLastYear="$pathBackups/$lastYear";
   $arrayLastYear=scandir($pathLastYear);
     if (sizeof($arrayLastYear)>2){
       $lastMonth=end($arrayLastYear);
       $pathLastMont="$pathLastYear/$lastMonth";
       $arrayLastMonth=scandir($pathLastMont);
       $lastBackup=end($arrayLastMonth);
       $fileBackup="$pathLastMont/$lastBackup";
       $newFileBackup="$pathLastYear/$lastBackup";
       copy($fileBackup,$newFileBackup);
       for ($i=2;$i<sizeof($arrayLastYear);$i++){
         $pathMonth="$pathLastYear/{$arrayLastYear[$i]}";
         deleteDir($pathMonth);
       }
    }
}
$month=date("m");
$pathMonth="$pathYear/$month";
if (!file_exists($pathMonth)){
   mkdir($pathMonth,0777);
   if ($month!="01"){
     $lastMonth=date("m",strtotime("-1 month"));
     $pathLastMonth="$pathBackups/$year/$lastMonth";
     $arrayLastMonth=scandir($pathLastMonth);
     for ($i=0;$i<(sizeof($arrayLastMonth)-1);$i++){
       $fileBackup="$pathLastMonth/{$arrayLastMonth[$i]}";
       unlink($fileBackup);
     }
   }
}
$arrayMonth=scandir($pathMonth);
if (sizeof($arrayMonth)>10){
  $fileBackup="$pathMonth/{$arrayMonth[2]}";
  unlink($fileBackup);
}
$day=date("d");
$fileBackup="$pathMonth/backup$year-$month-$day.sql";
exec("mysqldump -uroot -pPolifem@1 --no-create-db --no-create-info $db>$fileBackup");

?>
