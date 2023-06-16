<?php
class Files {
private $db;
private $urls;
private $status;

public function __construct() {
$this->db = new Db_layer();
$this->urls = new Urls();
$this->status = new Status_messages();
}
//---[CZ]: nahrání souboru
public function upload() {
if (!isset($_FILES["file"]["name"])) { $_FILES["file"]["name"]=""; }
if ($_FILES["file"]["name"]!="") {
$last=strrpos($_FILES["file"]["name"], ".");
$name=$this->urls->transfer_to_seo(substr($_FILES["file"]["name"], 0, $last));
$ext=substr($_FILES["file"]["name"], $last);
$ok=0;
$check=$this->db->query("SELECT COUNT(*) AS c FROM terr_files WHERE file='".substr($name,0,22).$ext."'");
$check=$this->db->fetch_array($check);
if ($check["c"]==0) {
  move_uploaded_file($_FILES["file"]["tmp_name"], "./files/".substr($name,0,22).$ext);
  $sql=$this->db->query("INSERT INTO terr_files (file, added, uploader) values ('".substr($name,0,22).$ext."', ".time().", ".$_SESSION["user_id"].")");
  $this->status->print_success(LANG_ACTION_WAS_SUCCESSFULLY_COMPLETED);
}
else { $this->status->print_error(LANG_THIS_FILE_ALREADY_EXISTS); }
}
else { $this->status->print_error(LANG_YOU_HAVE_NOT_FILLED_SOME_ITEMS); }
}
//---[CZ]: smazání souboru
public function delete() {
unlink("./files/".$this->db->unescape(rawurldecode($_GET["file"])));
$sql=$this->db->query("DELETE FROM terr_files WHERE file='".$this->db->unescape(rawurldecode($_GET["file"]))."'");
$this->status->print_success(LANG_ACTION_WAS_SUCCESSFULLY_COMPLETED);
}
//---[CZ]: získání dat pro editaci
public function read($column) {
return $this->db->unescape($this->db->result($this->db->query("SELECT $column FROM terr_files WHERE id=".intval($_GET["id"]))));
}
//---[CZ]: editace souboru
public function edit() {
if ($_POST["file"]!="") {
$old_filename=$this->db->query("SELECT file FROM terr_files WHERE id=".intval($_GET["id"]));
$old_filename=$this->db->fetch_array($old_filename);
if ($old_filename["file"]!=$_POST["file"]) {
$check=$this->db->query("SELECT id FROM terr_files WHERE file='".$this->db->escape($_POST["file"])."'");
$check=$this->db->fetch_array($check);
}
else { $check["id"]=""; }
if ($check["id"]=="") {
rename("./files/".$old_filename["file"], "./files/".$_POST["file"]);
$sql=$this->db->query("UPDATE terr_files SET file='".$this->db->escape($_POST["file"])."', uploader=".intval($_POST["uploader"])." WHERE id=".intval($_GET["id"]));
$this->status->print_success(LANG_ACTION_WAS_SUCCESSFULLY_COMPLETED);
}
else { $this->status->print_error(LANG_THIS_FILE_ALREADY_EXISTS); }
}
else { $this->status->print_error(LANG_YOU_HAVE_NOT_FILLED_SOME_ITEMS); }
}
//---[CZ]: načteme uživatele
public function load_users($current) {
$sql=$this->db->query("SELECT id, login FROM terr_users WHERE rights>0 ORDER by login");
while(list($id, $login)=$this->db->fetch_row($sql)) {
echo "<option value=\"$id\"";
if ($current==$id) { echo " selected=\"selected\""; }
echo ">".$this->db->unescape($login)."</option>\n";
}
}
//---[CZ]: vypsání souborů
public function print_files() {
if ($_GET["function"]=="my_content") { $where=" WHERE uploader=".$_SESSION["user_id"]; } else { $where=""; }
$sql=$this->db->query("SELECT id, file, added, uploader FROM terr_files$where ORDER by added DESC");
while(list($id, $file, $added, $uploader)=$this->db->fetch_row($sql)) {
$user=$this->db->query("SELECT login FROM terr_users WHERE id=$uploader");
$user=$this->db->fetch_array($user);
echo "<tr>
<td><a href=\"/files/".$this->db->unescape($file)."\">".$this->db->unescape($file)."</a></td>
<td><span class=\"small-text\">".$user["login"]." &bull; ".date("d.m.Y, H:i", $added)." &bull; ".round(filesize("./files/$file")/1024)." kB</span></td>
<td style=\"text-align: center;\"><input type=\"button\" class=\"yellowbutton\" value=\"".LANG_EDIT."\" onclick=\"parent.location='./admin.php?function=files&amp;part=edit&amp;id=$id';\" /><input type=\"button\" value=\"".LANG_DELETE."\" class=\"redbutton\" onclick=\"if (!confirm('".LANG_ARE_YOU_SURE."')) return false; parent.location='./admin.php?function=".$_GET["function"]."&amp;tab=".$_GET["tab"]."&amp;action=delete_file&amp;file=".$this->db->unescape(rawurlencode($file))."';\" /></td>
</tr>\n";
}
}
}
?>