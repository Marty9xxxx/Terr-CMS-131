<?php
class Images {
private $db;
private $urls;
private $status;
private $config_variables;

public function __construct() {
$this->db = new Db_layer();
$this->urls = new Urls();
$this->status = new Status_messages();
$this->config_variables = new Config_variables();
}
//---[CZ]: nahrání obrázku
public function upload() {
for ($m=0;$m<>count($_FILES["file"]["name"]);$m++) {
if (!isset($_FILES["file"]["name"][$m])) { $_FILES["file"]["name"][$m]=""; }
if ($_FILES["file"]["name"][$m]!="" AND $_POST["gallery"]!="") {
$last=strrpos($_FILES["file"]["name"][$m], ".");
$name=substr($_FILES["file"]["name"][$m], 0, $last);
$conname=$this->urls->transfer_to_seo($name);
$ext=substr($_FILES["file"]["name"][$m], $last+1);
$check=$this->db->result($this->db->query("SELECT COUNT(id) FROM terr_images WHERE section='".$_POST["gallery"]."' AND file='".substr($conname, 0, 22).".".strtolower($ext)."'"));
if ($check==0) {
if ($ext=="jpg" OR $ext=="JPG") { $img_data=imagecreatefromjpeg($_FILES["file"]["tmp_name"][$m]); }
if ($ext=="png" OR $ext=="PNG") { $img_data=imagecreatefrompng($_FILES["file"]["tmp_name"][$m]); }
if ($ext=="gif" OR $ext=="GIF") { $img_data=imagecreatefromgif($_FILES["file"]["tmp_name"][$m]); }
$ok=move_uploaded_file($_FILES["file"]["tmp_name"][$m], "./gallery/".substr($conname, 0, 22).".".strtolower($ext));
if ($ok) {
$create=imagecreatetruecolor(120, 90);
if (imagesx($img_data)>imagesy($img_data)) {
$i=90/imagesy($img_data);
$new_width=$i*imagesx($img_data);
imagecopyresampled($create, $img_data, 0, 0, 0, 0, $new_width, 90, imagesx($img_data), imagesy($img_data));
imagejpeg($create, "./gallery/thumbs/".substr($conname, 0, 22).".jpg", 90);
}
else {
$i=120/imagesx($img_data);
$new_height=$i*imagesy($img_data);
imagecopyresampled($create, $img_data, 0, 0, 0, 0, 120, $new_height, imagesx($img_data), imagesy($img_data));
imagejpeg($create, "./gallery/thumbs/".substr($conname, 0, 22).".jpg", 90);
}
$sql=$this->db->query("INSERT INTO terr_images (section, file, added, title, uploader) values (".intval($_POST["gallery"]).", '".substr($conname, 0, 22).".".strtolower($ext)."', ".time().", '".substr($name, 0, 22)."', ".$_SESSION["user_id"].")");
imagedestroy($img_data);
}
$this->status->print_success(LANG_ACTION_WAS_SUCCESSFULLY_COMPLETED);
}
else { $this->status->print_error(LANG_THIS_FILE_ALREADY_EXISTS); }
}
else { $this->status->print_error(LANG_YOU_HAVE_NOT_FILLED_SOME_ITEMS); }
}
}
//---[CZ]: smazání galerie
public function delete_gallery() {
$sql=$this->db->query("SELECT file FROM terr_images WHERE section=".intval($_GET["id"]));
while(list($file)=$this->db->fetch_row($sql)) {
unlink("./gallery/".$file);
$thumb=explode(".", $file);
unlink("./gallery/thumbs/".$thumb[0].".jpg");
}
$sql=$this->db->query("DELETE FROM terr_images WHERE section=".intval($_GET["id"]));
$sql=$this->db->query("DELETE FROM terr_images_sections WHERE id=".intval($_GET["id"]));
$this->status->print_success(LANG_ACTION_WAS_SUCCESSFULLY_COMPLETED);
}
//---[CZ]: smazání obrázku
public function delete_image() {
unlink("./gallery/".$_GET["file"]);
$thumb=explode(".", $_GET["file"]);
unlink("./gallery/thumbs/".$thumb[0].".jpg");
$sql=$this->db->query("DELETE FROM terr_images WHERE file='".$this->db->escape($_GET["file"])."'");
$this->status->print_success(LANG_ACTION_WAS_SUCCESSFULLY_COMPLETED);
}
//---[CZ]: editace galerie
public function edit_gallery() {
if ($_POST["gallery"]!="") {
if (!isset($_POST["public"])) { $_POST["public"]=0; }
$quick=$this->urls->transfer_to_seo($_POST["gallery"]);
$old_quick=$this->db->result($this->db->query("SELECT quick FROM terr_images_sections WHERE id=".intval($_GET["id"])));
if ($old_quick!=$quick) {
$check=$this->db->result($this->db->query("SELECT count(id) FROM terr_images_sections WHERE quick='".$this->db->escape($quick)."'"));
}
else { $check=0; }
if ($check==0) {
$sql=$this->db->query("UPDATE terr_images_sections SET quick='".$this->db->escape($quick)."', name='".$this->db->escape($_POST["gallery"])."', public=".intval($_POST["public"])." WHERE id=".intval($_GET["id"]));
$this->status->print_success(LANG_ACTION_WAS_SUCCESSFULLY_COMPLETED);
}
else { $this->status->print_error(LANG_THIS_GALLERY_ALREADY_EXISTS); }
}
else { $this->status->print_error(LANG_YOU_HAVE_NOT_FILLED_SOME_ITEMS); }
}
//---[CZ]: editace obrázku
public function edit_image() {
$sql=$this->db->query("UPDATE terr_images SET title='".$this->db->escape($_POST["title"])."', section=".intval($_POST["gallery"]).", uploader=".intval($_POST["uploader"])." WHERE id=".intval($_GET["id"]));
$this->status->print_success(LANG_ACTION_WAS_SUCCESSFULLY_COMPLETED);
}
//---[CZ]: načteme galerie pro selectbox
public function load_galleries($current) {
$sql=$this->db->query("SELECT id, name FROM terr_images_sections ORDER BY name");
while(list($id, $name)=$this->db->fetch_row($sql)) {
echo "<option value=\"$id\"";
if ($current==$id) { echo " selected=\"selected\""; }
echo ">".$this->db->unescape($name)."</option>\n";
}
}
//---[CZ]: přidání galerie
public function add_gallery() {
if ($_POST["gallery"]!="") {
$quick=$this->urls->transfer_to_seo($_POST["gallery"]);
$check=$this->db->result($this->db->query("SELECT count(id) FROM terr_images_sections WHERE quick='$quick'"));
if ($check==0) {
$sql=$this->db->query("INSERT INTO terr_images_sections (quick, name, public) values ('$quick', '".$this->db->escape($_POST["gallery"])."', '1')");
$this->status->print_success(LANG_ACTION_WAS_SUCCESSFULLY_COMPLETED);
}
else { $this->status->print_error(LANG_THIS_GALLERY_ALREADY_EXISTS); }
}
else { $this->status->print_error(LANG_YOU_HAVE_NOT_FILLED_SOME_ITEMS); }
}
//---[CZ]: vypsání galerií
public function print_galleries() {
$print=$this->db->query("SELECT id, name, public FROM terr_images_sections ORDER by name");
while(list($id, $name, $public)=$this->db->fetch_row($print)) {
$count=$this->db->result($this->db->query("SELECT COUNT(id) FROM terr_images WHERE section=$id"));
echo "<tr>
<td>".$this->db->unescape($name)." (".$count.")</td>
<td style=\"text-align: center;\">".(($public==1)?LANG_YES:LANG_NO)."</td>
<td style=\"text-align: center;\"><input type=\"button\" class=\"yellowbutton\" value=\"".LANG_BROWSE."\" onclick=\"parent.location='./admin.php?function=images&amp;part=browse_gallery&amp;id=$id';\" /> <input type=\"button\" class=\"yellowbutton\" value=\"".LANG_EDIT."\" onclick=\"parent.location='./admin.php?function=images&amp;part=edit_gallery&amp;id=$id';\" /> <input type=\"button\" value=\"".LANG_DELETE."\" class=\"redbutton\" onclick=\"if (!confirm('".LANG_ARE_YOU_SURE."')) return false; parent.location='./admin.php?function=images&amp;action=delete_gallery&amp;id=$id';\" /></td>
</tr>\n";
}
}
//---[CZ]: vypsání miniatur obrázků
public function print_images() {
$i=0;
if ($_GET["function"]=="") { $location="index"; $on_one_row=3; } else { $location="admin"; $on_one_row=4; }
if ($_GET["function"]=="my_content") {
$where="uploader=".$_SESSION["user_id"];
} else {
if ($location=="admin") {
$where="section=".intval($_GET["id"]);
} else {
$sql=$this->db->query("SELECT id FROM terr_images_sections WHERE quick='".$this->db->escape($_GET["gallery"])."'");
$sql=$this->db->fetch_array($sql);
$where="section=".$sql["id"];
}
}
$print=$this->db->query("SELECT id, file, added, title, section, uploader FROM terr_images WHERE $where ORDER BY ".(($this->config_variables->get("sort_images_by_name")==1)?"title ASC":"added DESC"));
while(list($id, $file, $added, $title, $section, $uploader)=$this->db->fetch_row($print)) {
if ($location=="admin") { $me=$this->db->result($this->db->query("SELECT rights FROM terr_users WHERE id=".$_SESSION["user_id"])); }
$user=$this->db->query("SELECT login, rights FROM terr_users WHERE id=$uploader");
$user=$this->db->fetch_array($user);
$i++;
$filename=explode(".", $file);
$size=getimagesize("./gallery/".$file);
$gallery=$this->db->result($this->db->query("SELECT name FROM terr_images_sections WHERE id=$section"));
echo "<div class=\"gallery-cell\"".(($location=="index" AND $i==1)?" style=\"margin-left: 0px;\"":"").">
<span class=\"bubble\">";
if ($title!="" AND $location=="admin") { echo "<strong>".LANG_TITLE.":</strong> $title<br />"; }
if ($location=="index") { echo "<strong>".LANG_FILE.":</strong> $file<br />"; }
echo "<strong>".LANG_UPLOADER.":</strong> ".$user["login"]." (".date("j.n.Y H:i", $added).")<br /><strong>".LANG_SIZE.":</strong> ".round(filesize("./gallery/$file")/1024)." kB (".$size[0]."x".$size[1].")</span>";
if ($location=="admin" AND $me>=$user["rights"]) { echo "<input type=\"button\" class=\"yellowbutton\" value=\"".LANG_EDIT."\" onclick=\"parent.location='./admin.php?function=images&amp;part=edit_image&amp;id=$id';\" /> <input type=\"button\" class=\"redbutton\" value=\"".LANG_DELETE."\" onclick=\"if (!confirm('".LANG_ARE_YOU_SURE."')) return false; parent.location='./admin.php?function=".$_GET["function"]."&amp;tab=".$_GET["tab"]."&amp;part=browse_gallery&amp;id=$section&amp;action=delete_image&amp;file=".rawurlencode($file)."';\" /><br /><span class=\"desc\">"; }
echo "<a href=\"".$this->urls->root()."gallery/$file\"";
if ($location=="index") { echo " rel=\"lightbox-".$gallery."\""; }
else { echo " rel=\"lightbox\""; }
echo "><img src=\"".$this->urls->root()."gallery/thumbs/".$filename[0].".jpg\" alt=\"$file\" width=\"120\" height=\"90\" /></a>";
if ($location=="admin") { echo "<br /><strong>$file</strong></span>"; } elseif ($title!="") { echo "<br /><span class=\"small-text\">$title</span>"; }
echo "</div>\n\n";
if ($i==$on_one_row) { $i=0; echo "<div class=\"float-ending\"><!-- --></div>\n\n"; }
}
if ($i!=$on_one_row) { echo "<div class=\"float-ending\"><!-- --></div>\n\n"; }
if ($this->db->num_rows($print)==0) { echo "<p style=\"text-align: center;\"><em>".LANG_NO_IMAGES_IN_THIS_GALLERY."</em></p>"; }
}
//---[CZ]: získání dat pro editaci
public function read($column, $table) {
return $this->db->unescape($this->db->result($this->db->query("SELECT $column FROM terr_$table WHERE id=".intval($_GET["id"]))));
}
//---[CZ]: načteme uživatele
public function load_users($current) {
$sql=$this->db->query("SELECT id, login FROM terr_users WHERE rights>0 ORDER by login");
while(list($id, $login)=$this->db->fetch_row($sql)) {
echo "<option value=\"$id\"".(($current==$id)?" selected=\"selected\"":"").">".$this->db->unescape($login)."</option>\n";
}
}
}
?>