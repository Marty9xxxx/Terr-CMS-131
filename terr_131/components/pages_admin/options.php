<?php
//---[CZ]: ověříme přístup
if ($login->check_access("options")==1):

//---[CZ]: načteme potřebné třídy
include CLASSES_PATH."Options.php";

//---[CZ]: vytvoříme instance
$options=new Options();

//---[CZ]: uložení změn v nastavení
if (isset($_POST["submit"])) { $status_messages->print_success(LANG_ACTION_WAS_SUCCESSFULLY_COMPLETED); }
?>

<h2><?php echo LANG_OPTIONS; ?></h2>
<ul id="tab-bar">
<li<?php if ($_GET["tab"]=="") { echo " class=\"active-item\""; } ?>><a href="./admin.php?function=options"><?php echo LANG_SETTINGS; ?></a></li>
<li<?php if ($_GET["tab"]=="access") { echo " class=\"active-item\""; } ?>><a href="./admin.php?function=options&amp;tab=access"><?php echo LANG_ACCESS; ?></a></li>
<li<?php if ($_GET["tab"]=="meta_tags") { echo " class=\"active-item\""; } ?>><a href="./admin.php?function=options&amp;tab=meta_tags"><?php echo LANG_META_TAGS; ?></a></li>
<li><span class="float-ending"><!-- --></span></li>
</ul>

<?php if ($_GET["tab"]==""): ?>

<form action="./admin.php?function=options" enctype="multipart/form-data" method="post"><div>
<table><tr>
<td style="width: 33%;"><strong><?php echo LANG_LANGUAGE_FILE; ?>:</strong><br />
<select name="lang" style="width: 200px;">
<?php $options->selection($config_variables->get("lang"), "languages"); ?>
</select></td>
<td style="width: 34%;"><strong><?php echo LANG_THEME; ?>:</strong><br />
<select name="theme" style="width: 200px;">
<?php $options->selection($config_variables->get("theme"), "themes"); ?>
</select></td>
<td style="width: 33%;"><strong><?php echo LANG_TEMPLATE; ?>:</strong><br />
<select name="template" style="width: 200px;">
<?php $options->selection($config_variables->get("template"), "templates"); ?>
</select></td>
</tr></table></div>
<div><table><tr>
<td style="width: 50%;"><strong><?php echo LANG_SITENAME; ?>:</strong><br /><input type="text" name="sitename" value="<?php echo htmlspecialchars($config_variables->get("sitename")); ?>" style="width: 300px;" /></td>
<td style="width: 50%;"><strong><?php echo LANG_URL_ADDRESSES_TYPE; ?>:</strong><br /><input type="radio" name="url_type" value="static"<?php if (URL_TYPE=="static") { echo " checked"; } ?> id="static_sections" /> <label for="static_sections"><?php echo LANG_STATIC_ADDRESSES; ?></label> <input type="radio" name="url_type" value="dynamic"<?php if (URL_TYPE=="dynamic") { echo " checked"; } ?> id="dynamic_sections" /> <label for="dynamic_sections"><?php echo LANG_DYNAMIC_ADDRESSES; ?></td>
</tr><tr>
<td style="width: 50%;"><strong><?php echo LANG_NAME_OF_MENU; ?>:</strong><br /><input type="text" name="name_of_menu" value="<?php echo htmlspecialchars($config_variables->get("name_of_menu")); ?>" style="width: 300px;" /></td>
<td style="width: 50%;"><strong><?php echo LANG_NAME_OF_HP; ?>:</strong><br /><input type="text" name="name_of_hp" value="<?php echo htmlspecialchars($config_variables->get("name_of_hp")); ?>" style="width: 300px;" /></td>
</tr><tr>
<td style="width: 50%;"><strong>Nahrát obrázek pro favicon (16x16):</strong><br /><input type="file" accept="image/*" name="favicon" style="width: 300px;" /><?php if (file_exists("./components/images/favicon/favicon.ico")==true) { echo "<img src=\"./components/images/favicon/favicon.ico\" width=\"16\" height=\"16\" alt=\"favicon\" />"; } ?></td>
</tr></table></div>
<div><table><tr>
<td style="width: 50%;"><strong><?php echo LANG_USERS_MUST_BE_LOGGED_IN_FOR_WRITING_COMMENTS; ?>:</strong><br /><input type="radio" name="login_for_comments" value="1"<?php if ($config_variables->get("login_for_comments")==1) { echo " checked"; } ?> id="login_for_comments_YES" /> <label for="login_for_comments_YES"><?php echo LANG_YES; ?></label> <input type="radio" name="login_for_comments" value="0"<?php if ($config_variables->get("login_for_comments")==0) { echo " checked"; } ?> id="login_for_comments_NO" /> <label for="login_for_comments_NO"><?php echo LANG_NO; ?></label></td>
<td style="width: 50%;"><strong><?php echo LANG_SHOW_TEXT_OF_THE_ARTICLE_IF_PEREX_IS_NULL; ?>:</strong><br /><input type="radio" name="show_text" value="1"<?php if ($config_variables->get("show_text")==1) { echo " checked"; } ?> id="show_text_YES" /> <label for="show_text_YES"><?php echo LANG_YES; ?></label> <input type="radio" name="show_text" value="0"<?php if ($config_variables->get("show_text")==0) { echo " checked"; } ?> id="show_text_NO" /> <label for="show_text_NO"><?php echo LANG_NO; ?></label></td>
</tr><tr>
<td><strong><?php echo LANG_SHOW_UNCONFIRMED_COMMENTS; ?>:</strong><br /><input type="radio" name="show_unconfirmed_comments" value="1"<?php if ($config_variables->get("show_unconfirmed_comments")==1) { echo " checked"; } ?> id="show_unconfirmed_comments_YES" /> <label for="show_unconfirmed_comments_YES"><?php echo LANG_YES; ?></label> <input type="radio" name="show_unconfirmed_comments" value="0"<?php if ($config_variables->get("show_unconfirmed_comments")==0) { echo " checked"; } ?> id="show_unconfirmed_comments_NO" /> <label for="show_unconfirmed_comments_NO"><?php echo LANG_NO; ?></label></td>
<td><strong><?php echo LANG_SORT_IMAGES_BY_NAME; ?>:</strong><br /><input type="radio" name="sort_images_by_name" value="1"<?php if ($config_variables->get("sort_images_by_name")==1) { echo " checked"; } ?> id="_YES" /> <label for="_YES"><?php echo LANG_YES; ?></label> <input type="radio" name="sort_images_by_name" value="0"<?php if ($config_variables->get("sort_images_by_name")==0) { echo " checked"; } ?> id="sort_images_by_name_NO" /> <label for="sort_images_by_name_NO"><?php echo LANG_NO; ?></label></td>
</tr><tr>
<td><strong><?php echo LANG_SORT_COMMENTS_FROM_NEWEST; ?>:</strong><br /><input type="radio" name="sort_comments_from_newest" value="1"<?php if ($config_variables->get("sort_comments_from_newest")==1) { echo " checked"; } ?> id="sort_comments_from_newest_YES" /> <label for="sort_comments_from_newest_YES"><?php echo LANG_YES; ?></label> <input type="radio" name="sort_comments_from_newest" value="0"<?php if ($config_variables->get("sort_comments_from_newest")==0) { echo " checked"; } ?> id="sort_comments_from_newest_NO" /> <label for="sort_comments_from_newest_NO"><?php echo LANG_NO; ?></label></td>
<td><strong><?php echo LANG_USE_EMOTICONS; ?>:</strong><br /><input type="radio" name="use_emoticons" value="1"<?php if ($config_variables->get("use_emoticons")==1) { echo " checked"; } ?> id="use_emoticons_YES" /> <label for="use_emoticons_YES"><?php echo LANG_YES; ?></label> <input type="radio" name="use_emoticons" value="0"<?php if ($config_variables->get("use_emoticons")==0) { echo " checked"; } ?> id="use_emoticons_NO" /> <label for="use_emoticons_NO"><?php echo LANG_NO; ?></label></td>
</tr><tr>
<td><strong><?php echo LANG_SHOW_LAST_COMMENT_MODULE; ?>:</strong><br /><input type="radio" name="show_menu_with_last_comments" value="1"<?php if ($config_variables->get("show_menu_with_last_comments")==1) { echo " checked"; } ?> id="show_menu_with_last_comments_YES" /> <label for="show_menu_with_last_comments_YES"><?php echo LANG_YES; ?></label> <input type="radio" name="show_menu_with_last_comments" value="0"<?php if ($config_variables->get("show_menu_with_last_comments")==0) { echo " checked"; } ?> id="show_menu_with_last_comments_NO" /> <label for="show_menu_with_last_comments_NO"><?php echo LANG_NO; ?></label></td>
<td><strong><?php echo LANG_SHOW_LAST_LOGIN_MODULE; ?>:</strong><br /><input type="radio" name="show_menu_with_last_login" value="1"<?php if ($config_variables->get("show_menu_with_last_login")==1) { echo " checked"; } ?> id="show_menu_with_last_login_YES" /> <label for="show_menu_with_last_login_YES"><?php echo LANG_YES; ?></label> <input type="radio" name="show_menu_with_last_login" value="0"<?php if ($config_variables->get("show_menu_with_last_login")==0) { echo " checked"; } ?> id="show_menu_with_last_login_NO" /> <label for="show_menu_with_last_login_NO"><?php echo LANG_NO; ?></label></td>
</tr><tr>
<td><strong><?php echo LANG_SHOW_RANDOM_IMAGE; ?>:</strong><br /><input type="radio" name="show_random_image" value="1"<?php if ($config_variables->get("show_random_image")==1) { echo " checked"; } ?> id="show_random_image_YES" /> <label for="show_random_image_YES"><?php echo LANG_YES; ?></label> <input type="radio" name="show_random_image" value="0"<?php if ($config_variables->get("show_random_image")==0) { echo " checked"; } ?> id="show_random_image_NO" /> <label for="show_random_image_NO"><?php echo LANG_NO; ?></label></td>
<td><strong><?php echo LANG_SHOW_RECENT_ARTICLES; ?>:</strong><br /><input type="radio" name="show_recent_articles" value="1"<?php if ($config_variables->get("show_recent_articles")==1) { echo " checked"; } ?> id="show_recent_articles_YES" /> <label for="show_recent_articles_YES"><?php echo LANG_YES; ?></label> <input type="radio" name="show_recent_articles" value="0"<?php if ($config_variables->get("show_recent_articles")==0) { echo " checked"; } ?> id="show_recent_articles_NO" /> <label for="show_recent_articles_NO"><?php echo LANG_NO; ?></label></td>
</tr><tr>
<td><strong><?php echo LANG_SHOW_THE_LINK_TO_OPEN_WHOLE_ARTICLE; ?>:</strong><br /><input type="radio" name="show_the_link_to_open_whole_article" value="1"<?php if ($config_variables->get("show_the_link_to_open_whole_article")==1) { echo " checked"; } ?> id="show_the_link_to_open_whole_article_YES" /> <label for="show_the_link_to_open_whole_article_YES"><?php echo LANG_YES; ?></label> <input type="radio" name="show_the_link_to_open_whole_article" value="0"<?php if ($config_variables->get("show_the_link_to_open_whole_article")==0) { echo " checked"; } ?> id="show_the_link_to_open_whole_article_NO" /> <label for="show_the_link_to_open_whole_article_NO"><?php echo LANG_NO; ?></label></td>
<td><strong><?php echo LANG_SHOW_AVATAR_IN_LOGIN_FORM; ?>:</strong><br /><input type="radio" name="show_avatar_in_login_form" value="1"<?php if ($config_variables->get("show_avatar_in_login_form")==1) { echo " checked"; } ?> id="show_avatar_in_login_form_YES" /> <label for="show_avatar_in_login_form_YES"><?php echo LANG_YES; ?></label> <input type="radio" name="show_avatar_in_login_form" value="0"<?php if ($config_variables->get("show_avatar_in_login_form")==0) { echo " checked"; } ?> id="show_avatar_in_login_form_NO" /> <label for="show_avatar_in_login_form_NO"><?php echo LANG_NO; ?></label></td>
</tr><tr>
<td><strong><?php echo LANG_SHOW_USER_NUMBER_MODULE; ?>:</strong><br /><input type="radio" name="show_accounts_count" value="1"<?php if ($config_variables->get("show_accounts_count")==1) { echo " checked"; } ?> id="show_accounts_count_YES" /> <label for="show_accounts_count_YES"><?php echo LANG_YES; ?></label> <input type="radio" name="show_accounts_count" value="0"<?php if ($config_variables->get("show_accounts_count")==0) { echo " checked"; } ?> id="show_accounts_count_NO" /> <label for="show_accounts_count_NO"><?php echo LANG_NO; ?></label></td>
<td><strong><?php echo LANG_MAIL_REQUIRED; ?>:</strong><br /><input type="radio" name="mail_required" value="1"<?php if ($config_variables->get("mail_required")==1) { echo " checked"; } ?> id="mail_required_YES" /> <label for="mail_required_YES"><?php echo LANG_YES; ?></label> <input type="radio" name="mail_required" value="0"<?php if ($config_variables->get("mail_required")==0) { echo " checked"; } ?> id="mail_required_NO" /> <label for="mail_required_NO"><?php echo LANG_NO; ?></label></td>
</tr></table></div>
<div><table><tr>
<td style="width: 50%;"><strong><?php echo LANG_NUMBER_OF_ARTICLES_ON_ONE_PAGE; ?>:</strong><br /><input type="text" name="articles_number" value="<?php echo $config_variables->get("articles_number"); ?>" style="width: 30px;" /></td>
<td style="width: 50%;"><strong><?php echo LANG_NUMBER_OF_COMMENTS; ?>:</strong><br /><input type="text" name="comments_number" value="<?php echo $config_variables->get("comments_number"); ?>" style="width: 30px;" /></td>
</tr></table></div>
<div><strong><?php echo LANG_ARTICLE_INFO; ?>:</strong><br />
<table><tr>
<td style="width: 50%;"><input type="checkbox" name="show_article_date" value="1"<?php if ($config_variables->get("show_article_date")==1) { echo " checked"; } ?> id="show_article_date" /> <label for="show_article_date"><?php echo LANG_SHOW_DATE; ?></label></td>
<td style="width: 50%;"><input type="checkbox" name="show_article_section" value="1"<?php if ($config_variables->get("show_article_section")==1) { echo " checked"; } ?> id="show_article_section" /> <label for="show_article_section"><?php echo LANG_SHOW_SECTION; ?></label></td>
</tr><tr>
<td><input type="checkbox" name="show_article_comments_number" value="1"<?php if ($config_variables->get("show_article_comments_number")==1) { echo " checked"; } ?> id="show_article_comments_number" /> <label for="show_article_comments_number"><?php echo LANG_SHOW_COMMENTS_NUMBER; ?></label></td>
<td><input type="checkbox" name="show_article_views_number" value="1"<?php if ($config_variables->get("show_article_views_number")==1) { echo " checked"; } ?> id="show_views_number" /> <label for="show_views_number"><?php echo LANG_SHOW_VIEWS_NUMBER; ?></label></td>
</tr><tr>
<td><input type="checkbox" name="show_article_author" value="1"<?php if ($config_variables->get("show_article_author")==1) { echo " checked"; } ?> id="show_author" /> <label for="show_author"><?php echo LANG_SHOW_AUTHOR; ?></label></td>
<td><input type="checkbox" name="show_addthis" value="1"<?php if ($config_variables->get("show_addthis")==1) { echo " checked"; } ?> id="show_addthis" /> <label for="show_addthis"><?php echo LANG_SHOW_ADDTHIS; ?></label></td>
</tr></table></div>
<div><p><input type="submit" class="greenbutton" name="submit" value="<?php echo LANG_SAVE_CHANGES; ?>" /></p></div>
</form>

<?php endif; if ($_GET["tab"]=="access"): ?>

<form action="./admin.php?function=options&amp;tab=access" method="post"><div><table>
<tr><td><strong><?php echo LANG_NAMES_OF_RIGHT_LEVELS; ?>:</strong></td><td><input type="text" name="5" disabled value="5" size="1" /> <input type="text" name="admin3" value="<?php echo $config_variables->get("admin3"); ?>" /></td>
<td><input type="text" name="4" disabled value="4" size="1" /> <input type="text" name="admin2" value="<?php echo $config_variables->get("admin2"); ?>" /></td></tr>
<tr><td><input type="text" name="3" disabled value="3" size="1" /> <input type="text" name="admin1" value="<?php echo $config_variables->get("admin1"); ?>" /></td>
<td><input type="text" name="2" disabled value="2" size="1" /> <input type="text" name="corrector" value="<?php echo $config_variables->get("corrector"); ?>" /></td>
<td><input type="text" name="1" disabled value="1" size="1" /> <input type="text" name="redactor" value="<?php echo $config_variables->get("redactor"); ?>" /></td></tr>
</table></div><div>
<p><strong><?php echo LANG_REQUIRED_RIGHTS_FOR_ACCESS_TO_THE_ADMIN_SECTIONS; ?>:</strong></p>
<table><tr>
<td><select name="rights_overview"<?php $options->rights_selection($config_variables->get("rights_overview")); ?></select> <?php echo LANG_OVERVIEW; ?></td>
<td><select name="rights_add_content"<?php $options->rights_selection($config_variables->get("rights_add_content")); ?></select> <?php echo LANG_ADD_CONTENT; ?></td>
<td><select name="rights_my_content"<?php $options->rights_selection($config_variables->get("rights_my_content")); ?></select> <?php echo LANG_MY_CONTENT; ?></td>
</tr><tr>
<td><select name="rights_sections"<?php $options->rights_selection($config_variables->get("rights_sections")); ?></select> <?php echo LANG_SECTIONS." & ".LANG_SERIES; ?></td>
<td><select name="rights_columns"<?php $options->rights_selection($config_variables->get("rights_columns")); ?></select> <?php echo LANG_COLUMNS; ?></td>
<td><select name="rights_images"<?php $options->rights_selection($config_variables->get("rights_images")); ?></select> <?php echo LANG_IMAGES; ?></td>
</tr><tr>
<td><select name="rights_files"<?php $options->rights_selection($config_variables->get("rights_files")); ?></select> <?php echo LANG_FILES; ?></td>
<td><select name="rights_comments"<?php $options->rights_selection($config_variables->get("rights_comments")); ?></select> <?php echo LANG_COMMENTS; ?></td>
<td><select name="rights_unverified_articles"<?php $options->rights_selection($config_variables->get("rights_unverified_articles")); ?></select> <?php echo LANG_UNVERIFIED_ARTICLES; ?></td>
</tr><tr>
<td><select name="rights_unpublished_articles"<?php $options->rights_selection($config_variables->get("rights_unpublished_articles")); ?></select> <?php echo LANG_UNPUBLISHED_ARTICLES; ?></td>
<td><select name="rights_archive"<?php $options->rights_selection($config_variables->get("rights_archive")); ?></select> <?php echo LANG_ARCHIVE; ?></td>
<td><select name="rights_polls"<?php $options->rights_selection($config_variables->get("rights_polls")); ?></select> <?php echo LANG_POLLS; ?></td>
</tr><tr>
<td><select name="rights_options"<?php $options->rights_selection($config_variables->get("rights_options")); ?></select> <?php echo LANG_OPTIONS; ?></td>
<td><select name="rights_edit_content"<?php $options->rights_selection($config_variables->get("rights_edit_content")); ?></select> <?php echo LANG_EDIT_CONTENT; ?></td>
<td><select name="rights_users"<?php $options->rights_selection($config_variables->get("rights_users")); ?></select> <?php echo LANG_USERS; ?></td>
</tr><tr>
<td><select name="rights_bans"<?php $options->rights_selection($config_variables->get("rights_bans")); ?></select> <?php echo LANG_BANS; ?></td>
<td><select name="rights_mail_messages"<?php $options->rights_selection($config_variables->get("rights_mail_messages")); ?></select> <?php echo LANG_MAIL_MESSAGES; ?></td>
<td><select name="rights_advanced_options"<?php $options->rights_selection($config_variables->get("rights_advanced_options")); ?></select> <?php echo LANG_ADVANCED_OPTIONS; ?></td>
</tr></table>
<p><input type="submit" class="greenbutton" name="submit" value="<?php echo LANG_SAVE_CHANGES; ?>" /></p></div></form>

<?php endif; if ($_GET["tab"]=="meta_tags"): ?>

<form action="./admin.php?function=options&amp;tab=meta_tags" method="post"><div>
<p><strong><?php echo LANG_META_COPYRIGHT; ?>:</strong><br /><input type="text" name="meta_copyright" value="<?php echo htmlspecialchars($config_variables->get("meta_copyright")); ?>" style="width: 700px;" /></p>
<p><strong><?php echo LANG_META_KEYWORDS; ?>:</strong><br /><textarea name="meta_keywords" cols="" rows="" style="width: 700px; height: 100px;"><?php echo $config_variables->get("meta_keywords"); ?></textarea></p>
<p><strong><?php echo LANG_META_DESCRIPTION; ?>:</strong><br /><textarea name="meta_desc" cols="" rows="" style="width: 700px; height: 100px;"><?php echo $config_variables->get("meta_desc"); ?></textarea></p>
<p><input type="submit" name="submit" class="greenbutton" value="<?php echo LANG_SAVE_CHANGES; ?>" /></p>
</div></form>

<?php endif; ?>
<?php else: $access_denied=1; endif; ?>