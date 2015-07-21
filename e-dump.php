<?php
/*

-------------------------------------------------------------------------------
[ E-DUMP ]---------------------------------------------------------------------
- Extracts email addresses from a MySQL database.

-------------------------------------------------------------------------------
[ TO RUN THE SCRIPT ]----------------------------------------------------------
PHP Version       5.6.8
 php5-cli         Lib
MySQL support     Enabled
MySQL version     5.0.11
Permission        Writing

-------------------------------------------------------------------------------
[ ABOUT DEVELOPER ]------------------------------------------------------------
NAME              CoderPirata
Blog              http://coderpirata.blogspot.com.br/
Twitter           https://twitter.com/coderpirata
Google+           https://plus.google.com/103146866540699363823
Pastebin          http://pastebin.com/u/CoderPirata
Github            https://github.com/coderpirata/

*/

ini_set('error_log',NULL);
ini_set('log_errors',FALSE);
ini_set('display_errors', FALSE);
ini_set('max_execution_time', FALSE);

$oo = getopt('h::', ['help::', 'host:', 'user:', 'pass:', 'dbname:', 'save:', 'no-info']);

function cores($nome){
$cores = array("r" => "\033[1;31m", "g" => "\033[0;32m", "b" => "\033[1;34m", "g2" => "\033[1;30m", "g1" => "\033[0;37m");
if(substr(strtolower(PHP_OS), 0, 3) != "win"){ return $cores[strtolower($nome)]; }
}

echo cores("g1")."
 ooooooooooo       ooooooooo  ooooo  oooo oooo     oooo oooooooooo
  888    88         888    88o 888    88   8888o   888   888    888
  888ooo8    oooooo 888    888 888    88   88 888o8 88   888oooo88
  888    oo         888    888 888    88   88  888  88   888
 o888ooo8888       o888ooo88    888oo88   o88o  8  o88o o888o\n
\t       ".cores("g2")."[  ".cores("g1")."DUMP EMAILS FROM ".cores("b")."MYSQL".cores("g1")." DATABASE! ".cores("g2")."]
 -----------------------------------------------------------------\n\n"; 

if(isset($oo['h']) or isset($oo['help'])){
die(cores("g1")."\t  ooooo ooooo ooooooooooo ooooo       oooooooooo
\t   888   888   888    88   888         888    888
\t   888ooo888   888ooo8     888         888oooo88
\t   888   888   888    oo   888      o  888
\t  o888o o888o o888ooo8888 o888ooooo88 o888o\n
 ".cores("g2")."-----------------------------------------------------------------\n".cores("g1")."
COMMAND:: ".cores("b")."--host ".cores("g1")."~ Sets the Host.
       Example: {$_SERVER["SCRIPT_NAME"]} ".cores("b")."--host ".cores("g1")."localhost
\n
COMMAND:: ".cores("b")."--user ".cores("g1")."~ Sets the User.
       Example: {$_SERVER["SCRIPT_NAME"]} ".cores("b")."--user ".cores("g1")."root
\n
COMMAND:: ".cores("b")."--pass ".cores("g1")."~ Sets the Password.
       Example: {$_SERVER["SCRIPT_NAME"]} ".cores("b")."--pass ".cores("g1")."admin123
\n
COMMAND:: ".cores("b")."--dbname ".cores("g1")."~ Command to set the db that will be scanned. By default, all dbs will be scanned.
       Example: {$_SERVER["SCRIPT_NAME"]} ".cores("b")."--dbname ".cores("g1")."mydatabase
\n
COMMAND:: ".cores("b")."--save ".cores("g1")."~ Saves the emails found on a list.
       Example: {$_SERVER["SCRIPT_NAME"]} ".cores("b")."--save ".cores("g1")."output.txt
                {$_SERVER["SCRIPT_NAME"]} ".cores("b")."--save ".cores("g1")."\"\"

COMMAND:: ".cores("b")."--no-info ".cores("g1")."~ It does not display the \"databases\" and the tables.
       Example: {$_SERVER["SCRIPT_NAME"]} ".cores("b")."--no-info
\n");
}
 
 
if(empty($oo['host']) or empty($oo['user']) or !isset($oo['pass'])){ die(); }				
$db_blacklist = array('information_schema', 'performance_schema');

if(isset($oo["save"])){ 
$save = cores("g")."YES";
if(!empty($oo["save"])){$save .= cores("g2")."\n| ".cores("g1")."FILE NAME:: ".cores("b").$oo["save"].cores("g2"); } 
}else{ $save = cores("r")."NOT"; }
if(isset($oo["dbname"])){ $dbnme = cores("g").$oo["dbname"]; }else{ $dbnme = cores("r")."NOT DEFINED"; }
echo cores("g2").".-[ ".cores("g1")."INFOS".cores("g2")." ] ------------------------------------------------------
| ".cores("g1")."HOST:: ".cores("b").$oo["host"].cores("g2")."
| ".cores("g1")."USER:: ".cores("b").$oo["user"].cores("g2")."
| ".cores("g1")."PASS:: ".cores("b").$oo["pass"].cores("g2")."
| ".cores("g1")."DBNAME:: ".cores("b").$dbnme.cores("g2")."
| ".cores("g1")."SAVE:: {$save}".cores("g2")."
'-----------------------------------------------------------------\n";

$conect = mysql_connect($oo["host"],$oo["user"],$oo["pass"]) or die(cores("r")."\n\nERROR:: ".mysql_error()."\n\n");
if(empty($oo["dbname"])){
$resultado = mysql_query("SHOW DATABASES;", $conect) or die(cores("r")."\n\nERROR:: ".mysql_error()."\n\n");
while($data = mysql_fetch_row($resultado)){ $resultado_query .= implode('|-|-|-|-|-|',$data)."\n"; }
$lim = explode("|-|-|-|-|-|", $resultado_query);
foreach($lim as $lin){ if(!empty($lin)){ $dbnames = $lin; } }
$dbname = explode("\n", $dbnames);
foreach($dbname as $n => $d){ 
 foreach($db_blacklist as $blocked){ if(empty($d) or $d == $blocked){ unset($dbname[$n]); } } 
}
}else{ $dbname[] = $oo["dbname"]; }

if(!isset($oo["no-info"])){ echo cores("g1")."\n SEARCHIN IN:: "; }
foreach($dbname as $dbnam3){
if(!isset($oo["no-info"])){ echo cores("g2")."\n -----------------------------------\n  ".cores("g2")."[ ".cores("g1")."DBNAME".cores("g2")." ]::".cores("b")."$dbnam3\n".cores("g2")."  [ ".cores("g1")."TABLES ".cores("g2")."]::"; }
mysql_select_db($dbnam3) or die(cores("r")."\n\nERROR:: ".mysql_error()."\n\n");
$res = mysql_list_tables($dbnam3) or die(cores("r")."\n\nERROR:: ".mysql_error()."\n\n"); 
while ($row = mysql_fetch_row($res)){
 $table = $row[0]; 
 $res2 = mysql_query("SHOW CREATE TABLE $table");
 if(!isset($oo["no-info"])){ echo cores("g2")." - ".cores("b")."{$table}"; }
 while ($lin = mysql_fetch_row($res2)){ 
  $res3 = mysql_query("SELECT * FROM $table");
   while($r=mysql_fetch_row($res3)){ 
   $sql="INSERT INTO $table VALUES (";

   for($j=0; $j<mysql_num_fields($res3);$j++){
    if($r[$j] != ""){ $dados .= " - ".$r[$j]." - "; }
   }
   }
  }
$table = NULL;
}
}
mysql_close($coneccao);

if(!isset($oo["no-info"])){ echo cores("g2")."\n\n -----------------------------------------------------------------\n"; }
preg_match_all("/([\w\d\.\-\_]+)@([\w\d\.\_\-]+)/mi", $dados, $possiveis);
$email = array_unique(array_unique($possiveis[0]));
if(empty($email)){ echo cores("r")."\n EMAIL(s) NOT FOUND\n"; goto sai; }else{ echo cores("g")."\n  ".count($email)." EMAIL(s) FOUND".cores("g1")."\n\n"; }
foreach ($email as $emails){ if(eregi(".", $emails)){ echo cores("g1")."\n EMAIL:: ".cores("g")."{$emails}"; } }
echo "\n\n";

if(!empty($email) and isset($oo["save"])){
if(isset($oo["save"]) and empty($oo["save"])){ $name = "e-dump_emails.txt"; }else{ $name = $oo["save"]; }
if(!file_exists($name)){ file_put_contents($name, "EMAIL's LEAKED FROM {$oo["host"]} WITH E-DUMP! ".PHP_EOL.PHP_EOL.PHP_EOL); }
foreach ($email as $emails){ file_put_contents($name, "EMAIL: ".$emails.PHP_EOL, FILE_APPEND); }
echo "  ".cores("g1")."EMAILS SAVED IN: ".cores("b")."{$name}\n";
}

sai:
echo cores("g2")." -----------------------------------------------------------------\n";

#END
