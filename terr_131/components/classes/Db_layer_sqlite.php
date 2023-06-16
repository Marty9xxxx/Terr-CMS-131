<?php
class Db_layer {
public function query($str) {$str=str_replace(array("&&","||"),array("AND","OR"),$str); return sqlite_query(DB_HOST, $str);}
public function fetch_array($str) {return sqlite_fetch_array($str);}
public function fetch_assoc($str) {return sqlite_fetch_assoc($str);}
public function fetch_row($str) {return sqlite_fetch_array($str);}
public function num_rows($str) {return sqlite_num_rows($str);}
public function num_fields($str) {return sqlite_num_fields($str);}
public function escape($str) {return sqlite_escape_string($str);}
public function unescape($str) {return stripslashes($str);}
public function result($str) {return sqlite_fetch_single($str);}
public function ajaxQuery($str) {return sqlite_query(sqlite_open("../../components/sqlite/sqlite.db"), $str);}
}
?>