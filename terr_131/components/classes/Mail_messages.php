<?php
class Mail_messages extends Admin {
public function __construct() { parent::__construct(); }

//---[CZ]: odeslání mailů
public function send() {
if ($_POST["from"]!="" AND $_POST["subject"]!="" AND $_POST["text"]!="") {
$i=0;
$sql=$this->db->query("SELECT mail FROM terr_users WHERE mail!='' AND mail_messages=1");
while($data=$this->db->fetch_array($sql)) {
  $i++;
  mail($data["mail"], $_POST["subject"], $_POST["text"], "Content-Type: text/html; charset=\"utf-8\"\nFrom:".$_POST["from"]);
}
if ($sql) { parent::$this->status_messages->print_success(LANG_ACTION_WAS_SUCCESSFULLY_COMPLETED." (".$i."&times;)"); }
else { parent::$this->status_messages->print_error(LANG_AN_ERROR_OCCURED); }
}
else { parent::$this->status_messages->print_error(LANG_YOU_HAVE_NOT_FILLED_SOME_ITEMS); }
}
}
?>