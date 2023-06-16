<?php
//---[CZ]: ověříme přístup
if ($login->check_access("advanced_options")==1):

//---[CZ]: načteme potřebné třídy
include CLASSES_PATH."Advanced_options.php";

//---[CZ]: vytvoříme instance
$advanced_options=new Advanced_options();

//---[CZ]: vytvoření zálohy
if ($_GET["action"]=="db_backup") { $advanced_options->db_backup(); }
if ($_GET["action"]=="ftp_backup") { $advanced_options->ftp_backup(); }

//---[CZ]: smazání zálohy
if ($_GET["action"]=="destroy") { $advanced_options->backup_destroy(); }

//---[CZ]: obnovení databáze
if ($_GET["action"]=="restore") { $advanced_options->db_restore(); }

//---[CZ]: mazání
if ($_GET["tab"]=="destroying" && $_POST["submit"]==LANG_PERFORM) { $advanced_options->destroying(); }
?>

<h2><?php echo LANG_OPTIONS; ?></h2>

<ul id="tab-bar">
<li<?php if ($_GET["tab"]=="") { echo " class=\"active-item\""; } ?>><a href="./admin.php?function=advanced_options"><?php echo LANG_THEME_EDIT; ?></a></li>
<li<?php if ($_GET["tab"]=="backup") { echo " class=\"active-item\""; } ?>><a href="./admin.php?function=advanced_options&amp;tab=backup"><?php echo LANG_BACKUPS; ?></a></li>
<li<?php if ($_GET["tab"]=="restore") { echo " class=\"active-item\""; } ?>><a href="./admin.php?function=advanced_options&amp;tab=restore"><?php echo LANG_RESTORE; ?></a></li>
<li<?php if ($_GET["tab"]=="destroying") { echo " class=\"active-item\""; } ?>><a href="./admin.php?function=advanced_options&amp;tab=destroying"><?php echo LANG_DELETE; ?></a></li>
<li><span class="float-ending"><!-- --></span></li>
</ul>

<?php
if ($_GET["tab"]==""):
if ($_GET["file"]=="") { $file = $config_variables->get("theme"); } else { $file = $_GET["file"]; }
if ($_POST["save"]==LANG_SAVE_CHANGES) {
  $text = stripslashes($_POST["content"]);
  $relace = FOpen("themes/$file.css", "r+");
  $writted = FWrite($relace,$text);
  fclose($relace);
  if ($writted) { $status_messages->print_success(LANG_ACTION_WAS_SUCCESSFULLY_COMPLETED); } else { $status_messages->print_error(LANG_AN_ERROR_OCCURED); }
}
?>

<script>
  function editing(name) {
    location.href = '/admin.php?function=advanced_options&file=' + name;
  }
</script>
<p><select name="theme" style="width: 200px;" onchange="editing(options[this.selectedIndex].value);">
<?php $advanced_options->selection($file, "themes"); ?>
</select></p>

<form method="post">
<textarea name="content" style="width: 722px; height: 400px;">
<?php echo file_get_contents("themes/$file.css"); ?>
</textarea>
<input type="submit" name="save" value="<?php echo LANG_SAVE_CHANGES; ?>" class="float-right greenbutton" />
</form>

<?php endif; if ($_GET["tab"]=="backup"): ?>

<form metho="post"><div><table><tr>
<td><?php echo LANG_DATABASE_BACKUP; ?></td><td style="width: 10%;"><input type="button" onclick="location.href = './admin.php?function=advanced_options&amp;tab=backup&amp;action=db_backup';" value="<?php echo LANG_PERFORM; ?>" class="greenbutton" /></td>
</tr><tr>
<td><?php echo LANG_FTP_BACKUP; ?></td>
<td style="width: 10%;"><input type="button" onclick="location.href = './admin.php?function=advanced_options&amp;tab=backup&amp;action=ftp_backup';" value="<?php echo LANG_PERFORM; ?>" class="greenbutton" /></td>
</tr><tr>
<td style="font-size: 80%;"><strong><?php echo LANG_WARNING; ?>: </strong><?php echo LANG_IT_WILL_USE_MORE_OF_SPACE; ?></td></tr>
</table></div></form>
<form><div><table>
<tr style="font-size: 80%; font-weight: bold;"><td><?php echo LANG_TYPE; ?></td><td><?php echo LANG_CREATE_DATE; ?></td><td><?php echo LANG_SIZE; ?></td><td style="width: 21%;"><?php echo LANG_OPTIONS; ?></td></tr>

<?php
function format_size($size) {
  $units = array("B","kB","MB","GB","TB");
  for ($i = 0; $size >= 1024 && $i < 4; $i++) { $size /= 1024; }
  return round($size, 2)." ".$units[$i];
}
$dir=opendir("./backups/");
while ($file=readdir($dir)) {
  if ($file!=".." AND $file!=".") {
  $part = explode("_", $file);
  $name = explode(".", $part[1]);
  $array[$file] = $name[0];
  }
}
if (count($array)!=0) {
  arsort($array);
  foreach ($array as $key=>$value) {
    $part = explode("_", $key);
    $name = explode(".", $part[1]);
    echo "<tr><td>";
    if ($part[0]=="db") { echo "Záloha databáze"; }
    elseif ($part[0]=="ftp") { echo "Záloha FTP"; }
    echo "</td><td>".date("d.m.Y H:i:s", $name[0])."</td><td>".format_size(filesize("backups/".$key))."</td>
    <td><input type=\"button\" class=\"greenbutton\" onclick=\"location.href='./backups/$key'\" value=\"".LANG_DOWNLOAD."\" />
    <input type=\"button\" class=\"redbutton\" onclick=\"location.href = './admin.php?function=advanced_options&amp;tab=backup&amp;action=destroy&amp;name=$key';\" value=\"".LANG_DELETE."\" /></td></tr>";
  }
}
?>
</table></div></form>

<?php endif; if ($_GET["tab"]=="restore"): ?>

<form method="post" action="./admin.php?function=advanced_options&amp;tab=restore&amp;action=restore" enctype="multipart/form-data">
<div><table><tr>
<td><input type="file" accept="application/zip" name="file" /></td><td><input type="submit" class="greenbutton" name="submit" value="<?php echo LANG_RESTORE_DATABASE; ?>" /></td>
</tr></table></div>
</form>

<?php endif; if ($_GET["tab"]=="destroying"): ?>

<form method="post" action="./admin.php?function=advanced_options&amp;tab=destroying"><div>
<h3><?php echo LANG_DELETE; ?></h3>
<input type="radio" name="reset_type" value="soft" id="soft" /><label for="soft"><?php echo LANG_SOFT_DELETE; ?></label><br />
<input type="radio" name="reset_type" value="hard" id="hard" /><label for="hard"><?php echo LANG_HARD_DELETE; ?></label><hr />
<label><?php echo LANG_MAIN_ADMIN_PASSWORD; ?>:</label> <input type="password" name="password" />
<input type="submit" name="submit" value="<?php echo LANG_PERFORM; ?>" class="greenbutton float-right" />
</div></form>

<?php endif; ?>
<?php else: $access_denied=1; endif; ?>