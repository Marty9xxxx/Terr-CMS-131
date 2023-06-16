<?php
class Options extends Admin {
public function __construct() { parent::__construct(); }

//---[CZ]: výběr jazyka/vzhledu/šablony
public function selection($selected, $folder) {
$dir=opendir("./$folder/");
while ($file=readdir($dir)){
$name=explode(".", $file);
if ($name[0]!="") {
if ($name[0]!="admin" AND $name[1]=="css" OR $folder!="themes") {
echo ("<option value=\"".$name[0]."\"");
if ($name[0]==$selected) { echo " selected=\"selected\""; }
echo ">$file</option>\n";
}
}
}
}
//---[CZ]: výběr práv
public function rights_selection($item) {
if ($this->login->check_rights()<=$item) { echo " disabled>"; } else { echo ">"; }
for ($i=0; $i<6; $i++) {
echo "<option value=\"$i\"";
if ($item==$i) { echo " selected=\"selected\""; }
echo ">$i</option>\n";
}
}
//---[CZ]: ověřovací funkce
function analyzing($select, $from, $where, $order, $limit) {
if ($order!="") { $order = " ORDER BY $order"; }
if ($limit!="") { $limit = " LIMIT $limit"; }
if ($where!="") { $where = " WHERE $where"; }
$sql=$this->db->query("SELECT $select FROM $from".$where.$order.$limit);
$sql=$this->db->fetch_array($sql);
if ($sql[$select]!="") { return 1; } else { return 0; }
}
}
?>