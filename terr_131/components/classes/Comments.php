<?php
class Comments extends Admin {
public function __construct() { parent::__construct(); }

//---[CZ]: výstup
public function output() {
$sql=$this->db->query("SELECT id, article, author, text, user, added, ip, hidden, confirmed FROM terr_comments".(($_GET["function"]=="overview")?" WHERE confirmed=0":"")." ORDER BY added DESC LIMIT ".((!isset($_GET["page"]))?0:intval($_GET["page"]*20-20)).",20");
while($data=$this->db->fetch_array($sql)) {
  $article=$this->db->query("SELECT quick, title, section FROM terr_articles WHERE id=".$data["article"]);
  $article=$this->db->fetch_array($article);
  echo "<tr><td><span class=\"small-text\">".date("d.m.Y [H:i]", $data["added"])." &bull; "; if ($data["user"]==1) { echo parent::author_link($data["author"]); } else { echo $this->db->unescape($data["author"]); } echo " &bull; <a href=\"".$this->urls->article($article["quick"])."\" target=\"_blank\">".$this->db->unescape($article["title"])."</a></span><br />".$this->db->unescape($data["text"])."</td>";
  if ($_GET["function"]=="comments") { echo "<td style=\"text-align: center;\">".$data["ip"]."</td><td style=\"text-align: center;\"><input type=\"button\" class=\"yellowbutton\" value=\"".(($data["hidden"]==0)?LANG_NO:LANG_YES)."\" onClick=\"parent.location='./admin.php?function=".$_GET["function"]."&amp;action=hidden&amp;id=".$data["id"]."';\" /></td>"; }
  echo "<td style=\"text-align: center;\"><input type=\"button\" class=\"redbutton\" value=\"".LANG_DELETE."\" onclick=\"if (!confirm('".LANG_ARE_YOU_SURE."')) return false; parent.location='./admin.php?function=".$_GET["function"]."&amp;action=delete&amp;id=".$data["id"]."';\" />";
  if ($_GET["function"]=="overview" AND $data["confirmed"]==0) { echo "<br /><input type=\"button\" class=\"greenbutton\" value=\"".LANG_CONFIRM."\" onclick=\"parent.location='./admin.php?action=confirm&amp;id=".$data["id"]."';\" />"; }
  echo "</td></tr>\n";
}
}
//---[CZ]: stránkování
public function paging() {
echo "<h4 style=\"text-align: right;\">";
$url="./admin.php?function=comments&amp;page="; 
$end="";
$pages=ceil($this->db->result($this->db->query("SELECT count(id) FROM terr_comments"))/20);
if ($pages>1 AND $_GET["page"]>1) {
  $i=$_GET["page"]-1;
  echo "<a href=\"$url$i$end\">&laquo;</a>&nbsp;&nbsp;";
}
if ($pages<=15) {
  $i=1;
  while ($i<=$pages) {
    echo (($i==$_GET["page"])?" $i":" <a href=\"$url$i$end\">$i</a>");
    $i++;
  }
}
else {
  if ($_GET["page"]<7) { echo (($_GET["page"]==1)?"1":" <a href=\"$url"."1"."$end\">1</a>"); } else { if ($_GET["page"]==1) { echo "1 ..."; } else { echo " <a href=\"$url"."1"."$end\">1</a> ..."; } }
  $i=$_GET["page"]-5;
  $i2=$_GET["page"]+4;
  while ($i<$i2) {
    $i++;
    if ($i>1 AND $i<$pages) {
      if ($i==$_GET["page"]) { echo " $i"; } else { echo " <a href=\"$url$i$end\">$i</a>"; }
    }
  }
  if ($_GET["page"]>$pages-6) { if ($_GET["page"]==$pages) { echo " $pages"; } else { echo " <a href=\"$url$pages$end\">$pages</a>"; } } else { if ($_GET["page"]==$pages) { echo " ... $pages"; } else { echo " ... <a href=\"$url$pages$end\">$pages</a>"; } }
}
if ($pages>1 AND $_GET["page"]<$pages) {
  $i=$_GET["page"]+1;
  echo "&nbsp;&nbsp;&nbsp;<a href=\"$url$i$end\">&raquo;</a>";
}
echo "</h4>";
}
//---[CZ]: 2. výstup
public function output_main() {
$pocet=$this->db->result($this->db->query("SELECT count(id) FROM terr_comments WHERE confirmed=0"));
if ($_GET["function"]=="overview" AND $pocet!=0) {
  $min=$this->db->result($this->db->query("SELECT min(added) FROM terr_comments WHERE confirmed=0"));
  $max=$this->db->result($this->db->query("SELECT max(added) FROM terr_comments WHERE confirmed=0"));
  echo "<table><tr><th style=\"width: 85%;\">".LANG_TEXT."</th><th style=\"width: 15%;\">".LANG_OPTIONS."</th></tr>
  <tr><td><span class=\"small-text\">".date("d.m.Y", $min)." - ".date("d.m.Y", $max)."</span><br />".LANG_NUMBER_OF_UNCONFIRMED_COMMENTS.": $pocet</td>
  <td style=\"text-align: center;\"><input type=\"button\" class=\"greenbutton\" value=\"".LANG_CONFIRM."\" onclick=\"parent.location='./admin.php?action=confirmall';\" />
  </td></tr></table>";
}
}
//---[CZ]: smazání
public function delete() {
$sql=$this->db->query("DELETE FROM terr_comments WHERE id=".intval($_GET["id"]));
if ($sql) { $this->status_messages->print_success(LANG_ACTION_WAS_SUCCESSFULLY_COMPLETED); }
}
//---[CZ]: schválení
public function confirm() {
$sql=$this->db->query("UPDATE terr_comments SET confirmed=1 WHERE id=".intval($_GET["id"]));
if ($sql) { $this->status_messages->print_success(LANG_ACTION_WAS_SUCCESSFULLY_COMPLETED); }
}
//---[CZ]: schválení všech komentářů
public function confirmall() {
$sql=$this->db->query("UPDATE terr_comments SET confirmed=1 WHERE confirmed=0");
if ($sql) { $this->status_messages->print_success(LANG_ACTION_WAS_SUCCESSFULLY_COMPLETED); }
}
//---[CZ]: zobrazování
public function hidden() {
$sql=$this->db->query("SELECT hidden FROM terr_comments WHERE id=".intval($_GET["id"]));
$sql=$this->db->fetch_array($sql);
$sql=$this->db->query("UPDATE terr_comments SET hidden=".(($sql["hidden"]==0)?1:0)." WHERE id=".intval($_GET["id"]));
$this->status_messages->print_success(LANG_ACTION_WAS_SUCCESSFULLY_COMPLETED);
}
}
?>