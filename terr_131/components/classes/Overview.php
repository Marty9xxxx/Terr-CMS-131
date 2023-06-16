<?php
class Overview extends Admin {
public function __construct() { parent::__construct(); }

//---[CZ]: nejčtenější články
public function most_read_articles($what) {
if (!isset($what)) { $what = ""; } else { $what = " type=$what AND"; }
$print=$this->db->query("SELECT id, quick, title, section, views, published FROM terr_articles WHERE$what published!=0 ORDER BY views DESC LIMIT 0,20");
while(list($id, $quick, $title, $section, $views, $published)=$this->db->fetch_row($print)) {
$comments=$this->db->result($this->db->query("SELECT count(id) FROM terr_comments WHERE article=$id"));
echo "<tr>
<td><a href=\"".$this->urls->article($quick)."\" target=\"_blank\">$title</a></td>
<td style=\"text-align: right;\">".$views."</td>
<td style=\"text-align: right;\">".$comments."</td>
<td style=\"text-align: center;\" class=\"small-text\">".date("d.m.Y [H:i]", $published)."</td>
</tr>\n";
}
}
}
?>