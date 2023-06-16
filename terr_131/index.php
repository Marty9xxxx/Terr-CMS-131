<?php
//error_reporting(E_ALL);

//---[CZ]: pokud chceme aktualizovat systém
if (file_exists("install/install.php")==1) { header("Location: install/install.php"); }

//---[CZ]: složka s třídami
define("CLASSES_PATH", "components/classes/");

//---[CZ]: načteme soubor s DB údaji
@include "config.php";

//---[CZ]: definování proměnných
if (!isset($_GET["action"])) { $_GET["action"]=""; }
if (!isset($_GET["function"])) { $_GET["function"]=""; }
if (!isset($_GET["link"])) { $_GET["link"]=""; }
if (!isset($_GET["article"])) { $_GET["article"]=""; }
if (!isset($_GET["section"])) { $_GET["section"]=""; }
if (!isset($_GET["profile"])) { $_GET["profile"]=""; }
if (!isset($_GET["gallery"])) { $_GET["gallery"]=""; }
if (!isset($_GET["string"])) { $_GET["string"]=""; }
if (!isset($_GET["pm"])) { $_GET["pm"]=""; }
if (!isset($_GET["page"])) { $_GET["page"]=1; }

//---[CZ]: načteme potřebné třídy
include CLASSES_PATH."Db_layer_".DB_TYPE.".php";
include CLASSES_PATH."Index.php";
include CLASSES_PATH."Login.php";
include CLASSES_PATH."Status_messages.php";
include CLASSES_PATH."Config_variables.php";
include CLASSES_PATH."Urls.php";
include CLASSES_PATH."Captcha.php";
include CLASSES_PATH."Profile.php";
include CLASSES_PATH."Registration.php";

$index=new Index();
$login=new Login();
$status_messages=new Status_messages();
$config_variables=new Config_variables();
$profile=new Profile();
$urls=new Urls();
$registration=new Registration();

//---[CZ]: zkontrolujeme, zda není IP zabanována
if ($index->check_ban()==0):

//---[CZ]: zvolíme typ URL
define("URL_TYPE", $config_variables->get("url_type"));

//---[CZ]: načteme jazykový soubor
include ("languages/".$config_variables->get("lang").".php");

//---[CZ]: odstartujeme session
session_start();

//---[CZ]: přihlášení
if ($_GET["action"]=="login") { $login->login(); }

//---[CZ]: automatické přihlášení
if (isset($_COOKIE["user_check"]) AND !isset($_SESSION["user_id"])) { $login->autologin(); }

if ($_GET["link"]!="") {

$link=explode("/", $_GET["link"]);

if ($link[0]=="articles") { $_GET["article"]=$link[1]; if ($link[2]!="") { $_GET["page"]=$link[2]; } else { $_GET["page"]=1; } }

if ($link[0]=="sections") { $_GET["section"]=$link[1]; if ($link[2]!="") { $_GET["page"]=$link[2]; } else { $_GET["page"]=1; } }

if ($link[0]=="profiles") { $_GET["profile"]=$link[1]; }

if ($link[0]=="galleries") { $_GET["gallery"]=$link[1]; }

if ($link[0]=="settings") { $_GET["function"]=$link[0]; }

if ($link[0]=="registration") { $_GET["function"]=$link[0]; }

if ($link[0]=="comments") { $_GET["function"]="comments"; if ($link[1]!="") { $_GET["user"]=$link[1]; } else { $_GET["user"]=""; } }

if ($link[0]=="search") { $_GET["function"]="search"; }

if ($link[0]=="files") { $_GET["function"]="files"; if ($link[1]!="") { $_GET["page"]=$link[1]; } else { $_GET["page"]=""; } }

if ($link[0]=="inbox") { $_GET["function"]="inbox"; if ($link[1]!="") { $_GET["stuff"]=$link[1]; } else { $_GET["stuff"]=""; } }

if ($link[0]=="outbox") { $_GET["function"]="outbox"; if ($link[1]!="") { $_GET["stuff"]=$link[1]; } else { $_GET["stuff"]=""; } }

if ($link[0]=="new-message") { $_GET["function"]="new-message"; }

if ($link[0]=="logout") { $_GET["action"]="logout"; }

}

//---[CZ]: odhlášení
if ($_GET["action"]=="logout") { $login->logout(); }

//---[CZ]: přidání komentáře
if ($_GET["action"]=="add_comment") { $index->add_comment(); }

//---[CZ]: hlasování
if ($_GET["action"]=="vote") { $index->vote(); }

//---[CZ]: uložení změn v profilu
if ($_GET["profile"]!="" AND isset($_POST["submit"])) { $profile->update(); }

include "templates/".$config_variables->get("template").".phtml";

else: include "components/pages_index/ban_page.php"; endif; ?>