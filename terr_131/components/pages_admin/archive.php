<?php
//---[CZ]: ověříme přístup
if ($login->check_access("archive")==1):

//---[CZ]: načteme potřebné třídy
include CLASSES_PATH."Archive.php";
include CLASSES_PATH."Articles.php";

//---[CZ]: vytvoříme instance
$archive=new Archive();
$articles=new Articles();

//---[CZ]: smazání článku
if ($_GET["action"]=="delete_article") { $articles->delete(); }
?>

<h2><?php echo LANG_ARCHIVE; ?></h2>
<ul id="tab-bar">
<li<?php if ($_GET["tab"]=="") { echo " class=\"active-item\""; } ?>><a href="./admin.php?function=archive&amp;tab="><?php echo LANG_ARTICLES; ?></a></li>
<li><span class="float-ending"><!-- --></span></li>
</ul>

<?php if ($_GET["tab"]==""): $archive->split_articles(); ?>

<p><strong><?php echo LANG_FILTER; ?>:</strong> <a href="./admin.php?function=archive&amp;filter=1<?php if (isset($_GET["month"])) { echo "&amp;month=".$_GET["month"]; } if (isset($_GET["year"])) { echo "&amp;year=".$_GET["year"]; } ?>"><?php echo LANG_NEWS; ?></a> | <a href="./admin.php?function=archive&amp;filter=2<?php if (isset($_GET["month"])) { echo "&amp;month=".$_GET["month"]; } if (isset($_GET["year"])) { echo "&amp;year=".$_GET["year"]; } ?>"><?php echo LANG_STANDARD_ARTICLE; ?></a> | <a href="./admin.php?function=archive&amp;filter=3<?php if (isset($_GET["month"])) { echo "&amp;month=".$_GET["month"]; } if (isset($_GET["year"])) { echo "&amp;year=".$_GET["year"]; } ?>"><?php echo LANG_IMPORTANT_ARTICLE; ?></a></p>

<?php $articles->output(); ?>

<?php endif; ?>
<?php else: $access_denied=1; endif; ?>