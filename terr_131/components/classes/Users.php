<?php
class Users extends Admin {
public function __construct() { parent::__construct(); }

//---[CZ]: změna práv
public function change_rights() {
$sql=$this->db->query("UPDATE terr_users SET rights=".intval($_POST["rights"])." WHERE id=".intval($_GET["id"]));
if ($sql) { parent::$this->status_messages->print_success(LANG_ACTION_WAS_SUCCESSFULLY_COMPLETED); } else { $this->status->print_error(LANG_AN_ERROR_OCCURED); }
}
//---[CZ]: smazání
public function delete() {
$sql=$this->db->query("DELETE FROM terr_users WHERE id=".intval($_GET["id"]));
if ($sql) { parent::$this->status_messages->print_success(LANG_ACTION_WAS_SUCCESSFULLY_COMPLETED); } else { $this->status->print_error(LANG_AN_ERROR_OCCURED); }
}
//---[CZ]: vypsání
public function output() {
if (!isset($_GET["page"])) { $skip = 0; } else { $skip=$_GET["page"]*20-20; }
if (!isset($_GET["order"])) { $order = "rights DESC, login, lastvisit DESC, regdate DESC"; } else { $ex = explode("_", $_GET["order"]); $order = $ex[0]." ".$ex[1]; }
$myrights=$this->login->check_rights();
$sql=$this->db->query("SELECT id, login, rights, regdate, lastvisit FROM terr_users ORDER by $order LIMIT $skip,20");
while ($data=$this->db->fetch_array($sql)) {
echo "<form action=\"./admin.php?function=users&amp;action=change_rights&amp;id=".$data["id"]."\" method=\"post\"><tr>
<td><a href=\"".parent::$this->urls->profile($data["login"])."\" target=\"_blank\">".$data["login"]."</a></td>
<td style=\"text-align: center;\" class=\"small-text\">".date("d.m.Y, H:i", $data["regdate"])."</td>
<td style=\"text-align: center;\" class=\"small-text\">"
.(($data["lastvisit"]==0)?"-":date("d.m.Y, H:i", $data["lastvisit"]))."</td><td style=\"text-align: center;\"><select name=\"rights\">";
if ($data["id"]=="1") { echo "<option value=\"5\" selected=\"selected\">5</option>\n"; }
else {
for ($i=0; $i<6; $i++) {
echo "<option value=\"$i\"";
if ($data["rights"]==$i) { echo " selected=\"selected\""; } elseif ($myrights<=$i AND $_SESSION["user_id"]!=1) { echo " disabled"; }
echo ">$i</option>\n";
}}
echo "</select></td><td style=\"text-align: center;\">";
if ($data["id"]==1) { echo LANG_CANNOT_BE_MODIFIED; } elseif ($myrights>$data["rights"] OR $_SESSION["user_id"]==$data["id"] OR $_SESSION["user_id"]==1) { echo "<input type=\"submit\" name=\"submit\" class=\"greenbutton\" value=\"".LANG_SAVE_CHANGES."\" />"; if ($data["id"]!="1") { echo "<input type=\"button\" class=\"redbutton\" class=\"trfbutton\" value=\"".LANG_DELETE."\" onclick=\"parent.location='./admin.php?function=users&amp;action=delete&amp;id=".$data["id"]."';\" />"; }} else { echo LANG_YOU_HAVE_NOT_REQUIRED_RIGHTS;}
echo "</td></tr></form>";
}
}
//---[CZ]: filtr
public function order($what, $how) {
if (isset($_GET["page"])) { $page="&page=$_GET[page]"; }
if ($how=="up") { return "<a href=\"./admin.php?function=users$page&order=$what\" style=\"text-decoration: none;\">&#x2191;</a> "; }
if ($how=="down") { return " <a href=\"./admin.php?function=users$page&order=".$what."_desc\" style=\"text-decoration: none;\">&#x2193;</a>"; }
}
//---[CZ]: stránkování
public function paging() {
echo "<h4 style=\"text-align: right;\">";
if ($_GET["order"]!="") { $order = "&amp;order=".$_GET["order"]; }
$url="./admin.php?function=users$order&amp;page="; $end="";
$pages=$this->db->query("SELECT id FROM terr_users");
$pages=ceil($this->db->num_rows($pages)/20);
if ($pages>1 AND $_GET["page"]>1) {
$i=$_GET["page"]-1;
echo "<a href=\"$url$i$end\">&laquo;</a>&nbsp;&nbsp;";
}
if ($pages<=15) {
$i=1;
while ($i<=$pages) {
if ($i==$_GET["page"]) { echo " $i"; } else { echo " <a href=\"$url$i$end\">$i</a>"; }
$i++;
}
} else {
if ($_GET["page"]<7) { if ($_GET["page"]==1) { echo "1"; } else { echo " <a href=\"$url"."1"."$end\">1</a>"; } } else { if ($_GET["page"]==1) { echo "1 ..."; } else { echo " <a href=\"$url"."1"."$end\">1</a> ..."; } }
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
}
?>