<?php
//---[CZ]: ověříme přístup
if ($login->check_rights()>=3):

//---[CZ]: načteme potřebné třídy
include CLASSES_PATH."Mail_messages.php";

//---[CZ]: vytvoříme instance
$mail_messages=new Mail_messages();

//---[CZ]: definování proměnných
if (!isset($_POST["from"])) { $_POST["from"]=""; }
if (!isset($_POST["subject"])) { $_POST["subject"]=""; }
if (!isset($_POST["text"])) { $_POST["text"]=""; }

//---[CZ]: rozeslání
if (isset($_POST["submit"])) { $mail_messages->send(); }
?>

<h2><?php echo LANG_MAIL_MESSAGES; ?></h2>
<form action="./admin.php?function=mail_messages" method="post"><div>
<p><strong><?php echo LANG_FROM; ?>:</strong><br /><input type="text" value="<?php $_POST["from"]; ?>" name="from" style="width: 300px;" /><?php if (isset($_POST["submit"]) AND $_POST["from"]=="") { echo " <span class=\"input-error\">&times;</span>"; } ?></p>
<p><strong><?php echo LANG_SUBJECT; ?>:</strong><br /><input type="text" value="<?php htmlspecialchars($_POST["subject"]); ?>" name="subject" style="width: 400px;" /><?php if (isset($_POST["submit"]) AND $_POST["subject"]=="") { echo " <span class=\"input-error\">&times;</span>"; } ?></p>
<p><strong><?php echo LANG_TEXT; ?>:</strong><?php if (isset($_POST["submit"]) AND $_POST["text"]=="") { echo " <span class=\"input-error\">&times;</span>"; } ?></p>
</div>
<div class="normal"><textarea name="text" style="width: 600px; height: 250px; margin: -10px 0 0 10px;"><?php $_POST["text"]; ?></textarea></div>
<div><p><input type="submit" name="submit" class="greenbutton" value="<?php echo LANG_SEND; ?>" /></p></div>
</form>

<?php else: $access_denied=1; endif; ?>