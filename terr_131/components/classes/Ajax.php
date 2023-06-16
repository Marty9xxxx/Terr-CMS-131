<?php
class Ajax {
private $db;
private $urls;
private $config_variables;

public function __construct() {
  $this->db=new Db_layer();
  $this->urls=new Urls();
  $this->config_variables=new Config_variables();
}
public function update_comment() {
$sql=$this->db->ajaxQuery("SELECT id, text FROM terr_comments WHERE id=".$_GET["id"]);
$sql=$this->db->fetch_array($sql);
echo "<textarea name=\"c".$_GET["id"]."\" id=\"c".$_GET["id"]."\" cols=\"\" rows=\"\" style=\"width: 515px; height: 80px;\">".str_replace("<br />", "\r\n", $sql["text"])."</textarea>
<input type=\"button\" onclick=\"save_comment(".$sql["id"].", document.getElementById('c".$_GET["id"]."').value);\" value=\"Uložit\" />";
echo "<span style=\"display: none\"><endora></span>";
}
public function save_comment() {
$text=htmlspecialchars($_POST["text"], ENT_NOQUOTES);
$text=str_replace("\r\n", "<br />", $text);
$sql=$this->db->ajaxQuery("UPDATE terr_comments SET text='".$this->db->escape($text)."' WHERE id=".$_POST["id"]);
$comment=$this->db->ajaxQuery("SELECT author, user, mail FROM terr_comments WHERE id=".$_POST["id"]);
$comment=$this->db->fetch_array($comment);
if ($comment["user"]==0) { echo "<img src=\"http://www.gravatar.com/avatar/".md5(strtolower(trim($comment["mail"]))).".png?d=mm&s=45\" class=\"comment-avatar\" />$text<span class=\"float-ending\><!-- --></span>"; }
else {
  $author=$this->db->ajaxQuery("SELECT id, mail, avatar_type, avatar, sign FROM terr_users WHERE id=".$comment["author"]);
  $author=$this->db->fetch_array($author);
  if ($author["avatar_type"]==1 && $author["mail"]!="") { echo "<img src=\"http://www.gravatar.com/avatar/".md5(strtolower(trim($author["mail"]))).".png?d=mm&s=45\" class=\"comment-avatar\" />"; }
  elseif ($author["avatar_type"]==0 && $author["avatar"]!="") { echo "<img src=\"".$this->urls->root()."avatars/".$id.".".$avatar."\" alt=\"avatar\" class=\"comment-avatar\" />"; }
  else { echo "<img src=\"http://www.gravatar.com/avatar/".md5(strtolower(trim(0))).".png?d=mm&s=45\" class=\"comment-avatar\" />"; }
  echo $this->db->unescape($text)."<span class=\"float-ending\"><!-- --></span>";
}
echo "<span style=\"display: none\"><endora></span>";
}
public function delete_comment() {
$sql=$this->db->ajaxQuery("DELETE FROM terr_comments WHERE id=".intval($_GET["id"]));
}
public function hide_pm() {
$sql=$this->db->ajaxQuery("UPDATE terr_pm SET hidden=1 WHERE id=".$_GET["id"]);
}
public function seen() {
$sql=$this->db->ajaxQuery("UPDATE terr_pm SET seen=".(($_GET["checked"]==1)?1:0)." WHERE id=".$_GET["id"]);
}
public function reciever() {
if ($_GET["reciever"]!="") {
  $data=$this->db->ajaxQuery("SELECT login FROM terr_users WHERE login!='".$_GET["reciever"]."' AND login LIKE ('".$_GET["reciever"]."%') LIMIT 0,4");
  while($find=$this->db->fetch_array($data)) {
    echo "<strong>".$_GET["reciever"]."</strong>".mb_substr($find["login"], mb_strlen($_GET["reciever"], "utf-8"))." ";
  }
}
echo "<span style=\"display: none\"><endora></span>";
}
public function openID() {
echo "<span id=\"avatar\">".md5(strtolower($_GET["mail"]))."</span>";
}
public function check_login_nick() {
$check=$this->db->fetch_array($this->db->ajaxQuery("SELECT id FROM terr_bans WHERE type='1' AND nick='".$this->db->escape($_GET["nick"])."'"));
if ($check["id"]!="") { echo "<div><p><strong>".$_GET["nick"]."</strong></p><p>Toto už. jméno neni přístupné</p></div>"; }
echo "<span style=\"display: none\"><endora></span>";
}
public function online_users_expand() {
$online=$this->db->result($this->db->ajaxQuery("SELECT count(id) FROM terr_users WHERE lastvisit>".(time()-60*2)));
$url_type=$this->config_variables->get("url_type");
if ($url_type=="static") { $url="/profiles/"; }
elseif ($url_type=="dynamic") { $url="./index.php?profile="; }
if ($online==0) { echo "<li>Nikdo</li>"; }
else {
  $sql=$this->db->ajaxQuery("SELECT login FROM terr_users WHERE lastvisit>".(time()-60*2)." ORDER BY lastvisit DESC");
  while($data=$this->db->fetch_array($sql)) { echo "<li><a href=\"$url".$data["login"]."\">".$data["login"]."</a></li>"; }
}
echo "<span style=\"display: none\"><endora></span>";
}
public function save_positions() {
$ids=explode(" ",$_GET["positions"]);
$higher=$this->db->result($this->db->ajaxQuery("SELECT higher FROM terr_sections WHERE id=".intval($ids[0])));
$pocet=$this->db->result($this->db->ajaxQuery("SELECT count(id) FROM terr_sections WHERE higher=".intval($higher)));
$sql2=$this->db->ajaxQuery("SELECT id FROM terr_sections WHERE higher=".intval($higher));
while($setup=$this->db->fetch_array($sql2)) { $sql=$this->db->ajaxQuery("UPDATE terr_sections SET position=".$this->getIndexPrvku($setup["id"],$ids,$pocet)." WHERE id=".intval($setup["id"])); }
echo "<span style=\"display: none\"><endora></span>";
}
private function getIndexPrvku($prvek, $pole, $delka) {
for ($i = 0; $i<$delka; $i++) {
  if ($prvek == $pole[$i]) { return intval($i+1); }
}
}
public function save_col_positions() {
$cols=explode("|", $_GET["positions"]);
$lids=explode(" ", $cols[0]);
$rids=explode(" ", $cols[1]);
foreach($lids as $index => $value) { $sql=$this->db->ajaxQuery("UPDATE terr_columns SET col='left', position=".intval($index+1)." WHERE id=".intval($value)); }
foreach($rids as $index => $value) { $sql=$this->db->ajaxQuery("UPDATE terr_columns SET col='right', position=".intval($index+1)." WHERE id=".intval($value)); }
}
}
?>