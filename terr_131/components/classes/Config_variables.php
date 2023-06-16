<?php
class Config_variables {
private $db;

public function __construct() { $this->db = new Db_layer(); }

public function get($row) {
$value=$this->db->result($this->db->query("SELECT value FROM terr_variables WHERE variable='$row'"));
${$row}=str_replace("&", "&amp;", ${$row}=$value);
${$row}=str_replace("&amp;copy;", "&copy;", ${$row}=$value);
${$row}=str_replace("&amp;amp;", "&amp;", ${$row}=$value);
${$row}=$this->db->unescape(${$row}=$value);
return ${$row};
}
}
?>