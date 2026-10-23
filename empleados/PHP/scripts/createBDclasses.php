<?php
 require_once "../core/Database.php";
 $query="SHOW TABLES";
 $num=1;
 $tablas=Database::ExecuteQuery($query);
 print_r($tablas);
 while ($row=$tablas->fetch_row()){
   print($row[0]);
   echo $row[0]."$num\n";
   $query="DESCRIBE {$row[0]}";
   $tabla=Database::ExecuteQuery($query);
   $classString="<?php \nclass {$row[0]} {\n";
   $first=true;
   $listadoColumnas="";
   while ($row2=$tabla->fetch_row()){
     $type=substr($row2[1],0,3);
     if ($first) $first=0;
     else {
       $listadoColumnas.="{$row2[0]},";
     }
     $value="''";
     switch  ($type){
	      case 'int':
          $value=0;
          if ($row2[4]!="") $value=$row2[4];
        break;
        case 'dec':
          $value=0;
          if ($row2[4]!="") $value=$row2[4];
        break;
        case 'tin':
          $value=0;
          if ($row2[4]!="") $value=$row2[4];
        break;
        case 'var':
            if ($row2[4]!="") $value="'{$row2[4]}'";
        break;
        case 'dat':
              if ($row2[4]!="") $value="'{$row2[4]}'";
        break;
        case 'boo':
            $value=0;
            if ($row2[4]!="") $value=$row2[4];
        break;


     }
    $classString.="  public \${$row2[0]}=$value;\n";
   }

   $listadoColumnas=substr($listadoColumnas, 0, -1);

   $classString.=" public function __construct(\$params=false){\n";
   $classString.="  if (\$params){\n";
   $classString.="   \$type=gettype(\$params);\n";
   $classString.="    switch(\$type){\n";
   $classString.="     case 'integer':\n";
   $classString.="      \$query=\"SELECT * FROM {$row[0]} WHERE id=\$params\";\n";
   $classString.="      \$result=Database::ExecuteQuery(\$query);\n";
   $classString.="      \$row=\$result->fetch_assoc();\n";
   $classString.="       foreach(\$row as \$key=>\$value){\n";
   $classString.="        \$type=gettype(\$value);\n";
   $classString.="         if (\$type=='string') eval(\"\\\$this->\$key='\$value';\");\n";
   $classString.="         else  eval(\"\\\$this->\$key=\$value;\");\n";
   $classString.="       }\n";
   $classString.="     break;\n";
   $classString.="     case 'string':\n";
    $classString.="     \$params=json_decode(\$params);\n";
   $classString.="     case 'object':\n";
   $classString.="      foreach (\$params as \$key => \$value) {\n";
   $classString.="      \$type=gettype(\$value);\n";
   $classString.="      if (\$type=='string') eval(\"\\\$this->\$key='\$value';\");\n";
   $classString.="      else  eval(\"\\\$this->\$key=\$value;\");\n";
   $classString.="      }\n";
   $classString.="     break;\n";
   $classString.="    \n";
   $classString.="   }\n";
   $classString.="  }\n";
   $classString.=" }\n";
   $classString.=" public function insert(){\n";
   $classString.="  \$listadoValores='';\n";
   $classString.="  foreach (\$this as \$key => \$value) {\n";
   $classString.="   if (\$key=='id' || \$key=='detalles') continue;\n";

   $classString.="   if (gettype(\$value)=='string') \$listadoValores.=\"'\$value',\";\n";
   $classString.="   else \$listadoValores.=\"\$value,\";\n";
   $classString.="  }\n";
   $classString.="  \$listadoValores=substr(\$listadoValores, 0, -1);\n";
   $classString.="  \$query=\"INSERT INTO {$row[0]} ($listadoColumnas) VALUES (\$listadoValores);SELECT id FROM {$row[0]} ORDER BY id DESC LIMIT 1\";\n";
   $classString.="  \$this->id=Database::ExecuteMultiQuery(\$query,true);\n";
   $classString.=" }\n";
   $classString.=" public function update(){\n";
   $classString.="  \$listadoValores='';\n";
   $classString.="  foreach (\$this as \$key => \$value) {\n";
   $classString.="   if (\$key=='id' || \$key=='detalles') continue;\n";
   $classString.="   if (gettype(\$value)=='string') \$listadoValores.=\"\$key='\$value',\";\n";
   $classString.="   else \$listadoValores.=\"\$key=\$value,\";\n";
   $classString.="  }\n";
   $classString.=" \$listadoValores=substr(\$listadoValores, 0, -1);\n";
   $classString.=" \$query=\"UPDATE {$row[0]} SET \$listadoValores WHERE id={\$this->id}\";\n";
   $classString.="  Database::ExecuteQuery(\$query);\n";
   $classString.=" }\n";
   $classString.=" public function delete(){\n";
   $classString.="  \$query=\"DELETE FROM {$row[0]} WHERE id={\$this->id}\";\n";
   $classString.="  Database::ExecuteQuery(\$query);\n";
   $classString.=" }\n";
   $classString.=" \n";
   $classString.=" \n";
   $classString.=" \n";
   $classString.=" public static function getAll(\$query=false,\$ext=false){\n";
   $classString.="   if (!\$query){\n";
   $classString.="    \$query='SELECT *  FROM {$row[0]} ';\n";
   $classString.="    if (\$ext) \$query .= \$ext;\n";
   $classString.="    else \$query.=' ORDER BY id DESC';\n";
   $classString.="   }\n";
   $classString.="  \$result = Database::ExecuteQuery(\$query);\n";
   $classString.="  \$listado=false;\n";
   $classString.="  while (\$row=\$result->fetch_assoc()){\n";
   $classString.="   	\$listado[]=\$row;\n";
   $classString.="  }\n";
   $classString.="  if (!\$listado) return false;\n";
   $classString.="  else return \$listado;\n";
   $classString.=" }\n";
   $classString.=" \n";
   $classString.=" public static function getRow(\$query){\n";
   $classString.="  \$result = Database::ExecuteQuery(\$query);\n";
   $classString.="  \$listado=false;\n";
   $classString.="  while (\$row=\$result->fetch_assoc()){\n";
   $classString.="   	\$listado[]=\$row;\n";
   $classString.="  }\n";
   $classString.="  if (!\$listado) return false;\n";
   $classString.="  else return \$listado[0];\n";
   $classString.=" }\n";
   $classString.=" \n";
   $classString.=" public static function getValue(\$query){\n";
   $classString.="  \$result = Database::ExecuteQuery(\$query);\n";
   $classString.="  \$row=\$result->fetch_row();\n";
   $classString.="  return \$row[0];\n";
   $classString.=" }\n";
   $classString.=" public static function jsonAdapter(\$json){\n";
   $classString.="  if (\$json){\n";
   $classString.="  \$object=new {$row[0]}();\n";
   $classString.="  \$type=gettype(\$json);\n";
   $classString.="  switch(\$type){\n";
   $classString.="   case 'string':\n";
   $classString.="    \$json=json_decode(\$json);\n";
   $classString.="    break;\n";
   $classString.="   }\n";
   $classString.="   foreach(\$json as \$key=>\$value){\n";
   $classString.="    if (!isset(\$value) || \$value==\"\") continue;\n";
   $classString.="    if (\$type=='string') eval(\"\\\$object->\$key='\$value';\");\n";
   $classString.="    else   eval(\"\\\$object->\$key=\$value;\");\n";
   $classString.="   }\n";
   $classString.="   return \$object;\n";
   $classString.="   }\n";
   $classString.="  }\n";
   $classString.="  public static function toJSON(\$query=false){\n";
   $classString.="   if (!\$query) \$query='SELECT * FROM {$row[0]} ORDER BY id DESC';\n";
   $classString.="    \${$row[0]}=self::toARRAY(\$query);";
   $classString.="    if (\${$row[0]}) return json_encode(\${$row[0]});\n";
   $classString.="    else return null;\n";
   $classString.=" }\n";
   $classString.="  public static function toARRAY(\$query=false){\n";
   $classString.="   if (!\$query) \$query='SELECT * FROM {$row[0]} ORDER BY id DESC';\n";
   $classString.="   \${$row[0]}=Database::ExecuteQuery(\$query);\n";
   $classString.="   if (\${$row[0]}->num_rows!=0){\n";
   $classString.="    while (\$row=\${$row[0]}->fetch_assoc()){\n";
   $classString.="      \$rows[]=\$row;\n";
   $classString.="    }\n";
   $classString.="    return \$rows;\n";
   $classString.="   }\n";
   $classString.="   else return false;\n";
   $classString.=" }\n";
   $classString.="}\n";
   $classString.="\n?>";
   $archivo = fopen("../dbclasses/{$row[0]}.php", "w");
   fwrite($archivo, $classString);
   echo "paso";
   $num++;
 }

?>
