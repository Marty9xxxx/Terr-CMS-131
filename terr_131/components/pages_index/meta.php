<meta charset="utf-8" />
<meta name="author" content="Powered by TerrCMS v<?php echo $config_variables->get("version"); ?>, (c) 2011-2013 Michal Lepíček (original core - K:CMS)" />
<meta name="keywords" content="<?php if ($_GET["article"]!="" AND $index->current_article("keywords")!="") { echo $index->current_article("keywords"); } else { echo $config_variables->get("meta_keywords"); } ?>" />
<meta name="robots" content="index, follow" />
<meta name="description" content="<?php echo $config_variables->get("meta_desc"); ?>" />
<link rel="stylesheet" href="<?php echo $urls->root(); ?>themes/<?php echo $config_variables->get("theme"); ?>.css" type="text/css" />
<link rel="stylesheet" href="<?php echo $urls->root(); ?>components/slimbox/css/slimbox2.css" type="text/css" />
<link rel="icon" href="/components/images/favicon/favicon<?php echo ((!file_exists(".".$urls->root()."components/images/favicon/favicon.ico"))?"cms":""); ?>.ico" />
<link rel="alternate" type="application/rss+xml" href="<?php echo $urls->root(); ?>rss.php" />
<title><?php echo $index->get_title(); ?></title>
<script type="text/javascript" src="<?php echo $urls->rootJS(); ?>components/js/jquery.js"></script>
<script type="text/javascript" src="<?php echo $urls->rootJS(); ?>components/js/ajax.js"></script>
<script type="text/javascript" src="<?php echo $urls->rootJS(); ?>components/slimbox/js/slimbox2.js"></script>
<!--[if IE]><script type="text/javascript" src="<?php echo $urls->rootJS(); ?>components/js/ie.js"></script><![endif]-->