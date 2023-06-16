<?php
//---[CZ]: ověříme přístup
if ($login->check_access("images")==1):

//---[CZ]: načteme potřebné třídy
include CLASSES_PATH."Images.php";

//---[CZ]: vytvoříme instance
$images=new Images();

//---[CZ]: obrázek
if (isset($_POST["submit_upload"])) { $images->upload(); }
if (isset($_POST["submit_edit_image"])) { $images->edit_image(); }
if ($_GET["action"]=="delete_image") { $images->delete_image(rawurldecode($_GET["file"])); }

//---[CZ]: galerie
if (isset($_POST["submit_add_gallery"])) { $images->add_gallery(); }
if (isset($_POST["submit_edit_gallery"])) { $images->edit_gallery(); }
if ($_GET["action"]=="delete_gallery") { $images->delete_gallery(); }
?>

<h2><?php echo LANG_IMAGES; ?></h2>

<?php if ($_GET["part"]==""): ?>

<h3><?php echo LANG_UPLOAD_AN_IMAGE; ?></h3>
<form action="./admin.php?function=images" method="post" enctype="multipart/form-data"><div>
<table>
<tr>
<td style="width: 50%;"><strong><?php echo LANG_FILE; ?>:</strong><br />
<input type="file" accept="image/*" multiple="true" name="file[]" /><?php if (isset($_POST["submit_upload"]) AND $_FILES["file"]["name"]=="") { echo " <span class=\"input-error\">&times;</span>"; } ?></td>
<td><strong><?php echo LANG_GALLERY; ?>:</strong><br />
<select name="gallery" style="width: 200px;">
<option value=""><?php echo LANG_CHOOSE; ?></option>
<?php $images->load_galleries($_POST["gallery"]); ?>
</select><?php if (isset($_POST["submit_upload"]) AND $_POST["gallery"]=="") { echo " <span class=\"input-error\">&times;</span>"; } ?>
</tr></table>
<p><input type="submit" name="submit_upload" class="greenbutton" value="<?php echo "Nahrát"; ?>" /></p>
</div></form>
<h3><?php echo LANG_ADD_GALLERY; ?></h3>
<form action="./admin.php?function=images" method="post"><div>
<p><strong><?php echo LANG_NAME; ?>:</strong><br /><input type="text" name="gallery" style="width: 200px;" /><?php if (isset($_POST["submit_add_gallery"]) AND $_POST["gallery"]=="") { echo " <span class=\"input-error\">&times;</span>"; } ?></p>
<p><input type="submit" class="greenbutton" name="submit_add_gallery" value="<?php echo LANG_ADD; ?>" /></p>
</div></form>
<table><tr>
<th class="table-title" colspan="3"><?php echo LANG_LIST_OF_GALLERIES; ?></th>
</tr><tr>
<th style="width: 45%;"><?php echo LANG_NAME; ?></th>
<th style="width: 20%;"><?php echo LANG_PUBLIC_GALLERY; ?></th>
<th style="width: 35%;"><?php echo LANG_OPTIONS; ?></th>
</tr>
<?php $images->print_galleries(); ?>
</table>

<?php endif; if ($_GET["part"]=="edit_gallery"): ?>

<h3><?php echo LANG_GALLERY_EDITATION.": ".$images->read("name", "images_sections"); ?></h3>
<form action="./admin.php?function=images&amp;part=edit_gallery&amp;id=<?php echo($_GET["id"]); ?>" method="post"><div>
<p><strong><?php echo LANG_NAME; ?>:</strong><br /><input type="text" name="gallery" style="width: 200px;" value="<?php echo htmlspecialchars($images->read("name", "images_sections")); ?>" /><?php if (isset($_POST["submit_edit_gallery"]) AND $_POST["gallery"]=="") { echo " <span class=\"input-error\">&times;</span>"; } ?></p>
<p><strong><?php echo LANG_OPTIONS; ?>:</strong><br /><input type="checkbox" name="public" value="1"<?php if ($images->read("public", "images_sections")==1) { echo " checked"; } ?> /> <?php echo LANG_PUBLIC_GALLERY; ?></p>
<p><input type="button" class="yellowbutton" onclick="parent.location='./admin.php?function=images';" value="<?php echo LANG_BACK; ?>" /> <input type="submit" name="submit_edit_gallery" class="greenbutton" value="<?php echo LANG_SAVE_CHANGES; ?>" /></p>
</div></form>

<?php endif; if ($_GET["part"]=="browse_gallery"): ?>

<h3><?php echo LANG_BROWSE.": ".$images->read("name", "images_sections"); ?></h3>
<?php $images->print_images(); ?>

<?php endif; if ($_GET["part"]=="edit_image"): ?>

<h3><?php echo LANG_IMAGE_EDITATION.": ".$images->read("file", "images"); ?></h3>
<form action="./admin.php?function=images&amp;part=edit_image&amp;id=<?php echo($_GET["id"]); ?>" method="post"><div>
<p><strong><?php echo LANG_TITLE; ?>:</strong><br /><input type="text" name="title" style="width: 200px;" value="<?php echo htmlspecialchars($images->read("title", "images")); ?>" /></p>
<p><strong><?php echo LANG_SECTION; ?>:</strong><br /><select name="gallery" style="width: 200px;"><?php $images->load_galleries($images->read("section", "images")); ?></select></p>
<p><strong><?php echo LANG_UPLOADER; ?>:</strong><br /><select name="uploader" style="width: 200px;"><?php $images->load_users($images->read("uploader", "images")); ?></select>
<p><input type="button" class="yellowbutton" onclick="parent.location='./admin.php?function=images&amp;part=browse_gallery&amp;id=<?php echo $images->read("section", "images"); ?>';" value="<?php echo LANG_BACK; ?>" /> <input type="submit" name="submit_edit_image" class="greenbutton" value="<?php echo LANG_SAVE_CHANGES; ?>" /></p>
</div></form>

<?php endif; ?>
<?php else: $access_denied=1; endif; ?>