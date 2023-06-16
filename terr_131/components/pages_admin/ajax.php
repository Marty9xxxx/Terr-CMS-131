<?php
include "../../config.php";
include "../../components/classes/Db_layer_".DB_TYPE.".php";
include "../../components/classes/Ajax.php";
include "../../components/classes/Config_variables.php";
include "../../components/classes/Urls.php";

$ajax=new Ajax();
$config_variables=new Config_variables();
$urls=new Urls();

if ($_GET["action"]=="save_positions") { $ajax->save_positions(); }
if ($_GET["action"]=="save_col_positions") { $ajax->save_col_positions(); }
?>
