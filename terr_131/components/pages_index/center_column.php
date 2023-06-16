<?php
if ($_GET["function"]=="" AND $_GET["article"]=="" AND $_GET["profile"]=="" AND $_GET["gallery"]=="") {
$index->print_articles();
}

if ($_GET["article"]!="") {
$index->view_article();
}

if ($_GET["gallery"]!="") {
include CLASSES_PATH."Images.php";
$images=new Images();
$images->print_images();
}

if ($_GET["profile"]!="") {
$profile->get_page();
}

if ($_GET["function"]=="inbox" OR $_GET["function"]=="outbox" OR $_GET["function"]=="new-message") {
$index->get_pm();
}

if ($_GET["function"]=="settings") {
$settings->get_page();
}

if ($_GET["function"]=="files") {
$index->print_files();
}

if ($_GET["function"]=="comments") {
$index->get_comments();
}

if ($_GET["function"]=="search") {
$index->print_articles();
}

if ($_GET["function"]=="registration") {
$registration->get_page();
}
?>