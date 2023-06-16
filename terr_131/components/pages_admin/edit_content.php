<?php
//---[CZ]: ověříme přístup
if ($login->check_access("edit_content")==1):

//---[CZ]: načteme potřebné třídy
include CLASSES_PATH."Articles.php";
include CLASSES_PATH."Sections.php";
include CLASSES_PATH."Polls.php";

//---[CZ]: vytvoříme instance
$articles=new Articles();
$sections=new Sections();
$polls=new Polls();

//---[CZ]: definování proměnných
if (!isset($_POST["article_title"])) { $_POST["article_title"]=""; }
if (!isset($_POST["article_link"])) { $_POST["article_link"]=""; }
if (!isset($_POST["section"])) { $_POST["section"]=""; }
if (!isset($_POST["article_type"])) { $_POST["article_type"]=""; }
if (!isset($_POST["hp"])) { $_POST["hp"]=""; }
if (!isset($_POST["complete"])) { $_POST["complete"]=""; }
if (!isset($_POST["confirmed"])) { $_POST["confirmed"]=""; }
if (!isset($_POST["top"])) { $_POST["top"]=""; }
if (!isset($_POST["show_article_info"])) { $_POST["show_article_info"]=""; }
if (!isset($_POST["discussion"])) { $_POST["discussion"]=""; }
if (!isset($_POST["perex"])) { $_POST["perex"]=""; }
if (!isset($_POST["text"])) { $_POST["text"]=""; }
if (!isset($_POST["publish_now"])) { $_POST["publish_now"]=""; }
if (!isset($_POST["keywords"])) { $_POST["keywords"]=""; }
if (!isset($_POST["hours"])) { $_POST["hours"]=0; }
if (!isset($_POST["minutes"])) { $_POST["minutes"]=0; }
if (!isset($_POST["cancel_publication"])) { $_POST["cancel_publication"]=""; }
if (!isset($_POST["publish"])) { $_POST["publish"]=""; }
if (!isset($_POST["ReqRights"])) { $_POST["ReqRights"]=""; }
if (!isset($_POST["age_limit"])) { $_POST["age_limit"]=""; }
if (!isset($_POST["series"])) { $_POST["series"]=""; }

//---[CZ]: upravit článek
if ($_GET["tab"]=="article" AND isset($_POST["submit"])) { $articles->update(); }
?>

<h2><?php echo LANG_EDIT_CONTENT; ?></h2>

<?php if ($_GET["tab"]=="article"): ?>

<ul id="tab-bar">
<li class="active-item"><a href=<?php echo "\"".$urls->article($articles->get("quick"))."\" target=\"_blank\">".LANG_GO_TO.": ".$articles->get("title"); ?></a></li>
<li><span class="float-ending"><!-- --></span></li>
</ul>
<script type="text/javascript">
/* <![CDATA[ */
function generate_link() {
var data = document.getElementById("article_title").value;
!window.open("./components/pages_admin/link_generator.php?article_title="+data, "link_generator", "width=1,height=1,left=0,top=0");
return false;
}
document.write('<iframe frameborder="0px" name="link_generator" src="" style="display: none;"></iframe>');
/* ]]> */
</script>
<form action="./admin.php?function=edit_content&amp;tab=article&amp;id=<?php echo $_GET["id"]; ?>" method="post">
<div><table><tr>
<td style="width: 50%;"><strong><?php echo LANG_ARTICLE_TITLE; ?>:</strong>
<br /><input type="text" name="article_title" id="article_title" value="<?php echo $articles->get("title"); ?>" style="width: 300px;"<?php if (isset($_POST["submit"]) AND $_POST["article_title"]=="") { echo " class=\"input-error\""; } ?> />
<br /><strong><?php echo LANG_GENERATED_LINK; ?>:</strong>
<br /><input type="text" name="article_link" id="article_link"  value="<?php echo $articles->get("quick"); ?>" style="width: 218px;"<?php if (isset($_POST["submit"]) AND $_POST["article_link"]=="") { echo " class=\"input-error\""; } ?> /> <input type="button" class="yellowbutton" value="<?php echo LANG_GENERATE; ?>" onclick="generate_link();" /></td>
<td><strong><?php echo LANG_SECTION; ?>:</strong>
<br /><select name="section" style="width: 307px;">
<option value=""><?php echo LANG_CHOOSE; ?></option>
<?php $sections->load_sections(0, $articles->get("section")); ?>
</select><?php if (isset($_POST["submit"]) AND $_POST["section"]=="") { echo " <span class=\"input-error\">&nbsp;!&nbsp;</span>"; } ?>
<br /><strong><?php echo LANG_META_KEYWORDS; ?>:</strong>
<br /><input type="text" name="keywords" value="<?php echo htmlspecialchars($articles->get("keywords")); ?>" style="width: 300px;" /></td>
</tr></table>
</div><div>
<p><strong><?php echo LANG_OPTIONS; ?>:</strong></p>
<table><tr>
<td><input type="radio" name="article_type" value="1"<?php if ($articles->get("type")==1) { echo " checked=\"checked\""; } ?> id="news" /> <label for="news"><?php echo LANG_NEWS; ?></label></td>
<td><input type="checkbox" name="hp" value="1"<?php if ($articles->get("hp")==1) { echo " checked=\"checked\""; } ?> id="show_on_homepage" /> <label for="show_on_homepage"><?php echo LANG_SHOW_ON_HOMEPAGE; ?></label></td>
</tr><tr>
<td><input type="radio" name="article_type" value="2"<?php if ($articles->get("type")==2) { echo " checked=\"checked\""; } ?> id="standard_article" /> <label for="standard_article"><?php echo LANG_STANDARD_ARTICLE; ?></label></td>
<td><input type="checkbox" name="complete" value="1"<?php if ($articles->get("complete")==1) { echo " checked=\"checked\""; } ?> id="article_is_complete" /> <label for="article_is_complete"><?php echo LANG_ARTICLE_IS_COMPLETE; ?></label></td>
</tr><tr>
<td><input type="radio" name="article_type" value="3"<?php if ($articles->get("type")==3) { echo " checked=\"checked\""; } ?> id="important_article" /> <label for="important_article"><?php echo LANG_IMPORTANT_ARTICLE; ?></label></td>
<td><input type="checkbox" name="confirmed" value="1"<?php if ($articles->get("confirmed")==1) { echo " checked=\"checked\""; } if ($login->check_access("unverified_articles")==0) { echo " disabled=\"disabled\""; } ?> id="article_is_verified" /> <label for="article_is_verified"><?php echo LANG_ARTICLE_IS_VERIFIED; ?></label></td>
</tr><tr>
<td><input type="checkbox" name="top" value="1"<?php if ($articles->get("top")==1) { echo " checked=\"checked\""; } ?> id="top" /> <label for="top">TOP</label></td>
<td><input type="checkbox" name="discussion" value="1"<?php if ($articles->get("discussion")==1) { echo " checked=\"checked\""; } ?> id="allow_discussion" /> <label for="allow_discussion"><?php echo LANG_ALLOW_DISCUSSION; ?></label></td>
</tr><tr>
<td><input type="checkbox" name="show_article_info" value="1"<?php if ($articles->get("show_article_info")==1) { echo " checked=\"checked\""; } ?> id="show_article_info" /> <label for="show_article_info"><?php echo LANG_SHOW_ARTICLE_INFO; ?></label></td>
<td><input type="checkbox" name="cancel_publication" value="1"<?php if ($articles->get("published")==0 OR $login->check_access("unpublished_articles")==0) { echo " disabled=\"disabled\""; } ?> id="cancel_publication" /> <label for="cancel_publication"><?php echo LANG_CANCEL_PUBLICATION; ?></label></td>
</tr><tr>
<td><strong><?php echo LANG_POLL; ?>:</strong><br /><select name="poll" style="width: 200px;">
<option value=""><?php echo LANG_CHOOSE; ?></option>
<?php $polls->load_polls($articles->get("poll")); ?>
</select></td><td><strong><?php echo LANG_SERIES; ?>:</strong><br /><select name="series" style="width: 200px;">
<option value=""><?php echo LANG_CHOOSE; ?></option>
<?php $sections->load_series($articles->get("series")); ?>
</select></td>
</tr></table></div><div>
<p><strong><?php echo LANG_PUBLICATION; ?>:</strong> <?php if ($login->check_access("unpublished_articles")==1) { $articles->publish_select_boxes(); } else { echo date("d.m.Y | H:i", $articles->get("published")); } echo " (".$urls->author_link($articles->get("publisher")).")"; ?></p>
<p><strong><?php echo LANG_CREATION_DATE; ?>:</strong> <?php echo date("d.m.Y | H:i", $articles->get("added"))." (".$urls->author_link($articles->get("author")).")"; ?></p>
<p><strong><?php echo LANG_LAST_EDITATION; ?>:</strong> <?php if ($articles->get("edited")==0) { echo LANG_NOT_EDITED_YET; } else { echo date("d.m.Y | H:i", $articles->get("edited")); if ($articles->get("last_editor")!="" AND $articles->get("last_editor")!=0) { echo " (".$urls->author_link($articles->get("last_editor")).")"; }} ?></p>
<p><strong><?php echo LANG_NUMBER_OF_VIEWS; ?>:</strong> <?php  echo $articles->get("views"); ?>&times;</p>
</div><div>
<table><tr><td><strong><?php echo LANG_ACCESS; ?>:</strong></td><td><strong><?php echo LANG_AGE_LIMIT; ?>: </strong></td></tr><tr><td>
<input type="radio" name="ReqRights" value="All" id="access_all"<?php if ($articles->get("ReqRights")=="All") { echo " checked=\"checked\""; } ?> /> <label for="access_all"><?php echo LANG_ACCESS_ALL; ?></label>
<input type="radio" name="ReqRights" value="0" id="access_registered"<?php if ($articles->get("ReqRights")=="0") { echo " checked=\"checked\""; } ?> /> <label for="access_registered"><?php echo LANG_ACCESS_0; ?></label>
<input type="radio" name="ReqRights" value="1" id="access_1"<?php if ($articles->get("ReqRights")=="1") { echo " checked=\"checked\""; } ?> /> <label for="access_1"><?php echo LANG_ACCESS_1; ?></label>
<input type="radio" name="ReqRights" value="2" id="access_2"<?php if ($articles->get("ReqRights")=="2") { echo " checked=\"checked\""; } ?> /> <label for="access_2"><?php echo LANG_ACCESS_2; ?></label>
<input type="radio" name="ReqRights" value="3" id="access_3"<?php if ($articles->get("ReqRights")=="3") { echo " checked=\"checked\""; } ?> /> <label for="access_3"><?php echo LANG_ACCESS_3; ?></label>
<input type="radio" name="ReqRights" value="4" id="access_4"<?php if ($articles->get("ReqRights")=="4") { echo " checked=\"checked\""; } ?> /> <label for="access_4"><?php echo LANG_ACCESS_4; ?></label>
<input type="radio" name="ReqRights" value="5" id="access_5"<?php if ($articles->get("ReqRights")=="5") { echo " checked=\"checked\""; } ?> /> <label for="access_5"><?php echo LANG_ACCESS_5; ?></label>
</td><td style="width: 20%;">
<select name="age_limit" style="width: 130px;">
<option value=""<?php if ($articles->get("age_limit")=="") { echo " selected"; } ?>><?php echo LANG_NO_LIMIT; ?></option>
<option value="15"<?php if ($articles->get("age_limit")=="15") { echo " selected"; } ?>><?php echo LANG_15_MORE; ?></option>
<option value="18"<?php if ($articles->get("age_limit")=="18") { echo " selected"; } ?>><?php echo LANG_18_MORE; ?></option>
</select>
</td></tr></table></div><div>
<p><strong><?php echo LANG_PEREX_IMAGE; ?>:</strong><br /><input type="text" name="pereximg" value="<?php if ($articles->get("pereximg")=="") { echo "/gallery/"; } else { echo $articles->get("pereximg"); } ?>" style="width: 300px;" /></p>
<p><strong><?php echo LANG_PEREX; ?>:</strong></p></div>
<div class="normal" id="perex"><textarea name="perex" class="tinymce"><?php echo htmlspecialchars($articles->get("perex")); ?></textarea></div>
<div><p><strong><?php echo LANG_TEXT; ?>:</strong></p></div>
<div class="normal" id="text"><textarea name="text" class="tinymce"><?php echo $articles->get("text"); ?></textarea></div>
<div><p><input type="submit" name="submit" class="greenbutton" value="<?php echo LANG_SAVE_CHANGES; ?>" /></p></div>
</form>

<?php endif; ?>
<?php else: $access_denied=1; endif; ?>