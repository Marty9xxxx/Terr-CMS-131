<?php
//---[CZ]: ověříme přístup
if ($login->check_access("polls")==1):

//---[CZ]: načteme potřebné třídy
include CLASSES_PATH."Polls.php";

//---[CZ]: vytvoříme instance
$polls=new Polls();

//---[CZ]: anketa
if (isset($_POST["submit_add_poll"])) { $polls->add_poll(); }
if (isset($_POST["submit_edit_poll"])) { $polls->update_poll(); }
if ($_GET["action"]=="delete_poll") { $polls->delete_poll(); }

//---[CZ]: odpověď
if (isset($_POST["submit_add_answer"])) { $polls->add_answer(); }
if ($_GET["action"]=="edit_answer") { $polls->update_answer(); }
if ($_GET["action"]=="delete_answer") { $polls->delete_answer(); }
?>

<h2><?php echo LANG_POLLS; ?></h2>
<ul id="tab-bar">
<li<?php if ($_GET["tab"]=="") { echo " class=\"active-item\""; } ?>><a href="./admin.php?function=polls"><?php echo LANG_MAIN_POLLS; ?></a></li>
<li<?php if ($_GET["tab"]=="articles_polls") { echo " class=\"active-item\""; } ?>><a href="./admin.php?function=polls&amp;tab=articles_polls"><?php echo LANG_ARTICLES_POLLS; ?></a></li>
<li><span class="float-ending"><!-- --></span></li>
</ul>

<?php if ($_GET["part"]==""): ?>

<h3><?php echo LANG_ADD_POLL; ?></h3>
<form action="./admin.php?function=polls&amp;tab=<?php echo $_GET["tab"]; ?>" method="post"><div>
<p><strong><?php echo LANG_NAME; ?>:</strong><br /><input type="text" name="name" style="width: 150px;" /><?php if (isset($_POST["submit_add_poll"]) AND $_POST["name"]=="") { echo " <span class=\"input-error\">&times;</span>"; } ?></p>
<p><strong><?php echo LANG_OPTIONS; ?>:</strong><br /><input type="checkbox" name="visible" value="1" /> <?php echo LANG_SHOW; ?></p>
</div><div>
<p><strong><?php echo LANG_ANSWERS; ?>:</strong><br /><input type="text" name="answer1" style="width: 200px;" /><br /><input type="text" name="answer2" style="width: 200px;" /><br /><input type="text" name="answer3" style="width: 200px;" /><br /><input type="text" name="answer4" style="width: 200px;" /><br /><input type="text" name="answer5" style="width: 200px;" /></p>
</div><div>
<p><input type="submit" class="greenbutton" name="submit_add_poll" value="<?php echo LANG_ADD; ?>" /></p>
</div></form>
<table><tr>
<th class="table-title" colspan="2"><?php echo LANG_POLLS ?></th>
</tr><tr>
<th style="width: 80%;"><?php echo LANG_NAME; ?></th>
<th style="width: 20%;"><?php echo LANG_OPTIONS; ?></th>
</tr>
<?php $polls->output_polls(); ?>
</table>

<?php endif; if ($_GET["part"]=="edit_poll"): ?>

<h3><?php echo LANG_POLL_EDITATION.": ".$polls->get("name"); ?></h3>
<form action="./admin.php?function=polls&amp;tab=<?php echo $_GET["tab"]; ?>&amp;part=edit_poll&amp;poll_id=<?php echo($_GET["poll_id"]); ?>" method="post"><div>
<p><strong><?php echo LANG_NAME; ?>:</strong><br /><input type="text" name="name" style="width: 200px;" value="<?php echo $polls->get("name"); ?>" /><?php if (isset($_POST["submit_edit_poll"]) AND $_POST["name"]=="") { echo " <span class=\"input-error\">&times;</span>"; } ?></p>
<p><strong><?php echo LANG_OPTIONS; ?>:</strong><br /><input type="checkbox" name="visible" value="1"<?php if ($polls->get("visible")==1) { echo " checked=\"checked\""; } ?> /> <?php echo LANG_SHOW; ?></p>
<p><input type="button" class="yellowbutton" onclick="parent.location='./admin.php?function=polls';" value="<?php echo LANG_BACK; ?>" /> <input type="submit" name="submit_edit_poll" class="greenbutton" value="<?php echo LANG_SAVE_CHANGES; ?>" /></p>
</div></form>
<h3><?php echo LANG_ADD_ANSWER; ?></h3>
<form action="./admin.php?function=polls&amp;tab=<?php echo $_GET["tab"]; ?>&amp;part=edit_poll&amp;poll_id=<?php echo($_GET["poll_id"]); ?>" method="post"><div>
<p><strong><?php echo LANG_NAME; ?>:</strong><br /><input type="text" name="answer" style="width: 150px;" /><?php if (isset($_POST["submit_add_answer"]) AND $_POST["answer"]=="") { echo " <span class=\"input-error\">&times;</span>"; } ?></p>
<p><input type="submit" name="submit_add_answer" class="greenbutton" value="<?php echo LANG_ADD; ?>" /></p>
</div></form>
<table><tr>
<th class="table-title" colspan="2"><?php echo LANG_ANSWERS; ?></th>
</tr><tr>
<th style="width: 70%;"><?php echo LANG_NAME; ?></th>
<th style="width: 30%;"><?php echo LANG_OPTIONS; ?></th>
</tr>
<?php $polls->output_answers(); ?>
</table>

<?php endif; ?>
<?php else: $access_denied=1; endif; ?>