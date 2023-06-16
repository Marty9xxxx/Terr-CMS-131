<?php
//---[CZ]: ověříme přístup
if ($login->check_access("unpublished_articles")==1):

//---[CZ]: načteme potřebné třídy
include CLASSES_PATH."Articles.php";

//---[CZ]: vytvoříme instance
$articles=new Articles();

//---[CZ]: článek
if ($_GET["tab"]=="publish_article" AND isset($_GET["id"])) { $articles->publish_article(); }
if ($_GET["action"]=="delete") { $articles->delete(); }
?>

<h2><?php echo LANG_UNPUBLISHED_ARTICLES; ?></h2>

<?php $articles->output(); ?>

<?php else: $access_denied=1; endif; ?>