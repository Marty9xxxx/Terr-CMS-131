<?php
class Registration extends Index {
public function __construct() { parent::__construct(); }

//---[CZ]: registrace
public function register() {
if (substr(md5($_POST["hash"]), 0, 5)==$_POST["check"]) {
if (preg_match("/^[0-9a-zA-Z]{3,128}$/", $_POST["login"])) {
if ($this->db->fetch_array($this->db->query("SELECT id FROM terr_bans WHERE type='1' AND nick='".$this->db->escape($_POST["login"])."'"))=="") {
if ($_POST["password1"]!="" AND $_POST["password1"]==$_POST["password2"] AND ($_POST["mail"]!="" OR $this->config_variables->get("mail_required")==0)) {
$check=$this->db->result($this->db->query("SELECT count(id) FROM terr_users WHERE login='".$this->db->escape($_POST["login"])."'"));
if ($check==0) {
$sql=$this->db->query("INSERT INTO terr_users (login, password, regdate, mail_messages, mail) values ('".$this->db->escape($_POST["login"])."', '".SHA1($_POST["password1"])."', ".time().", 1, '".$this->db->escape($_POST["mail"])."')");
$this->status_messages->print_success(LANG_ACTION_WAS_SUCCESSFULLY_COMPLETED);
} else { $this->status_messages->print_error(LANG_THIS_USER_ALREADY_EXISTS); }
} else { $this->status_messages->print_error(LANG_YOU_HAVE_NOT_FILLED_SOME_ITEMS); }
} else { $this->status_messages->print_error(LANG_THIS_USERNAME_IS_NOT_ACCESSIBLE); }
} else { $this->status_messages->print_error(LANG_SOME_ITEMS_HAVE_INCORRECT_FORMAT); }
} else { $this->status_messages->print_error(LANG_YOU_HAVE_ENTERED_BAD_CONTROL_CODE); }
}
//---[CZ]: vypsání stránky
public function get_page() {
if (!isset($_SESSION["user_id"])) {
if (isset($_POST["submit"])) { $this->register(); }
echo "<article><h3>".LANG_REGISTRATION."</h3>\n\n<form action=\"".$this->urls->fction("registration")."\" name=\"register\" id=\"reg\" method=\"post\">
<p><strong>".LANG_USERNAME.":</strong></p>
<input type=\"text\" name=\"login\" style=\"width: 200px;\" />
".(($this->config_variables->get("mail_required")==1)?"<p><strong>".LANG_EMAIL.":</strong></p><input type=\"mail\" name=\"mail\" style=\"width: 200px;\" />":"")."
<p><strong>".LANG_PASSWORD.":</strong></p>
<input type=\"password\" name=\"password1\" style=\"width: 200px;\" />
<p><strong>".LANG_PASSWORD_AGAIN.":</strong></p>
<input type=\"password\" name=\"password2\" style=\"width: 200px;\" />
<p><strong>".LANG_RETYPE_THIS_TEXT."</strong>:</p>";
$this->captcha->create(1);
echo "<br /><input type=\"submit\" name=\"submit\" value=\"".LANG_REGISTER."\" /></form></article>";
}
else {
  echo "<article><h3>".LANG_REGISTRATION."</h3><p>".LANG_YOU_CANNOT_SIGN_UP."</p></article>";
}
}
}
?>