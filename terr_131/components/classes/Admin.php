<?php
class Admin {
public $db;
public $urls;
public $status_messages;
public $login;

public function __construct() {
  $this->db = new Db_layer();
  $this->urls = new Urls();
  $this->status_messages = new Status_messages();
  $this->login = new Login();
}
//---[CZ]: uložíme konfigurační proměnné
public function save($value, $variable) {
  $sql=$this->db->query("UPDATE terr_variables SET value='".$this->db->escape($value)."' WHERE variable='".$this->db->escape($variable)."'");
}
//---[CZ]: smazání článku
public function delete_article() {
  if ($_GET["id"]!="") {
    if ($this->login->check_access("archive")==0) { $sql=$this->db->query("DELETE FROM terr_articles WHERE author=".$_SESSION["user_id"]." && id=".intval($_GET["id"])); }
    if ($this->login->check_access("archive")==1) { $sql=$this->db->query("DELETE FROM terr_articles WHERE id=".intval($_GET["id"])); }
    if ($sql) { $sql=$this->db->query("DELETE FROM terr_comments WHERE article=".intval($_GET["id"])); }
    if ($sql) { $this->status_messages->print_success(LANG_ACTION_WAS_SUCCESSFULLY_COMPLETED); }
  }      
}
//---[CZ]: odkaz na profil autora
public function author_link($id) {  
  $sql=$this->db->query("SELECT login, realname FROM terr_users WHERE id=$id");
  $sql=$this->db->fetch_array($sql);
  if ($sql["realname"]!="") { return "<a href=\"".$this->urls->profile($sql["login"])."\" target=\"_blank\">".$this->db->unescape($sql["realname"])."</a>"; }
  if ($sql["realname"]=="") { return "<a href=\"".$this->urls->profile($sql["login"])."\" target=\"_blank\">".$this->db->unescape($sql["login"])."</a>"; }
}
//---[CZ]: sekce článku
public function article_section($id) {
  $name=$this->db->result($this->db->query("SELECT name FROM terr_sections WHERE quick=\"$id\""));  
  return "<a href=\"".$this->urls->section($id)."\" target=\"_blank\">".$this->db->unescape($name)."</a>";
}
}
?>