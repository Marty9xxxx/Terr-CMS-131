<?php
class Db_layer {
public function __construct() { global $mysqli; $this->mysqli=$mysqli; }
public function query($str) {return $this->mysqli->query($str);}
public function fetch_array($str) {return $str->fetch_array();}
public function fetch_assoc($str) {return $str->fetch_assoc();}
public function fetch_row($str) {return $str->fetch_row();}
public function num_rows($str) {return $str->num_rows;}
public function num_fields($str) {return $str->num_fields;}
public function escape($str) {return $this->mysqli->real_escape_string($str);}
public function unescape($str) {return stripslashes($str);}
public function result($str) {$row=$str->fetch_row(); return $row[0];}
public function ajaxQuery($str) {return $this->mysqli->query($str);}
}
?>