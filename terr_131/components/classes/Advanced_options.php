<?php
class Advanced_options {
public $status_messages;
public $db;

public function __construct() {
  $this->status_messages=new Status_messages();
  $this->db=new Db_layer();
}

//---[CZ]: výběr jazyka/vzhledu/šablony
public function selection($selected, $folder) {
$dir=opendir("./$folder/");
while ($file=readdir($dir)){
  $name=explode(".", $file);
  if ($name[0]!="" AND $name[0]!="admin" && ($name[1]=="css" || $folder!="themes")) {
    echo "<option value=\"".$name[0]."\"".(($name[0]==$selected)?" selected=\"selected\"":"").">$file</option>\n";
  }
}}
//---[CZ]: obnova databáze
public function db_restore() {
if (!isset($_FILES["file"]["name"])) { $_FILES["file"]["name"]=""; }
if ($_FILES["file"]["name"]!="") {
  $name=explode(".", $_FILES["file"]["name"]);
  if (strtolower($name[1])=="zip") {
    move_uploaded_file($_FILES["file"]["tmp_name"], "./backups/".$_FILES["file"]["name"]);
    $zip=new ZipArchive;
    $zip->open('./backups/'.$_FILES["file"]["name"]);
    $zip->extractTo('./backups/');
    $zip->close();
    @$remove1=chmod("./backups/".$_FILES["file"]["name"], 0777);
    @$remove1=unlink("./backups/".$_FILES["file"]["name"]);
    $array = array("articles", "bans", "columns", "comments", "files", "images", "images_sections", "pm", "polls", "polls_data", "sections", "users", "variables", "series");
    while (list($index, $stav) = each($array)) { $sql=$this->db->query("TRUNCATE table terr_$stav"); }
    if ($sql) {
      $file = file_get_contents("./backups/$name[0].sql");
      $radek = explode("\n",$file);
      foreach ($radek as $temp) { $sql = $this->db->query($temp); }
      if ($sql) {
        @$remove2 = chmod("./backups/$name[0].sql", 0777);
        @$remove2 = unlink("./backups/$name[0].sql");
      }
    }
  }
}}
//---[CZ]: smazání zálohy databáze
public function destroying() {
if ($_POST["password"]!="" && sha1($_POST["password"])==$this->db->result($this->db->query("SELECT password FROM terr_users WHERE id=1"))) {
switch ($_POST["reset_type"]) {
case "soft":
$avatars=glob("./avatars/*.*");
$images=glob("./gallery/*.*");
$thumbs=glob("./gallery/thumbs/*.*");
$files=glob("./files/*.*");
if ($images!="") { foreach ($images as $file) { unlink($file); }}
if ($thumbs!="") { foreach ($thumbs as $file) { unlink($file); }}
if ($files!="") { foreach ($files as $file) { unlink($file); }}
if ($avatars!="") { foreach ($avatars as $file) { if (substr($file,-6,3)!="/1.") { unlink($file); }}}
if (file_exists("./components/images/favicon/favicon.ico")) { unlink("./components/images/favicon/favicon.ico"); }
$sql=$this->db->query("DELETE FROM terr_users WHERE id!=1");
$terr=array("articles","articles","bans","columns","pm","comments","polls","polls_data","sections","series","sections","series","files","images","images_sections");
foreach ($terr as $table) { $sql=$this->db->query("DELETE FROM terr_".$table); }
$arr=array("sitename" => "Website","login_for_comments" => "0","show_text" => "0","articles_number" => "10","comments_number" => "10","meta_copyright" => "Copyright","meta_keywords" => "","meta_desc" => "","use_emoticons" => "1","sort_images_by_name" => "0","sort_comments_from_newest" => "1","rights_add_content" => "1","rights_archive" => "3","rights_bans" => "3","rights_columns" => "3","rights_comments" => "2","rights_unverified_articles" => "2","rights_edit_content" => "1","rights_files" => "1","rights_images" => "1","rights_mail_messages" => "3","rights_meta_tags" => "3","rights_my_content" => "1","rights_options" => "3","rights_overview" => "1","rights_polls" => "3","rights_unpublished_articles" => "3","rights_rights" => "3","rights_sections" => "3","rights_users" => "3","rights_advanced_options" => "4","show_addthis" => "0","show_article_date" => "1","show_article_section" => "1","show_article_comments_number" => "1","show_article_views_number" => "1","show_article_author" => "1","template" => "terr_default","theme" => "terr_default","show_menu_with_last_login" => "0","show_menu_with_last_comments" => "0","show_recent_articles" => "0","show_random_image" => "0","show_unconfirmed_comments" => "1","name_of_menu" => "Sekce","name_of_hp" => "Titulní strana","show_the_link_to_open_whole_article" => "0","show_avatar_in_login_form" => "0","show_accounts_count" => "0","redactor" => "redaktor","corrector" => "korektor","admin1" => "administrátor 1. úrovně","admin2" => "administrátor 2. úrovně","admin3" => "administrátor 3. úrovně");
foreach ($arr as $variable => $value) { $sql=$this->db->query("UPDATE terr_variables SET value='".$value."' WHERE variable='".$variable."'"); }
$this->status_messages->print_success(LANG_ACTION_WAS_SUCCESSFULLY_COMPLETED);
break;
case "hard":
if (!unlink("./robots.txt")) { $this->status_messages->print_error(LANG_AN_ERROR_OCCURED); }
else {
$this->recursive_remove(".");
$sql=$this->db->query("DROP TABLE terr_articles, terr_bans, terr_columns, terr_comments, terr_files, terr_images, terr_images_sections, terr_pm, terr_polls, terr_polls_data, terr_sections, terr_series, terr_users, terr_variables");
header("Location: /");
}
break;
}
}
}
private function recursive_remove($dir) {
$files=glob($dir);
if ($files!="") {
foreach ($files as $file) {
if (is_dir($file)) {
  $this->recursive_remove($file."/*");
  rmdir($file);
}
else { unlink($file); }
}
}
}
//---[CZ]: smazání zálohy databáze
public function backup_destroy() {
@$remove2 = chmod("./backups/".$_GET["name"], 0777);
@$remove2 = unlink("./backups/".$_GET["name"]);
header("Location: admin.php?function=advanced_options&tab=backup");
}
//---[CZ]: záloha databáze
public function db_backup_aux($table) {
$sql=$this->db->query("select * from $table");
$num_fields=$this->db->num_fields($sql);
$numrow=$this->db->num_rows($sql);
if ($numrow!=0) {
  $result.="INSERT INTO ".$table." VALUES";
  $a=0;
  while($data=$this->db->fetch_array($sql)) {
    $a++;
    $result .= "(";
    for($i=0; $i<$num_fields; $i++) {
      $data[$i]=ereg_replace("\n","\\n",$data[$i]);
      $data[$i]=ereg_replace("\r","\\r",$data[$i]);
      $data[$i]=ereg_replace("'","''",$data[$i]);
      $result.=((isset($data[$i]))?((mysql_field_type($sql, $i)=="int")?$data[$i]:"'".$data[$i]."'"):"''");
      if ($i<($num_fields-1)) { $result .= ", "; }
    }
    if ($a!=$numrow) { $result .= "),"; }
  }
  $result .= ");\n";
}
return $result;
}
//---[CZ]: záloha databáze
public function db_backup() {
$array=array("articles", "bans", "columns", "comments", "files", "images", "images_sections", "pm", "polls", "polls_data", "sections", "users", "variables", "series");
foreach ($array as $name) { $content.=$this->db_backup_aux("terr_".$name);}
$time=time();
$file=fopen("backups/db_".$time.".sql", "w+");
fwrite($file, $content);
fclose($file);
$newZip=fopen("backups/db_".$time.".zip", "a");
fclose($newZip);
$zip = new ZipArchive();
if ($zip->open('backups/db_'.$time.'.zip')===TRUE) {
  $zip->addFile("backups/db_".$time.".sql", "db_".$time.".sql");
}
$zip->close();
@$remove2 = chmod("./backups/db_".$time.".sql", 0777);
@$remove2 = unlink("./backups/db_".$time.".sql");
}
//---[CZ]: záloha ftp
public function ftp_backup() {
  $newZip = fopen("backups/ftp_".time().".zip", "a");
  fclose($newZip);
  $zip = new ZipArchive();
  if ($zip->open('backups/ftp_'.time().'.zip')===TRUE) {
    $zip->addFile("./config.php", "config.php");
    $dirs = array(1 => "avatars/", 2 => "backups/", 3 => "files/", 4 => "gallery/", 5 => "templates/", 6 => "themes/");
    for ($i=1;$i<>7;$i++) {
      $way=opendir("./$dirs[$i]");
      while ($file=readdir($way)) {
        if ($file!=".." && $file!=".") { $zip->addFile("./".$dirs[$i].$file, $dirs[$i].$file); }
      }
    }
    $zip->close();
  }
}
}
?>