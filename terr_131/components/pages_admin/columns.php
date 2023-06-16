<?php
//---[CZ]: ověříme přístup
if ($login->check_access("columns")==1):

//---[CZ]: načteme potřebné třídy
include CLASSES_PATH."Columns.php";

//---[CZ]: vytvoříme instance
$columns=new Columns();

//---[CZ]: položka sloupce
if (isset($_POST["submit"])) { $columns->add_column_item(); }
if ($_GET["action"]=="delete") { $columns->delete_column_item(); }
if (isset($_POST["submit_edit"])) { $columns->edit_column_item(); }
?>

<h2><?php echo LANG_COLUMNS; ?></h2>

<?php if ($_GET["part"]==""): ?>

<h3><?php echo LANG_ADD_ITEM; ?></h3>
<form action="./admin.php?function=columns" method="post"><div>
<p><strong><?php echo LANG_NAME; ?>:</strong><br /><input type="text" name="name" style="width: 150px;" /><?php if (isset($_POST["submit"]) AND $_POST["name"]=="") { echo " <span class=\"input-error\">&times;</span>"; } ?></p>
<p><strong><?php echo LANG_PROPERTIES; ?>:</strong><br /><input type="radio" name="column" value="left" checked="checked" /> <?php echo LANG_LEFT_COLUMN; ?><br /><input type="radio" name="column" value="right" /> <?php echo LANG_RIGHT_COLUMN; ?></p>
<p><input type="submit" class="greenbutton" name="submit" value="<?php echo LANG_ADD; ?>" /></p>
</div></form>
<table style="float: left; width: 360px;">
<tr>
<th class="table-title" colspan="3"><?php echo LANG_LEFT_COLUMN; ?></th>
</tr>
<tr>
<th style="width: 20%;"><?php echo LANG_POSITION; ?></th>
<th style="width: 50%;"><?php echo LANG_NAME; ?></th>
<th style="width: 30%;"><?php echo LANG_OPTIONS; ?></th>
</tr>
<tbody class="sortable">
<?php $columns->output_columns("left"); ?>
</tbody>
</table>
<table style="float: left; width: 360px; margin-left: 10px;">
<tr>
<th class="table-title" colspan="3"><?php echo LANG_RIGHT_COLUMN; ?></th>
</tr><tr>
<th style="width: 20%;"><?php echo LANG_POSITION; ?></th>
<th style="width: 50%;"><?php echo LANG_NAME; ?></th>
<th style="width: 30%;"><?php echo LANG_OPTIONS; ?></th>
</tr>
<tbody class="sortable">
<?php $columns->output_columns("right"); ?>
</tbody>
</table>
<div class="float-ending"><!-- --></div>

<?php endif; if ($_GET["part"]=="edit"): ?>

<h3><?php echo LANG_EDIT." &rarr; ".$columns->get("name"); ?></h3>
<form action="./admin.php?function=columns&amp;part=edit&amp;id=<?php echo($_GET["id"]); ?>" method="post"><div>
<p><strong><?php echo LANG_NAME; ?>:</strong><br /><input type="text" name="name" style="width: 150px;" value="<?php echo htmlspecialchars($columns->get("name")); ?>" /><?php if (isset($_POST["submit_edit"]) AND $_POST["name"]=="") { echo " <span class=\"input-error\">&times;</span>"; } ?></p>
<p><strong><?php echo LANG_PROPERTIES; ?>:</strong><br /><input type="checkbox" name="hidden" value="1"<?php if ($columns->get("hidden")==1) { echo " checked=\"checked\""; } ?> /> <?php echo LANG_HIDE; ?></p>
<p><strong><?php echo LANG_CONTENT; ?>:</strong><br /></p>
</div>
<div class="normal"><textarea name="itemcontent" class="tinymce"><?php echo $columns->get("content"); ?></textarea></div>
<div>
<p><input type="submit" class="greenbutton" name="submit_edit" value="<?php echo LANG_SAVE_CHANGES; ?>" /></p>
</div></form>

<?php endif; ?>
<?php else: $access_denied=1; endif; ?>