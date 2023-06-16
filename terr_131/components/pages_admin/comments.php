<?php
// check rights
if ($login->check_access("comments")==1):
// loading of reqired classes
include CLASSES_PATH."Comments.php";
// creating of instances
$comments=new Comments();

//---[CZ]: smazání
if ($_GET["action"]=="delete") { $comments->delete(); }

//---[CZ]: zobrazení
if ($_GET["action"]=="hidden") { $comments->hidden(); }
?>

<h2><?php echo LANG_COMMENTS; ?></h2>
<table><tr>
<th style="width: 60%;"><?php echo LANG_TEXT; ?></th>
<th style="width: 20%;"><?php echo LANG_IP_ADDRESS; ?></th>
<th style="width: 5%;"><?php echo LANG_SHOW; ?></th>
<th style="width: 15%;"><?php echo LANG_OPTIONS; ?></th>
</tr>
<?php $comments->output(); ?>
</table>
<?php $comments->paging(); ?>

<?php else: $access_denied=1; endif; ?>