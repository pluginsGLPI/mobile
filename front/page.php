<?php
define('GLPI_ROOT', '../../..');
include (GLPI_ROOT."/inc/includes.php");

if(!isset($_REQUEST['itemtype'])) $_REQUEST['itemtype'] = "";
if(!isset($_REQUEST['id'])) $_REQUEST['id'] = 1;
$title = PluginMobilePage::getTitle($_REQUEST['itemtype'], $_REQUEST['id']);

PluginMobileHtml::header($title, $_SERVER['PHP_SELF']);
PluginMobilePage::showItem($_REQUEST['itemtype'], $_REQUEST['id']);
PluginMobileHtml::footer();
?>
