<?php
class Installing {
public function __construct($lang, $version) { $this->lang=$lang; $this->version=$version;}
var $db_error;

public function get_page() {
echo "<script src=\"../components/js/jquery.js\"></script>";
echo "<div id=\"content\">";
if ($this->check_file_permissions()==false) {
echo "<h1>".LANG_INSTALLATION."</h1>
  <h2>Error</h2>
  <div class=\"rect error\" style=\"display: block; text-align: left;\">
  <h3>".LANG_CANNOT_CHANGE_FP."</h3>".LANG_SET_FP_MANUALLY."<ol>
  <li>".LANG_CONNECT_TO_FTP."</li>
  <li>".LANG_SET_FP_TO_SEVEN."<ul>
  ".((substr(sprintf("%o", @fileperms("../avatars")), -3)!="777")?"<li>/avatars <span class=\"small\">(".substr(sprintf("%o", @fileperms("../avatars")), -3).")</span></li>":"").
  ((substr(sprintf("%o", @fileperms("../files")), -3)!="777")?"<li>/files <span class=\"small\">(".substr(sprintf("%o", @fileperms("../files")), -3).")</span></li>":"").
  ((substr(sprintf("%o", @fileperms("../gallery")), -3)!="777")?"<li>/gallery <span class=\"small\">(".substr(sprintf("%o", @fileperms("../gallery")), -3).")</span></li>":"").
  ((substr(sprintf("%o", @fileperms("../backups")), -3)!="777")?"<li>/backups <span class=\"small\">(".substr(sprintf("%o", @fileperms("../backups")), -3).")</span></li>":"").
  ((substr(sprintf("%o", @fileperms("../gallery/thumbs")), -3)!="777")?"<li>/gallery/thumbs <span class=\"small\">(".substr(sprintf("%o", @fileperms("../gallery/thumbs")), -3).")</span></li>":"").
  ((substr(sprintf("%o", @fileperms("../components/sqlite")), -3)!="777")?"<li>/components/sqlite <span class=\"small\">(".substr(sprintf("%o", @fileperms("../components/sqlite")), -3).")</span></li>":"").
  ((substr(sprintf("%o", @fileperms("../install")), -3)!="777")?"<li>/install <span class=\"small\">(".substr(sprintf("%o", @fileperms("../install")), -3).")</span></li>":"")."
  </ul></li></ol></div>
  <h2>".LANG_LANGUAGE."</h2>
  <div class=\"rect\" id=\"parent\">
  <div id=\"cze-white\" onclick=\"location.href='install.php'\"><div id=\"cze-red\"><!-- --></div><div id=\"cze-blue\"><!-- --></div></div><div id=\"space\"></div><div id=\"us-white\" onclick=\"location.href='?lang=en'\"><div class=\"us-red\"><!-- --></div><div class=\"us-red\"><!-- --></div><div class=\"us-red\"><!-- --></div><div class=\"us-red\"><!-- --></div><div class=\"us-red\"><!-- --></div><div class=\"us-red\"><!-- --></div><div class=\"us-red\"><!-- --></div><div id=\"us-blue\"><div>&#10031;&#10031;&#10031;&#10031;&#10031;&#10031;&#10031;&#10031;</div><div>&#10031;&#10031;&#10031;&#10031;&#10031;&#10031;&#10031;</div><div>&#10031;&#10031;&#10031;&#10031;&#10031;&#10031;&#10031;&#10031;</div><div>&#10031;&#10031;&#10031;&#10031;&#10031;&#10031;&#10031;</div><div>&#10031;&#10031;&#10031;&#10031;&#10031;&#10031;&#10031;&#10031;</div><div>&#10031;&#10031;&#10031;&#10031;&#10031;&#10031;&#10031;</div></div></div>
  </div>
  <input type=\"submit\" onclick=\"location.reload();\" value=\"".LANG_RELOAD."\" />";
}
else {
echo "<form method=\"post\" id=\"install\">
  <h1>".LANG_INSTALLATION."</h1>
  <div id=\"parent\">
  <h2>".LANG_DATABASE_TYPE."</h2>
  <div class=\"rect\">
  <select name=\"db_type\" id=\"db_type\">
  <option value=\"mysql\"".(($_POST["db_type"]=="mysql")?" selected":"").">MySQL</option>
  <option value=\"sqlite\"".(($_POST["db_type"]=="sqlite")?" selected":"").">SQLite</option>
  </select>
  </div>
  </div>
  <div id=\"parent\" class=\"right\">
  <h2>".LANG_LANGUAGE."</h2>
  <div class=\"rect\">
  <div id=\"cze-white\" onclick=\"location.href='install.php'\"><div id=\"cze-red\"><!-- --></div><div id=\"cze-blue\"><!-- --></div></div><div id=\"space\"></div><div id=\"us-white\" onclick=\"location.href='?lang=en'\"><div class=\"us-red\"><!-- --></div><div class=\"us-red\"><!-- --></div><div class=\"us-red\"><!-- --></div><div class=\"us-red\"><!-- --></div><div class=\"us-red\"><!-- --></div><div class=\"us-red\"><!-- --></div><div class=\"us-red\"><!-- --></div><div id=\"us-blue\"><div>&#10031;&#10031;&#10031;&#10031;&#10031;&#10031;&#10031;&#10031;</div><div>&#10031;&#10031;&#10031;&#10031;&#10031;&#10031;&#10031;</div><div>&#10031;&#10031;&#10031;&#10031;&#10031;&#10031;&#10031;&#10031;</div><div>&#10031;&#10031;&#10031;&#10031;&#10031;&#10031;&#10031;</div><div>&#10031;&#10031;&#10031;&#10031;&#10031;&#10031;&#10031;&#10031;</div><div>&#10031;&#10031;&#10031;&#10031;&#10031;&#10031;&#10031;</div></div></div>
  </div>
  </div>
  <div class=\"hide\"".(($_POST["db_type"]=="sqlite")?" style=\"display: none;\"":"").">
  <h2>".LANG_DATABASE."</h2>
  <div class=\"rect\">
  <input placeholder=\"".LANG_DATABASE_SERVER."\" type=\"text\" name=\"db_server\" value=\"".$_POST["db_server"]."\" />
  <input placeholder=\"".LANG_USERNAME."\" type=\"text\" name=\"db_login\" value=\"".$_POST["db_login"]."\" />
  <input placeholder=\"".LANG_PASSWORD."\" type=\"password\" name=\"db_password\" />
  <input placeholder=\"".LANG_DATABASE_NAME."\" type=\"text\" name=\"db_name\" value=\"".$_POST["db_name"]."\" />
  <p><input type=\"radio\" name=\"db_new\" value=\"0\"".(($_POST["db_new"]!=1)?" checked":"")." id=\"old\" /> <label for=\"old\">".LANG_USE_AN_EXISTING_DATABASE."</p>
  <p><input type=\"radio\" name=\"db_new\" value=\"1\"".(($_POST["db_new"]==1)?" checked":"")." id=\"new\" /> <label for=\"new\">".LANG_CREATE_A_NEW_DATABASE."</p>
  </div>
  </div>
  <div class=\"rect error\" id=\"db_error\">".$this->db_error."</div>
  <h2>".LANG_ADMINISTRATION."</h2>
  <div class=\"rect\">
  <input placeholder=\"".LANG_PASSWORD."\" type=\"password\" name=\"admin_password\" />
  <input placeholder=\"".LANG_RETYPE_PASSWORD."\" type=\"password\" name=\"admin_password_repeat\" />
  <input type=\"radio\" name=\"url_type\" value=\"static\"".(($_POST["url_type"]!="dynamic")?" checked":"")." id=\"static\" /> <label for=\"static\">".LANG_STATIC_URL."</label>
  <input type=\"radio\" name=\"url_type\" value=\"dynamic\"".(($_POST["url_type"]=="dynamic")?" checked":"")." id=\"dynamic\" /> <label for=\"dynamic\">".LANG_DYNAMIC_URL."</label>
  </div>
  <div class=\"rect error\" id=\"pass_error\"></div>
  <input name=\"submit\" type=\"submit\" value=\"".LANG_INSTALL."\" />
  </form></div>";
  
echo "<script>       
  $('document').ready(function() {
    if ($('#db_error').html()!='') { $('#db_error').slideDown().delay(5000).slideUp(); }
  });
  $('#db_type').change(function () {
    if ($('#db_type').val()=='sqlite') { $('.hide').slideUp(); }
    else { $('.hide').slideDown(); }
  });  
  $('#install').submit(function() {    
    if ($('input[name=admin_password]').val()=='') { $('#pass_error').html('".LANG_EMPTY_PASSWORD."').slideDown().delay(4000).slideUp(); return false; }
    else if ($('input[name=admin_password]').val().length<5) { $('#pass_error').html('".LANG_SHORT_PASSWORD."').slideDown().delay(4000).slideUp(); return false; } 
    else if ($('input[name=admin_password]').val()!=$('input[name=admin_password_repeat]').val()) { $('#pass_error').html('".LANG_PASSWORDS_DONT_MATCH."').slideDown().delay(4000).slideUp(); return false; }
  });     
  </script>";
}
}

public function install() {
$db_type=$_POST["db_type"];
$db_new=intval($_POST["db_new"]);
$db_server=$_POST["db_server"];
$db_login=$_POST["db_login"];
$db_password=$_POST["db_password"];
$admin_password=sha1((($_POST["admin_password"]!="")?$_POST["admin_password"]:"admin"));
$url_type=$_POST["url_type"];
$db_name=$_POST["db_name"];

if ($db_type=="mysql") {
  @$dbserver=new mysqli($db_server, $db_login, $db_password);
if ($dbserver) {
if ($db_new==1) {
  @$create_db=$dbserver->query("CREATE DATABASE ".$db_name." DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;");
  @$select_db=$dbserver->select_db($db_name);
}
elseif ($_POST["db_new"]==0) { @$select_db=$dbserver->select_db($db_name); }

if (($db_new==1 AND $create_db AND $select_db) OR ($db_new==0 AND $select_db)) {
$dbserver->query("SET CHARACTER SET utf8");

$sql1=$dbserver->query("CREATE TABLE terr_articles (
  id int(11) NOT NULL auto_increment,
  quick varchar(160) NOT NULL,
  title varchar(128) NOT NULL,
  type int(1) NOT NULL,
  hp int(1) NOT NULL,
  complete int(1) NOT NULL,
  confirmed int(1) NOT NULL,
  discussion int(1) NOT NULL,
  section int(3) NOT NULL,
  perex text,
  text text,
  added int(11) NOT NULL,
  published int(11) NOT NULL,
  author int(11) NOT NULL,
  publisher int(11) NOT NULL,
  edited int(11) NOT NULL,
  last_editor int(11) NOT NULL,
  pereximg varchar(128) NOT NULL,
  views int(11) NOT NULL,
  top int(1) NOT NULL,
  show_article_info int(1) NOT NULL,
  keywords text,
  poll int(11) NOT NULL,
  ReqRights varchar(5) NOT NULL DEFAULT 'All',
  age_limit int(11) NOT NULL,
  series int(11) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci");

$sql2=$dbserver->query("CREATE TABLE terr_bans (
  id int(11) NOT NULL auto_increment,
  ip varchar(128) NOT NULL,
  nick varchar(128) NOT NULL,
  type int(1) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci");

$sql3=$dbserver->query("CREATE TABLE terr_columns (
  id int(11) NOT NULL auto_increment,
  col varchar(5) NOT NULL,
  name varchar(128) NOT NULL,
  content text,
  hidden int(1) NOT NULL,
  position int(11) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci");

$sql4=$dbserver->query("CREATE TABLE terr_comments (
  id int(11) NOT NULL auto_increment,
  article int(11) NOT NULL,
  added int(11) default NULL,
  author varchar(128) NOT NULL,
  text text NOT NULL,
  confirmed int(1) NOT NULL,
  user int(1) NOT NULL,
  ip varchar(32) NOT NULL,
  hidden int(1) NOT NULL,
  mail varchar(128) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci");

$sql5=$dbserver->query("CREATE TABLE terr_files (
  id int(11) NOT NULL auto_increment,
  file varchar(128) NOT NULL,
  added int(11) NOT NULL,
  uploader int(11) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci");

$sql6=$dbserver->query("CREATE TABLE terr_images (
  id int(11) NOT NULL auto_increment,
  section int(11) NOT NULL,
  file varchar(128) NOT NULL,
  added int(11) NOT NULL,
  title varchar(128),
  uploader int(11) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci");

$sql7=$dbserver->query("CREATE TABLE terr_images_sections (
  id int(11) NOT NULL auto_increment,
  quick varchar(160) NOT NULL,
  name varchar(128) NOT NULL,
  public int(1) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci");

$sql8=$dbserver->query("CREATE TABLE terr_polls (
  id int(11) NOT NULL auto_increment,
  name varchar(128) NOT NULL,
  visible int(1) NOT NULL,
  type int(1) NOT NULL,
  author int(11) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci");

$sql9=$dbserver->query("CREATE TABLE terr_polls_data (
  id int(11) NOT NULL auto_increment,
  poll int(11) NOT NULL,
  answer varchar(128) NOT NULL,
  votes int(11) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci");

$sql10=$dbserver->query("CREATE TABLE terr_sections (
  id int(11) NOT NULL auto_increment,
  quick varchar(160) NOT NULL,
  name varchar(128) NOT NULL,
  highlight int(1) NOT NULL,
  outlink text NOT NULL,
  ntbl int(1) NOT NULL,
  position int(3) NOT NULL,
  hidden int(1) NOT NULL,
  level int(3) NOT NULL,
  higher int(3) NOT NULL,
  module int(1) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci");

$sql11=$dbserver->query("CREATE TABLE terr_users (
  id int(11) NOT NULL auto_increment,
  login varchar(128) NOT NULL,
  password varchar(128) NOT NULL,
  rights int(1) NOT NULL,
  jabber varchar(128),
  skype varchar(128),
  about text,
  mail varchar(128),
  realname varchar(128) NOT NULL,
  regdate int(11) NOT NULL,
  lastvisit int(11) NOT NULL,
  birthday varchar(11) NOT NULL,
  birth_type int(1) NOT NULL,
  mail_messages int(1) NOT NULL,
  msn varchar(128),
  avatar varchar(4) NOT NULL,
  sign text NOT NULL,
  user_check text NOT NULL,
  icq varchar(9),
  fb varchar(128),
  twitter varchar(128),
  linkedin varchar(128),
  name_custom_1 varchar(128) NOT NULL,
  value_custom_1 varchar(128) NOT NULL,
  name_custom_2 varchar(128) NOT NULL,
  value_custom_2 varchar(128) NOT NULL,
  name_custom_3 varchar(128) NOT NULL,
  value_custom_3 varchar(128) NOT NULL,
  avatar_type int(1) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci");

$sql12=$dbserver->query("CREATE TABLE terr_variables (
  variable varchar(64) default NULL,
  value text
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci");

$sql13=$dbserver->query("CREATE TABLE terr_pm (
  id int(11) NOT NULL auto_increment,
  sender int(11) NOT NULL,
  reciever int(11) NOT NULL,
  subject varchar(128) NOT NULL,
  text text NOT NULL,
  replied int(1) NOT NULL,
  date int(11) NOT NULL,
  seen int(1) NOT NULL,
  hidden int(1) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci");

$sql14=$dbserver->query("CREATE TABLE terr_series (
  id int(11) NOT NULL auto_increment,
  name varchar(128) NOT NULL,
  author int(11) NOT NULL,
  added int(11) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci");

$sql=$dbserver->query("INSERT INTO terr_users (login, password, rights, regdate) values ('admin', '".$admin_password."', 5, ".time().")");

$arr=array("lang" => $this->lang,"sitename" => "Website","login_for_comments" => "1","show_text" => "0","articles_number" => "10","comments_number" => "10","meta_copyright" => "Copyright","meta_keywords" => "","meta_desc" => "","version" => $this->version,"url_type" => $url_type,"use_emoticons" => "1","sort_images_by_name" => "0","sort_comments_from_newest" => "1","rights_add_content" => "1","rights_archive" => "3","rights_bans" => "3","rights_columns" => "3","rights_comments" => "2","rights_unverified_articles" => "2","rights_edit_content" => "1","rights_files" => "1","rights_images" => "1","rights_mail_messages" => "3","rights_meta_tags" => "3","rights_my_content" => "1","rights_options" => "3","rights_overview" => "1","rights_polls" => "3","rights_unpublished_articles" => "3","rights_rights" => "3","rights_sections" => "3","rights_users" => "3","rights_advanced_options" => "4","show_addthis" => "0","show_article_date" => "1","show_article_section" => "1","show_article_comments_number" => "1","show_article_views_number" => "1","show_article_author" => "1","template" => "terr_default","theme" => "terr_default","show_menu_with_last_login" => "0","show_menu_with_last_comments" => "0","show_recent_articles" => "0","show_random_image" => "0","show_unconfirmed_comments" => "1","name_of_menu" => "Sekce","name_of_hp" => "Titulní strana","show_the_link_to_open_whole_article" => "0","show_avatar_in_login_form" => "0","show_accounts_count" => "0","redactor" => "redaktor","corrector" => "korektor","admin1" => "administrátor 1. úrovně","admin2" => "administrátor 2. úrovně","admin3" => "administrátor 3. úrovně","mail_required" => "0");
foreach ($arr as $variable => $value) {
  $sql=$dbserver->query("INSERT INTO terr_variables values ('$variable', '".$value."')");
}

if ($sql14 AND $sql) {
$config=fopen("../config.php", "w+");
$data="<?php\ndefine(\"DB_TYPE\", \"mysql\");\n$"."mysqli=new mysqli(\"".$db_server."\", \"".$db_login."\", \"".$db_password."\", \"".$db_name."\");\n$"."mysqli->query(\"SET CHARACTER SET utf8\");\n$"."mysqli->query(\"SET NAMES utf8\");\n?".">";
fwrite($config, $data);
fclose($config);
}
if (!file_exists("../config.php")) { $this->download_config(htmlspecialchars($data)); } else { $this->clear(); }
}
else {
  if ($db_new==1) { if ($create_db) { $this->db_error=LANG_CANNOT_SELECT_CREATED_DATABASE; $this->get_page(); } else { $this->db_error=LANG_CANNOT_CREATE_DATABASE; $this->get_page(); } }
  elseif($db_new==0) { $this->db_error=LANG_CANNOT_SELECT_ENTERED_DATABASE; $this->get_page(); }
}
}
else { $this->db_error=LANG_CANNOT_CONNECT_TO_DATABASE; $this->get_page(); }
}
elseif ($db_type=="sqlite") {
$sqlite_db=sqlite_open("../components/sqlite/sqlite.db");
if ($sqlite_db) {

$sql1=sqlite_query($sqlite_db, "CREATE TABLE terr_articles (
  id INTEGER PRIMARY KEY NOT NULL,
  quick varchar(160) NOT NULL,
  title varchar(128) NOT NULL,
  type INTEGER NOT NULL,
  hp INTEGER NOT NULL DEFAULT '0',
  complete INTEGER NOT NULL DEFAULT '0',
  confirmed INTEGER NOT NULL DEFAULT '0',
  discussion INTEGER NOT NULL DEFAULT '0',
  section INTEGER NOT NULL,
  perex text,
  text text,
  added INTEGER NOT NULL,
  published INTEGER NOT NULL DEFAULT '0',
  author INTEGER NOT NULL,
  publisher INTEGER NOT NULL DEFAULT '0',
  edited INTEGER NOT NULL DEFAULT '0',
  last_editor INTEGER NOT NULL DEFAULT '0',
  pereximg varchar(128) NOT NULL,
  views INTEGER NOT NULL DEFAULT '0',
  top int(1) NOT NULL DEFAULT '0',
  show_article_info int(1) NOT NULL DEFAULT '0',
  keywords text,
  ReqRights varchar(5) NOT NULL DEFAULT 'All',
  age_limit INTEGER NOT NULL DEFAULT '0',
  series INTEGER NOT NULL DEFAULT '0',
  poll int(1) NOT NULL DEFAULT '0')");

$sql2=sqlite_query($sqlite_db, "CREATE TABLE terr_bans (
  id INTEGER PRIMARY KEY NOT NULL,
  ip varchar(128) NOT NULL,
  nick varchar(128) NOT NULL,
  type int(1) NOT NULL)");

$sql3=sqlite_query($sqlite_db, "CREATE TABLE terr_columns (
  id INTEGER PRIMARY KEY NOT NULL,
  col varchar(5) NOT NULL,
  name varchar(128) NOT NULL,
  content text,
  hidden int(1) NOT NULL DEFAULT '0',
  position INTEGER NOT NULL)");

$sql4=sqlite_query($sqlite_db, "CREATE TABLE terr_comments (
  id INTEGER PRIMARY KEY NOT NULL,
  article INTEGER NOT NULL,
  added INTEGER default NULL,
  author varchar(128) NOT NULL,
  text text NOT NULL,
  confirmed int(1) NOT NULL DEFAULT '0',
  user int(1) NOT NULL DEFAULT '0',
  hidden int(1) NOT NULL DEFAULT '0',
  mail varchar(128) NOT NULL,
  ip varchar(32) NOT NULL)");

$sql5=sqlite_query($sqlite_db, "CREATE TABLE terr_files (
  id INTEGER PRIMARY KEY NOT NULL,
  file varchar(128) NOT NULL,
  added INTEGER NOT NULL,
  uploader INTEGER NOT NULL)");

$sql6=sqlite_query($sqlite_db, "CREATE TABLE terr_images (
  id INTEGER PRIMARY KEY NOT NULL,
  section INTEGER NOT NULL,
  file varchar(128) NOT NULL,
  added INTEGER NOT NULL,
  title varchar(128),
  uploader INTEGER NOT NULL)");

$sql7=sqlite_query($sqlite_db, "CREATE TABLE terr_images_sections (
  id INTEGER PRIMARY KEY NOT NULL,
  quick varchar(160) NOT NULL,
  name varchar(128) NOT NULL,
  public int(1) NOT NULL DEFAULT '0')");

$sql8=sqlite_query($sqlite_db, "CREATE TABLE terr_polls (
  id INTEGER PRIMARY KEY NOT NULL,
  name varchar(128) NOT NULL,
  visible int(1) NOT NULL DEFAULT '0',
  type int(1) NOT NULL,
  author INTEGER NOT NULL)");

$sql9=sqlite_query($sqlite_db, "CREATE TABLE terr_polls_data (
  id INTEGER PRIMARY KEY NOT NULL,
  poll INTEGER NOT NULL,
  answer varchar(128) NOT NULL,
  votes INTEGER NOT NULL DEFAULT '0')");

$sql10=sqlite_query($sqlite_db, "CREATE TABLE terr_sections (
  id INTEGER PRIMARY KEY NOT NULL,
  quick varchar(160) NOT NULL,
  name varchar(128) NOT NULL,
  highlight int(1) NOT NULL DEFAULT '0',
  outlink text,
  ntbl int(1) NOT NULL DEFAULT '0',
  position int(3) NOT NULL DEFAULT '0',
  hidden int(1) NOT NULL DEFAULT '0',
  level int(3) NOT NULL DEFAULT '0',
  module int(1) NOT NULL DEFAULT '0',
  higher int(3) NOT NULL DEFAULT '0')");

$sql11=sqlite_query($sqlite_db, "CREATE TABLE terr_users (
  id INTEGER PRIMARY KEY NOT NULL,
  login varchar(128) NOT NULL,
  password varchar(128) NOT NULL,
  rights int(1) NOT NULL DEFAULT '0',
  jabber varchar(128),
  skype varchar(128),
  about text,
  mail varchar(128),
  realname varchar(128),
  regdate int(11) NOT NULL DEFAULT '0',
  lastvisit int(11) NOT NULL DEFAULT '0',
  birthday varchar(11),
  birth_type int(1) NOT NULL DEFAULT '0',
  mail_messages int(1) NOT NULL DEFAULT '0',
  msn varchar(128),
  avatar varchar(4),
  sign text,
  user_check text,
  fb varchar(128),
  twitter varchar(128),
  linkedin varchar(128),
  name_custom_1 varchar(128),
  value_custom_1 varchar(128),
  name_custom_2 varchar(128),
  value_custom_2 varchar(128),
  name_custom_3 varchar(128),
  value_custom_3 varchar(128),
  icq varchar(9),
  avatar_type int(1))");

$sql12=sqlite_query($sqlite_db, "CREATE TABLE terr_variables (
  variable varchar(64) default NULL,
  value text)");

$sql13=sqlite_query($sqlite_db, "CREATE TABLE terr_pm (
  id INTEGER PRIMARY KEY NOT NULL,
  sender int(11) NOT NULL DEFAULT '0',
  reciever int(11) NOT NULL DEFAULT '0',
  subject varchar(128),
  text text,
  replied int(1) NOT NULL DEFAULT '0',
  date int(11) NOT NULL DEFAULT '0',
  seen int(1) NOT NULL DEFAULT '0',
  hidden int(1) NOT NULL DEFAULT '0')");

$sql14=sqlite_query($sqlite_db, "CREATE TABLE terr_series (
  id INTEGER PRIMARY KEY NOT NULL,
  name varchar(128),
  author INTEGER NOT NULL,
  added INTEGER NOT NULL)");

$sql=sqlite_query($sqlite_db, "INSERT INTO terr_users (login, password, rights, regdate) values ('admin', '".$admin_password."', 5, ".time().")");

$arr=array("lang" => $this->lang,"sitename" => "Website","login_for_comments" => "1","show_text" => "0","articles_number" => "10","comments_number" => "10","meta_copyright" => "Copyright","meta_keywords" => "","meta_desc" => "","version" => $this->version,"url_type" => $url_type,"use_emoticons" => "1","sort_images_by_name" => "0","sort_comments_from_newest" => "1","rights_add_content" => "1","rights_archive" => "3","rights_bans" => "3","rights_columns" => "3","rights_comments" => "2","rights_unverified_articles" => "2","rights_edit_content" => "1","rights_files" => "1","rights_images" => "1","rights_mail_messages" => "3","rights_meta_tags" => "3","rights_my_content" => "1","rights_options" => "3","rights_overview" => "1","rights_polls" => "3","rights_unpublished_articles" => "3","rights_rights" => "3","rights_sections" => "3","rights_users" => "3","rights_advanced_options" => "4","show_addthis" => "0","show_article_date" => "1","show_article_section" => "1","show_article_comments_number" => "1","show_article_views_number" => "1","show_article_author" => "1","template" => "terr_default","theme" => "terr_default","show_menu_with_last_login" => "0","show_menu_with_last_comments" => "0","show_recent_articles" => "0","show_random_image" => "0","show_unconfirmed_comments" => "1","name_of_menu" => "Sekce","name_of_hp" => "Titulní strana","show_the_link_to_open_whole_article" => "0","show_avatar_in_login_form" => "0","show_accounts_count" => "0","redactor" => "redaktor","corrector" => "korektor","admin1" => "administrátor 1. úrovně","admin2" => "administrátor 2. úrovně","admin3" => "administrátor 3. úrovně","mail_required" => "0");
foreach ($arr as $variable => $value) {
  $sql=sqlite_query($sqlite_db, "INSERT INTO terr_variables (variable, value) values ('$variable', '".$value."')");
}

if ($sql14 AND $sql) {
$config=fopen("../config.php", "w+");
$data="<?php\ndefine(\"DB_TYPE\", \"sqlite\");\ndefine(\"DB_HOST\", sqlite_open(\"components/sqlite/sqlite.db\"));\n?".">";
fwrite($config, $data);
fclose($config);
}
if (!file_exists("../config.php")) { $this->download_config(htmlspecialchars($data)); } else { $this->clear(); }
} else { $this->db_error=LANG_CANNOT_OPEN_DATABASE; $this->get_page(); }
}
}

private function check_file_permissions() {
@chmod("../avatars", 0777);
@chmod("../files", 0777);
@chmod("../gallery", 0777);
@chmod("../gallery/thumbs", 0777);
@chmod("../components/sqlite", 0777);
@chmod("../backups", 0777);
@chmod("../install", 0777);
if (substr(sprintf("%o", @fileperms("../avatars")), -3) != "0777") { return false; }
if (substr(sprintf("%o", @fileperms("../files")), -3) != "0777") { return false; }
if (substr(sprintf("%o", @fileperms("../gallery")), -3) != "0777") { return false; }
if (substr(sprintf("%o", @fileperms("../gallery/thumbs")), -3) != "0777") { return false; }
if (substr(sprintf("%o", @fileperms("../components/sqlite")), -3) != "0777") { return false; }
if (substr(sprintf("%o", @fileperms("../backups")), -3) != "0777") { return false; }
if (substr(sprintf("%o", @fileperms("../install")), -3) != "0777") { return false; }
return true;
}

private function download_config($data) {
echo "<div id=\"content\">
  <h2>Error</h2>
  <div class=\"rect error\" style=\"display: block;\">
  ".LANG_COULDNT_CREATE_CONFIG."
  <textarea style=\"margin-top: 10px; width: 240px; height: 60px;\">".$data."</textarea>
  </div>
  </div>";
}

private function clear() {
$files=glob("../install/*.*");
foreach($files as $value) { chmod($value, 0777); unlink($value); }
chmod("../install", 0777);
rmdir("../install");
if (!file_exists("../install")) { header("Location: ../index.php"); }
else {
echo "<div id=\"content\"><div class=\"rect error\" style=\"display: block\">".LANG_COULDNT_REMOVE_INSTALL_DIR."</div>
<input type=\"submit\" value=\"".LANG_RELOAD."\" onclick=\"location.reload();\" /></div>";
}
}
}
?>