<?php
class Columns extends Admin {
public function __construct() { parent::__construct(); }

//---[CZ]: smazání položky sloupce
public function delete_column_item() {
$sql=$this->db->query("DELETE FROM terr_columns WHERE id=".intval($_GET["id"]));
if ($sql) { $this->status_messages->print_success(LANG_ACTION_WAS_SUCCESSFULLY_COMPLETED); } else { $this->status_messages->print_error(LANG_AN_ERROR_OCCURED); }
}
//---[CZ]: editace položky sloupce
public function edit_column_item() {
if ($_POST["name"]!="") {
  if (!isset($_POST["hidden"])) { $_POST["hidden"]=0; }
  $sql=$this->db->query("UPDATE terr_columns SET name='".$this->db->escape($_POST["name"])."', content='".$this->db->escape($_POST["itemcontent"])."', hidden=".$this->db->escape($_POST["hidden"])." WHERE id=".intval($_GET["id"]));
  if ($sql) { $this->status_messages->print_success(LANG_ACTION_WAS_SUCCESSFULLY_COMPLETED); }
}
else { $this->status_messages->print_error(LANG_YOU_HAVE_NOT_FILLED_SOME_ITEMS); }
}
//---[CZ]: získání dat pro editaci
public function get($column) {
return $this->db->unescape($this->db->result($this->db->query("SELECT $column FROM terr_columns WHERE id=".intval($_GET["id"]))));
}
public function output_columns($column) {
$sql=$this->db->query("SELECT id, name, position, hidden FROM terr_columns WHERE col='".$column."' ORDER BY position");
while($data=$this->db->fetch_array($sql)) {
  $i++;
  echo (($data["hidden"]==1)?"<tr id=\"".$data["id"]."\" style=\"background: #f0f0f0;\">":"<tr id=\"".$data["id"]."\">")."
  <td style=\"text-align: center;\" class=\"dragHandle\"> &#8597; </td>
  <td>".$this->db->unescape($data["name"])."</td>";
  echo "<td style=\"text-align: center;\"><input type=\"button\" value=\"".LANG_EDIT."\" onclick=\"parent.location='./admin.php?function=columns&amp;part=edit&amp;id=".$data["id"]."';\" /> <input type=\"button\" class=\"redbutton\" value=\"".LANG_DELETE."\" onclick=\"if (!confirm('".LANG_ARE_YOU_SURE."')) return false; parent.location='./admin.php?function=columns&amp;action=delete&amp;id=".$data["id"]."';\" /></td></tr>\n";
}
}
//---[CZ]: přidání položky do sloupce
public function add_column_item() {
if ($_POST["name"]!="") {
  $position=$this->db->query("SELECT position, col, name FROM terr_columns WHERE col='".$this->db->escape($_POST["column"])."' ORDER BY position DESC");
  $position=$this->db->fetch_array($position);
  $position=$position["position"]+1;
  $sql=$this->db->query("INSERT INTO terr_columns (col, name, position) values ('".$this->db->escape($_POST["column"])."', '".$this->db->escape($_POST["name"])."', $position)");
  $this->status_messages->print_success(LANG_ACTION_WAS_SUCCESSFULLY_COMPLETED);
}
else { $this->status_messages->print_error(LANG_YOU_HAVE_NOT_FILLED_SOME_ITEMS); }
}
}
?>