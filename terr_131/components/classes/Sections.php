<?php
class Sections extends Admin {
public function __construct() { parent::__construct(); }

//---[CZ]: přidání sekce
public function add_section() {
if ($_POST["name"]!="") {
$quick=$this->urls->transfer_to_seo($_POST["name"]);
if (isset($_GET["id"])) { $higher=intval($_GET["id"]); } else { $higher=0; }
$check=$this->db->result($this->db->query("SELECT count(id) FROM terr_sections WHERE quick='".$this->db->escape($quick)."'"));
if ($check==0) {
if (isset($_GET["id"])) {
$level=$this->db->query("SELECT level FROM terr_sections WHERE id=".intval($_GET["id"]));
$level=$this->db->fetch_array($level);
$level=$level["level"]+1;
}
else { $level=0; }
$count=$this->db->result($this->db->query("SELECT count(id) FROM terr_sections"));
if ($count==0) { $position=1; } else { $position=$this->db->result($this->db->query("SELECT position FROM terr_sections ORDER BY position DESC"))+1; }
$sql=$this->db->query("INSERT INTO terr_sections (quick, name, position, level, higher) values ('".$this->db->escape($quick)."', '".$this->db->escape($_POST["name"])."', $position, $level, $higher)");
$this->status_messages->print_success(LANG_ACTION_WAS_SUCCESSFULLY_COMPLETED);
}
else { $this->status_messages->print_error(LANG_THIS_SECTION_ALREADY_EXISTS); }
}
else { $this->status_messages->print_error(LANG_YOU_HAVE_NOT_FILLED_SOME_ITEMS); }
}
//---[CZ]: smazání
public function delete_section() {
$sql=$this->db->query("DELETE FROM terr_sections WHERE id=".intval($_GET["id"]));
$this->delete_subsections(intval($_GET["id"]));
if ($sql) { $this->status_messages->print_success(LANG_ACTION_WAS_SUCCESSFULLY_COMPLETED); }
else { $this->status_messages->print_error(LANG_AN_ERROR_OCCURED); }
}
public function delete_subsections($higher) {
$sql=$this->db->query("SELECT id FROM terr_sections WHERE higher=$higher");
while(list($id)=$this->db->fetch_row($sql)) {
$sql=$this->db->query("DELETE FROM terr_sections WHERE id=$id");
$this->delete_subsections($id);
}
}
//---[CZ]: editace sekce
public function edit_section() {
if ($_POST["name"]!="") {
$quick=$this->urls->transfer_to_seo($_POST["name"]);
$old_quick=$this->db->result($this->db->query("SELECT quick FROM terr_sections WHERE id=".intval($_GET["id"])));
if ($old_quick!=$quick) { $check=$this->db->result($this->db->query("SELECT count(id) FROM terr_sections WHERE quick='".$this->db->escape($quick)."'")); }
if (!isset($_POST["highlight"])) { $_POST["highlight"]=0; }
if (!isset($_POST["ntbl"])) { $_POST["ntbl"]=0; }
if (!isset($_POST["hidden"])) { $_POST["hidden"]=0; }
if (!isset($_POST["module"])) { $_POST["module"]=0; }
if ($check==0) {
$sql=$this->db->query("UPDATE terr_sections SET quick='".$this->db->escape($quick)."', name='".$this->db->escape($_POST["name"])."', highlight=".intval($_POST["highlight"]).", outlink='".$this->db->escape($_POST["outlink"])."', ntbl=".intval($_POST["ntbl"]).", hidden=".intval($_POST["hidden"]).", module=".intval($_POST["module"])." WHERE id=".intval($_GET["id"]));
$this->status_messages->print_success(LANG_ACTION_WAS_SUCCESSFULLY_COMPLETED);
}
else { $this->status_messages->print_error(LANG_THIS_SECTION_ALREADY_EXISTS); }
}
else { $this->status_messages->print_error(LANG_YOU_HAVE_NOT_FILLED_SOME_ITEMS); }
}
// vypsání sekcí
public function output_sections($higher) {
$i=0;
$sql=$this->db->query("SELECT id, name, highlight, outlink, position, level, hidden, module FROM terr_sections WHERE higher=".intval($higher)." ORDER by position");
while(list($id, $name, $highlight, $outlink, $position, $level, $hidden, $module)=$this->db->fetch_row($sql)) {
if ($i==0) { $style[0] = " style=\"width: 8%;\""; $style[1] = " style=\"width: 44%;\""; $style[2] = " width: 9%;"; $style[3] = " width: 8%;"; $style[4] = " width: 23%;"; $style[5] = " width: 8%;"; }
$i++;
echo (($hidden==1)?"<tr id=\"$id\" style=\"background: #f0f0f0;\">":"<tr id=\"$id\">")."<td$style[0] class=\"dragHandle\"> &#8597; </td>
<td".(($level!=0)?" style=\"padding-left: ".($level*15)."px;".substr($style[1],8,11)."\">&raquo;&nbsp;&nbsp;":"$style[1]>").$this->db->unescape($name)."</td>
<td style=\"text-align: center;$style[5]\">".(($module==1)?LANG_YES:LANG_NO)."</td>
<td style=\"text-align: center;$style[2]\">".(($highlight==1)?LANG_YES:LANG_NO)."</td>
<td style=\"text-align: center;$style[3]\">".(($outlink!="")?LANG_YES:LANG_NO)."</td>
<td style=\"text-align: center;$style[4]\"><input type=\"button\" class=\"yellowbutton\" value=\"".LANG_EDIT."\" onclick=\"parent.location='./admin.php?function=sections&amp;part=edit&amp;id=$id';\" /> <input type=\"button\" class=\"redbutton\" value=\"".LANG_DELETE."\" onclick=\"if (!confirm('".LANG_ARE_YOU_SURE."')) return false; parent.location='./admin.php?function=sections&amp;action=delete&amp;id=$id';\" /></td></tr>\n";
}
}
//---[CZ]: editace série
public function edit_series_item() {
if ($_POST["name"]!="") {
$check=$this->db->result($this->db->query("SELECT count(id) FROM terr_series WHERE id!=".intval($_GET["id"])." && name='".$this->db->escape($_POST["name"])."'"));
if ($check==0) {
$sql=$this->db->query("UPDATE terr_series SET name='".$this->db->escape($_POST["name"])."' WHERE id=".intval($_GET["id"]));
if ($sql) { $this->status_messages->print_success(LANG_ACTION_WAS_SUCCESSFULLY_COMPLETED); }
}
else { $this->status_messages->print_error(LANG_THIS_SERIES_ITEM_ALREADY_EXISTS); }
}
else { $this->status_messages->print_error(LANG_YOU_HAVE_NOT_FILLED_SOME_ITEMS); }
}
// vypsání sérií
public function output_series() {
$sql=$this->db->query("SELECT terr_series.id, terr_series.name, terr_series.added, terr_users.login FROM terr_series INNER JOIN terr_users ON terr_users.id=terr_series.author ORDER BY terr_series.name");
while($data=$this->db->fetch_array($sql)) {
$count=$this->db->result($this->db->query("SELECT count(id) FROM terr_articles WHERE series=".intval($data["id"])));
echo "<tr><td>".$this->db->unescape($data["name"])."</td><td style=\"text-align: center;\">".$data["login"]."</td><td class=\"small-text\" style=\"text-align: center;\">".date("d.m.Y | H:i", $data["added"])."</td><td style=\"text-align: center;\">$count</td>
<td style=\"text-align: center;\"><input type=\"button\" class=\"yellowbutton\" value=\"".LANG_RENAME."\" onclick=\"parent.location='./admin.php?function=sections&amp;tab=series&amp;part=edit&amp;id=".intval($data["id"])."';\" /> <input type=\"button\" class=\"redbutton\" value=\"".LANG_DELETE."\" onclick=\"if (!confirm('".LANG_ARE_YOU_SURE."')) return false; parent.location='./admin.php?function=sections&amp;tab=series&amp;action=delete&amp;id=".intval($data["id"])."';\" /></td></tr>";
}
}
//---[CZ]: smazání série
public function delete_series_item() {
$sql=$this->db->query("DELETE FROM terr_series WHERE id=".intval($_GET["id"]));
$sql2=$this->db->query("UPDATE terr_articles SET series=0 WHERE series=".intval($_GET["id"]));
if ($sql && $sql2) { $this->status_messages->print_success(LANG_ACTION_WAS_SUCCESSFULLY_COMPLETED); } else { $this->status_messages->print_error(LANG_AN_ERROR_OCCURED); }
}
//---[CZ]: přidání série
public function add_series_item() {
if ($_POST["name"]!="") {
$check=$this->db->result($this->db->query("SELECT count(id) FROM terr_series WHERE name='".$this->db->escape($_POST["name"])."'"));
if ($check==0) {
$sql=$this->db->query("INSERT INTO terr_series (name, author, added) values ('".$this->db->escape($_POST["name"])."', ".intval($_SESSION["user_id"]).", ".time().")");
if ($sql) { $this->status_messages->print_success(LANG_ACTION_WAS_SUCCESSFULLY_COMPLETED); }
}
else { $this->status_messages->print_error(LANG_THIS_SERIES_ITEM_ALREADY_EXISTS); }
}
else {$this->status_messages->print_error(LANG_YOU_HAVE_NOT_FILLED_SOME_ITEMS); }
}
//---[CZ]: získání dat pro editaci
public function get($column) {
if ($_GET["tab"]=="") { $table="terr_sections"; }
if ($_GET["tab"]=="series") { $table="terr_series"; }
return $this->db->unescape($this->db->result($this->db->query("SELECT $column FROM $table WHERE id=".intval($_GET["id"]))));
}
public function load_series($current) {
$sql=$this->db->query("SELECT id, name FROM terr_series ORDER BY name");
while(list($id, $name)=$this->db->fetch_row($sql)) {
echo "<option value=\"$id\"";
if ($id==$current) { echo " selected=\"selected\""; }
echo ">".$this->db->unescape($name)."</option>\n";
}
}
public function load_sections($higher, $current) {
$sql=$this->db->query("SELECT id, name, level FROM terr_sections WHERE higher=$higher ORDER BY position");
while(list($id, $name, $level)=$this->db->fetch_row($sql)) {
echo "<option value=\"$id\"";
if ($id==$current) { echo " selected=\"selected\""; }
echo ">";
for ($i=0; $i<$level; $i++) {
echo "&nbsp;&nbsp;&raquo;";
}
if ($level!=0) { echo "&nbsp;&nbsp;"; }
echo $this->db->unescape($name)."</option>\n";
$this->load_sections($id, $current);
}
}
}
?>