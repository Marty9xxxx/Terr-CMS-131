<?php
//---[CZ]: složka s třídami
define("CLASSES_PATH", "components/classes/");

//---[CZ]: načteme soubor s DB údaji
include "config.php";

//---[CZ]: definování proměnných
if (!isset($_GET["action"])) { $_GET["action"]=""; }
if (!isset($_GET["function"])) { $_GET["function"]="overview"; }
if (!isset($_GET["tab"])) { $_GET["tab"]=""; }
if (!isset($_GET["part"])) { $_GET["part"]=""; }

//---[CZ]: načteme potřebné třídy
include CLASSES_PATH."Db_layer_".DB_TYPE.".php";
include CLASSES_PATH."Admin.php";
include CLASSES_PATH."Login.php";
include CLASSES_PATH."Status_messages.php";
include CLASSES_PATH."Config_variables.php";
include CLASSES_PATH."Urls.php";

$admin=new Admin();
$login=new Login();
$config_variables=new Config_variables();
$urls=new Urls();
$status_messages=new Status_messages();
$version=$config_variables->get("version");

//---[CZ]: zvolíme typ URL
if ($_GET["function"]=="options" AND $_GET["tab"]=="" AND isset($_POST["submit"]) AND $login->check_access("options")==1 AND isset($_POST["url_type"])) { define("URL_TYPE", $_POST["url_type"]); }
else { define("URL_TYPE", $config_variables->get("url_type")); }

//---[CZ]: načteme jazykový soubor
if ($_GET["function"]=="options" AND $_GET["tab"]=="" AND isset($_POST["submit"]) AND $login->check_access("options")==1 AND isset($_POST["lang"])) { include ("languages/".$_POST["lang"].".php"); }
else { include ("languages/".$config_variables->get("lang").".php"); }

//---[CZ]: odstartujeme session
session_start();

//---[CZ]: přihlášení & odhlášení
if ($_GET["action"]=="login") { $login->login(); }
if (isset($_COOKIE["user_check"]) AND !isset($_SESSION["user_id"])) { $login->autologin(); }
if ($_GET["action"]=="logout") { $login->logout(); }

//---[CZ]: uložení konfiguračních proměnných
if ($_GET["function"]=="options" AND $_GET["tab"]=="" AND isset($_POST["submit"]) AND $login->check_access("options")==1) {
if (!isset($_POST["show_article_date"])) { $_POST["show_article_date"]=0; }
if (!isset($_POST["show_article_section"])) { $_POST["show_article_section"]=0; }
if (!isset($_POST["show_article_comments_number"])) { $_POST["show_article_comments_number"]=0; }
if (!isset($_POST["show_article_views_number"])) { $_POST["show_article_views_number"]=0; }
if (!isset($_POST["show_article_author"])) { $_POST["show_article_author"]=0; }
if (!isset($_POST["show_addthis"])) { $_POST["show_addthis"]=0; }
if (!isset($_FILES["favicon"]["name"])) { $_FILES["favicon"]["name"]=""; }

if ($_FILES["favicon"]["name"]!="") {
  $explode=explode(".", $_FILES["favicon"]["name"]);
  if ($explode[1]=="jpg" OR $explode[1]=="JPG" OR $explode[1]=="png" OR $explode[1]=="PNG" OR $explode[1]=="gif" OR $explode[1]=="GIF") {
    $ext = strtolower($explode[1]);
    switch ($ext) {
      case "gif": $img_data = imagecreatefromgif($_FILES["favicon"]["tmp_name"]); break;
      case "jpg": $img_data = imagecreatefromjpeg($_FILES["favicon"]["tmp_name"]); break;
      case "png": $img_data = imagecreatefrompng($_FILES["favicon"]["tmp_name"]); break;
      default: return false;
    }
    $create=imagecreatetruecolor(16, 16);
    imagecopyresampled($create, $img_data, 0, 0, 0, 0, 16, 16, imagesx($img_data), imagesy($img_data));
    imagejpeg($create, "./components/images/favicon/favicon.ico", 90);
    imagedestroy($img_data);
  }
}

$admin->save($_POST["lang"], "lang");
$admin->save($_POST["sitename"], "sitename");
$admin->save($_POST["login_for_comments"], "login_for_comments");
$admin->save($_POST["show_text"], "show_text");
$admin->save($_POST["sort_images_by_name"], "sort_images_by_name");
$admin->save($_POST["sort_comments_from_newest"], "sort_comments_from_newest");
$admin->save($_POST["articles_number"], "articles_number");
$admin->save($_POST["comments_number"], "comments_number");
$admin->save($_POST["theme"], "theme");
$admin->save($_POST["template"], "template");
$admin->save($_POST["use_emoticons"], "use_emoticons");
$admin->save($_POST["show_article_date"], "show_article_date");
$admin->save($_POST["show_article_section"], "show_article_section");
$admin->save($_POST["show_article_comments_number"], "show_article_comments_number");
$admin->save($_POST["show_article_views_number"], "show_article_views_number");
$admin->save($_POST["show_article_author"], "show_article_author");
$admin->save($_POST["show_addthis"], "show_addthis");
$admin->save($_POST["show_menu_with_last_comments"], "show_menu_with_last_comments");
$admin->save($_POST["show_menu_with_last_login"], "show_menu_with_last_login");
$admin->save($_POST["show_random_image"], "show_random_image");
$admin->save($_POST["show_recent_articles"], "show_recent_articles");
$admin->save($_POST["name_of_menu"], "name_of_menu");
$admin->save($_POST["name_of_hp"], "name_of_hp");
$admin->save($_POST["url_type"], "url_type");
$admin->save($_POST["show_the_link_to_open_whole_article"], "show_the_link_to_open_whole_article");
$admin->save($_POST["show_avatar_in_login_form"], "show_avatar_in_login_form");
$admin->save($_POST["show_unconfirmed_comments"], "show_unconfirmed_comments");
$admin->save($_POST["show_accounts_count"], "show_accounts_count");
$admin->save($_POST["mail_required"], "mail_required");
}

//---[CZ]: uložení meta tagů
if ($_GET["function"]=="options" AND $_GET["tab"]=="meta_tags" AND isset($_POST["submit"]) AND $login->check_access("options")==1) {
$admin->save($_POST["meta_copyright"], "meta_copyright");
$admin->save($_POST["meta_desc"], "meta_desc");
$admin->save($_POST["meta_keywords"], "meta_keywords");
}

//---[CZ]: uložení nastavení přístupu
if ($_GET["function"]=="options" AND $_GET["tab"]=="access" AND isset($_POST["submit"]) AND $login->check_access("options")==1) {
$admin->save($_POST["rights_overview"], "rights_overview");
$admin->save($_POST["rights_add_content"], "rights_add_content");
$admin->save($_POST["rights_edit_content"], "rights_edit_content");
$admin->save($_POST["rights_my_content"], "rights_my_content");
$admin->save($_POST["rights_images"], "rights_images");
$admin->save($_POST["rights_files"], "rights_files");
$admin->save($_POST["rights_sections"], "rights_sections");
$admin->save($_POST["rights_comments"], "rights_comments");
$admin->save($_POST["rights_unverified_articles"], "rights_unverified_articles");
$admin->save($_POST["rights_unpublished_articles"], "rights_unpublished_articles");
$admin->save($_POST["rights_archive"], "rights_archive");
$admin->save($_POST["rights_polls"], "rights_polls");
$admin->save($_POST["rights_options"], "rights_options");
$admin->save($_POST["rights_advanced_options"], "rights_advanced_options");
$admin->save($_POST["rights_users"], "rights_users");
$admin->save($_POST["rights_mail_messages"], "rights_mail_messages");
$admin->save($_POST["rights_bans"], "rights_bans");
$admin->save($_POST["admin3"], "admin3");
$admin->save($_POST["admin2"], "admin2");
$admin->save($_POST["admin1"], "admin1");
$admin->save($_POST["corrector"], "corrector");
$admin->save($_POST["redactor"], "redactor");
}
?>
<!DOCTYPE html>
<html lang="<?php echo $config_variables->get("lang"); ?>">
<head>
<meta charset="utf-8" />
<meta name="author" content="Powered by TerrCMS v<?php echo $config_variables->get("version"); ?>, (c) 2011-2013 Michal Lepíček (original core - K:CMS)" />
<meta name="robots" content="noindex, nofollow" />
<link rel="stylesheet" href="./themes/admin.css" type="text/css" />
<link rel="icon" href="./components/images/favicon/faviconcms.ico" />
<title><?php echo LANG_ADMINISTRATION." - ".$config_variables->get("sitename"); ?></title>
<?php if(($_GET["function"]=="images" && $_GET["part"]=="browse_gallery") || ($_GET["function"]=="my_content" && $_GET["tab"]=="images")): ?>
<link rel="stylesheet" href="./components/slimbox/css/slimbox2.css" type="text/css" />
<script type="text/javascript" src="./components/slimbox/js/slimbox2.js"></script>
<?php endif; ?>
<!--[if IE]><script type="text/javascript" src="./components/js/ie.js"></script><![endif]-->
<?php if(($_GET["function"]=="sections" && $_GET["tab"]=="") || ($_GET["function"]=="add_content" && $_GET["tab"]=="") || $_GET["function"]=="edit_content" || $_GET["function"]=="columns"): ?>
<script type="text/javascript" src="./components/js/jquery.js"></script>
<?php endif; if(($_GET["function"]=="sections" || $_GET["function"]=="columns") && $_GET["tab"]==""): ?>
<script type="text/javascript" src="./components/js/ajax.js"></script>
<script type="text/javascript" src="./components/js/sortable.js"></script>
<script type="text/javascript">
$(document).ready(function() {
$('.sortable').sortable({
  group: 'sortable',
  handle: 'td.dragHandle',
  containerSelector: 'tbody',
  itemSelector: 'tr',
  placeholder: '<tr class="placeholder"/>',
  onDrop: function  (item, container, _super) {
  item.removeClass("dragged").attr("style","");
  $("body").removeClass("dragging");
<?php if($_GET["function"]=="sections" && $_GET["tab"]==""): ?>
  var rows = container.el.context.children;
  var debugStr = "";
  for (var i=0; i<rows.length; i++) {
    debugStr += rows[i].id+" ";
  }
  save_positions(debugStr);
<?php endif; if($_GET["function"]=="columns" && $_GET["tab"]==""): ?>
  var leftCol = $('table')[0].children[1].children;
  var rightCol = $('table')[1].children[1].children;
  var debugStr = "";
  for (var i=0; i<leftCol.length; i++) {
    debugStr += leftCol[i].id+" ";
  }
  debugStr += "|";
  for (var i=0; i<rightCol.length; i++) {
    debugStr += rightCol[i].id+" ";
  }
  save_col_positions(debugStr);
<?php endif; ?>
}
});
});
</script>
<?php endif; ?>
<?php if(($_GET["function"]=="add_content" && $_GET["tab"]=="") || $_GET["function"]=="edit_content" || ($_GET["function"]=="columns" && $_GET["part"]=="edit")): ?>
<script type="text/javascript" src="./components/tiny_mce/tiny_mce.js"></script>
<script type="text/javascript">
		// General options
tinyMCE.init({
  mode : "textareas",
  theme : "advanced",
  editor_selector : "tinymce",
  width : "730",
	height : "300",
	entity_encoding: "raw",
	convert_urls : false,
	verify_html : true,
	apply_source_formatting : true,
	language: "<?php echo $config_variables->get('lang'); ?>",

  plugins : "tabfocus,autosave,table,advlink,inlinepopups,searchreplace,contextmenu,fullscreen,visualchars",
  theme_advanced_buttons1 : "code,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect,|,forecolor,backcolor",
	theme_advanced_buttons2 : "replace,|,bullist,numlist,|,outdent,indent,|,undo,redo,|,link,unlink,image,visualchars|,hr,removeformat,|,sub,sup,|,charmap,advhr,|,fullscreen",
	theme_advanced_buttons3 : "tablecontrols,visualaid",
	theme_advanced_toolbar_location : "top",
	theme_advanced_toolbar_align : "center",
	theme_advanced_statusbar_location : "none",
});
</script>
<?php endif; ?>
</head>
<body>
<header><h1><?php echo $config_variables->get("sitename"); ?></h1></header>
<nav><?php echo LANG_ADMINISTRATION; ?></nav>
<div id="content">
<aside id="left-column">
<h2><?php echo $config_variables->get("name_of_menu"); ?></h2>
<ul>
<li<?php if ($_GET["function"]=="overview") { echo " class=\"active-item\""; } ?>><a href="./admin.php"><?php echo LANG_OVERVIEW; ?></a></li>
<li><a href="./index.php"><strong><?php echo LANG_HOMEPAGE; ?></strong></a></li>
</ul>
<?php if ($login->check_rights()>0): ?>
<h2><?php echo LANG_CONTENT; ?></h2>

<ul>
<li<?php if ($_GET["function"]=="add_content") { echo " class=\"active-item\""; } ?>><a href="./admin.php?function=add_content"><?php echo LANG_ADD_CONTENT; ?></a></li>
<li<?php if ($_GET["function"]=="my_content") { echo " class=\"active-item\""; } ?>><a href="./admin.php?function=my_content"><?php echo LANG_MY_CONTENT; ?></a></li>
<li<?php if ($_GET["function"]=="sections") { echo " class=\"active-item\""; } ?>><a href="./admin.php?function=sections"><?php echo LANG_SECTIONS." &amp; ".LANG_SERIES; ?></a></li>
<li<?php if ($_GET["function"]=="columns") { echo " class=\"active-item\""; } ?>><a href="./admin.php?function=columns"><?php echo LANG_COLUMNS; ?></a></li>
<li<?php if ($_GET["function"]=="images") { echo " class=\"active-item\""; } ?>><a href="./admin.php?function=images"><?php echo LANG_IMAGES; ?></a></li>
<li<?php if ($_GET["function"]=="files") { echo " class=\"active-item\""; } ?>><a href="./admin.php?function=files"><?php echo LANG_FILES; ?></a></li>
</ul>

<h2><?php echo LANG_ORGANIZATION; ?></h2>

<ul>
<li<?php if ($_GET["function"]=="comments") { echo " class=\"active-item\""; } ?>><a href="./admin.php?function=comments"><?php echo LANG_COMMENTS; ?></a></li>
<li<?php if ($_GET["function"]=="unverified_articles") { echo " class=\"active-item\""; } ?>><a href="./admin.php?function=unverified_articles"><?php echo LANG_UNVERIFIED_ARTICLES; ?></a></li>
<li<?php if ($_GET["function"]=="unpublished_articles") { echo " class=\"active-item\""; } ?>><a href="./admin.php?function=unpublished_articles"><?php echo LANG_UNPUBLISHED_ARTICLES; ?></a></li>
<li<?php if ($_GET["function"]=="archive") { echo " class=\"active-item\""; } ?>><a href="./admin.php?function=archive"><?php echo LANG_ARCHIVE; ?></a></li>
<li<?php if ($_GET["function"]=="polls") { echo " class=\"active-item\""; } ?>><a href="./admin.php?function=polls"><?php echo LANG_POLLS; ?></a></li>
</ul>

<h2><?php echo LANG_SETTINGS; ?></h2>

<ul>
<li<?php if ($_GET["function"]=="options") { echo " class=\"active-item\""; } ?>><a href="./admin.php?function=options"><?php echo LANG_OPTIONS; ?></a></li>
<li<?php if ($_GET["function"]=="advanced_options") { echo " class=\"active-item\""; } ?>><a href="./admin.php?function=advanced_options"><?php echo LANG_ADVANCED_OPTIONS; ?></a></li>
<li<?php if ($_GET["function"]=="users") { echo " class=\"active-item\""; } ?>><a href="./admin.php?function=users"><?php echo LANG_USERS; ?></a></li>
<li<?php if ($_GET["function"]=="bans") { echo " class=\"active-item\""; } ?>><a href="./admin.php?function=bans"><?php echo LANG_BANS; ?></a></li>
</ul>

<h2><?php echo LANG_OTHER; ?></h2>

<ul>
<li<?php if ($_GET["function"]=="mail_messages") { echo " class=\"active-item\""; } ?>><a href="./admin.php?function=mail_messages"><?php echo LANG_MAIL_MESSAGES; ?></a></li>
<li><a href="./admin.php?action=logout"><em><?php echo LANG_LOGOUT; ?></em></a></li>
</ul>

<?php else: ?>

<h2><?php echo LANG_LOGIN; ?></h2>
<form action="./admin.php?action=login" method="post"><div>
<p><strong><?php echo LANG_USERNAME; ?>:</strong><br /><input type="text" name="login" /></p>
<p><strong><?php echo LANG_PASSWORD; ?>:</strong><br /><input type="password" name="password" /></p>
</div><div>
<p><input type="submit" name="submit" value="<?php echo LANG_LOGIN; ?>" /></p>
</div></form>

<?php endif; ?>

</aside>
<section>

<?php
$access_denied=0;
if ($login->check_rights()>0) {
$poss = array("options", "polls", "users", "sections", "columns", "link_generator", "unpublished_articles", "unverified_articles", "advanced_options", "mail_messages", "files", "add_content", "overview", "comments", "my_content", "archive", "ajax", "images", "bans", "edit_content");
if (isset($_GET["function"]) && in_array($_GET["function"], $poss)) {
include "components/pages_admin/".$_GET["function"].".php";
}
} else { $access_denied=1; }
if ($access_denied==1) { echo "<h3>".LANG_YOU_HAVE_NOT_ACCESS_TO_THIS_SECTION."</h3>"; }
?>

</section>
<div class="float-ending"><!-- --></div>
</div>
<footer>
<div class="float-left"><?php echo $config_variables->get("meta_copyright")."</div>
<div class=\"float-right\">powered by <a href=\"http://terrcms.eu\" target=\"blank\">TerrCMS</a> v".$config_variables->get("version")."</div>\n"; ?>
<div class="float-ending"><!-- --></div>
</footer>
</body>
</html>