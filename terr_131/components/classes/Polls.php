<?php
class Polls {
private $db;
private $urls;
private $status;

public function __construct() {
$this->db = new Db_layer();
$this->urls = new Urls();
$this->status = new Status_messages();
}
//---[CZ]: přidání sekce
public function add_poll() {
if ($_POST["name"]!="") {
if ($_GET["tab"]=="") { $type=1; }
if ($_GET["tab"]=="articles_polls") { $type=2; }
if (!isset($_POST["visible"])) { $_POST["visible"]=0; }
$sql=$this->db->query("INSERT INTO terr_polls (name, visible, type, author) values ('".$this->db->escape($_POST["name"])."', ".intval($_POST["visible"]).", $type, ".intval($_SESSION["user_id"]).")");
$poll=$this->db->result($this->db->query("SELECT id FROM terr_polls ORDER BY id DESC"));
if ($_POST["answer1"]!="") { $sql=$this->db->query("INSERT INTO terr_polls_data (poll, answer) values (".$poll["id"].", '".$this->db->escape($_POST["answer1"])."')"); }
if ($_POST["answer2"]!="") { $sql=$this->db->query("INSERT INTO terr_polls_data (poll, answer) values (".$poll["id"].", '".$this->db->escape($_POST["answer2"])."')"); }
if ($_POST["answer3"]!="") { $sql=$this->db->query("INSERT INTO terr_polls_data (poll, answer) values (".$poll["id"].", '".$this->db->escape($_POST["answer3"])."')"); }
if ($_POST["answer4"]!="") { $sql=$this->db->query("INSERT INTO terr_polls_data (poll, answer) values (".$poll["id"].", '".$this->db->escape($_POST["answer4"])."')"); }
if ($_POST["answer5"]!="") { $sql=$this->db->query("INSERT INTO terr_polls_data (poll, answer) values (".$poll["id"].", '".$this->db->escape($_POST["answer5"])."')"); }
$this->status->print_success(LANG_ACTION_WAS_SUCCESSFULLY_COMPLETED);
}
else { $this->status->print_error(LANG_YOU_HAVE_NOT_FILLED_SOME_ITEMS); }
}
//---[CZ]: přidání sekce
public function add_answer() {
if ($_POST["answer"]!="") {
$sql=$this->db->query("INSERT INTO terr_polls_data (poll, answer) values (".intval($_GET["poll_id"]).", '".$this->db->escape($_POST["answer"])."')");
$this->status->print_success(LANG_ACTION_WAS_SUCCESSFULLY_COMPLETED);
}
else { $this->status->print_error(LANG_YOU_HAVE_NOT_FILLED_SOME_ITEMS); }
}
//---[CZ]: smazání ankety
public function delete_poll() {
$sql=$this->db->query("DELETE FROM terr_polls WHERE id=".intval($_GET["poll_id"]));
$sql=$this->db->query("DELETE FROM terr_polls_data WHERE poll=".intval($_GET["poll_id"]));
$sql=$this->db->query("UPDATE terr_articles SET poll=0 WHERE poll=".intval($_GET["poll_id"]));
if ($sql) { $this->status->print_success(LANG_ACTION_WAS_SUCCESSFULLY_COMPLETED); }
else { $this->status->print_error(LANG_AN_ERROR_OCCURED); }
}
//---[CZ]: smazání odpovědi
public function delete_answer() {
$sql=$this->db->query("DELETE FROM terr_polls_data WHERE id=".intval($_GET["answer_id"]));
if ($sql) { $this->status->print_success(LANG_ACTION_WAS_SUCCESSFULLY_COMPLETED); }
else { $this->status->print_error(LANG_AN_ERROR_OCCURED); }
}
//---[CZ]: editace ankety
public function update_poll() {
if ($_POST["name"]!="") {
if (!isset($_POST["visible"])) { $_POST["visible"]=0; }
$sql=$this->db->query("UPDATE terr_polls SET name='".$this->db->escape($_POST["name"])."', visible=".intval($_POST["visible"])." WHERE id=".intval($_GET["poll_id"]));
$this->status->print_success(LANG_ACTION_WAS_SUCCESSFULLY_COMPLETED);
}
else { $this->status->print_error(LANG_YOU_HAVE_NOT_FILLED_SOME_ITEMS); }
}
//---[CZ]: editace odpovědi
public function update_answer() {
if ($_POST["answer"]!="") {
$sql=$this->db->query("UPDATE terr_polls_data SET answer='".$this->db->escape($_POST["answer"])."' WHERE id=".intval($_GET["answer_id"]));
$this->status->print_success(LANG_ACTION_WAS_SUCCESSFULLY_COMPLETED);
}
else { $this->status->print_error(LANG_YOU_HAVE_NOT_FILLED_SOME_ITEMS); }
}
//---[CZ]: vypsat ankety
public function output_polls() {
if ($_GET["tab"]=="") { $type=1; }
if ($_GET["tab"]=="articles_polls") { $type=2; }
if ($_GET["function"]=="my_content") { $where="type=2 AND author=".$_SESSION["user_id"]; }
else { $where="type=$type"; }
$sql=$this->db->query("SELECT id, name FROM terr_polls WHERE $where ORDER BY id DESC");
while($data=$this->db->fetch_array($sql)) {
echo "<tr>
<td>".$this->db->unescape($data["name"])."</td>
<td style=\"text-align: center;\"><input type=\"button\" class=\"yellowbutton\" value=\"".LANG_EDIT."\" onclick=\"parent.location='./admin.php?function=polls&amp;tab=".$_GET["tab"]."&amp;part=edit_poll&amp;poll_id=".$data["id"]."';\" /> <input type=\"button\" class=\"redbutton\" value=\"".LANG_DELETE."\" onclick=\"if (!confirm('".LANG_ARE_YOU_SURE."')) return false; parent.location='./admin.php?function=".$_GET["function"]."&amp;tab=".$_GET["tab"]."&amp;action=delete_poll&amp;poll_id=".$data["id"]."';\" /></td>
</tr>\n";
}
}
//---[CZ]: vypsat odpovědi
public function output_answers() {
$sql=$this->db->query("SELECT id, answer, votes FROM terr_polls_data WHERE poll=".intval($_GET["poll_id"])." ORDER BY id");
while($data=$this->db->fetch_array($sql)) {
echo "<form action=\"./admin.php?function=polls&amp;tab=".$_GET["tab"]."&amp;part=edit_poll&amp;poll_id=".$_GET["poll_id"]."&amp;action=edit_answer&amp;answer_id=".$data["id"]."\" method=\"post\">
<tr>
<td><input type=\"text\" name=\"answer\" value=\"".htmlspecialchars($this->db->unescape($data["answer"]))."\" style=\"width: 300px;\" /> <input type=\"text\" value=\"".$data["votes"]."\" style=\"width: 50px;\" disabled=\"disabled\" /></td>
<td style=\"text-align: center;\"><input type=\"submit\" name=\"submit_edit_answer\" class=\"greenbutton\" value=\"".LANG_SAVE_CHANGES."\" /> <input type=\"button\" class=\"redbutton\" value=\"".LANG_DELETE."\" onclick=\"if (!confirm('".LANG_ARE_YOU_SURE."')) return false; parent.location='./admin.php?function=polls&amp;tab=".$_GET["tab"]."&amp;part=edit_poll&amp;poll_id=".$_GET["poll_id"]."&amp;action=delete_answer&amp;answer_id=".$data["id"]."';\" /></td>
</tr>
</form>\n";
}
}
//---[CZ]: získání dat pro editaci
public function get($column) {
return $this->db->unescape($this->db->result($sql=$this->db->query("SELECT $column FROM terr_polls WHERE id=".intval($_GET["poll_id"]))));
}
public function load_polls($current) {
$sql=$this->db->query("SELECT id, name FROM terr_polls WHERE type=2 ORDER BY id DESC");
while(list($id, $name)=$this->db->fetch_row($sql)) {
echo "<option value=\"$id\"".(($id==$current)?" selected=\"selected\"":"").">".$this->db->unescape($name)."</option>\n";
}
}
}
?>