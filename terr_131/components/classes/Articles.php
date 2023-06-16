<?php
class Articles extends Admin {
public function __construct() { parent::__construct(); }

//---[CZ]: výpis
public function output() {
echo "<table><tr>
<th style=\"width: 30%;\">".LANG_ARTICLE_TITLE."</th>
<th style=\"width: 17%;\" class=\"small-text\">"; if ($_GET["function"]!="my_content") { echo LANG_AUTHOR."<br />"; } echo LANG_SECTION."</th>
<th style=\"width: 3%;\" class=\"small-text\">".LANG_COMMENTED."</th>
<th style=\"width: 8%;\" class=\"small-text\">".LANG_ACCESS."</th>
<th style=\"width: 19%;\" class=\"small-text\">".LANG_CREATION_DATE; if ($_GET["function"]!="unpublished_articles" && $_GET["function"]!="unverified_articles") { echo "<br />".LANG_PUBLICATION_DATE; } echo "</th>
<th style=\"width: 23%;\">".LANG_OPTIONS."</th>
</tr>";
if ($_GET["function"]=="archive") {
  if (!isset($_GET["month"])) { $_GET["month"]=date("m", time()); }
  if (!isset($_GET["year"])) { $_GET["year"]=date("Y", time()); }
  $where="published>".mktime(0, 0, 0, $_GET["month"], 1, $_GET["year"])." && published<".mktime(23, 59, 59, $_GET["month"], 31, $_GET["year"]);
}
if ($_GET["function"]=="my_content") { $where="author=".intval($_SESSION["user_id"]); }
if ($_GET["function"]=="unpublished_articles") { $where="confirmed=1 && published=0 && complete=1"; }
if ($_GET["function"]=="unverified_articles") { $where="confirmed=0 && published=0 && complete=1"; }
$type=((isset($_GET["filter"]))?" && type=".$_GET["filter"]:"");
$sql=$this->db->query("SELECT id, quick, title, section, added, published, author, ReqRights FROM terr_articles WHERE $where$type ORDER by added DESC");
while($data=$this->db->fetch_array($sql)) {
  $comments=$this->db->result($this->db->query("SELECT count(id) FROM terr_comments WHERE article=".$data["id"]));
  $last_comm=$this->db->fetch_array($this->db->query("SELECT user, author FROM terr_comments WHERE hidden=0 && article=".$data["id"]." ORDER BY added DESC LIMIT 1"));
  $quick=$this->db->result($this->db->query("SELECT quick FROM terr_sections WHERE id=".$data["section"]));
  echo "<tr><td><a href=\"".$this->urls->article($data["quick"])."\" target=\"_blank\">".$this->db->unescape($data["title"])."</a></td>
  <td style=\"text-align: center;\" class=\"small-text\">".(($_GET["function"]!="my_content")?parent::author_link($data["author"])."<br />":"").parent::article_section($quick)."</td>
  <td style=\"text-align: center;\" class=\"small-text\">$comments&times;<br />";
  if ($last_comm["user"]==0) { echo $last_comm["author"]; } 
  else { $last_commr=$this->db->fetch_array($this->db->query("SELECT login FROM terr_users WHERE id=".$last_comm["author"])); echo $last_commr["login"]; }
  echo "</td><td style=\"text-align: center;\" class=\"small-text\">";
  if ($data["ReqRights"]=="All") { echo LANG_ACCESS_ALL; } elseif ($data["ReqRights"]=="0") { echo LANG_ACCESS_0; } else { echo "".LANG_USERS_WITH_ACCESS."".$data["ReqRights"].""; }
  echo "</td><td style=\"text-align: center;\" class=\"small-text\">".date("d.m.Y, H:i", $data["added"])."<br />";
  if ($_GET["function"]!="unpublished_articles" && $_GET["function"]!="unverified_articles") { echo (($data["published"]!=0)?date("d.m.Y, H:i", $data["published"]):LANG_NOT_PUBLISHED_YET); }
  echo "</td><td style=\"text-align: center;\">";
  if ($_GET["function"]=="archive") { if ($this->login->check_rights() >= $data["ReqRights"] || $data["ReqRights"]=="All") { echo "<input type=\"button\" value=\"".LANG_EDIT."\" onclick=\"parent.location='./admin.php?function=edit_content&amp;tab=article&amp;id=".$data["id"]."';\" />"; } echo "<input type=\"button\" class=\"redbutton\" value=\"".LANG_DELETE."\" onclick=\"if (!confirm('".LANG_ARE_YOU_SURE."')) return false; parent.location='./admin.php?function=archive&amp;month=".$_GET["month"]."&amp;year=".$_GET["year"]."&amp;action=delete_article&amp;id=".$data["id"]."';\" />"; }
  if ($_GET["function"]=="my_content") { echo "<input type=\"button\" value=\"".LANG_EDIT."\" class=\"yellowbutton\" onclick=\"parent.location='./admin.php?function=edit_content&amp;tab=article&amp;id=".$data["id"]."';\" /><input type=\"button\" value=\"".LANG_DELETE."\" class=\"redbutton\" onclick=\"if (!confirm('".LANG_ARE_YOU_SURE."')) return false; parent.location='./admin.php?function=my_content&amp;action=delete_article&amp;id=".$data["id"]."';\" />"; }
  if ($_GET["function"]=="unpublished_articles") { echo "<input type=\"button\" value=\"".LANG_PUBLISH."\" class=\"greenbutton\" onclick=\"parent.location='./admin.php?function=unpublished_articles&amp;tab=publish_article&amp;id=".$data["id"]."';\" /><input type=\"button\" value=\"".LANG_EDIT."\" class=\"yellowbutton\" onclick=\"parent.location='./admin.php?function=edit_content&amp;tab=article&amp;id=".$data["id"]."';\" />"; }
  if ($_GET["function"]=="unverified_articles") { echo "<input type=\"button\" value=\"".LANG_EDIT."\" class=\"yellowbutton\" onclick=\"parent.location='./admin.php?function=edit_content&amp;tab=article&amp;id=".$data["id"]."';\" /><input type=\"button\" value=\"".LANG_CONFIRM."\" class=\"greenbutton\" onclick=\"parent.location='./admin.php?function=unverified_articles&amp;action=confirm&amp;id=".$data["id"]."';\" />"; }
  echo "</td></tr>\n";
}
if ($this->db->num_rows($sql)==0) { echo "<tr><td colspan=\"6\" style=\"text-align: center;\"><em>".LANG_NO_ARTICLES_FOUND."</em></td></tr>"; }
echo "</table>";
}
//---[CZ]: publikace článku
public function publish_article() {
$sql=$this->db->query("UPDATE terr_articles SET published=".time()." WHERE id=".$_GET["id"]);
if ($sql) { parent::$this->status_messages->print_success(LANG_ACTION_WAS_SUCCESSFULLY_COMPLETED); }
}
//---[CZ]: přidání
public function add() {
if ($_POST["article_title"]!="" && $_POST["article_link"]!="" && $_POST["section"]!="" && $_POST["article_type"]!="") {
  $id=$this->db->result($this->db->query("SELECT count(id) FROM terr_articles WHERE quick='".$this->db->escape($_POST["article_link"])."'"));
  if ($id==0) {    
    $sql=$this->db->query("INSERT INTO terr_articles (quick, title, type, hp, complete, confirmed, discussion, section, perex, text, added, author, pereximg, top, show_article_info, keywords, ReqRights, poll, age_limit, series) values (
    '".$this->db->escape($_POST["article_link"])."',
    '".$this->db->escape(htmlspecialchars($_POST["article_title"], ENT_NOQUOTES))."',
    ".intval($_POST["article_type"]).",
    ".intval($_POST["hp"]).",
    ".intval($_POST["complete"]).",
    ".intval($_POST["confirmed"]).",
    ".intval($_POST["discussion"]).",
    ".intval($_POST["section"]).",
    '".$this->db->escape($_POST["perex"])."',
    '".$this->db->escape($_POST["text"])."',
    ".time().",
    ".$_SESSION["user_id"].",
    '".$this->db->escape((($_POST["pereximg"]=="/gallery/" || $_POST["pereximg"]=="")?"":$_POST["pereximg"]))."',
    ".intval($_POST["top"]).",
    ".intval($_POST["show_article_info"]).",
    '".$this->db->escape($_POST["keywords"])."',
    '".$this->db->escape($_POST["ReqRights"])."',
    ".intval($_POST["poll"]).",
    ".intval($_POST["age_limit"]).",
    ".intval($_POST["series"])."
    )");
    $inserted=$this->db->result($this->db->query("SELECT id FROM terr_articles ORDER BY id DESC"));    
    if ($this->login->check_access("publish_articles")==1 && $_POST["publish_now"]==1) { $sql=$this->db->query("UPDATE terr_articles SET published=".time().", publisher=".$_SESSION["user_id"]." WHERE id=".intval($inserted)); }
    if ($sql) { parent::$this->status_messages->print_success(LANG_ACTION_WAS_SUCCESSFULLY_COMPLETED); }
  } 
  else { parent::$this->status_messages->print_error(LANG_THIS_ARTICLE_ALREADY_EXISTS); }
}
else { parent::$this->status_messages->print_error(LANG_YOU_HAVE_NOT_FILLED_SOME_ITEMS); }
}
//---[CZ]: smazání
public function delete() {
if ($_GET["id"]!="") {
  if ($this->login->check_access("archive")==0) { $sql=$this->db->query("DELETE FROM terr_articles WHERE author=".$_SESSION["user_id"]." && id=".intval($_GET["id"])); }
  if ($this->login->check_access("archive")==1) { $sql=$this->db->query("DELETE FROM terr_articles WHERE id=".intval($_GET["id"])); }
  if ($sql) { $sql=$this->db->query("DELETE FROM terr_comments WHERE article=".intval($_GET["id"])); }
}
parent::$this->status_messages->print_success(LANG_ACTION_WAS_SUCCESSFULLY_COMPLETED);
}
//---[CZ]: úprava článku
public function update() {
if ($_POST["article_title"]!="" && $_POST["article_link"]!="" && $_POST["section"]!="" && $_POST["article_type"]!="") {
  $check_access=$this->db->result($this->db->query("SELECT author FROM terr_articles WHERE id=".intval($_GET["id"])));
  if ($check_access==$_SESSION["user_id"] || $this->login->check_access("confirm_articles")==1) {
    $old_quick=$this->db->result($this->db->query("SELECT quick FROM terr_articles WHERE id=".intval($_GET["id"])));
    if ($old_quick!=$_POST["article_link"]) { $check_duplication=$this->db->result($this->db->query("SELECT id FROM terr_articles WHERE quick='".$this->db->escape($_POST["article_link"])."'")); } 
    if ($check_duplication=="") {
      $sql=$this->db->query("UPDATE terr_articles SET
      quick='".$this->db->escape($_POST["article_link"])."',
      title='".$this->db->escape(htmlspecialchars($_POST["article_title"], ENT_NOQUOTES))."',
      type=".intval($_POST["article_type"]).",
      hp=".intval($_POST["hp"]).",
      complete=".intval($_POST["complete"]).",
      confirmed=".intval($_POST["confirmed"]).",
      discussion=".intval($_POST["discussion"]).",
      show_article_info=".intval($_POST["show_article_info"]).",
      top=".intval($_POST["top"]).",
      section=".intval($_POST["section"]).",
      perex='".$this->db->escape($_POST["perex"])."',
      text='".$this->db->escape($_POST["text"])."',
      keywords='".$this->db->escape($_POST["keywords"])."',
      edited=".time().",
      last_editor=".$_SESSION["user_id"].",
      pereximg='".$this->db->escape((($_POST["pereximg"]=="/gallery/" || $_POST["pereximg"]=="")?"":$_POST["pereximg"]))."',
      ReqRights='".$this->db->escape($_POST["ReqRights"])."',
      age_limit=".intval($_POST["age_limit"]).",
      poll=".intval($_POST["poll"]).",
      series=".intval($_POST["series"])."
      WHERE id=".intval($_GET["id"]));
      if ($_POST["cancel_publication"]==1 && $this->login->check_access("publish_articles")==1) { $sql=$this->db->query("UPDATE terr_articles SET published=0 WHERE id=".intval($_GET["id"])); } 
      elseif ($this->login->check_access("unpublished_articles")==1 && $_POST["publish"]==1) { $sql=$this->db->query("UPDATE terr_articles SET published=".mktime($_POST["hours"], $_POST["minutes"], 0, $_POST["month"], $_POST["day"], $_POST["year"]).", publisher=".$_SESSION["user_id"]." WHERE id=".intval($_GET["id"])); }
      if ($sql) { parent::$this->status_messages->print_success(LANG_ACTION_WAS_SUCCESSFULLY_COMPLETED); }
    } 
    else { parent::$this->status_messages->print_error(LANG_THIS_ARTICLE_ALREADY_EXISTS); }
  } 
  else { parent::$this->status_messages->print_error(LANG_YOU_ARE_NOT_AUTHORIZED_TO_PERFORM_EDITATION); }
} 
else { parent::$this->status_messages->print_error(LANG_YOU_HAVE_NOT_FILLED_SOME_ITEMS); }
}
public function get($column) {
return $this->db->unescape($this->db->result($this->db->query("SELECT $column FROM terr_articles WHERE id=".intval($_GET["id"]))));
}
public function confirm() {
$sql=$this->db->query("UPDATE terr_articles SET confirmed=1 WHERE id=".intval($_GET["id"]));
}
public function publish_select_boxes() {
if ($this->get("published")==0) { $date=getdate(time()); } else { $date=getdate($this->get("published")); }
echo "<select name=\"day\">";
for ($i=1; $i<=31; $i++) { echo "<option value=\"$i\"".(($date["mday"]==$i)?" selected":"").">$i</option>\n"; }
echo "</select> . <select name=\"month\">";
for ($i=1; $i<=12; $i++) { echo "<option value=\"$i\"".(($date["mon"]==$i)?" selected":"").">$i</option>\n"; }
echo "</select> . <select name=\"year\">";
for ($i=2000; $i<=2020; $i++) { echo "<option value=\"$i\"".(($date["year"]==$i)?" selected":"").">$i</option>\n"; }
if ($date["hours"]>=0 && $date["hours"]<=9) { $date["hours"]="0".$date["hours"]; }
if ($date["minutes"]>=0 && $date["minutes"]<=9) { $date["minutes"]="0".$date["minutes"]; }
echo "</select>&nbsp;&nbsp;&nbsp;<input type=\"text\" name=\"hours\" value=\"".$date["hours"]."\" style=\"width: 20px;\" /> : <input type=\"text\" name=\"minutes\" value=\"".$date["minutes"]."\" style=\"width: 20px;\" />".(($this->get("published")==0)?"&nbsp;&nbsp;&nbsp;<input type=\"checkbox\" name=\"publish\" value=\"1\" /> ".LANG_PUBLISH:"<input type=\"hidden\" name=\"publish\" value=\"1\" />");
}
}
?>