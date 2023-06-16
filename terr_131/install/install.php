<?php
error_reporting(0);

$version="1.3.1";
$prev_version="1.3.0";

@$existconf=file_exists("../config.php");
if ($existconf) {
  if (!file_exists("../components/sqlite/sqlite.db")) { include "../config.php"; } else { define("DB_TYPE", "sqlite"); define("DB_HOST", sqlite_open("../components/sqlite/sqlite.db")); }
  include "./upgrading.php"; 
  include "../components/classes/Db_layer_".DB_TYPE.".php";
  
  $up=new Upgrading($prev_version,$version);

  $lang=$up->get_value("lang");
}
else {
  $lang=(($_GET["lang"]=="")?"cs":$_GET["lang"]);

  include "./installing.php";   
  $in=new Installing($lang,$version);
}

include $lang.".php";

?>
<!DOCTYPE html>
<html lang=<?php echo $lang; ?>>
<head>
<meta charset=utf-8 />
<meta name=author content="Powered by TerrCMS v<?php echo $version; ?>, (c) Michal Lepíček 2011-2012 (original core - K:CMS)" />
<meta name=robots content="noindex, nofollow" />
<link rel=stylesheet href=./install.css type=text/css />
<title>TerrCMS - <?php if ($existconf) { echo LANG_UPDATE; } else { echo LANG_INSTALLATION; } ?></title>
</head>
<body>
<?php 
if ($existconf) {
  if (isset($_POST["submit"])) { $up->update(); }
  else { $up->get_page(); }
}
else {
  if (isset($_POST["submit"])) { $in->install(); } 
  else { $in->get_page(); } 
} 
?>
<!--<endora>-->
</body>
</html>