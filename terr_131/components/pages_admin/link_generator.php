<!DOCTYPE html>
<html lang="en">
<head><meta charset="utf-8" /></head>
<body>
<?php
//---[CZ]: načteme potřebné třídy
include "../classes/Db_layer_mysql.php";
include "../classes/Urls.php";

//---[CZ]: vytvoříme instance
$urls=new Urls();
?>
<script type="text/javascript">
/* <![CDATA[ */
top.document.getElementById('article_link').value='<?php echo $urls->transfer_to_seo($_GET["article_title"]); ?>';
/* ]]> */
</script>
</body>
</html>