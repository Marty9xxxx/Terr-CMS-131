<?php
//---[CZ]: ověříme přístup
if ($login->check_access("add_content")==1):

//---[CZ]: načteme potřebné třídy
include CLASSES_PATH."Articles.php";
include CLASSES_PATH."Images.php";
include CLASSES_PATH."Files.php";
include CLASSES_PATH."Polls.php";
include CLASSES_PATH."Sections.php";

//---[CZ]: vytvoříme instance
$articles=new Articles();
$images=new Images();
$files=new Files();
$polls=new Polls();
$sections=new Sections();

//---[CZ]: definování proměnných
if (!isset($_POST["article_title"])) { $_POST["article_title"]=""; }
if (!isset($_POST["article_link"])) { $_POST["article_link"]=""; }
if (!isset($_POST["section"])) { $_POST["section"]=""; }
if (!isset($_POST["article_type"])) { $_POST["article_type"]=""; }
if (!isset($_POST["hp"]) AND isset($_POST["submit"])) { $_POST["hp"]=""; }
if (!isset($_POST["complete"])) { $_POST["complete"]=""; }
if (!isset($_POST["confirmed"])) { $_POST["confirmed"]=""; }
if (!isset($_POST["top"])) { $_POST["top"]=""; }
if (!isset($_POST["show_article_info"])) { $_POST["show_article_info"]=""; }
if (!isset($_POST["discussion"])) { $_POST["discussion"]=""; }
if (!isset($_POST["perex"])) { $_POST["perex"]=""; }
if (!isset($_POST["text"])) { $_POST["text"]=""; }
if (!isset($_POST["publish_now"])) { $_POST["publish_now"]=""; }
if (!isset($_POST["keywords"])) { $_POST["keywords"]=""; }
if (!isset($_POST["ReqRights"])) { $_POST["ReqRights"]="All"; }
if (!isset($_POST["age_limit"])) { $_POST["age_limit"]=""; }
if (!isset($_POST["series"])) { $_POST["series"]=""; }

//---[CZ]: přidávání obsahu
if ($_GET["tab"]=="" AND isset($_POST["submit"])) { $articles->add(); }
if ($_GET["tab"]=="images" AND isset($_POST["submit"])) { $images->upload(); }
if ($_GET["tab"]=="files" AND isset($_POST["submit"])) { $files->upload(); }
if ($_GET["tab"]=="articles_polls" AND isset($_POST["submit"])) { $polls->add_poll(); }
?>

<h2><?php echo LANG_ADD_CONTENT; ?></h2>

<ul id="tab-bar">
<li<?php if ($_GET["tab"]=="") { echo " class=\"active-item\""; } ?>><a href="./admin.php?function=add_content"><?php echo LANG_ARTICLES; ?></a></li>
<li<?php if ($_GET["tab"]=="images") { echo " class=\"active-item\""; } ?>><a href="./admin.php?function=add_content&amp;tab=images"><?php echo LANG_UPLOAD_AN_IMAGE; ?></a></li>
<li<?php if ($_GET["tab"]=="files") { echo " class=\"active-item\""; } ?>><a href="./admin.php?function=add_content&amp;tab=files"><?php echo LANG_UPLOAD_A_FILE; ?></a></li>
<li<?php if ($_GET["tab"]=="articles_polls") { echo " class=\"active-item\""; } ?>><a href="./admin.php?function=add_content&amp;tab=articles_polls"><?php echo LANG_ADD_ARTICLE_POLL; ?></a></li>
<li><span class="float-ending"><!-- --></span></li>
</ul>

<?php if ($_GET["tab"]==""): ?>

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

<form action="./admin.php?function=add_content" method="post">
<div><table><tr>
<td style="width: 50%;"><strong><?php echo LANG_ARTICLE_TITLE.":".((isset($_POST["submit"]) AND $_POST["article_title"]=="")?" <span class=\"input-error\">&times;</span>":""); ?></strong>
<br /><input type="text" name="article_title" id="article_title" value="<?php echo htmlspecialchars($_POST["article_title"]); ?>" style="width: 300px;" />
<br /><strong><?php echo LANG_GENERATED_LINK.":".((isset($_POST["submit"]) AND $_POST["article_link"]=="")?" <span class=\"input-error\">&times;</span>":""); ?></strong>
<br /><input type="text" name="article_link" id="article_link" value="<?php echo $_POST["article_link"]; ?>" style="width: 218px;" /> <input type="button" class="yellowbutton" value="<?php echo LANG_GENERATE; ?>" onclick="generate_link();" /></td>
<td><strong><?php echo LANG_SECTION; ?>:</strong>
<br /><select name="section" style="width: 307px;">
<option value=""><?php echo LANG_CHOOSE; ?></option>
<?php $sections->load_sections(0, $_POST["section"]); ?>
</select><?php if (isset($_POST["submit"]) AND $_POST["section"]=="") { echo " <span class=\"input-error\">&times;</span>"; } ?>
<br /><strong><?php echo LANG_META_KEYWORDS; ?>:</strong>
<br /><input type="text" name="keywords" value="<?php echo htmlspecialchars($_POST["keywords"]); ?>" style="width: 300px;" /></td>
</tr></table></div><div>
<p><strong><?php echo LANG_PROPERTIES; ?>:</strong></p>
<table><tr>                                          
<td><input type="radio" name="article_type" value="1"<?php if ($_POST["article_type"]==1) { echo " checked"; } ?> id="news" /> <label for="news"><?php echo LANG_NEWS; ?><?php if (isset($_POST["submit"]) AND $_POST["article_type"]=="") { echo " <span class=\"input-error\">&times;</span>"; } ?></label></td>
<td><input type="checkbox" name="hp" value="1"<?php if ($_POST["hp"]==1 OR !isset($_POST["hp"])) { echo " checked"; } ?> id="show_on_homepage" /> <label for="show_on_homepage"><?php echo LANG_SHOW_ON_HOMEPAGE; ?></label></td>
</tr><tr>
<td><input type="radio" name="article_type" value="2"<?php if ($_POST["article_type"]==2) { echo " checked"; } ?> id="standard_article" /> <label for="standard_article"><?php echo LANG_STANDARD_ARTICLE; ?></label></td>
<td><input type="checkbox" name="complete" value="1"<?php if ($_POST["complete"]==1) { echo " checked"; } ?> id="article_is_complete" /> <label for="article_is_complete"><?php echo LANG_ARTICLE_IS_COMPLETE; ?></label></td>
</tr><tr>
<td><input type="radio" name="article_type" value="3"<?php if ($_POST["article_type"]==3) { echo " checked"; } ?> id="important_article" /> <label for="important_article"><?php echo LANG_IMPORTANT_ARTICLE; ?></label></td>
<td><input type="checkbox" name="confirmed" value="1"<?php if ($_POST["confirmed"]==1) { echo " checked"; } if ($login->check_access("unverified_articles")==0) { echo " disabled"; } ?> id="article_is_verified" /> <label for="article_is_verified"><?php echo LANG_ARTICLE_IS_VERIFIED; ?></label></td>
</tr><tr>
<td><input type="checkbox" name="top" value="1"<?php if ($_POST["top"]==1) { echo " checked"; } ?> id="top" /> <label for="top">TOP</label></td>
<td><input type="checkbox" name="discussion" value="1"<?php if ($_POST["discussion"]==1 AND isset($_POST["submit"]) OR $_POST["discussion"]=="" AND !isset($_POST["submit"])) { echo " checked"; } ?> id="allow_discussion" /> <label for="allow_discussion"><?php echo LANG_ALLOW_DISCUSSION; ?></label></td>
</tr><tr>
<td><input type="checkbox" name="show_article_info" value="1"<?php if ($_POST["show_article_info"]==1 AND isset($_POST["submit"]) OR $_POST["show_article_info"]=="" AND !isset($_POST["submit"])) { echo " checked"; } ?> id="show_article_info" /> <label for="show_article_info"><?php echo LANG_SHOW_ARTICLE_INFO; ?></label></td>
<td><input type="checkbox" name="publish_now" value="1"<?php if ($_POST["publish_now"]==1) { echo " checked"; } if ($login->check_access("unpublished_articles")==0) { echo " disabled"; } ?> id="publish_now" /> <label for="publish_now"><?php echo LANG_PUBLISH_NOW; ?></label></td>
</tr><tr>
<td><strong><?php echo LANG_POLL; ?>:</strong><br /><select name="poll" style="width: 200px;">
<option value=""><?php echo LANG_CHOOSE; ?></option>
<?php $polls->load_polls($_POST["poll"]); ?>
</select></td>
<td><strong><?php echo LANG_SERIES; ?>:</strong><br /><select name="series" style="width: 200px;">
<option value=""><?php echo LANG_CHOOSE; ?></option>
<?php $sections->load_series($_POST["series"]); ?>
</select></td></tr></table></div>
<div><table>
<tr><td><strong><?php echo LANG_ACCESS; ?>:</strong></td><td><strong><?php echo LANG_AGE_LIMIT; ?>:</strong></td></tr><tr><td>
<input type="radio" name="ReqRights" value="All" id="access_all" checked /> <label for="access_all"><?php echo LANG_ACCESS_ALL; ?></label>
<input type="radio" name="ReqRights" value="0" id="access_registered"<?php if ($_POST["ReqRights"]=="0") { echo " checked"; } ?> /> <label for="access_registered"><?php echo LANG_ACCESS_0; ?></label>
<input type="radio" name="ReqRights" value="1" id="access_1"<?php if ($_POST["ReqRights"]=="1") { echo " checked"; } ?> /> <label for="access_1"><?php echo LANG_ACCESS_1; ?></label>
<input type="radio" name="ReqRights" value="2" id="access_2"<?php if ($_POST["ReqRights"]=="2") { echo " checked"; } ?> /> <label for="access_2"><?php echo LANG_ACCESS_2; ?></label>
<input type="radio" name="ReqRights" value="3" id="access_3"<?php if ($_POST["ReqRights"]=="3") { echo " checked"; } ?> /> <label for="access_3"><?php echo LANG_ACCESS_3; ?></label>
<input type="radio" name="ReqRights" value="4" id="access_4"<?php if ($_POST["ReqRights"]=="4") { echo " checked"; } ?> /> <label for="access_4"><?php echo LANG_ACCESS_4; ?></label>
<input type="radio" name="ReqRights" value="5" id="access_5"<?php if ($_POST["ReqRights"]=="5") { echo " checked"; } ?> /> <label for="access_5"><?php echo LANG_ACCESS_5; ?></label>
</td><td style="width: 20%;">
<select name="age_limit" style="width: 130px;">
<option value=""<?php if ($_POST["age_limit"]=="") { echo " selected"; } ?>><?php echo LANG_NO_LIMIT; ?></option>
<option value="15"<?php if ($_POST["age_limit"]=="15") { echo " selected"; } ?>><?php echo LANG_15_MORE; ?></option>
<option value="18"<?php if ($_POST["age_limit"]=="18") { echo " selected"; } ?>><?php echo LANG_18_MORE; ?></option>
</select></td></tr></table></div><div>                                                              
<p><strong><?php echo LANG_PEREX_IMAGE; ?>:</strong><br /><input type="text" name="pereximg" value="<?php echo ((isset($_POST["submit"]))?$_POST["pereximg"]:"/gallery/"); ?>" style="width: 300px;" /></p>
<p><strong><?php echo LANG_PEREX; ?>:</strong>
</p></div>
<div class="normal" id="perex"><textarea name="perex" class="tinymce"><?php echo $_POST["perex"]; ?></textarea></div>
<div><p><strong><?php echo LANG_TEXT; ?>:</strong></p></div>
<div class="normal" id="text"><textarea name="text" class="tinymce"><?php echo $_POST["text"]; ?></textarea></div>
<div><p><input type="submit" name="submit" class="greenbutton" value="<?php echo LANG_ADD; ?>" /></p></div>
</form>

<?php endif; if ($_GET["tab"]=="images"): ?>

<form action="./admin.php?function=add_content&amp;tab=images" method="post" enctype="multipart/form-data">
<div><table><tr>
<td style="width: 50%; vertical-align: top;"><strong><?php echo LANG_FILE; ?>:</strong><br />
<input type="file" accept="image/*" multiple="true" name="file[]" /><?php if (isset($_POST["submit"]) AND $_FILES["file"]["name"]=="") { echo " <span class=\"input-error\">&times;</span>"; } ?></td>
<td style="width: 50%; vertical-align: top;"><strong><?php echo LANG_GALLERY; ?>:</strong><br />
<select name="gallery" style="width: 200px;">
<option value=""><?php echo LANG_CHOOSE; ?></option>
<?php $images->load_galleries($_POST["gallery"]); ?>
</select><?php if (isset($_POST["submit"]) AND $_POST["gallery"]=="") { echo " <span class=\"input-error\">&times;</span>"; } ?>
</tr></table>
<p><input type="submit" name="submit" class="greenbutton" value="<?php echo LANG_UPLOAD_AN_IMAGE; ?>" /></p>
</div></form>

<?php endif; if ($_GET["tab"]=="files"): ?>

<form action="./admin.php?function=add_content&amp;tab=files" method="post" enctype="multipart/form-data"><div>
<p><strong><?php echo LANG_FILE; ?>:</strong><br /><input type="file" name="file" /><?php if (isset($_POST["submit"]) AND $_FILES["file"]["name"]=="") { echo " <span class=\"input-error\">&times;</span>"; } ?></p>
<p><input type="submit" name="submit" class="greenbutton" value="<?php echo LANG_UPLOAD_A_FILE; ?>" /></p>
</div></form>

<?php endif; if ($_GET["tab"]=="articles_polls"): ?>

<form action="./admin.php?function=add_content&amp;tab=articles_polls" method="post"><div>
<p><strong><?php echo LANG_NAME; ?>:</strong><br /><input type="text" name="name" style="width: 200px;" /><?php if (isset($_POST["submit"]) AND $_POST["name"]=="") { echo " <span class=\"input-error\">&times;</span>"; } ?></p>
<p><strong><?php echo LANG_OPTIONS; ?>:</strong><br /><input type="checkbox" name="visible" value="1" /> <?php echo LANG_SHOW; ?></p>
</div><div>
<p><strong><?php echo LANG_ANSWERS; ?>:</strong><br /><input type="text" name="answer1" style="width: 200px;" /><br /><input type="text" name="answer2" style="width: 200px;" /><br /><input type="text" name="answer3" style="width: 200px;" /><br /><input type="text" name="answer4" style="width: 200px;" /><br /><input type="text" name="answer5" style="width: 200px;" /></p>
</div><div>
<p><input type="submit" class="greenbutton" name="submit" value="<?php echo LANG_ADD; ?>" /></p>
</div></form>

<?php endif; ?>
<?php else: $access_denied=1; endif; ?>