<?php
class Profile extends Index {
public function __construct() { parent::__construct(); }

//---[CZ]: počet dat
public function get_count($table, $author) { return $sql=$this->db->result($this->db->query("SELECT count(id) FROM $table WHERE author=$author")); }

//---[CZ]: hodnost
public function rank($rights) {
  if ($rights==0) { return LANG_NONE; }
  if ($rights==1) { return $this->config_variables->get("redactor"); }
  if ($rights==2) { return $this->config_variables->get("corrector"); }
  if ($rights==3) { return $this->config_variables->get("admin1"); }
  if ($rights==4) { return $this->config_variables->get("admin2"); }
  if ($rights==5) { return $this->config_variables->get("admin3"); }
}
//---[CZ]: uložení profilu
public function update() {
  if (preg_match("/^[0-9a-zA-Z]{3,128}$/", $_POST["login"])) {
    if (!isset($_POST["delete_account"])) { $_POST["delete_account"]=0; }
    if (!isset($_POST["delete_avatar"])) { $_POST["delete_avatar"]=0; }
    if (!isset($_POST["mail_messages"])) { $_POST["mail_messages"]=0; }
    if ($this->login->check_access("users")==1) {
      $sql=$this->db->fetch_array($this->db->query("SELECT id, rights FROM terr_users WHERE login='".$this->db->escape($_GET["profile"])."'"));
      $id=$sql["id"]; }
    else { $id=$_SESSION["user_id"]; }
    if ($_POST["delete_account"]==1 && $sql["rights"]==0) {
      $sql=$this->db->query("DELETE FROM terr_users WHERE id=$id");
      session_destroy();
      setcookie("user_check", "", time()-1);
      header("Location:  ../../index.php"); }
    else {
      $about=htmlspecialchars($_POST["about"]);
      $about=str_replace("\r\n", "<br />", $about);
      $sign=htmlspecialchars($_POST["sign"]);
      $sign=str_replace("\r\n", "<br />", $sign);
      if ($_POST["day"]=="" || $_POST["month"]=="" || $_POST["year"]=="") { $birthday = ""; } else { $birthday = intval(mktime(0,0,0,intval($_POST["month"]),intval($_POST["day"]),intval($_POST["year"]))); }
      $sql=$this->db->query("UPDATE terr_users SET
        realname='".htmlspecialchars($_POST["realname"])."',
        birthday='".$birthday."',
        birth_type=".intval($_POST["birth_type"]).",
        about='$about',
        sign='$sign',
        jabber='".htmlspecialchars($_POST["jabber"])."',
        skype='".htmlspecialchars($_POST["skype"])."',
        msn='".htmlspecialchars($_POST["msn"])."',
        fb='".htmlspecialchars($_POST["fb"])."',
        twitter='".htmlspecialchars($_POST["twitter"])."',
        linkedin='".htmlspecialchars($_POST["linkedin"])."',
        avatar_type=".intval($_POST["avatar-type"]).",
        mail_messages=".intval($_POST["mail_messages"])."
        WHERE id=$id");
      if ($_FILES["file"]["name"]!="") {
        $explode=explode(".", $_FILES["file"]["name"]);
        if ($explode[1]=="jpg" || $explode[1]=="JPG") { $img_data=imagecreatefromjpeg($_FILES["file"]["tmp_name"]); }
        if ($explode[1]=="png" || $explode[1]=="PNG") { $img_data=imagecreatefrompng($_FILES["file"]["tmp_name"]); }
        if ($explode[1]=="gif" || $explode[1]=="GIF") { $img_data=imagecreatefromgif($_FILES["file"]["tmp_name"]); }

        if (imagesx($img_data)>imagesy($img_data)) {
          $i=120/imagesx($img_data);
          $height=$i*imagesy($img_data);
          $width=120; }
        else {
          $i=90/imagesx($img_data);
          $height=$i*imagesy($img_data);
          $width=90;
        }
        $create=imagecreatetruecolor($width, $height);
        imagecopyresampled($create, $img_data, 0, 0, 0, 0, $width, $height, imagesx($img_data), imagesy($img_data));
        if ($explode[1]=="jpg" || $explode[1]=="JPG") { imagejpeg($create, "./avatars/$id.".$explode[1], 100); }
        if ($explode[1]=="png" || $explode[1]=="PNG") { imagepng($create, "./avatars/$id.".$explode[1], 0); }
        if ($explode[1]=="gif" || $explode[1]=="GIF") { imagegif($create, "./avatars/$id.".$explode[1]); }
        $sql=$this->db->query("UPDATE terr_users SET avatar='".$explode[1]."' WHERE id=$id");
        imagedestroy($img_data);
      }
      if ($_POST["delete_avatar"]==1) {
        $check=$this->db->query("SELECT avatar FROM terr_users WHERE id=$id");
        $check=$this->db->fetch_array($check);
        unlink("./avatars/$id.".$check["avatar"]);
        $sql=$this->db->query("UPDATE terr_users SET avatar='' WHERE id=$id");
      }
      $old=$this->db->query("SELECT login, password FROM terr_users WHERE id=$id");
      $old=$this->db->fetch_array($old);
      if ($old["login"]!=$_POST["login"]) {
        $check=$this->db->query("SELECT id FROM terr_users WHERE login='".$this->db->escape($_POST["login"])."'");
        $check=$this->db->fetch_array($check);
        if ($check["id"]=="" && preg_match("/^[0-9a-zA-Z]{3,128}$/", $_POST["login"])) { $sql=$this->db->query("UPDATE terr_users SET login='".$this->db->escape(htmlspecialchars($_POST["login"]))."' WHERE id=$id"); }
      }
      if ($_POST["new_password1"]!="" && SHA1($_POST["old_password"])==$old["password"] && $_POST["new_password1"]==$_POST["new_password2"]) { $sql=$this->db->query("UPDATE terr_users SET password='".SHA1($_POST["new_password1"])."' WHERE id=$id"); }
      if ($old["login"]!=$_POST["login"]) {
        $check=$this->db->query("SELECT id FROM terr_users WHERE login='".$this->db->escape($_POST["login"])."'");
        $check=$this->db->fetch_array($check);
        if ($check["id"]=="" && preg_match("/^[0-9a-zA-Z]{3,128}$/", $_POST["login"])) { $sql=$this->db->query("UPDATE terr_users SET login='".$this->db->escape(htmlspecialchars($_POST["login"]))."' WHERE id=$id"); }
        header("Location: ".$this->urls->profile($_POST["login"])."");
      }
      if (isset($_POST["mail"])) {
        if (preg_match("/^[0-9a-zA-Z@.-]{0,128}$/", $_POST["mail"])) { $sql=$this->db->query("UPDATE terr_users SET mail='".$this->db->escape(htmlspecialchars($_POST["mail"]))."' WHERE id=$id"); }
      }
      if (isset($_POST["icq"])) {
        if (preg_match("/^[0-9]{0,128}$/", $_POST["icq"])) { $sql=$this->db->query("UPDATE terr_users SET icq='".$this->db->escape(htmlspecialchars($_POST["icq"]))."' WHERE id=$id"); }
      }
      if (isset($_POST["name_custom_1"]) && isset($_POST["value_custom_1"])) {
        $sql=$this->db->query("UPDATE terr_users SET name_custom_1='".$this->db->escape(htmlspecialchars($_POST["name_custom_1"]))."' WHERE id=$id");
        $sql=$this->db->query("UPDATE terr_users SET value_custom_1='".$this->db->escape(htmlspecialchars($_POST["value_custom_1"]))."' WHERE id=$id");
      }
      if (isset($_POST["name_custom_2"]) && isset($_POST["value_custom_2"])) {
        $sql=$this->db->query("UPDATE terr_users SET name_custom_2='".$this->db->escape(htmlspecialchars($_POST["name_custom_2"]))."' WHERE id=$id");
        $sql=$this->db->query("UPDATE terr_users SET value_custom_2='".$this->db->escape(htmlspecialchars($_POST["value_custom_2"]))."' WHERE id=$id");
      }
      if (isset($_POST["name_custom_3"]) && isset($_POST["value_custom_3"])) {
        $sql=$this->db->query("UPDATE terr_users SET name_custom_3='".$this->db->escape(htmlspecialchars($_POST["name_custom_3"]))."' WHERE id=$id");
        $sql=$this->db->query("UPDATE terr_users SET value_custom_3='".$this->db->escape(htmlspecialchars($_POST["value_custom_3"]))."' WHERE id=$id");
      }
    }
  }
}
//---[CZ]: vypsání stránky
public function get_page() {
  if (isset($_SESSION["user_id"])) {
    $login=$this->db->result($this->db->query("SELECT login FROM terr_users WHERE id=".$_SESSION["user_id"]));
    if ($login==$_GET["profile"]) { $access=1; } else { $access=0; }}
  else { $access=0; }
  if (($this->login->check_access("users")==1 || $access==1) && isset($_POST["edit_profile"])) {
    $item=$this->db->query("SELECT * FROM terr_users WHERE login='".$_GET["profile"]."'");
    $item=$this->db->fetch_array($item);
    $about=str_replace("<br />", "\r\n", $item["about"]);
    $sign=str_replace("<br />", "\r\n", $item["sign"]);
    $birth = explode(".", date("d.m.Y", intval($item["birthday"])));
    if ($item["birthday"]=="") { unset($birth); }
    echo "\n\n<form action=\"".$this->urls->profile($_GET["profile"])."\" method=\"post\" enctype=\"multipart/form-data\">
      <div>
      <h3>".LANG_GENERAL."</h3><table style=\"width:auto; text-align: left;\"><tr>
      <td style=\"width: 50%;\"><strong>".LANG_USERNAME.":</strong><br /><input type=\"text\" name=\"login\" value=\"".$item["login"]."\" style=\"width: 225px;\" /></td>
      <td style=\"width: 50%;\"><strong>".LANG_REAL_NAME.":</strong><br /><input type=\"text\" name=\"realname\" value=\"".$item["realname"]."\" style=\"width: 228px;\" /></td>
      </tr><tr><td colspan=\"2\">
      <p><strong>".LANG_ABOUT_USER.":</strong><br /><textarea name=\"about\" style=\"height: 80px; width: 458px;\">$about</textarea></p>
      </td></tr><tr><td colspan=\"2\">
      <p><strong>".LANG_SIGNATURE.":</strong><br /><textarea name=\"sign\" style=\"height: 80px; width: 458px;\">$sign</textarea></p>
      </td></tr><tr>
      <td style=\"width: 50%;\"><strong>".LANG_EMAIL.":</strong><br /><input type=\"email\" name=\"mail\" value=\"".$item["mail"]."\" style=\"width: 225px;\" onKeyUp=\"openID(this.value);\" /></td>
      <td style=\"width: 50%;\"><strong>".LANG_SKYPE.":</strong><br /><input type=\"text\" name=\"skype\" value=\"".$item["skype"]."\" style=\"width: 225px;\" /></td>
      </tr><tr>
      <td style=\"width: 50%;\"><strong>".LANG_WINDOWS_LIVE_MESSENGER.":</strong><br /><input type=\"email\" name=\"msn\" value=\"".$item["msn"]."\" style=\"width: 225px;\" /></td>
      <td style=\"width: 50%;\"><strong>".LANG_JABBER.":</strong><br /><input type=\"email\" name=\"jabber\" value=\"".$item["jabber"]."\" style=\"width: 225px;\" /></td>
      </tr><tr>
      <td style=\"width: 50%;\"><strong>Facebook:</strong><br /><input type=\"text\" name=\"fb\" value=\"".$item["fb"]."\" style=\"width: 225px;\" /></td>
      <td style=\"width: 50%;\"><strong>Twitter:</strong><br /><input placeholder=\"prezdivka\" type=\"text\" name=\"twitter\" value=\"".$item["twitter"]."\" style=\"width: 225px;\" /></td>
      </tr><tr>
      <td style=\"width: 50%;\"><strong>".LANG_ICQ.":</strong><br /><input pattern=\"[0-9]{1,25}\" type=\"text\" name=\"icq\" value=\"".$item["icq"]."\" style=\"width: 225px;\" /></td>
      <td style=\"width: 50%;\"><strong>".LANG_DATE_OF_BIRTH.":</strong><br />
      <select name=\"day\" style=\"width: 60px;\"><optgroup label=\"".LANG_DAY."\">
      <option value=\"\">--</option>";
    for ($i=1; $i<=31; $i++) { echo "<option value=\"$i\"".(($birth[0]==$i)?" selected=\"selected\"":"").">$i</option>\n"; }
    echo "</optgroup></select>&nbsp;
      <select name=\"month\" style=\"width: 60px;\"><optgroup label=\"".LANG_MONTH."\">
      <option value=\"\">--</option>";
    for ($i=1; $i<=12; $i++) { echo "<option value=\"$i\"".(($birth[1]==$i)?" selected=\"selected\"":"").">$i</option>\n"; }
    echo "</optgroup></select>&nbsp;
      <select name=\"year\" style=\"width: 89px;\" /><optgroup label=\"".LANG_YEAR."\">
      <option value=\"\">--</option>";
    for ($i=date(Y); $i>=date(Y)-100; $i--) { echo "<option value=\"$i\"".(($birth[2]==$i)?" selected=\"selected\"":"").">$i</option>\n"; }
    echo "</optgroup></select></td>
      </tr><tr>
      <td style=\"width: 50%;\"><strong>LinkedIn:</strong><br /><input type=\"text\" name=\"linkedin\" value=\"".$item["linkedin"]."\" style=\"width: 225px;\" /></td>
      <td style=\"width: 50%;\"><strong>".LANG_SHOW_DATE_OF_BIRTH.":</strong><br /><input type=\"radio\" name=\"birth_type\" value=\"0\"".(($item["birth_type"]==0)?" checked":"")." id=\"birth_type_NO\" /> <label for=\"birth_type_NO\">".LANG_NO."</label> <input type=\"radio\" name=\"birth_type\" value=\"1\"".(($item["birth_type"]==1)?" checked":"")." id=\"birth_type_DATE\" /> <label for=\"birth_type_DATE\">".LANG_DATE."</label> <input type=\"radio\" name=\"birth_type\" value=\"2\"".(($item["birth_type"]==2)?" checked":"")." id=\"birth_type_AGE\" /> <label for=\"birth_type_AGE\">".LANG_AGE."</label></td>
      </tr></table></div>
      <h3>".LANG_CUSTOM."</h3>
      <div><table style=\"width:auto; text-align: left;\"><tr>
      <td>".LANG_NAME.": </td><td><input placeholder=\"Miluju holky jménem\" type=\"text\" name=\"name_custom_1\" value=\"".$item["name_custom_1"]."\" /></td><td>".LANG_CONTENT.": </td><td><input placeholder=\"Terka\" type=\"text\" name=\"value_custom_1\" value=\"".$item["value_custom_1"]."\" /></td>
      </tr><tr>
      <td>".LANG_NAME.": </td><td><input type=\"text\" name=\"name_custom_2\" value=\"".$item["name_custom_2"]."\" /></td><td>".LANG_CONTENT.": </td><td><input type=\"text\" name=\"value_custom_2\" value=\"".$item["value_custom_2"]."\" /></td>
      </tr><tr>
      <td>".LANG_NAME.": </td><td><input type=\"text\" name=\"name_custom_3\" value=\"".$item["name_custom_3"]."\" /></td><td>".LANG_CONTENT.": </td><td><input type=\"text\" name=\"value_custom_3\" value=\"".$item["value_custom_3"]."\" /></td>
      </tr></table style=\"width:auto; text-align: left;\"></div>
      <h3>Avatar</h3>
      <div><table style=\"width:auto; text-align: left;\"><tr>
      <td style=\"width: 50%;\"><input type=\"radio\" name=\"avatar-type\" value=\"0\"".(($item["avatar_type"]==0)?" checked":"")." /><strong>".LANG_FROM_FILE."</strong></td>
      <td style=\"width: 50%;\"><input type=\"radio\" name=\"avatar-type\" value=\"1\"".(($item["avatar_type"]==1)?" checked":"")." /><strong>OpenID</strong></td>
      </tr><tr>
      <td style=\"width: 50%;\"><input type=\"file\" name=\"file\" /></td>
      <td>".LANG_USE_OPENID_AND_FILL_EMAIL."</td>
      </tr><tr>
      <td style=\"width: 50%;\">
      ".(($item["avatar"]!="")?"<img src=\"".$this->urls->root()."avatars/".$item["id"].".".$item["avatar"]."\" alt=\"".$item["avatar"]."\" style=\"width: 45px; height: 45px;\" />":"").(($item["avatar"]!="")?"<input type=\"checkbox\" name=\"delete_avatar\" value=\"1\" /> ".LANG_DELETE:"")."
      </td><td>
      <img id=\"acc_openID_avatar\" src=\"http://www.gravatar.com/avatar/".md5(strtolower(trim($item["mail"]))).".png?d=mm&s=45\" />
      </td></tr></table></div>
      <h3>".LANG_OPTIONS."</h3>
      <div>
      <p><input type=\"checkbox\" name=\"mail_messages\" value=\"1\"".(($item["mail_messages"]==1)?" checked=\"checked\"":"")." /> ".LANG_I_AGREE_WITH_THE_SENDING_OF_INFORMATIVE_MAILS_FROM_THIS_WEBSITE."</p>
      ".(($_SESSION["user_id"]==$item["id"] && $item["rights"]==0)?"<p><input type=\"checkbox\" name=\"delete_account\" value=\"1\" /> Smazat účet</p>":"")."
      </div>
      <h3>".LANG_CHANGE_OF_PASSWORD."</h3>
      <div><table style=\"width:auto; text-align: left;\"><tr>
      <td style=\"width: 50%;\"><strong>".LANG_CURRENT_PASSWORD.":</strong><br /><input type=\"password\" name=\"old_password\" style=\"width: 200px;\" /></td>
      <td style=\"width: 50%; font-size: 90%; font-weight: bold;\">".LANG_FILL_ONLY_IF_YOU_WANT_TO_CHANGE_IT."</td>
      </tr><tr>
      <td style=\"width: 50%;\"><strong>".LANG_NEW_PASSWORD.":</strong><br /><input type=\"password\" name=\"new_password1\" style=\"width: 200px;\" /></td>
      <td style=\"width: 50%;\"><strong>".LANG_PASSWORD_AGAIN.":</strong><br /><input type=\"password\" name=\"new_password2\" style=\"width: 200px;\" /></td>
      </tr></table></div><div>
      <p><input type=\"submit\" name=\"submit\" value=\"".LANG_SAVE_CHANGES."\" /></p>
      </div></form>"; }
  else {
    $item=$this->db->query("SELECT * FROM terr_users WHERE login='".htmlspecialchars($_GET["profile"])."'");
    $item=$this->db->fetch_array($item);
    $birthday=date("d.m.Y", intval($item["birthday"]));
    echo "<h3>".LANG_ABOUT_USER."</h3>";
    if ($item["avatar_type"]==1) { echo "<img src=\"http://www.gravatar.com/avatar/".md5(strtolower(trim($item["mail"]))).".png?d=mm&s=45\" style=\"margin-right: 15px; float: right;\" />"; }
    elseif ($item["avatar"]!="" && $item["avatar_type"]==0) { echo "<img src=\"".$this->urls->root()."avatars/".$item["id"].".".$item["avatar"]."\" alt=\"".$item["login"]."\" style=\"float: right; width: 45px; height: 45px;\" />"; }
    echo "<p><strong>".LANG_USERNAME.":</strong> ".$item["login"]."</p>
      <p><strong>".LANG_REGISTRATION_DATE.":</strong> ".date("j.n.Y", $item["regdate"])."</p>
      <p><strong>".LANG_LAST_LOGIN.":</strong> ".date("d.m.Y H:i", $item["lastvisit"])."</p>
      ".(($item["realname"]!="")?"<p><strong>".LANG_REAL_NAME.":</strong> ".$item["realname"]."</p>":"");
    if ($item["birthday"]!="" && $item["birth_type"]!=0) {
      if ($item["birth_type"]==1) { echo "<p><strong>".LANG_DATE_OF_BIRTH.":</strong> $birthday</p>"; }
      elseif ($item["birth_type"]==2) {
        $today = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
        $age = intval(($today-$item["birthday"])/(60*60*24*365));
      echo "<p><strong>".LANG_AGE.":</strong> $age</p>";
      }
    }
    echo "<p><strong>".LANG_RIGHTS.":</strong> ".$this->rank($item["rights"])."</p>";
    if ($item["about"]!="") { echo "<p><strong>".LANG_ABOUT_USER.":</strong> ".$item["about"]."</p>"; }
    echo "\n\n<h3>".LANG_CONTACT."</h3>";
    if ($item["mail"]!="") { echo "<p><strong>".LANG_EMAIL.":</strong> ".str_replace("@", " [at] ", $item["mail"])."</p>"; }
    if ($item["jabber"]!="") { echo "<p><strong>".LANG_JABBER.":</strong> ".str_replace("@", " [at] ", $item["jabber"])."</p>"; }
    if ($item["msn"]!="") { echo "<p><strong>".LANG_WINDOWS_LIVE_MESSENGER.":</strong> ".str_replace("@", " [at] ", $item["msn"])."</p>"; }
    if ($item["icq"]!="") { echo "<p><strong>".LANG_ICQ.":</strong> ".$item["icq"]."</p>"; }
    if ($item["skype"]!="") { echo "<p><strong>".LANG_SKYPE.":</strong> ".$item["skype"]."</p>"; }
    if ($item["linkedin"]!="") { echo "<p><strong>LinkedIn:</strong> ".$item["linkedin"]."</p>"; }
    if ($item["fb"]!="") { echo "<p><strong>Facebook:</strong> ".$item["fb"]."</p>"; }
    if ($item["twitter"]!="") { echo "<p><strong>Twitter:</strong> <a href=\"http://twitter.com/".$item["twitter"]."/\" target=\"_blank\">".$item["twitter"]."</a></p>"; }
    echo "<p><strong>".LANG_SEND.": </strong>";
    if (isset($_SESSION["user_id"])) { echo "<a href=\"javascript: document.forms.send_pm.submit();\">".LANG_PRIVATE_MESSAGE."</a>"; }
    else { echo LANG_PRIVATE_MESSAGE." <strong class=\"small-text\">(".LANG_LOGIN_REQUIRED.")</strong>"; }
    echo "</p><form method=\"post\" name=\"send_pm\" action=\"".$this->urls->fction("new-message")."\"><input type=\"hidden\" name=\"reciever\" value=\"".$_GET["profile"]."\" /></form>";
    if ($item["value_custom_1"]!="" || $item["value_custom_2"]!="" || $item["value_custom_3"]!="") { echo "<h3>".LANG_CUSTOM."</h3>"; }
    if ($item["value_custom_1"]!="") { echo "<p><strong>".$item["name_custom_1"].":</strong> ".$item["value_custom_1"]."</p>"; }
    if ($item["value_custom_2"]!="") { echo "<p><strong>".$item["name_custom_2"].":</strong> ".$item["value_custom_2"]."</p>"; }
    if ($item["value_custom_3"]!="") { echo "<p><strong>".$item["name_custom_3"].":</strong> ".$item["value_custom_3"]."</p>"; }
    echo "<h3>".LANG_POSTS."</h3>";
    echo "<p><strong>".LANG_ARTICLES.":</strong> ".$this->get_count("terr_articles", $item["id"])."</p>";
    $comments = $this->get_count("terr_comments", $item["id"]);
    echo "<p><strong>".LANG_COMMENTS.":</strong> ".(($comments>0)?"<a href=\"".$this->urls->comments_of_user($item["login"])."\">$comments</a>":"0")."</p>";
    $files = $this->db->query("SELECT id FROM terr_files WHERE uploader=".$item["id"]); $files = $this->db->num_rows($files);
    echo "<p><strong>".LANG_FILES.":</strong> ".(($files>0)?"<a href=\"".$this->urls->files_of_user($item["login"])."\">$files</a>":"0")."</p>";
    if (isset($_SESSION["user_id"]) && ($this->login->check_rights()>$item["rights"] || $login==$_GET["profile"])) { echo "<div><form method=\"post\"><input class=\"greenbutton\" style=\"float: right;\" type=\"submit\" name=\"edit_profile\" value=\"".LANG_EDIT."\" /></form></div>"; }
  }
}
}
?>