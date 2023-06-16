<?php
class Index {
  public $db;
  public $config_variables;
  public $urls;
  public $login;
  public $captcha;
  public $status_messages;

  public function __construct() {
    $this->db = new Db_layer();
    $this->config_variables = new Config_variables();
    $this->urls = new Urls();
    $this->login = new Login();
    $this->captcha = new Captcha();
    $this->status_messages = new Status_messages();
  }

  //---[CZ]: kontrola banu
  public function check_ban() {
    return $this->db->result($this->db->query("SELECT COUNT(id) FROM terr_bans WHERE ip='".$_SERVER["REMOTE_ADDR"]."'"));
  }

  public function get_title() {

    if ($_GET["gallery"]!="") { echo $this->current_gallery("name")." | "; }
    if ($_GET["function"]=="registration") { echo LANG_REGISTRATION." | "; }
    if ($_GET["article"]!="") { echo $this->current_article("title")." | "; }
    if ($_GET["function"]=="settings") { echo $this->current_article("title")." | "; }
    if ($_GET["function"]=="search") { echo LANG_SEARCH." | "; }
    if ($_GET["function"]=="inbox" || $_GET["function"]=="outbox" || $_GET["function"]=="new-message") { echo LANG_PRIVATE_MESSAGES." | "; }
    if ($_GET["function"]=="files") { echo LANG_FILES." | "; }
    if ($_GET["profile"]!="") {
    $sql=$this->db->query("SELECT login, realname FROM terr_users WHERE login='".htmlspecialchars($_GET["profile"])."'");
    $sql=$this->db->fetch_array($sql);
    if ($sql["realname"]!="") { echo $sql["realname"]; } else { echo $sql["login"]; } echo " | "; }
    if ($_GET["section"]!="") { echo $this->current_section_by_quick("name")." | "; }
    echo $this->config_variables->get("sitename");
  }

  public function get_h1_bar() {
    if ($_GET["article"]!="") { echo "<a href=\"".$this->urls->section($this->current_article_section("quick"))."\">".$this->current_article_section("name")."</a> &raquo; ".$this->current_article("title"); }
    if ($_GET["gallery"]!="") { echo LANG_GALLERIES." &raquo; ".$this->current_gallery("name"); }
    if ($_GET["profile"]!="") {
    echo LANG_USER_PROFILES." &raquo; ";
    $sql=$this->db->query("SELECT login, realname FROM terr_users WHERE login='".htmlspecialchars($_GET["profile"])."'");
    $sql=$this->db->fetch_array($sql);
    if ($sql["realname"]!="") { echo $sql["realname"]; } else { echo $sql["login"]; } }
    if ($_GET["function"]=="settings") { echo $this->current_article("title"); }
    if ($_GET["function"]=="search") { echo LANG_SEARCH." &raquo; ".$_POST["search"]; }
    if ($_GET["function"]=="registration") { echo LANG_REGISTRATION; }
    if ($_GET["function"]=="inbox" || $_GET["function"]=="outbox" || $_GET["function"]=="new-message") { echo LANG_PRIVATE_MESSAGES; }
    if ($_GET["function"]=="inbox") { echo " &raquo; ".LANG_INBOX; }
    if ($_GET["function"]=="outbox") { echo " &raquo; ".LANG_OUTBOX; }
    if ($_GET["function"]=="new-message") { echo " &raquo; ".LANG_NEW_MESSAGE; }
    if ($_GET["function"]=="files") { echo LANG_FILES; if ($_GET["page"]!="") { echo " &raquo; ".LANG_OF_USER." ".$_GET["page"]; } }
    if ($_GET["function"]=="" && $_GET["section"]=="" && $_GET["article"]=="" && $_GET["profile"]=="" && $_GET["gallery"]=="") { echo $this->config_variables->get("name_of_hp"); }
    if ($_GET["section"]!="") { echo $this->config_variables->get("name_of_menu")." &raquo; ".$this->current_section_by_quick("name"); }
  }

  //---[CZ]: získání položek sloupců
  public function get_column_items($column) {
    $sql=$this->db->query("SELECT name, content FROM terr_columns WHERE col='$column' && hidden=0 ORDER BY position");
    while($data=$this->db->fetch_array($sql)) {
      echo "<figure><h2>".$this->db->unescape($data["name"])."</h2>\n\n".$this->db->unescape($data["content"])."</figure>\n\n";
    }
  }

  //---[CZ]: náhodný obrázek
  public function get_random_image() {
    if ($this->config_variables->get("show_random_image")==1) {
      echo "<figure><h2>".LANG_RANDOM_IMAGE."</h2>\n\n";
      $sql=$this->db->query("SELECT terr_images.file, terr_images.title FROM terr_images INNER JOIN terr_images_sections ON terr_images.section = terr_images_sections.id WHERE terr_images_sections.public=1".((DB_TYPE=="mysql")?" ORDER BY RAND()":" ORDER BY RANDOM()")." LIMIT 1");
      while($data=$this->db->fetch_array($sql)) {
        $thumb=explode(".", $data["file"]);
        $pack = "<p id=\"randomimage\"><a href=\"".$this->urls->root()."gallery/".$data["file"]."\" rel=\"lightbox\"><img src=\"".$this->urls->root()."gallery/thumbs/".$thumb[0].".jpg\" alt=\"".str_replace("\"", "", $data["title"])."\" /></a></p>";
      }
      if ($pack!="") { echo $pack; } else { echo "<p><em>".LANG_NO_IMAGES_FOUND."</em></p>"; }
      echo "</figure>";
    }
  }

  //---[CZ]: aktuální články
  public function get_recent_articles() {
    if ($this->config_variables->get("show_recent_articles")==1) {
      echo "<figure><h2>".LANG_RECENT_ARTICLES."</h2>\n\n";
      $sql=$this->db->query("SELECT quick, title, type FROM terr_articles WHERE published!=0 && published<=".time()." ORDER BY published DESC, type DESC LIMIT 0, ".floor($this->config_variables->get("articles_number")/2));
      while($data=$this->db->fetch_array($sql)) {
        $pack = "<li><a href=\"".$this->urls->article($data["quick"])."\">".(($data["type"]==3)?"<strong>".$data["title"]."</strong>":$data["title"])."</a></li>";
      }
      if ($pack!="") { echo "<ul>".$pack."</ul>"; } else { echo "<p><em>".LANG_NO_RECENT_ARTICLES_FOUND."</em></p>"; }
      echo "</figure>";
    }
  }

  //---[CZ]: získání sekcí - začátek
  public function get_sections() {
    echo "<figure><h2>".$this->config_variables->get("name_of_menu")."</h2>\n\n<ul>";
    echo "<li".(($_GET["section"]=="" && $_GET["gallery"]=="" && $_GET["profile"]=="" && $_GET["function"]=="" && $_GET["article"]=="")?" class=\"active-item\"":"")."><a href=\"".$this->urls->root()."index.php\">".$this->config_variables->get("name_of_hp")."</a></li>";
    $this->sections();
    echo "</ul></figure>";
  }

  //---[CZ]: hlavní anketa
  public function get_main_poll() {
    $count=$this->db->result($this->db->query("SELECT COUNT(id) FROM terr_polls WHERE visible=1 && type=1"));
    if ($count>0) {
    echo "<figure><h2>".LANG_POLL."</h2>\n\n";
    $poll=$this->db->query("SELECT id, name FROM terr_polls WHERE visible=1 && type=1 LIMIT ".rand(0, $count-1).",1");
    $poll=$this->db->fetch_array($poll);
    if (!isset($_COOKIE["votet_".$poll["id"]])) { echo "<form action=\"".$this->urls->root()."index.php?action=vote\" method=\"post\">"; }
    echo "<p><strong>".$this->db->unescape($poll["name"])."</strong></p>\n\n<p>";
    $all_votes=$this->db->result($this->db->query("SELECT sum(votes) FROM terr_polls_data WHERE poll=".$poll["id"].""));
    $print=$this->db->query("SELECT id, answer, votes FROM terr_polls_data WHERE poll=".$poll["id"]." ORDER BY id");
    while (list($id, $answer, $votes) = $this->db->fetch_row($print)) {
      if (!isset($_COOKIE["votet_".$poll["id"]])) { echo "<input type=\"radio\" name=\"vote\" value=\"$id\" /> "; }
      echo $this->db->unescape($answer)."<span class=\"float-right\">$votes | ".(($votes!=0)?round(100*($votes/$all_votes)):0)."%</span><span class=\"poll\" style=\"width: ".(($votes!=0)?1.25*round((100*$votes)/$all_votes):"10")."px;\"></span>\n";
    }
    echo "</p>\n\n<p>".LANG_TOTAL_VOTES.": $all_votes</p>\n\n";
    if (!isset($_COOKIE["votet_".$poll["id"]])) { echo "<div>\n\n<p><input type=\"hidden\" name=\"poll_id\" value=\"".$poll["id"]."\" /><input type=\"submit\" class=\"greenbutton\" value=\"".LANG_VOTE."\" /></p>\n\n</div></form>"; }
    echo "</figure>";
    }
  }

  //---[CZ]: článková anketa
  public function get_article_poll($id) {
    $poll=$this->db->query("SELECT id, name, visible FROM terr_polls WHERE id=$id");
    $poll=$this->db->fetch_array($poll);
    if ($poll["visible"]==1) {
      echo "<article><h2 class=\"title\">".LANG_POLL."</h2><form action=\"".$this->urls->root()."index.php?action=vote\" method=\"post\">
      <p><strong>".$this->db->unescape($poll["name"])."</strong></p>\n\n<p>";
      $all_votes=$this->db->result($this->db->query("SELECT sum(votes) FROM terr_polls_data WHERE poll=".$poll["id"].""));
      $print=$this->db->query("SELECT id, answer, votes FROM terr_polls_data WHERE poll=".$poll["id"]." ORDER BY id");
      while (list($id, $answer, $votes) = $this->db->fetch_row($print)) {
        if (!isset($_COOKIE["votet_".$poll["id"]])) { echo "<input type=\"radio\" name=\"vote\" value=\"$id\" /> "; }
        echo $this->db->unescape($answer)." ($votes)<span class=\"poll\" style=\"width: ".(($votes!=0)?2*round((100*$votes)/$all_votes):"10")."px;\"></span>\n";
      }
      echo "</p>\n\n<p>".LANG_TOTAL_VOTES.": $all_votes</p>\n\n";
      if (!isset($_COOKIE["votet_".$poll["id"]])) { echo "<div>\n\n<p><input type=\"hidden\" name=\"poll_id\" value=\"".$poll["id"]."\" /><input type=\"submit\" class=\"greenbutton\" value=\"".LANG_VOTE."\" /></p>\n\n</div>"; }
      echo "</form></article>";
    }
  }

  //---[CZ]: výpis galerií
  public function get_galleries() {
    if ($this->db->result($this->db->query("SELECT COUNT(id) FROM terr_images_sections WHERE public=1"))>0) {
      echo "<figure><h2>".LANG_GALLERIES."</h2>\n\n<ul>";
      $print=$this->db->query("SELECT quick, name FROM terr_images_sections WHERE public=1 ORDER BY name");
      while($data=$this->db->fetch_array($print)) {
        echo "<li".(($_GET["gallery"]==$data["quick"])?" class=\"active-item\"":"")."><a href=\"".$this->urls->gallery($data["quick"])."\">".$data["name"]."</a></li>\n";
      }
      echo "</ul></figure>";
    }
  }

  //---[CZ]: přihlašovací formulář
  public function get_login_form() {
    if (isset($_SESSION["user_id"])) {
      $sql=$this->db->query("SELECT id, login, mail, realname, avatar, avatar_type FROM terr_users WHERE id=".intval($_SESSION["user_id"]));
      $sql=$this->db->fetch_array($sql);
      echo "<figure><h2>".(($sql["realname"]!="")?$sql["realname"]:$sql["login"])."</h2><ul>";
      if ($this->config_variables->get("show_avatar_in_login_form")==1) { echo $this->get_avatar(1, $sql["avatar_type"], $sql["avatar"], $sql["mail"], $sql["id"], "account"); }
      if ($this->login->check_rights()>0) { echo "<li><a href=\"".$this->urls->root()."admin.php\"><strong>".LANG_ADMINISTRATION."</strong></a></li>"; }
      echo "<li><a href=\"".$this->urls->profile($sql["login"])."\">".LANG_PROFILE."</a></li>
      <li><a href=\"".$this->urls->pm("blank", "inbox")."\">".$this->unseen_message().LANG_PRIVATE_MESSAGES."</a></li>";
      if ($_GET["article"]!="") {
        if ($this->login->check_rights()>=$this->config_variables->get("rights_edit_content")) {
          $sql=$this->db->query("SELECT id, ReqRights FROM terr_articles WHERE quick='".$_GET["article"]."'");
          $article=$this->db->fetch_array($sql);
          if ($this->login->check_rights()>=$article["ReqRights"] || $article["ReqRights"]=="All") { echo "<li><a href=\"/admin.php?function=edit_content&amp;tab=article&amp;id=".$article["id"]."\">".LANG_EDIT_THIS_ARTICLE."</a></li>"; }
        }
      }
      elseif ($_GET["section"]!="") {
        if ($this->login->check_rights()>=$this->config_variables->get("rights_sections")) {
          $article=$this->db->result($this->db->query("SELECT id FROM terr_sections WHERE quick='".$_GET["section"]."'"));
          echo "<li><a href=\"/admin.php?function=sections&amp;part=edit&amp;id=".intval($article)."\">".LANG_EDIT_THIS_SECTION."</a></li>";
        }
      }
      echo "<li><a href=\"".$this->urls->root()."index.php?action=logout\"><em>".LANG_LOGOUT."</em></a></li>
      </ul></figure>";
    }
    else {
      echo "<figure><h2>".LANG_LOGIN."</h2>
      <form action=\"".$this->urls->root()."index.php?action=login\" name=\"login_form\" id=\"login_form\" method=\"post\">
      <div>
      <p><strong>".LANG_USERNAME.":</strong><br /><input autofocus autocomplete=\"on\" type=\"text\" name=\"login\" class=\"login-input\" onBlur=\"check_login_nick(this.value)\" /></p>
      <p><strong>".LANG_PASSWORD.":</strong><br /><input type=\"password\" name=\"password\" class=\"login-input\" /></p>
      <p><input type=\"submit\" name=\"submit\" value=\"".LANG_LOGIN."\" /> <a href=\"".$this->urls->fction("registration")."\">".LANG_REGISTRATION."</a></p>
      </div>
      </form></figure>";
    }
  }

  //---[CZ]: hlasování
  public function vote() {
    if (isset($_POST["vote"]) && !isset($_COOKIE["votet_".$_POST["poll_id"]])) {
      $sql=$this->db->query("SELECT votes FROM terr_polls_data WHERE id=".intval($_POST["vote"]));
      $sql=$this->db->fetch_array($sql);
      $sql=$this->db->query("UPDATE terr_polls_data SET votes=".($sql["votes"]+1)." WHERE id=".intval($_POST["vote"]));
      setcookie("votet_".$_POST["poll_id"], "1", time()+31104000);
      header("Location: ".$_SERVER["HTTP_REFERER"]);
    }
  }

  //---[CZ]: vypsání článků
  public function print_articles() {
    if ($_GET["section"]=="") {
        $where="published!=0 && published<=".time()." && hp=1"; }
    else {
        $get_section = $this->current_section_by_quick("id");
        $where="published!=0 && published<=".time()." && section=".intval($get_section);
    }

    if ($_GET["section"]!="" && $_SESSION["user_id"]=="" && $this->db->result($this->db->query("SELECT ntbl FROM terr_sections WHERE id=".intval($get_section)))==1) {
      echo "<h3>".LANG_YOU_MUST_BE_LOGGED_IN_FOR_ACCESS_TO_THIS_SECTION."</h3>"; }
    else {
      $pages=$this->db->result($this->db->query("SELECT count(id) FROM terr_articles WHERE $where"));
      if ($pages!=0) {
        $pages=ceil($pages/$this->config_variables->get("articles_number"));
        $sql=$this->db->query("SELECT id, quick, title, discussion, section, type, perex, text, author, published, pereximg, views, ReqRights FROM terr_articles WHERE $where ORDER BY top DESC, published DESC, type DESC LIMIT ".(($_GET["section"]=="")?0:$_GET["page"]*$this->config_variables->get("articles_number")-$this->config_variables->get("articles_number")).",".$this->config_variables->get("articles_number"));
        while(list($id, $quick, $title, $discussion, $section, $type, $perex, $text, $author, $published, $pereximg, $views, $ReqRights)=$this->db->fetch_row($sql)) {
          echo "<article><h2><a href=\"".$this->urls->article($quick)."\">".$this->db->unescape($title)."</a></h2>\n\n";
          $this->article_info($id);
          if ($perex!="") {
            echo "<div class=\"perex\">".(($pereximg!="")?"<img src=\"$pereximg\" alt=\"".$this->db->unescape(str_replace("\"", "", $title))."\" class=\"perex-img\" />":"").$this->db->unescape($perex).(($this->config_variables->get("show_the_link_to_open_whole_article")==1 && $text!="")?"<span class=\"float-ending\"><!-- --></span><div class=\"open-link\"><a href=\"".$this->urls->article($quick)."\">".LANG_READ_WHOLE_ARTICLE."</a></div>":"")."<span class=\"float-ending\"><!-- --></span></div>\n\n"; }

          //---[CZ]: pokud je zapnuto, při prázdném perexu zobrazíme rovnou text článku
          if ($perex=="" && $this->config_variables->get("show_text")==1 && ($this->login->check_rights() >= $ReqRights && isset($_SESSION["user_id"]) || $ReqRights=="All")) { echo $this->db->unescape($text); }
          echo "</article>";
        }
        if ($_GET["section"]!="") {
          $this->paging("section", $this->current_section_by_quick("quick"), $pages); }
      }
      else {
        echo "<h3>".LANG_NO_ARTICLES_FOUND."</h3>"; }
    }
  }

  public function paging($type, $id, $pages) {
    if ($pages>1) {
      echo "<h4 class=\"paging text-align-right\">";

      if ($type=="article") {
        if (URL_TYPE=="static") { $url="/articles/$id/"; $end="/#comments"; }
        if (URL_TYPE=="dynamic") { $url="./index.php?article=$id&amp;page="; $end="#comments"; }
      }
      if ($type=="section") {
        if (URL_TYPE=="static") { $url="/sections/$id/"; $end="/"; }
        if (URL_TYPE=="dynamic") { $url="./index.php?section=$id&amp;page="; $end=""; }
      }
      if ($pages>1 && $_GET["page"]>1) {
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
        if ($_GET["page"]<7) {
          echo (($_GET["page"]==1)?"1":" <a href=\"$url"."1"."$end\">1</a>"); }
        else {
          echo (($_GET["page"]==1)?"1 ...":" <a href=\"$url"."1"."$end\">1</a> ...");
        }
        $i=$_GET["page"]-5;
        $i2=$_GET["page"]+4;
        while ($i<$i2) {
          $i++;
          if ($i>1 && $i<$pages) {
            echo (($i==$_GET["page"])?" $i":" <a href=\"$url$i$end\">$i</a>");
          }
        }
        if ($_GET["page"]>$pages-6) {
          echo (($_GET["page"]==$page)?" $pages":" <a href=\"$url$pages$end\">$pages</a>"); }
        else {
          echo (($_GET["page"]==$page)?" ... $pages":" ... <a href=\"$url$pages$end\">$pages</a>");
        }
      }
      if ($pages>1 && $_GET["page"]<$pages) {
        $i=$_GET["page"]+1;
        echo "&nbsp;&nbsp;&nbsp;<a href=\"$url$i$end\">&raquo;</a>";
      }
      echo "</h4>";
    }
  }

  public function current_article($column) {
    $sql=$this->db->result($this->db->query("SELECT $column FROM terr_articles WHERE quick='".$this->db->escape($_GET["article"])."'"));
    return $this->db->unescape($sql);
  }
  public function current_section($column) {
    $sql=$this->db->result($this->db->query("SELECT $column FROM terr_sections WHERE id=".intval($_GET["section"])));
    return $this->db->unescape($sql);
  }
  public function current_section_by_quick($column) {
    $sql=$this->db->result($this->db->query("SELECT $column FROM terr_sections WHERE quick='".$_GET["section"]."'"));
    return $this->db->unescape($sql);
  }
  public function current_article_section($column) {
    $section=$this->db->result($this->db->query("SELECT section FROM terr_articles WHERE quick='".$_GET["article"]."'"));
    $sql=$this->db->result($this->db->query("SELECT $column FROM terr_sections WHERE id=".intval($section)));
    return $this->db->unescape($sql);
  }
  public function current_gallery($column) {
    $sql=$this->db->result($this->db->query("SELECT name FROM terr_images_sections WHERE quick='".$this->db->escape($_GET["gallery"])."'"));
    return $this->db->unescape($sql);
  }
  public function current_user($what, $where, $who) {
    $sql=$this->db->result($this->db->query("SELECT $what FROM terr_users WHERE $where='".$who."'"));
    return $this->db->unescape($sql);
  }
  public function unseen_message() {
    $num = $this->db->result($this->db->query("SELECT count(id) FROM terr_pm WHERE seen=0 && hidden=0 && reciever=".intval($_SESSION["user_id"])));
    return (($num==0)?"":"<span id=\"unseen_message\"> (".$num.")</span>");
  }
  public function date_time($when) {
    if (date("d.m.y", $when)==date("d.m.y", time())) { return LANG_TODAY.date(" | H:i", $when); }
    elseif (date("d.m.y", $when)==date("d.m.y", time()-86400)) { return LANG_YESTERDAY.date(" | H:i", $when); }
    else { return date("d.m.y | H:i", $when); }
  }
  public function get_avatar($user, $type, $avatar, $mail, $id, $which) {
    if ($user==1 && $type==1 && $mail!="") {
      return "<img src=\"http://www.gravatar.com/avatar/".md5(strtolower(trim($mail))).".png?d=mm&s=45\" class=\"$which-avatar\" />"; }
    elseif ($user==1 && $type==0 && $avatar!="") {
      return "<img src=\"".$this->urls->root()."avatars/".$id.".".$avatar."\" alt=\"avatar\" class=\"$which-avatar\" />"; }
    elseif ($user==0 && $type=="" && $mail!="") {
      return "<img src=\"http://www.gravatar.com/avatar/".md5(strtolower(trim($mail))).".png?d=mm&s=45\" class=\"$which-avatar\" />"; }
    else {
      return "<img src=\"http://www.gravatar.com/avatar/".md5(strtolower(trim(0))).".png?d=mm&s=45\" class=\"$which-avatar\" />"; }
  }

  public function rank($rights) {
    if ($rights==0) { return LANG_NONE; }
    if ($rights==1) { return $this->config_variables->get("redactor"); }
    if ($rights==2) { return $this->config_variables->get("corrector"); }
    if ($rights==3) { return $this->config_variables->get("admin1"); }
    if ($rights==4) { return $this->config_variables->get("admin2"); }
    if ($rights==5) { return $this->config_variables->get("admin3"); }
  }

  //---[CZ]: přídání komentáře
  public function add_comment() {
    if (!isset($_POST["author"])) { $_POST["author"]=""; }
    if (!isset($_POST["hash"])) { $_POST["hash"]=""; }

    if ((isset($_SESSION["user_id"]) || substr(md5($_POST["hash"]), 0, 5)==$_POST["check"]) && $_POST["text"]!="") {
    if ($_POST["author"]!="" || isset($_SESSION["user_id"])) {
      if (isset($_SESSION["user_id"])) {
        $registered_user=1;
        $nick=intval($_SESSION["user_id"]);
        $mail=""; }
      else {
        $nick=$_POST["author"];
        $registered_user=0;
      }
      $text=htmlspecialchars($_POST["text"], ENT_NOQUOTES);
      $text=str_replace("\r\n", "<br />", $text);
      if (isset($_POST["mail"])) {
        $mail=htmlspecialchars($_POST["mail"], ENT_NOQUOTES);
        $mail=str_replace("\r\n", "<br />", $mail);
      }
      if ($this->config_variables->get("use_emoticons")==1) {
        $text=str_replace("[.smile.]", "<img src=\"".$this->urls->root()."components/images/emoticons/", $text);
        $text=str_replace("[./smile.]", ".gif\" alt=\"Emoticon\" />", $text);
      }
      $sql=$this->db->query("INSERT INTO terr_comments (article, added, author, text, confirmed, user, hidden, mail, ip) values (
      ".intval($_GET["article_id"]).",
      '".time()."',
      '".htmlspecialchars($nick)."',
      '$text',
      '0',
      $registered_user,
      '0',
      '$mail',
      '".$_SERVER["REMOTE_ADDR"]."')");
    }}
    header("Location: ".$_SERVER["HTTP_REFERER"]);
  }

  public function print_comments() {
    if ($_GET["page"]=="") { $skip=0; } else { $skip=$_GET["page"]*$this->config_variables->get("comments_number")-$this->config_variables->get("comments_number"); }
    $comments=$this->db->result($this->db->query("SELECT count(id) FROM terr_comments WHERE article=".$this->current_article("id")));
    $pages=ceil($comments/$this->config_variables->get("comments_number"));
    if ($this->config_variables->get("sort_comments_from_newest")==1) { $sort="added DESC"; $i=$comments-$skip+1; } else { $sort="added"; $i=$skip; }
    $i2=0;
    $sql=$this->db->query("SELECT id, mail, article, added, author, text, confirmed, user, hidden, ip FROM terr_comments WHERE article=".$this->current_article("id")." ORDER BY $sort LIMIT $skip,".$this->config_variables->get("comments_number"));
    while(list($id, $mail, $article, $added, $author, $text, $confirmed, $user, $hidden, $ip)=$this->db->fetch_row($sql)) {
      if ($user==1) {
        $author_info=$this->db->query("SELECT login, rights, mail, realname, avatar, avatar_type, sign FROM terr_users WHERE id=$author");
        $author_info=$this->db->fetch_array($author_info);
        $pocet = $this->db->result($this->db->query("SELECT COUNT(id) FROM terr_comments WHERE author=$author"));
      }
      if ($this->config_variables->get("sort_comments_from_newest")==1) { $i=$i-1; } else { $i++; }
      if ($hidden==0) {
        $i2++;
        echo "<div id=\"div$id\">
        <div class=\"comment-$i2\">
        <p class=\"comment-info\"><strong><em>$i)</em>&nbsp;";
        if ($user==1 && $author_info["login"]=="") { echo LANG_THIS_USER_NO_LONGER_EXISTS; }
        elseif ($user==1 && $author_info["login"]!="") { echo "<a href=\"".$this->urls->profile($author_info["login"])."\">".(($author_info["realname"]!="")?$author_info["realname"]:$author_info["login"])."</a>"; } else { echo $this->db->unescape($author); }
        echo "</strong> (".$this->date_time($added).")";
        if (isset($_SESSION["user_id"])) {
          if ($author==$_SESSION["user_id"] || $this->login->check_access("comments")==1) { echo " &bull; <a href=\"#$id\" onclick=\"update_comment($id);\">".LANG_EDIT."</a> &bull; <a href=\"#$id\" onclick=\"if (!confirm('".LANG_ARE_YOU_SURE."')) return false; delete_comment($id);\">".LANG_DELETE."</a>"; }
        }
        if ($this->login->check_access("bans")==1) { echo " &bull; $ip"; }
        if ($user==1) { echo "<span class=\"float-right\">".$this->rank($author_info["rights"])."</span>"; }
        if ($user==0) { echo "<span class=\"float-right\">".LANG_UNREGISTERED."</span>"; }
        echo "</p>\n\n<p id=\"$id\">";
        echo $this->get_avatar($user, (($user==1)?$author_info["avatar_type"]:""), (($user==1)?$author_info["avatar"]:""), (($user==1)?$author_info["mail"]:$mail), $author, "comment");
        if ($this->config_variables->get("show_unconfirmed_comments")==1 || $confirmed==1) {
          if ($this->config_variables->get("use_emoticons")==1) {
            echo $this->db->unescape($this->smiles($text)); }
          else {
            echo $this->db->unescape($text);
          }
        }
        else {
          echo "<strong>&rArr; ".LANG_COMMENT_HAS_NOT_BEEN_CONFIRMED_YET."</strong>"; }
        echo "<span class=\"float-ending\"><!-- --></span>";
        if ($user==1) { echo "<span class=\"float-right small-text\">".LANG_NUMBER_OF_COMMENTS.": $pocet</span>"; }
        echo "</p>
        <span class=\"small-text\">".(($user==1)?$author_info["sign"]:"")."</span>";
        echo "<span class=\"float-ending\"><!-- --></span>";
        echo "</div>
        </div>";
        if ($i2==2) { $i2=0; }
      }
    }
    $this->paging("article", $this->current_article("quick"), $pages);
  }

  public function smiles($text) {

    $text=str_replace(":)", "<img src=\"".$this->urls->root()."components/images/emoticons/smile01.gif\" alt=\":)\" />", $text);
    $text=str_replace(":-)", "<img src=\"".$this->urls->root()."components/images/emoticons/smile01.gif\" alt=\":-)\" />", $text);
    $text=str_replace(";)", "<img src=\"".$this->urls->root()."components/images/emoticons/smile02.gif\" alt=\";)\" />", $text);
    $text=str_replace(";-)", "<img src=\"".$this->urls->root()."components/images/emoticons/smile02.gif\" alt=\";-)\" />", $text);
    $text=str_replace(":D", "<img src=\"".$this->urls->root()."components/images/emoticons/smile07.gif\" alt=\":D\" />", $text);
    $text=str_replace(":-D", "<img src=\"".$this->urls->root()."components/images/emoticons/smile07.gif\" alt=\":-D\" />", $text);
    $text=str_replace("xD", "<img src=\"".$this->urls->root()."components/images/emoticons/smile07.gif\" alt=\"xD\" />", $text);
    $text=str_replace(":(", "<img src=\"".$this->urls->root()."components/images/emoticons/smile04.gif\" alt=\":(\" />", $text);
    $text=str_replace(":-(", "<img src=\"".$this->urls->root()."components/images/emoticons/smile04.gif\" alt=\":-(\" />", $text);
    $text=str_replace(";(", "<img src=\"".$this->urls->root()."components/images/emoticons/smile05.gif\" alt=\";(\" />", $text);
    $text=str_replace(";-(", "<img src=\"".$this->urls->root()."components/images/emoticons/smile05.gif\" alt=\";-(\" />", $text);
    $text=str_replace(":'(", "<img src=\"".$this->urls->root()."components/images/emoticons/smile05.gif\" alt=\":'(\" />", $text);
    $text=str_replace(":'-(", "<img src=\"".$this->urls->root()."components/images/emoticons/smile05.gif\" alt=\":'-(\" />", $text);
    $text=str_replace(":p", "<img src=\"".$this->urls->root()."components/images/emoticons/smile06.gif\" alt=\":p\" />", $text);
    $text=str_replace(":-p", "<img src=\"".$this->urls->root()."components/images/emoticons/smile06.gif\" alt=\":-p\" />", $text);
    $text=str_replace(":P", "<img src=\"".$this->urls->root()."components/images/emoticons/smile06.gif\" alt=\":P\" />", $text);
    $text=str_replace(":-P", "<img src=\"".$this->urls->root()."components/images/emoticons/smile06.gif\" alt=\":-P\" />", $text);
    $text=str_replace("(y)", "<img src=\"".$this->urls->root()."components/images/emoticons/smile08.gif\" alt=\"(yes)\" />", $text);
    $text=str_replace("(n)", "<img src=\"".$this->urls->root()."components/images/emoticons/smile09.gif\" alt=\"(no)\" />", $text);

    $text=str_replace("[b]", "<strong>", $text);
    $text=str_replace("[/b]", "</strong>", $text);
    $text=str_replace("[i]", "<em>", $text);
    $text=str_replace("[/i]", "</em>", $text);
    $text=str_replace("[a]", "<a href=\"", $text);
    $text=str_replace("[/a]", "\">".LANG_OUTLINK."&raquo;</a>", $text);

    return $text;

  }

  //---[CZ]: zobrazení článku
  public function view_article() {
    $article=$this->db->query("SELECT * FROM terr_articles WHERE ".(($_GET["article"]!="")?$where="id=".$this->current_article("id"):"section=".$this->current_section("id")));
    $article=$this->db->fetch_array($article);

    if (isset($_SESSION["user_id"])) {
      $sql=$this->db->query("SELECT id, avatar_type, avatar, login, mail, rights, birthday FROM terr_users WHERE id=".intval($_SESSION["user_id"]));
      $sql=$this->db->fetch_array($sql);
      $age = intval((mktime(0, 0, 0, date("m"), date("d"), date("Y"))-$sql["birthday"])/(60*60*24*365));
    }

    if (($article["age_limit"]!="18" && $article["age_limit"]!="15") || isset($_SESSION["user_id"])) {
    if ((($article["age_limit"]=="18" || $article["age_limit"]=="15") && isset($_SESSION["user_id"]) && $age>=$article["age_limit"]) || ($article["age_limit"]!="18" && $article["age_limit"]!="15")) {
    if (($this->login->check_rights() >= $article["ReqRights"] && isset($_SESSION["user_id"])) || $article["ReqRights"]=="All") {

    if (!isset($_COOKIE["viewed_".intval($article["id"])]) OR $_COOKIE["viewed_".intval($article["id"])]!=1) {
    @setcookie("viewed_".intval($article["id"]), "1", time()+604800);
    $this->db->query("UPDATE terr_articles SET views=".($article["views"]+1)." WHERE id=".$article["id"]); }

    echo "<article id=\"opened\">";

    echo "<h2>".$article["title"]."</h2>";

    if ($article["show_article_info"]==1) { $this->article_info($article["id"]); }

    if ($article["perex"]!="") {

    echo "<div class=\"perex\">".(($article["pereximg"]!="")?"<img src=\"".$article["pereximg"]."\" alt=\"".$article["pereximg"]."\" class=\"perex-img\" />":"").$this->db->unescape($article["perex"]);

    if ($article["pereximg"]!="") { echo "<div class=\"float-ending\"><!-- --></div>"; }

    echo "</div>";

    }

    echo $this->db->unescape($article["text"]);

    if ($article["show_article_info"]==1 && $this->config_variables->get("show_addthis")==1) {
      echo "<p><!-- AddThis Button BEGIN -->
      <a class=\"addthis_button\" href=\"http://addthis.com/bookmark.php?v=250&amp;username=xa-4b31e4910cabd8f4\"><img src=\"http://s7.addthis.com/static/btn/v2/lg-share-en.gif\" width=\"125\" height=\"16\" alt=\"Bookmark and Share\" style=\"border:0\"/></a><script type=\"text/javascript\" src=\"http://s7.addthis.com/js/250/addthis_widget.js#username=xa-4b31e4910cabd8f4\"></script>
      <!-- AddThis Button END --></p>";
    }

    echo "</article>";

    if ($article["poll"]!=0) {
      $this->get_article_poll($article["poll"]);
    }

    if ($article["series"]!=0) {
      echo "<article><p><strong>".LANG_FROM_THE_SERIES."</strong> (".$this->db->unescape($this->db->result($this->db->query("SELECT name FROM terr_series WHERE id=".$article["series"]))).")</p>";
      $index=$this->db->result($this->db->query("SELECT count(id) FROM terr_articles WHERE id<".intval($article["id"])." && published!=0 && published<=".time()." && series=".intval($article["series"])));
      $older=$this->db->query("SELECT quick, title, published FROM terr_articles WHERE published!=0 && published<=".time()." && series=".intval($article["series"])." ORDER BY published LIMIT ".(($index-2<0)?"0,$index":intval($index-2).",2"));
      $newer=$this->db->query("SELECT quick, title, published FROM terr_articles WHERE published!=0 && published<=".time()." && series=".intval($article["series"])." ORDER BY published LIMIT ".intval($index+1).",3");
      while(list($quick, $title, $published)=$this->db->fetch_row($older)) { echo "<a href=\"".$this->urls->article($quick)."\">".$title."</a> <span class=\"small-text\">(".$this->date_time($published).")</span><br />"; }
      while(list($quick, $title, $published)=$this->db->fetch_row($newer)) { echo "<a href=\"".$this->urls->article($quick)."\">".$title."</a> <span class=\"small-text\">(".$this->date_time($published).")</span><br />"; }
      echo "</article>";
    }

    if ($article["discussion"]==1) {

    if ($this->config_variables->get("login_for_comments")==1 && isset($_SESSION["user_id"]) || $this->config_variables->get("login_for_comments")==0) {

    echo "<form action=\"".$this->urls->root()."index.php?action=add_comment&amp;article_id=".$article["id"]."\" method=\"post\"><div>";
    if (!isset($_SESSION["user_id"])) { echo "<p><strong>".LANG_AUTHOR.":</strong><br /><input type=\"text\" name=\"author\" style=\"width: 200px;\" /></p>"; }
    if (!isset($_SESSION["user_id"])) { echo "<p><strong>".LANG_EMAIL.":</strong><br /><input type=\"text\" name=\"mail\" style=\"width: 200px;\" /></p>"; }
    echo "<p><strong>".LANG_TEXT.":</strong></p>";
    echo "<textarea name=\"text\" cols=\"54\" rows=\"6\"></textarea>";
    if (!isset($_SESSION["user_id"])) {
    echo "<p><strong>".LANG_RETYPE_THIS_TEXT."</strong>:</p>";
    $this->captcha->create(1);
    }

    echo "<p><input type=\"submit\" class=\"greenbutton\" value=\"".LANG_ADD."\" /></p>";

    echo "</div>

    </form>";

    } else { echo "<h3>".LANG_USERS_MUST_BE_LOGGED_IN_FOR_WRITING_COMMENTS.".</h3>"; }

    $this->print_comments();

    }
    } else {
      if ($article["ReqRights"]=="0") { echo "<strong>".LANG_YOU_MUST_BE_LOGGED_IN_FOR_ENTER_THIS_ARTICLE."</strong>"; }
      else { echo "<strong>".LANG_FOR_ENTER_THIS_ARTICLE_ARE_REQUIRED_HIGHER_RIGHTS."</strong>";}
    }
  } else { echo "<strong>".LANG_THIS_CONTENT_IS_NOT_FOR_YOUR_AGE."</strong>"; }
  } else {
      echo "<strong>".LANG_YOU_MUST_BE_LOGGED_IN_FOR_ENTER_THIS_ARTICLE."</strong>"; }
  }

  public function article_info($id) {

    $article=$this->db->query("SELECT published, text, pereximg, author, section, views FROM terr_articles WHERE id=$id");
    $article=$this->db->fetch_array($article);

    $quick=$this->db->result($this->db->query("SELECT quick FROM terr_sections WHERE id=".$article["section"]));

    if ($this->config_variables->get("show_article_date")==1 || $this->config_variables->get("show_article_author")==1 || $this->config_variables->get("show_article_section")==1 || $this->config_variables->get("show_article_comments_number")==1 || $this->config_variables->get("show_article_views_number")==1) {

    echo "<p class=\"article-info\">";

    $article_info="";

    if ($this->config_variables->get("show_article_date")==1) {
      if ($article["published"]!=0) { $article_info.=$this->date_time($article["published"]); }
      else { $article_info.=LANG_NOT_PUBLISHED_YET; }}
    if ($this->config_variables->get("show_article_author")==1) {
      $article_info.=" &bull; ".$this->urls->author_link($article["author"]); }
    if ($this->config_variables->get("show_article_section")==1) {
      $article_info.=" &bull; ".$this->urls->section_link($quick); }
    if ($this->config_variables->get("show_article_comments_number")==1) {
      $count_comments=$this->db->result($this->db->query("SELECT COUNT(id) FROM terr_comments WHERE article=$id"));
      $article_info.=" &bull; ".LANG_COMMENTED." ".$count_comments."&times;"; }
    if ($this->config_variables->get("show_article_views_number")==1) { $article_info.=" &bull; ".LANG_VIEWED." ".$article["views"]."&times;"; }

    echo trim($article_info, " &bull; ");
    echo "</p>";
    }
  }

  //---[CZ]: vypsání struktury sekcí
  public function structure($higher, &$structure) {
    $above=$this->db->fetch_array($this->db->query("SELECT higher, position FROM terr_sections WHERE id=".intval($higher)));
    $sql=$this->db->query("SELECT id, position FROM terr_sections WHERE higher=".intval($above["higher"])." ORDER BY position");
    while(list($id, $position)=$this->db->fetch_row($sql)) {
      if ($position<= $above["position"]) { $before[] = $id; }
      else { $behind[] = $id; }
    }
    if (isset($before)) { array_splice($structure, 0, 0, $before); }
    if (isset($behind)) { array_splice($structure, count($structure), 0, $behind); }
    return $above["higher"];
  }

  //---[CZ]: vypsání položek menu podle zvolených kritérií
  public function sections() {
    if ($_GET["section"]!="") { $where = $this->current_section_by_quick("id"); } else { $where = 0; }
    $structure = array();
    $sql=$this->db->query("SELECT id FROM terr_sections WHERE higher=".intval($where)." ORDER BY position");
    while(list($id)=$this->db->fetch_row($sql)) {
      $structure[] = $id; }
    if ($_GET["section"]!="") {
      $higher=$this->db->result($this->db->query("SELECT higher FROM terr_sections WHERE id=".intval($where)));
      $this->structure($where, $structure);
      while ($higher!=0) {
        $higher = $this->structure($higher, $structure); }
    }
    foreach ($structure as $key => $value) {
      $print=$this->db->query("SELECT id, quick, name, highlight, outlink, position, level FROM terr_sections WHERE hidden!=1 && id=".intval($value));
      while($data=$this->db->fetch_array($print)) {
        echo "<li".@(($this->current_section_by_quick("id")==$data["id"])?" class=\"active-item\"":"")."><a href=\"";
        echo (($data["outlink"]!="")?$data["outlink"]:$this->urls->section($data["quick"]));
        if ($data["level"]!=0) { $padding=20; if ($data["level"]==1) { $padding=25;} echo "\" style=\"padding-left: ".($data["level"]*$padding)."px;\" class=\"subsection\">"; } else { echo "\">"; }
        echo (($data["highlight"]==1)?"<strong>":"").$this->db->unescape($data["name"]).(($data["highlight"]==1)?"</strong>":"");
        echo "</a></li>\n";
      }
    }
  }

  //---[CZ]: vypsání souborů
  public function print_files() {
    if ($_GET["page"]!="") { $where = " WHERE uploader='".$this->current_user("id", "login", $_GET["page"])."'"; $add = " uživatele: ".$_GET["page"]; }
    echo "<h3>".LANG_FILES."$add</h3><table id=\"files\"><tr><th>".LANG_NAME."</th><th>".LANG_UPLOADER."</th><th>".LANG_TYPE."</th><th style=\"width: 22%;\">".LANG_UPLOADED."</th><th>".LANG_DOWNLOAD."</th>";
    $sql=$this->db->query("SELECT id, file, added, uploader FROM terr_files$where ORDER BY added DESC");
    while($data=$this->db->fetch_array($sql)) {
      $user=$this->db->query("SELECT login FROM terr_users WHERE id=".$data["uploader"]);
      $user=$this->db->fetch_array($user);
      $last=strrpos($data["file"], ".");
      $name=substr($data["file"], 0, $last);
      $ext=substr($data["file"], $last+1);
      echo "<tr class=\"text-align-center\"><td>".$name."</td><td>".$user["login"]."</td><td>".$ext."</td><td>".date("d.m.Y H:i", $data["added"])."</td><td class=\"text-align-center\"><a href=\"".$this->urls->root()."files/".$data["file"]."\" target=\"_blank\">&dArr;</a></td></tr>";
    }
    echo "</table>";
  }

  //---[CZ]: vypsání komentářů
  public function get_comments() {
    if ($_GET["user"]!="") {
      echo "<h3>".LANG_COMMENTS." uživatele: ".$_GET["user"]."</h3>";
      $author=$this->db->query("SELECT * FROM terr_users WHERE login='".$_GET["user"]."'");
      $author_info=$this->db->fetch_array($author);
      $sql=$this->db->query("SELECT * FROM terr_comments WHERE user=1 && author='".$this->current_user("id", "login", $_GET["user"])."' ORDER BY added DESC");
      while($data=$this->db->fetch_array($sql)) {
        $article=$this->db->query("SELECT quick, title FROM terr_articles WHERE id=".$data["article"]);
        $article_info=$this->db->fetch_array($article);
        $i2++;
        echo "<div id=\"div".$data["id"]."\">
          <div class=\"comment-$i2\">
          <p class=\"comment-info\"><strong>
          <a href=\"".$this->urls->profile($author_info["login"])."\">".(($author_info["realname"]!="")?$author_info["realname"]:$author_info["login"])."</a>
          </strong> (".$this->date_time($data["added"]).")";
        if (isset($_SESSION["user_id"])) { if ($data["author"]==$_SESSION["user_id"] || $this->login->check_access("comments")==1) { echo " &bull; <a href=\"#".$data["id"]."\" onclick=\"update_comment(".$data["id"].");\">".LANG_EDIT."</a> &bull; <a href=\"#".$data["id"]."\" onclick=\"if (!confirm('".LANG_ARE_YOU_SURE."')) return false; delete_comment(".$data["id"].");\">".LANG_DELETE."</a>"; } }
        echo " &bull; <a href=\"".$this->urls->article($article_info["quick"])."\">".$article_info["title"]."</a>".(($data["user"]==1)?"<span class=\"float-right\">".$this->rank($author_info["rights"])."</span>":"<span class=\"float-right\">".LANG_UNREGISTERED."</span>")."</p>\n\n<p id=\"".$data["id"]."\">".$this->get_avatar(1, $author_info["avatar_type"], $author_info["avatar"], $author_info["mail"], $data["author"], "comment");
        if ($this->config_variables->get("show_unconfirmed_comments")==1 || $data["confirmed"]==1) {
          if ($this->config_variables->get("use_emoticons")==1) {
            echo $this->db->unescape($this->smiles($data["text"])); }
          else { echo $this->db->unescape($data["text"]); }}
        else { echo "<strong>&rArr; ".LANG_COMMENT_HAS_NOT_BEEN_CONFIRMED_YET."</strong>"; }
        echo "<span class=\"float-ending\"><!-- --></span></p></div><div class=\"comment-$i2-bottom-corners\"><!-- --></div></div>";
        if ($i2==2) { $i2=0; }
      }
    }
  }

  //---[CZ]: vypsání soukromé zprávy
  public function get_msg() {
    $sql=$this->db->query("SELECT * FROM terr_pm WHERE id=".$_GET["stuff"]);
    $data=$this->db->fetch_array($sql);
    if ($data["reciever"]==$_SESSION["user_id"] || $data["sender"]==$_SESSION["user_id"]) {
      if ($data["reciever"]==$_SESSION["user_id"]) { $sql=$this->db->query("UPDATE terr_pm SET seen=1 WHERE id=".$data["id"]); }
      $sender = $this->current_user("login", "id", $data["sender"]);
      if (strpos(" ".$data["subject"], "Re:")) { $subject = $data["subject"]; } else { $subject = "Re:".$data["subject"]; }
      echo "<form id=\"pms\" method=\"post\" action=\"".$this->urls->root()."new-message\">
        <input type=\"text\" readonly style=\"width: 28%;\" name=\"reciever\" value=\"$sender\" />
        <input type=\"text\" readonly style=\"width: 35%;\" value=\"".$data["subject"]."\" />
        ".date("d.m.Y | H:i", $data["date"])."
        <textarea readonly style=\"width: 99%; height: 200px;\" />".$data["text"]."</textarea>
        <input type=\"hidden\" name=\"subject\" value=\"".((strpos(" ".$data["subject"], "Re:"))?$data["subject"]:"Re:".$data["subject"])."\" />
        <input type=\"hidden\" name=\"replied\" value=\"".$data["id"]."\" />
        <input type=\"submit\" name=\"submit\" value=\"".LANG_RESPOND."\" /></form>";
    }
  }

  //---[CZ]: vypsání soukromých zpráv
  public function get_pm() {
    echo "<h3>Soukromé zprávy</h3>";
    echo "<p><a href=\"".$this->urls->pm("blank", "inbox")."\"".(($_GET["function"]=="inbox")?" style=\"font-weight: bold;\"":"").">".LANG_INBOX."</a> |
             <a href=\"".$this->urls->pm("blank", "outbox")."\"".(($_GET["function"]=="outbox")?" style=\"font-weight: bold;\"":"").">".LANG_OUTBOX."</a> |
             <a href=\"".$this->urls->pm("blank", "new-message")."\"".(($_GET["function"]=="new-message")?" style=\"font-weight: bold;\"":"").">".LANG_NEW_MESSAGE."</a></p>";
    if (isset($_SESSION["user_id"])) {
      switch ($_GET["function"]) {
      case "inbox":
        if ($_GET["stuff"]!="" && substr($_GET["stuff"], 0, 5)!="page-") { $this->get_msg(); break; }
        $pocet=$this->db->result($this->db->query("SELECT COUNT(id) FROM terr_pm WHERE hidden!=1 && reciever=".intval($_SESSION["user_id"])));
        if ($pocet==0) { echo "<h3>".LANG_YOU_HAVE_NO_MESSAGES."</h3>"; } else {
          echo "<table id=\"pms\"><tr><th>".LANG_SUBJECT."</th><th>".LANG_SENDER."</th><th>".LANG_SENDED."</th><th>".LANG_SEEN."</th><th>".LANG_DELETE."</th></tr>";
          $sql=$this->db->query("SELECT * FROM terr_pm WHERE hidden!=1 && reciever=".intval($_SESSION["user_id"])." ORDER BY date DESC".((substr($_GET["stuff"], 0, 5)=="page-")?" LIMIT ".intval(20*substr($_GET["stuff"], 5)-20).",20":" LIMIT 0,20"));
          while($data=$this->db->fetch_array($sql)) {
            $sender=$this->current_user("login", "id", $data["sender"]);
            echo "<tr id=\"tr".$data["id"]."\" class=\"text-align-center\"><td>".(($data["replied"]==1)?"<span style=\"color: #cc0000; font-weight: bold;\">&uarr; </span>":"").
              "<a href=\"".$this->urls->pm($data["id"], "inbox")."\">".substr($data["subject"], 0, 45)."</a></td>
              <td><a href=\"".$this->urls->profile($sender)."\">$sender</a></td>
              <td>".$this->date_time($data["date"])."</td>
              <td><input type=\"checkbox\" name=\"seen-".$data["id"]."\" onclick=\"if (this.checked) { seen(".$data["id"].", 1); } else { seen(".$data["id"].", 0); };\"".(($data["seen"]==1)?" checked":"")." /></td>
              <td><input type=\"checkbox\" name=\"hidden\" onclick=\"if (!confirm('".LANG_ARE_YOU_SURE."')) return false; hide_pm(".$data["id"].")\" /></td>
              </tr>";
          }
          echo "</table>";
          if (substr($_GET["stuff"], 5)=="") { $page = 1; } else { $page = substr($_GET["stuff"], 5); }
          $max=ceil($pocet/20);
          $next=$page+1;
          $previous=$page-1;
          echo "<h4 class=\"paging text-align-right\">".(($page!=1)?"<a href=\"".$this->urls->pm("page-".$previous, "inbox")."\">&laquo;</a> ":"&laquo; ").$page."/".ceil($pocet/20).(($page!=$max)?" <a href=\"".$this->urls->pm("page-".$next, "inbox")."\">&raquo;</a>":" &raquo;")."</h4>";
        }
      break;
      case "outbox":
        if ($_GET["stuff"]!="" && substr($_GET["stuff"], 0, 5)!="page-") { $this->get_msg(); break; }
        $pocet=$this->db->result($this->db->query("SELECT COUNT(id) FROM terr_pm WHERE hidden!=1 && sender=".intval($_SESSION["user_id"])));
        if ($pocet==0) { echo "<h3>".LANG_YOU_HAVE_NO_MESSAGES."</h3>"; } else {
          echo "<table id=\"pms\"><tr><th>".LANG_SUBJECT."</th><th>".LANG_RECIEVER."</th><th>".LANG_SENDED."</th><th>".LANG_SEEN."</th></tr>";
          $sql=$this->db->query("SELECT * FROM terr_pm WHERE hidden!=1 && sender=".intval($_SESSION["user_id"])." ORDER BY date DESC".((substr($_GET["stuff"], 0, 5)=="page-")?" LIMIT ".intval(20*substr($_GET["stuff"], 5)-20).",20":" LIMIT 0,20"));
          while($data=$this->db->fetch_array($sql)) {
            $reciever = $this->current_user("login", "id", $data["reciever"]);
            echo "<tr id=\"tr".$data["id"]."\" class=\"text-align-center\">
              <td><a href=\"".$this->urls->pm($data["id"], "outbox")."\">".substr($data["subject"], 0, 45)."</a></td>
              <td><a href=\"".$this->urls->profile($reciever)."\">$reciever</a></td>
              <td>".$this->date_time($data["date"])."</td>
              <td>".(($data["seen"]==0)?LANG_NO:LANG_YES)."</td>
              </tr>";
          }
          echo "</table>";
          if (substr($_GET["stuff"], 5)=="") { $page = 1; } else { $page = substr($_GET["stuff"], 5); }
          $max=ceil($pocet/20);
          $next=$page+1;
          $previous=$page-1;
          echo "<h4 class=\"paging text-align-right\">".(($page!=1)?"<a href=\"".$this->urls->pm("page-".$previous, "outbox")."\">&laquo;</a> ":"&laquo; ").$page."/".ceil($pocet/20).(($page!=$max)?" <a href=\"".$this->urls->pm("page-".$next, "outbox")."\">&raquo;</a>":" &raquo;")."</h4>";
        }
      break;
      case "new-message":
        if ($_POST["submit"]==LANG_SEND) {
          if ($_POST["reciever"]!="") {
          if ($_POST["subject"]!="") {
          if ($_POST["text"]!="") {
            $reciever = $this->current_user("id", "login", $_POST["reciever"]);
            if ($reciever!="") {
              $sql=$this->db->query("INSERT INTO terr_pm (sender, reciever, subject, text, date) values (
                '".intval($_SESSION["user_id"])."',
                '".intval($reciever)."',
                '".$this->db->escape(htmlspecialchars(substr($_POST["subject"],0,45)))."',
                '".$this->db->escape(htmlspecialchars($_POST["text"]))."',
                '".time()."'
              )");
              if (isset($_POST["replied"])) { $sql2=$this->db->query("UPDATE terr_pm SET replied=1 WHERE id=".$_POST["replied"]); }
              if ($sql) { $this->status_messages->print_success(LANG_MESSAGE_SENT); header("Location: ".$this->urls->pm("blank", "outbox")); } else { $this->status_messages->print_error(LANG_AN_ERROR_OCCURED); }
            } else { $this->status_messages->print_error(LANG_THIS_RECIEVER_DOES_NOT_EXIST); }
          } else { $this->status_messages->print_error(LANG_TEXT_IS_NOT_FILLED); }
          } else { $this->status_messages->print_error(LANG_SUBJECT_IS_REQUIRED); }
          } else { $this->status_messages->print_error(LANG_RECIEVER_IS_REQUIRED); }
        }
        echo "<form method=\"post\" id=\"pms\">
          <input type=\"text\" placeholder=\"".LANG_RECIEVER."\" style=\"width: 28%;\" name=\"reciever\" onkeyup=\"pm_reciever(this.value);\"".((isset($_POST["reciever"]))?" value=\"".htmlspecialchars($_POST["reciever"])."\"":"")."/>
          <input type=\"text\" placeholder=\"".LANG_SUBJECT."\" style=\"width: 35%;\" maxlength=\"25\" name=\"subject\"".((isset($_POST["subject"]))?" value=\"".htmlspecialchars($_POST["subject"])."\"":"")."/>
          <span id=\"reciever\"><!-- --></span>
          <textarea style=\"width: 99%; height: 200px;\" placeholder=\"Text...\" name=\"text\">".((isset($_POST["text"]))?htmlspecialchars($_POST["text"]):"")."</textarea>"
          .((isset($_POST["replied"]))?"<input type=\"hidden\" name=\"replied\" value=\"".$_POST["replied"]."\" />":"").
          "<input type=\"submit\" name=\"submit\" value=\"".LANG_SEND."\" /><input type=\"reset\" value=\"Reset\" /></form>";
      break;
      }
    }
  }

  //---[CZ]: vypsání posledních pěti komentářů
  public function get_last_5comments() {
    if ($this->config_variables->get("show_menu_with_last_comments")==1) {
      echo "<figure><h2>".LANG_LAST_COMMENTS."</h2>\n<ul>\n";
      $sql=$this->db->query("SELECT id, article, author, user, added FROM terr_comments ORDER BY added DESC LIMIT 0,5");
      while($data=$this->db->fetch_array($sql)) {
        $article=$this->db->query("SELECT quick, title FROM terr_articles WHERE id=".$data["article"]);
        $article=$this->db->fetch_array($article);
        if ($data["user"]==1) { @$author=$this->db->result($this->db->query("SELECT login FROM terr_users WHERE id=".$data["author"])); }
        echo "<li><a href=\"".$this->urls->article($article["quick"])."#div".$data["id"]."\" title=\"".$article["title"]."\">
        ".(($data["user"]==0)?$data["author"]:(($author=="")?"<em>xxx</em>":$author))."
        <span class=\"float-right\">".$this->date_time($data["added"])."</span></a></li>\n";
      }
      echo "</ul></figure>";
    }
  }

  //---[CZ]: vypsání posledních pěti přihlášených
  public function get_5lastvisitors() {
    if (isset($_SESSION["user_id"])) { $sql=$this->db->query("UPDATE terr_users SET lastvisit=".time()." WHERE id=".intval($_SESSION["user_id"])); }
    if ($this->config_variables->get("show_menu_with_last_login")==1) {
      $online = $this->db->result($this->db->query("SELECT COUNT(id) FROM terr_users WHERE lastvisit>".(time()-60*2)." && id!=".intval($_SESSION["user_id"])));
      if ($online!=0) {
        echo "<figure><h2>Online"
        .(($online>5)?"<a href=\"javascript:online_users_expand()\"><span class=\"float-right\">&#43;</span></a></h2>":"</h2>").
        "<ul id=\"online_users\">";
        $sql=$this->db->query("SELECT login FROM terr_users WHERE id!=".intval($_SESSION["user_id"])." && lastvisit>".(time()-60*2)." ORDER BY lastvisit DESC LIMIT 0,5");
        while($data=$this->db->fetch_array($sql)) {
          echo "<li><a href=\"".$this->urls->profile($data["login"])."\">".$data["login"]."</a></li>";
        }
        echo "</ul></figure>";
      }
      else {
        echo "<figure><h2>".LANG_LAST_VISITORS."</h2>\n<ul>\n";
        $sql=$this->db->query("SELECT id, login, rights, regdate, lastvisit FROM terr_users ORDER BY lastvisit DESC LIMIT 0,5");
        while ($data=$this->db->fetch_array($sql)) {
          echo "<li><a href=\"".$this->urls->profile($data["login"])."\">".substr($data["login"],0,12).(($data["lastvisit"]==0)?"-":"<span class=\"float-right\">".date("d.m. H:i", $data["lastvisit"]))."</span></a></li>"; }
        echo "</ul></figure>";
      }
    }
  }

  //---[CZ]: vypsání počtu uživatelů
  public function get_users_number() {
    if ($this->config_variables->get("show_accounts_count")==1) {
      $signed_up=$this->db->result($this->db->query("SELECT COUNT(id) FROM terr_users WHERE lastvisit!=0 && rights<=2"));
      $admins=$this->db->result($this->db->query("SELECT COUNT(id) FROM terr_users WHERE lastvisit!=0 && rights>2"));
      $everyone=$this->db->result($this->db->query("SELECT COUNT(id) FROM terr_users WHERE lastvisit!=0"));
      echo "<figure><h2>".LANG_USER_COUNT."</h2>
        <ul id=\"users_count\">
        <li>".LANG_REGISTERED.": $signed_up</li>
        <li>".LANG_ADMINS.": $admins</li>
        <li>".LANG_TOTAL.": $everyone</li>
        </ul></figure>";
    }
  }

  //---[CZ]: vypsání modulů sekcí
  public function get_sections_content() {
    $sql=$this->db->query("SELECT id, name FROM terr_sections WHERE module=1");
    while($data=$this->db->fetch_array($sql)) {
      echo "<figure><h2>".$data["name"]."</h2>\n<ul>";
      $sql2 = $this->db->query("SELECT quick, title FROM terr_articles WHERE published!=0 && published<=".time()." && section=".$data["id"]);
      while ($data2=$this->db->fetch_array($sql2)) {
        echo "<li><a href=\"".$this->urls->article($data2["quick"])."\">".$data2["title"]."</a></li>"; }
      echo "</ul></figure>";
    }
  }
}
?>