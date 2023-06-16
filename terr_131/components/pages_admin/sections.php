<?php
//---[CZ]: ověříme přístup
if ($login->check_access("sections")==1):

//---[CZ]: načteme potřebné třídy
include CLASSES_PATH."Sections.php";

//---[CZ]: vytvoříme instance
$sections=new Sections();

//---[CZ]: nová
if (isset($_POST["submit"]) AND $_GET["tab"]=="") { $sections->add_section(); }
if (isset($_POST["submit"]) AND $_GET["tab"]=="series") { $sections->add_series_item(); }

//---[CZ]: smazání
if ($_GET["action"]=="delete" AND $_GET["tab"]=="") { $sections->delete_section(); }
if ($_GET["action"]=="delete" AND $_GET["tab"]=="series") { $sections->delete_series_item(); }

//---[CZ]: editace
if (isset($_POST["submit_edit"]) AND $_GET["tab"]=="") { $sections->edit_section(); }
if (isset($_POST["submit_edit"]) AND $_GET["tab"]=="series") { $sections->edit_series_item(); }
?>

<h2><?php echo LANG_SECTIONS; ?></h2>
<ul id="tab-bar">
<li<?php if ($_GET["tab"]=="") { echo " class=\"active-item\""; } ?>><a href="./admin.php?function=sections&amp;tab="><?php echo LANG_SECTIONS; ?></a></li>
<li<?php if ($_GET["tab"]=="series") { echo " class=\"active-item\""; } ?>><a href="./admin.php?function=sections&amp;tab=series"><?php echo LANG_SERIES; ?></a></li>
<li><span class="float-ending"><!-- --></span></li>
</ul>

<?php if ($_GET["tab"]==""): ?>
<?php if ($_GET["part"]==""): ?>

<h3><?php echo LANG_ADD_SECTION; ?></h3>
<form action="./admin.php?function=sections" method="post"><div>
<p><strong><?php echo LANG_NAME; ?>:</strong><br /><input type="text" name="name" style="width: 150px;" /><?php if (isset($_POST["submit"]) AND $_POST["name"]=="") { echo " <span class=\"input-error\">&times;</span>"; } ?></p>
<p><input type="submit" class="greenbutton" name="submit" value="<?php echo LANG_ADD; ?>" /></p>
</div></form>
<table><thead><tr>
<th class="table-title" colspan="6"><?php echo LANG_LIST_OF_SECTIONS; ?></th>
</tr><tr>
<th style="width: 8%;"><?php echo LANG_POSITION; ?></th>
<th style="width: 44%;"><?php echo LANG_NAME; ?></th>
<th style="width: 8%;"><?php echo LANG_MODULE; ?></th>
<th style="width: 9%;"><?php echo LANG_HIGHLIGHT; ?></th>
<th style="width: 8%;"><?php echo LANG_OUTLINK; ?></th>
<th style="width: 23%;"><?php echo LANG_OPTIONS; ?></th>
</tr></thead><tbody class="sortable">
<?php $sections->output_sections(0); ?>
</tbody></table>

<?php endif; if ($_GET["part"]=="edit"): ?>

<h3><?php echo LANG_SECTION_EDITATION.": ".$sections->get("name"); ?></h3>
<form action="./admin.php?function=sections&amp;part=edit&amp;id=<?php echo($_GET["id"]); ?>" method="post"><div>
<p><strong><?php echo LANG_NAME; ?>:</strong><br /><input type="text" name="name" style="width: 200px;" value="<?php echo $sections->get("name"); ?>" /><?php if (isset($_POST["submit_edit"]) AND $_POST["name"]=="") { echo " <span class=\"input-error\">&times;</span>"; } ?></p>
<p><strong><?php echo LANG_OPTIONS; ?>:</strong><br /><input type="checkbox" name="highlight" value="1"<?php if ($sections->get("highlight")==1) { echo " checked"; } ?> /> <?php echo LANG_HIGHLIGHT; ?><br />
<input type="checkbox" name="ntbl" value="1"<?php if ($sections->get("ntbl")==1) { echo " checked"; } ?> /> <?php echo LANG_YOU_MUST_BE_LOGGED_IN_FOR_ACCESS_TO_THIS_SECTION; ?><br />
<input type="checkbox" name="hidden" value="1"<?php if ($sections->get("hidden")==1) { echo " checked"; } ?> /> <?php echo LANG_HIDE; ?></p>
<input type="checkbox" name="module" value="1"<?php if ($sections->get("module")==1) { echo " checked"; } ?> /> <?php echo LANG_USE_AS_MODULE; ?></p>
<p><strong><?php echo LANG_OUTLINK; ?>:</strong><br /><input type="text" name="outlink" style="width: 200px;" value="<?php echo $sections->get("outlink"); ?>" /></p>
<p><input type="button" class="yellowbutton" onclick="parent.location='./admin.php?function=sections';" value="<?php echo LANG_BACK; ?>" /> <input type="submit" name="submit_edit" class="greenbutton" value="<?php echo LANG_SAVE_CHANGES; ?>" /></p>
</div></form>
<h3><?php echo LANG_ADD_SUBSECTION; ?></h3>
<form action="./admin.php?function=sections&amp;part=edit&amp;id=<?php echo($_GET["id"]); ?>" method="post"><div>
<p><strong><?php echo LANG_NAME; ?>:</strong><br /><input type="text" name="name" style="width: 150px;" /><?php if (isset($_POST["submit"]) AND $_POST["name"]=="") { echo " <span class=\"input-error\">&times;</span>"; } ?></p>
<p><input type="submit" name="submit" class="greenbutton" value="<?php echo LANG_ADD; ?>" /></p>
</div></form>
<table><thead><tr>
<th class="table-title" colspan="6"><?php echo LANG_LIST_OF_SUBSECTIONS; ?></th>
</tr><tr>
<th style="width: 8%;"><?php echo LANG_POSITION; ?></th>
<th style="width: 44%;"><?php echo LANG_NAME; ?></th>
<th style="width: 8%;"><?php echo LANG_MODULE; ?></th>
<th style="width: 9%;"><?php echo LANG_HIGHLIGHT; ?></th>
<th style="width: 8%;"><?php echo LANG_OUTLINK; ?></th>
<th style="width: 23%;"><?php echo LANG_OPTIONS; ?></th>
</tr></thead><tbody class="sortable">
<?php $sections->output_sections($_GET["id"]); ?>
</tbody>
</table>

<?php endif; endif; ?>

<?php if ($_GET["tab"]=="series"): ?>
<?php if ($_GET["part"]==""): ?>

<h3><?php echo LANG_ADD_ITEM; ?></h3>
<form action="./admin.php?function=sections&amp;tab=series" method="post"><div>
<p><strong><?php echo LANG_NAME; ?>:</strong><br /><input type="text" name="name" style="width: 150px;" /><?php if (isset($_POST["submit"]) AND $_POST["name"]=="") { echo " <span class=\"input-error\">&times;</span>"; } ?></p>
<p><input type="submit" class="greenbutton" name="submit" value="<?php echo LANG_ADD; ?>" /></p>
</div></form>
<table><tr>
<th class="table-title" colspan="5"><?php echo LANG_SERIES; ?></th>
</tr><tr>
<th style="width: 35%;"><?php echo LANG_NAME; ?></th>
<th style="width: 20%;"><?php echo LANG_AUTHOR; ?></th>
<th style="width: 15%;" class="small-text"><?php echo LANG_CREATE_DATE; ?></th>
<th style="width: 4%;" class="small-text"><?php echo LANG_COUNT; ?></th>
<th style="width: 25%;"><?php echo LANG_OPTIONS; ?></th>
</tr>
<?php $sections->output_series(); ?>
</table>
<div class="float-ending"><!-- --></div>

<?php endif; if ($_GET["part"]=="edit"): ?>

<h3><?php echo LANG_EDIT." &rarr; ".$sections->get("name"); ?></h3>
<form action="./admin.php?function=sections&amp;tab=series&amp;part=edit&amp;id=<?php echo($_GET["id"]); ?>" method="post"><div>
<p><strong><?php echo LANG_NAME; ?>:</strong><br /><input type="text" name="name" style="width: 150px;" value="<?php echo htmlspecialchars($sections->get("name")); ?>" /><?php if (isset($_POST["submit_edit"]) AND $_POST["name"]=="") { echo " <span class=\"input-error\">&times;</span>"; } ?></p>
</div><div>
<p><input type="submit" class="greenbutton" name="submit_edit" value="<?php echo LANG_SAVE_CHANGES; ?>" /></p>
</div></form>

<?php endif; endif; ?>
<?php else: $access_denied=1; endif; ?>