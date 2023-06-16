<?php
//---[CZ]: ověříme přístup
if ($login->check_access("overview")==1):

//---[CZ]: načteme potřebné třídy
include CLASSES_PATH."Overview.php";
include CLASSES_PATH."Comments.php";

//---[CZ]: vytvoříme instance
$overview=new Overview();
$comments=new Comments();

//---[CZ]: komentáře
if ($_GET["tab"]=="" AND $_GET["action"]=="confirm") { $comments->confirm(); }
if ($_GET["tab"]=="" AND $_GET["action"]=="confirmall") { $comments->confirmall(); }
if ($_GET["tab"]=="" AND $_GET["action"]=="delete") { $comments->delete(); }
?>

<h2><?php echo LANG_OVERVIEW; ?></h2>
<ul id="tab-bar">
<li<?php if ($_GET["tab"]=="") { echo " class=\"active-item\""; } ?>><a href="./admin.php?function=overview"><?php echo LANG_LAST_COMMENTS; ?></a></li>
<li<?php if ($_GET["tab"]=="most_read_articles") { echo " class=\"active-item\""; } ?>><a href="./admin.php?function=overview&amp;tab=most_read_articles"><?php echo LANG_MOST_READ_ARTICLES; ?></a></li>
<li><span class="float-ending"><!-- --></span></li>
</ul>

<?php if ($_GET["tab"]==""): ?>

<?php echo file_get_contents("http://terrcms.eu/utilities/update.php?version=".$version); ?>

<?php $comments->output_main(); ?>

<table><tr>
<th style="width: 85%;"><?php echo LANG_TEXT; ?></th>
<th style="width: 15%;"><?php echo LANG_OPTIONS; ?></th>
</tr>
<?php $comments->output(); ?>
</table>

<?php endif; if ($_GET["tab"]=="most_read_articles"): ?>

<table><tr>
<th style="width: 50%;"><?php echo LANG_ARTICLE_TITLE; ?></th>
<th style="width: 15%;"><?php echo LANG_NUMBER_OF_VIEWS; ?></th>
<th style="width: 15%;"><?php echo LANG_NUMBER_OF_COMMENTS; ?></th>
<th style="width: 20%;"><?php echo LANG_PUBLICATION_DATE; ?></th>
</tr>
<?php $overview->most_read_articles($_GET["type"]); ?>
</table>

<p><span class="float-right">
<?php
$addr = "./admin.php?function=overview&amp;tab=most_read_articles";
if (isset($_GET["type"])) { echo "<a href=\"$addr\">Vše</a> "; }
for ($i=1;$i<4;$i++) {
  if ($i!=$_GET["type"]) { echo "<a href=\"$addr&type=$i\">"; }
  switch ($i) { case 1: echo "Zprávičky"; break; case 2: echo "Standardní články"; break; case 3: echo "Důležité články"; break; }
  echo (($i!=$_GET["type"])?"</a> ":" ");
}
?>
</span></p>

<?php endif; ?>
<?php else: $access_denied=1; endif; ?>