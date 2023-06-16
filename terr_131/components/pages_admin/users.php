<?php
//---[CZ]: ověříme přístup
if ($login->check_access("users")==1):

//---[CZ]: načteme potřebné třídy
include CLASSES_PATH."Users.php";

//---[CZ]: vytvoříme instance
$users=new Users();

//---[CZ]: změna práv
if ($_GET["action"]=="change_rights") { $users->change_rights(); }

//---[CZ]: smazání uživatele
if ($_GET["action"]=="delete") { $users->delete(); }
?>

<h2><?php echo LANG_USERS; ?></h2>
<table>                                            
<th style="width: 26%;"><?php echo $users->order("login", "up").LANG_USERNAME.$users->order("login", "down"); ?></th>
<th style="width: 17%;" class="small-text"><?php echo $users->order("regdate", "up").LANG_REGISTRATION_DATE.$users->order("regdate", "down"); ?></th>
<th style="width: 17%;" class="small-text"><?php echo $users->order("lastvisit", "up").LANG_LAST_LOGIN.$users->order("lastvisit", "down"); ?></th>
<th style="width: 10%;"><?php echo $users->order("rights", "up").LANG_RIGHTS.$users->order("rights", "down"); ?></th>
<th style="width: 30%;"><?php echo LANG_OPTIONS; ?></th>
<?php $users->output(); ?>
</table>
<?php $users->paging(); ?>

<?php else: $access_denied=1; endif; ?>