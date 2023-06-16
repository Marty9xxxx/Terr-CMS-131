<?php
class Captcha { public $urls;
public function __construct() { $this->urls = new Urls(); }

//---[CZ]: vytvoříme hash
public function create() {
if (!isset($_POST["hash"])) { $_POST["hash"]=""; }
if (!isset($_POST["check"])) { $_POST["check"]=""; }
$check1=sha1(mt_rand(0, 1000000));
$check2=sha1(time());
$check=substr($check1, 1, 15)."".substr($check2, 1, 15);
$check=substr($check, 1,5);
echo "<input type=\"text\" name=\"check\" size=\"6\" class=\"float-left\" /><img src=\"".$this->urls->root()."components/pages_index/captcha.php?hash=$check\" alt=\"CAPTCHA\" /><input type=\"hidden\" name=\"hash\" value=\"$check\" />";
}
}
?>