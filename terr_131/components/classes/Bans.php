<?php
class Bans extends Admin {
public function __construct() { parent::__construct(); }

//---[CZ]: přidání banu
public function add() {
if ($_POST["ip"]!="") {
  $sql=$this->db->query("INSERT INTO terr_bans (ip, type) values ('".$this->db->escape($_POST["ip"])."', '0')");
  if ($sql) { parent::$this->status_messages->print_success(LANG_ACTION_WAS_SUCCESSFULLY_COMPLETED); } else { parent::$this->status_messages->print_error(LANG_AN_ERROR_OCCURED); }
}
else { parent::$this->status_messages->print_error(LANG_YOU_HAVE_NOT_FILLED_SOME_ITEMS); }
}
//---[CZ]: smazání banu
public function delete() {
$sql=$this->db->query("DELETE FROM terr_bans WHERE type=0 && id=".intval($_GET["id"]));
if ($sql) { parent::$this->status_messages->print_success(LANG_ACTION_WAS_SUCCESSFULLY_COMPLETED); } else { parent::$this->status_messages->print_error(LANG_AN_ERROR_OCCURED); }
}
//---[CZ]: přidání nickBanu
public function add_nick() {
if ($_POST["nick"]!="") {
  $sql=$this->db->query("INSERT INTO terr_bans (nick, type) values ('".$this->db->escape($_POST["nick"])."', '1')");
  if ($sql) { parent::$this->status_messages->print_success(LANG_ACTION_WAS_SUCCESSFULLY_COMPLETED); } else { parent::$this->status_messages->print_error(LANG_AN_ERROR_OCCURED); }
}
else { parent::$this->status_messages->print_error(LANG_YOU_HAVE_NOT_FILLED_SOME_ITEMS); }
}
//---[CZ]: smazání nickBanu
public function delete_nick() {
$sql=$this->db->query("DELETE FROM terr_bans WHERE type=1 && id=".intval($_GET["id"]));
if ($sql) { parent::$this->status_messages->print_success(LANG_ACTION_WAS_SUCCESSFULLY_COMPLETED); } else { parent::$this->status_messages->print_error(LANG_AN_ERROR_OCCURED); }
}
//---[CZ]: vypsání
public function output() {
$sql=$this->db->query("SELECT id, ip FROM terr_bans WHERE type=0 ORDER BY id DESC");
while($data=$this->db->fetch_array($sql)) { echo "<tr><td>".$data["ip"]."</td><td style=\"text-align: center;\"><input type=\"button\" class=\"redbutton\" value=\"".LANG_DELETE."\" onclick=\"if (!confirm('".LANG_ARE_YOU_SURE."')) return false; parent.location='./admin.php?function=bans&amp;action=remove&amp;id=".$data["id"]."';\" /></td></tr>\n"; }
}
//---[CZ]: vypsání
public function nick_bans_output() {
$sql=$this->db->query("SELECT id, nick FROM terr_bans WHERE type=1 ORDER BY id DESC");
while($data=$this->db->fetch_array($sql)) { echo "<tr><td>".$data["nick"]."</td><td style=\"text-align: center;\"><input type=\"button\" class=\"redbutton\" value=\"".LANG_DELETE."\" onclick=\"if (!confirm('".LANG_ARE_YOU_SURE."')) return false; parent.location='./admin.php?function=bans&amp;tab=nick_bans&amp;action=remove_nick&amp;id=".$data["id"]."';\" /></td></tr>\n"; }
}
}
?>