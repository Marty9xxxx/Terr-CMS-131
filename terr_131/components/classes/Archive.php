<?php
class Archive extends Admin {
public function __construct() { parent::__construct(); }

//---[CZ]: vypsat měsíce a roky
public function split_articles() {
$sql=$this->db->query("SELECT published FROM terr_articles WHERE published!=0 ORDER BY published DESC");
while($data=$this->db->fetch_array($sql)) {
  if (date("Y", $data["published"])!=$previous_year) {
  $previous_year=date("Y", $data["published"]);
  echo "<h3>".date("Y", $data["published"])."</h3>\n\n<p>";
  $sql2=$this->db->query("SELECT published FROM terr_articles WHERE published>".mktime(0, 0, 0, 1, 1, $previous_year)." && published<".mktime(23, 59, 59, 12, 31, $previous_year)." ORDER BY published");
  while($data=$this->db->fetch_array($sql2)) {
    if (date("m", $data["published"])!=$previous_month) { echo ((isset($_GET["month"]) && $_GET["month"]==date("m", $data["published"]))?date("m", $data["published"])." ":"<a href=\"./admin.php?function=archive&amp;month=".date("m", $data["published"])."&amp;year=$previous_year\">".date("m", $data["published"])."</a> "); }
    $previous_month=date("m", $data["published"]);
  }
  echo "</p>\n\n";
}
}
}
}
?>