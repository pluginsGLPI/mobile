<?php
define('GLPI_ROOT', '../../..');
include (GLPI_ROOT."/inc/includes.php");

PluginMobileHtml::header($title, $_SERVER['PHP_SELF']);

if(!isset($_REQUEST['itemtype'])) $_REQUEST['itemtype'] = "";
if(!isset($_REQUEST['id'])) $_REQUEST['id'] = 1;
$title = $_REQUEST['itemtype']::getTypeName(1);

PluginMobilePage::showItem($_REQUEST['itemtype'], $_REQUEST['id']);

PluginMobileHtml::footer();
?>
