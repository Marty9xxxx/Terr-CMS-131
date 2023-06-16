<?php
//---[CZ]: ověříme přístup
if ($login->check_access("my_content")==1):

//---[CZ]: načteme potřebné třídy
include CLASSES_PATH."Articles.php";
include CLASSES_PATH."Images.php";
include CLASSES_PATH."Files.php";
include CLASSES_PATH."Polls.php";

//---[CZ]: vytvoříme instance
$articles=new Articles();
$images=new Images();
$files=new Files();
$polls=new Polls();

if ($_GET["action"]=="delete_article") { $articles->delete(); }
if ($_GET["action"]=="delete_image") { $images->delete_image(); }
if ($_GET["action"]=="delete_file") { $files->delete(); }
if ($_GET["action"]=="delete_poll") { $polls->delete_poll(); }
?>

<h2><?php echo LANG_MY_CONTENT; ?></h2>

<ul id="tab-bar">
<li<?php if ($_GET["tab"]=="") { echo " class=\"active-item\""; } ?>><a href="./admin.php?function=my_content"><?php echo LANG_ARTICLES; ?></a></li>
<li<?php if ($_GET["tab"]=="images") { echo " class=\"active-item\""; } ?>><a href="./admin.php?function=my_content&amp;tab=images"><?php echo LANG_IMAGES; ?></a></li>
<li<?php if ($_GET["tab"]=="files") { echo " class=\"active-item\""; } ?>><a href="./admin.php?function=my_content&amp;tab=files"><?php echo LANG_FILES; ?></a></li>
<li<?php if ($_GET["tab"]=="articles_polls") { echo " class=\"active-item\""; } ?>><a href="./admin.php?function=my_content&amp;tab=articles_polls"><?php echo LANG_ARTICLES_POLLS; ?></a></li>
<li><span class="float-ending"><!-- --></span></li>
</ul>

<?php if ($_GET["tab"]==""): ?>

<p><strong><?php echo LANG_FILTER; ?>:</strong> <a href="./admin.php?function=my_content&amp;filter=1"><?php echo LANG_NEWS; ?></a> | <a href="./admin.php?function=my_content&amp;filter=2"><?php echo LANG_STANDARD_ARTICLE; ?></a> | <a href="./admin.php?function=my_content&amp;filter=3"><?php echo LANG_IMPORTANT_ARTICLE; ?></a></p>

<?php $articles->output(); ?>

<?php 
endif; 
if ($_GET["tab"]=="images") { $images->print_images(); } 
if ($_GET["tab"]=="files"): 
?>

<table>
<tr>
<th style="width: 40%;"><?php echo LANG_FILE; ?></th>
<th style="width: 35%;"><?php echo LANG_DETAILS; ?></th>
<th style="width: 25%;"><?php echo LANG_OPTIONS; ?></th>
</tr>
<?php $files->print_files(); ?>
</table>

<?php endif; if ($_GET["tab"]=="articles_polls"): ?>

<table><tr>
<th style="width: 80%;"><?php echo LANG_NAME; ?></th>
<th style="width: 20%;"><?php echo LANG_OPTIONS; ?></th>
</tr>
<?php $polls->output_polls(); ?>
</table>

<?php endif; ?>
<?php else: $access_denied=1; endif; ?>