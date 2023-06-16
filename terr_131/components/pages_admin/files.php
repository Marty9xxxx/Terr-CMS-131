<?php
//---[CZ]: ověříme přístup
if ($login->check_access("files")==1):

//---[CZ]: načteme potřebné třídy
include CLASSES_PATH."Files.php";

//---[CZ]: vytvoříme instance
$files=new Files();

if (isset($_POST["submit"])) { $files->upload(); }
if (isset($_POST["submit_edit"])) { $files->edit(); }
if ($_GET["action"]=="delete_file") { $files->delete(); }
?>

<h2><?php echo LANG_FILES; ?></h2>

<?php if ($_GET["part"]==""): ?>

<h3><?php echo LANG_UPLOAD_A_FILE; ?></h3>
<form action="./admin.php?function=files" method="post" enctype="multipart/form-data"><div>
<p><strong><?php echo LANG_FILE; ?>:</strong><br /><input type="file" name="file" /><?php if (isset($_POST["submit"]) AND $_FILES["file"]["name"]=="") { echo " <span class=\"input-error\">&times;</span>"; } ?></p>
<p><input type="submit" name="submit" class="greenbutton" value="<?php echo LANG_UPLOAD_A_FILE; ?>" /></p>
</div></form>
<table><tr>
<th class="table-title" colspan="3"><?php echo LANG_LIST_OF_FILES; ?></th>
</tr><tr>
<th style="width: 40%;"><?php echo LANG_FILE; ?></th>
<th style="width: 35%;"><?php echo LANG_DETAILS; ?></th>
<th style="width: 25%;"><?php echo LANG_OPTIONS; ?></th>
</tr>
<?php $files->print_files(); ?>
</table>

<?php endif; if ($_GET["part"]=="edit"): ?>

<h3><?php echo LANG_FILE_EDITATION.": ".$files->read("file"); ?></h3>
<form action="./admin.php?function=files&amp;part=edit&amp;id=<?php echo($_GET["id"]); ?>" method="post"><div>
<p><strong><?php echo LANG_NAME; ?>:</strong><br /><input type="text" name="file" value="<?php echo htmlspecialchars($files->read("file")); ?>" style="width: 200px;" /><?php if (isset($_POST["submit_edit"]) AND $_POST["file"]=="") { echo " <span class=\"input-error\">&times;</span>"; } ?></p>
<p><strong><?php echo LANG_UPLOADER; ?>:</strong><br /><select name="uploader" style="width: 200px;"><?php $files->load_users($files->read("uploader")); ?></select>
</div><div>
<p style="text-align: right;"><input type="button" class="yellowbutton" onclick="parent.location='./admin.php?function=files';" value="<?php echo LANG_BACK; ?>" /> <input type="submit" name="submit_edit" class="greenbutton" value="<?php echo LANG_SAVE_CHANGES; ?>" /></p>
</div></form>

<?php endif; ?>
<?php else: $access_denied=1; endif; ?>