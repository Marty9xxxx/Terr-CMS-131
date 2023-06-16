<?php
class Upgrading {
var $version_error;

public function __construct($prev_version, $version) {
$this->prev_version=$prev_version;
$this->version=$version;
$this->db=new Db_layer();
}

public function get_value($variable) {
return $this->db->result($this->db->query("SELECT value FROM terr_variables WHERE variable='$variable'"));
}

public function get_page() {
echo "<script src=\"../components/js/jquery.js\"></script>";
echo "<div id=\"content\"><form method=\"post\" id=\"update\">
  <h1>".LANG_UPDATE."</h1>
  <h2>".LANG_CHANGELOG."</h2>
  <div class=\"rect left-text\">".LANG_CHANGES_LIST."</div>
  <h2>".LANG_VERSION."</h2>
  <div class=\"rect\">".$this->prev_version." &rarr; ".$this->version."</div>";
  echo "<div class=\"rect error\" id=\"version_error\">".$this->version_error."</div>
  <input type=\"submit\" name=\"submit\" value=\"".LANG_DO_UPDATE."\">
  </form></div>";

echo "<script>
$('document').ready(function() {
  if ($('#version_error').html()!='') { $('#version_error').slideDown().delay(5000).slideUp(); }
});
$('#update').submit(function() {
  if ('".$this->prev_version."'!='".$this->get_value("version")."') {
    $('#version_error').html('".LANG_VERSIONS_DONT_MATCH.$this->prev_version."<br />".LANG_VERSIONS_DONT_MATCH2.$this->get_value("version")."').slideDown().delay(4000).slideUp(); return false;
  }
});
</script>";
}

public function update() {
if ($this->prev_version==$this->get_value("version")) {
    $sql=$this->db->query("UPDATE terr_variables SET value='".$this->version."' WHERE variable='version'");
    if ($sql) { $this->clear(); }
}
else { $this->version_error=LANG_VERSIONS_DONT_MATCH.$this->prev_version."<br />".LANG_VERSIONS_DONT_MATCH2.$this->get_value("version"); $this->get_page(); }
}

private function clear() {
$files=glob("../install/*.*");
foreach($files as $value) { chmod($value, 0777); unlink($value); }
chmod("../install", 0777);
rmdir("../install");
if (!file_exists("../install")) { header("Location: ../index.php"); }
else {
echo "<div id=\"content\"><div class=\"rect error\" style=\"display: block\">".LANG_COULDNT_REMOVE_INSTALL_DIR."</div>
<input type=\"submit\" value=\"".LANG_RELOAD."\" onclick=\"location.href='/'\" /></div>";
}
}
}
?>