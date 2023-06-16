<?php
//---[CZ]: ověříme přístup
if ($login->check_access("unverified_articles")==1):

//---[CZ]: načteme potřebné třídy
include CLASSES_PATH."Articles.php";

//---[CZ]: vytvoříme instance
$articles=new Articles();

//---[CZ]: schválit
if ($_GET["action"]=="confirm") { $articles->confirm(); }
?>

<h2><?php echo LANG_UNVERIFIED_ARTICLES; ?></h2>

<?php $articles->output(); ?>

<?php else: $access_denied=1; endif; ?>