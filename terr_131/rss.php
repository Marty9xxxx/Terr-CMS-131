<?php
error_reporting(E_ALL);

//---[CZ]: složka s třídami
define("CLASSES_PATH", "components/classes/");

//---[CZ]: načteme soubor s DB údaji
include "config.php";

//---[CZ]: načteme potřebné třídy
include CLASSES_PATH."Db_layer_".DB_TYPE.".php";
include CLASSES_PATH."Config_variables.php";
include CLASSES_PATH."Urls.php";

$db=new Db_layer();
$config_variables=new Config_variables();
$urls=new Urls();

//---[CZ]: zvolíme typ URL
define("URL_TYPE", $config_variables->get("url_type"));

header("Expires: ".gmdate("D, d M Y H:i:s")." GMT");
header("Last-modified: ".gmdate("D, d M Y H:i:s")." GMT");
header("Content-type: application/rss+xml");

echo "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
<channel>
<title><?php echo $config_variables->get("sitename"); ?></title>
<link>http://<?php echo $_SERVER["SERVER_NAME"]; ?></link>
<description><?php echo $config_variables->get("meta_desc"); ?></description>
<lastBuildDate><?php echo gmdate("D, d M Y H:i:s")." GMT"; ?></lastBuildDate>
<atom:link href="http://<?php echo $_SERVER["SERVER_NAME"]; ?>/rss.php" rel="self" type="application/rss+xml" />
<docs>http://backend.userland.com/rss</docs>
<language><?php echo $config_variables->get("lang"); ?></language><?php

$print=$db->query("SELECT quick, title, perex, published, section FROM terr_articles WHERE published!=0 AND published<=".time()." ORDER by top DESC, published DESC, type DESC LIMIT 0,10");
while(list($quick, $title, $perex, $published, $section)=$db->fetch_row($print)) {
$section=$db->query("SELECT name FROM terr_sections WHERE id=$section");
$section=$db->fetch_array($section);
echo "\n<item>
<title>".$db->unescape($title)."</title>
<link>http://".$_SERVER["SERVER_NAME"].$urls->article($quick)."</link>
<guid>http://".$_SERVER["SERVER_NAME"].$urls->article($quick)."</guid>
<description>".strip_tags($db->unescape($perex))."</description>
<pubDate>".gmdate("D, d M Y H:i:s", $published)." GMT</pubDate>
<category>".$section["name"]."</category>
</item>";
}
?>
</channel>
</rss>