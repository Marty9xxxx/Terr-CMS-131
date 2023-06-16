<?php
class Login {
private $db;
private $status_messages;

public function __construct() {
$this->db = new Db_layer();
$this->status_messages = new Status_messages();
}
//---[CZ]: zjistíme práva přihlášeného uživatele
public function check_rights() {
if (isset($_COOKIE["user_check"])) {
$sql=$this->db->query("SELECT id, rights FROM terr_users WHERE user_check='".$this->db->escape($_COOKIE["user_check"])."'");
$sql=$this->db->fetch_array($sql);
if ($sql["id"]==1) { return 5; }
return $sql["rights"];
} else {
return 0;
}
}
//---[CZ]: zjistíme, zda má uživatel přístup do vybrané sekce administrace
public function check_access($section) {
if (isset($_COOKIE["user_check"])) {
$value=$this->db->result($this->db->query("SELECT value FROM terr_variables WHERE variable='rights_$section'"));
$sql2=$this->db->query("SELECT id, rights FROM terr_users WHERE user_check='".$this->db->escape($_COOKIE["user_check"])."'");
$sql2=$this->db->fetch_array($sql2);
if ($sql2["rights"]>=$value OR $sql2["id"]==1) { return 1; } else { return 0; }
} else {
return 0;
}
}
//---[CZ]: přihlášení
public function login() {
$nick_ban=$this->db->result($this->db->query("SELECT count(id) FROM terr_bans WHERE type='1' AND nick='".$this->db->escape($_POST["login"])."'"));
if ($nick_ban==0) {
$id=$this->db->result($this->db->query("SELECT id FROM terr_users WHERE login='".$this->db->escape($_POST["login"])."' AND password='".SHA1($_POST["password"])."'"));
if ($id!="") {
$check1=sha1(mt_rand(0, 1000000));
$check2=sha1(time());
$check=substr($check1, 1, 15)."".substr($check2, 1, 15);
$_SESSION["user_id"]=$id;
setcookie("user_check", $check, time()+3600*24*30*12);
$sql=$this->db->query("UPDATE terr_users SET lastvisit=".time().", user_check='$check' WHERE id=".$id);
header("Location: ".$_SERVER["HTTP_REFERER"]);
}
else { header("Location: ".$_SERVER["HTTP_REFERER"]); }
}
else { header("Location: ".$_SERVER["HTTP_REFERER"]); }
}
public function autologin() {
$sql=$this->db->query("SELECT id, login FROM terr_users WHERE user_check='".$this->db->escape($_COOKIE["user_check"])."'");
$sql=$this->db->fetch_array($sql);
$nick_ban=$this->db->result($this->db->query("SELECT count(id) FROM terr_bans WHERE type='1' AND nick='".$this->db->escape($sql["login"])."'"));
if ($nick_ban==0) {
if ($sql["id"]!="") {
$_SESSION["user_id"]=$sql["id"];
$sql=$this->db->query("UPDATE terr_users SET lastvisit=".time()." WHERE id=".$sql["id"]);
}
}
else { $this->logout(); }
}
//---[CZ]: odhlášení
public function logout() {
session_destroy();
setcookie("user_check", "", time()-1);
header("Location: ".$_SERVER["HTTP_REFERER"]);
}
}
?>