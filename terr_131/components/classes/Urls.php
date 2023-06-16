<?php
class Urls {
private $db;

public function __construct() { $this->db = new Db_layer(); }

//---[CZ]: vytvoření SEO URL
public function transfer_to_seo($data) {
$transfer=array("á"=>"a", "ä"=>"a", "č"=>"c", "ď"=>"d", "é"=>"e", "ě"=>"e", "ë"=>"e", "í"=>"i", "&#239;"=>"i", "ň"=>"n", "ó"=>"o", "ö"=>"o", "ř"=>"r", "š"=>"s", "ť"=>"t", "ú"=>"u", "ů"=>"u", "ü"=>"u", "ý"=>"y", "&#255;"=>"y", "ž"=>"z", "Á"=>"A", "Ä"=>"A", "Č"=>"C", "Ď"=>"D", "É"=>"E", "Ě"=>"E", "Ë"=>"E", "Í"=>"I", "&#207;"=>"I", "Ň"=>"N", "Ó"=>"O", "Ö"=>"O", "Ř"=>"R", "Š"=>"S","Ť"=>"T", "Ú"=>"U", "Ů"=>"U", "Ü"=>"U", "Ý"=>"Y", "&#376;"=>"Y", "Ž"=>"Z");
$data=strtr($data, $transfer);
$data=strtolower($data);
$data=preg_replace("/[^[:alpha:][:digit:]]/", "-", $data);
$data=trim($data, "-");
$data=preg_replace("/[-]+/", "-", $data);
return $data;
}
//---[CZ]: vypsání zanoření do sekce
public function section_name($higher, $i, $path) {
global $path;
$sql=$this->db->query("SELECT id, quick, name, higher FROM terr_sections WHERE id=$higher ORDER BY position");
while(list($id, $quick, $name, $higher)=$this->db->fetch_row($sql)) {
$i++;
$path="$name &raquo; $path";
$this->section_name($higher, $i, $path);
}
return substr($path, 0, -9);
}
//---[CZ]: sekce
public function section($i) {
if (URL_TYPE=="static") { return "/sections/$i/"; }
if (URL_TYPE=="dynamic") { return "./index.php?section=$i"; }
}
//---[CZ]: galerie
public function gallery($i) {
if (URL_TYPE=="static") { return "/galleries/$i/"; }
if (URL_TYPE=="dynamic") { return "./index.php?gallery=$i"; }
}
//---[CZ]: soubory
public function files() {
if (URL_TYPE=="static") { return "/files/"; }
if (URL_TYPE=="dynamic") { return "./index.php?function=files"; }
}
//---[CZ]: soukromé zprávy
public function pm($i, $a) {
if ($i=="blank") {
if (URL_TYPE=="static") { return "/$a/"; }
if (URL_TYPE=="dynamic") { return "./index.php?function=$a"; }}
else {
if (URL_TYPE=="static") { return "/$a/$i/"; }
if (URL_TYPE=="dynamic") { return "./index.php?function=$a&stuff=$i"; }
}
}
//---[CZ]: soubory někoho
public function files_of_user($i) {
if (URL_TYPE=="static") { return "/files/$i/"; }
if (URL_TYPE=="dynamic") { return "./index.php?function=files&page=$i"; }
}
//---[CZ]: komentáře někoho
public function comments_of_user($i) {
if (URL_TYPE=="static") { return "/comments/$i/"; }
if (URL_TYPE=="dynamic") { return "./index.php?function=comments&user=$i"; }
}
//---[CZ]: článek
public function article($i) {
if (URL_TYPE=="static") { return "/articles/$i/"; }
if (URL_TYPE=="dynamic") { return "./index.php?article=$i"; }
}
//---[CZ]: profil
public function profile($i) {
if (URL_TYPE=="static") { return "/profiles/$i/"; }
if (URL_TYPE=="dynamic") { return "./index.php?profile=$i"; }
}
//---[CZ]: root webu
public function root() {
if (URL_TYPE=="static") { return "/"; }
if (URL_TYPE=="dynamic") { return "./"; }
}
//---[CZ]: root pro JS
public function rootJS() {
if (URL_TYPE=="static") { return "../../"; }
if (URL_TYPE=="dynamic") {return "./"; }
}
//---[CZ]: funkce
public function fction($i) {
if (URL_TYPE=="static") { return "/$i/"; }
if (URL_TYPE=="dynamic") { return "./index.php?function=$i"; }
}
//---[CZ]: vyhledávání
public function search() {
if (URL_TYPE=="static") { header("Location: /search/?string=".rawurlencode($_POST["search"])); }
if (URL_TYPE=="dynamic") { header("Location: ./index.php?function=search&string=".rawurlencode($_POST["search"])."/"); }
}
//---[CZ]: odkaz na profil autora
public function author_link($id) {
$sql=$this->db->query("SELECT login, realname FROM terr_users WHERE id=$id");
$sql=$this->db->fetch_array($sql);
if ($sql["realname"]!="") { return "<a href=\"".$this->profile($sql["login"])."\">".$sql["realname"]."</a>"; }
if ($sql["realname"]=="") { return "<a href=\"".$this->profile($sql["login"])."\">".$sql["login"]."</a>"; }
}
//---[CZ]: odkaz na sekci
public function section_link($id) {
$name=$this->db->result($this->db->query("SELECT name FROM terr_sections WHERE quick=\"$id\""));
return "<a href=\"".$this->section($id)."\">".$name."</a>";
}
}
?>