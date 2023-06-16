<?php
//---[CZ]: ověříme přístup
if ($login->check_access("bans")==1):

//---[CZ]: načteme potřebné třídy
include CLASSES_PATH."Bans.php";

//---[CZ]: vytvoříme instance
$bans=new Bans();

//---[CZ]: ban
if ($_GET["action"]=="add") { $bans->add(); }
if ($_GET["action"]=="remove") { $bans->delete(); }

//---[CZ]: nickBan
if ($_GET["action"]=="add_nick") { $bans->add_nick(); }
if ($_GET["action"]=="remove_nick") { $bans->delete_nick(); }
?>

<h2><?php echo LANG_BANS; ?></h2>
<ul id="tab-bar">
<li<?php if ($_GET["tab"]=="") { echo " class=\"active-item\""; } ?>><a href="./admin.php?function=bans"><?php echo LANG_BANS; ?></a></li>
<li<?php if ($_GET["tab"]=="nick_bans") { echo " class=\"active-item\""; } ?>><a href="./admin.php?function=bans&amp;tab=nick_bans"><?php echo LANG_USERNAME_BANS_AND_RESERVATIONS; ?></a></li>
<li><span class="float-ending"><!-- --></span></li>
</ul>

<?php if ($_GET["tab"]==""): ?>

<h3><?php echo LANG_ADD_BAN; ?></h3>
<form action="./admin.php?function=bans&amp;action=add" method="post"><div>
<p><strong><?php echo LANG_IP_ADDRESS; ?>:</strong><br /><input type="text" name="ip" style="width: 150px;" /><?php if (isset($_POST["submit"]) AND $_POST["ip"]=="") { echo " <span class=\"input-error\">&times;</span>"; } ?></p>
<p><input type="submit" name="submit" class="greenbutton" value="<?php echo LANG_ADD; ?>" /></p>
</div></form>
<table><tr><th class="table-title" colspan="2"><?php echo LANG_BANS; ?></th>
</tr><tr>
<th><?php echo LANG_IP_ADDRESS; ?></th><th style="width: 15%;"><?php echo LANG_OPTIONS; ?></th>
</tr>
<?php $bans->output(); ?>
</table>

<?php endif; if ($_GET["tab"]=="nick_bans"): ?>

<h3><?php echo LANG_ADD_BAN."/".LANG_RESERVATION; ?></h3>
<form action="./admin.php?function=bans&amp;tab=nick_bans&amp;action=add_nick" method="post"><div>
<p><strong><?php echo LANG_USERNAME; ?>:</strong><br /><input type="text" name="nick" style="width: 150px;" /><?php if (isset($_POST["submit"]) AND $_POST["nick"]=="") { echo " <span class=\"input-error\">&times;</span>"; } ?></p>
<p><input type="submit" name="submit" class="greenbutton" value="<?php echo LANG_ADD; ?>" /></p>
</div></form>
<table><tr>
<th class="table-title" colspan="2"><?php echo LANG_USERNAME_BANS_AND_RESERVATIONS; ?></th>
</tr><tr>
<th><?php echo LANG_USERNAME; ?></th><th style="width: 15%;"><?php echo LANG_OPTIONS; ?></th>
</tr>
<?php $bans->nick_bans_output(); ?>
</table>

<?php endif; ?>
<?php else: $access_denied=1; endif; ?>

