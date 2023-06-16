<?php
class Status_messages {
private $db;

public function __construct() { $this->db = new Db_layer(); }

//---[CZ]: stavové zprávy pro index
public function create() {
if ($_GET["mt"]==0) { echo "<div id=\"popup-error\" onclick=\"this.style.display = 'none';\">".LANG_AN_ERROR_OCCURED."</div>"; }
if ($_GET["mt"]==1) { echo "<div id=\"popup-success\" onclick=\"close_status_message(this.id)\">".LANG_ACTION_WAS_SUCCESSFULLY_COMPLETED."</div>"; }
}
//---[CZ]: stavové zprávy pro admin - zelená
public function print_success($message) {
echo "<div id=\"popup-success\" onclick=\"this.style.display = 'none';\">$message</div>";
}
//---[CZ]: stavové zprávy pro admin - červená
public function print_error($message) {
echo "<div id=\"popup-error\" onclick=\"this.style.display = 'none';\">$message</div>";
}
}
?>