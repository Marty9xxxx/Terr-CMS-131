<?php
include "../../config.php";
include "../../components/classes/Db_layer_".DB_TYPE.".php";
include "../../components/classes/Ajax.php";
include "../../components/classes/Config_variables.php";
include "../../components/classes/Urls.php";

$ajax=new Ajax();
$config_variables=new Config_variables();
$urls=new Urls();

if ($_GET["action"]=="update_comment") { $ajax->update_comment(); }
if ($_GET["action"]=="save_comment") { $ajax->save_comment(); }
if ($_GET["action"]=="delete_comment") { $ajax->delete_comment(); }
if ($_GET["action"]=="hide_pm") { $ajax->hide_pm(); }
if ($_GET["action"]=="seen") { $ajax->seen(); }
if ($_GET["action"]=="reciever") { $ajax->reciever(); }
if ($_GET["action"]=="openID") { $ajax->openID(); }
if ($_GET["action"]=="check_login_nick") { $ajax->check_login_nick(); }
if ($_GET["action"]=="online_users_expand") { $ajax->online_users_expand(); }

?>