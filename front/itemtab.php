<?php
define('GLPI_ROOT', '../../..');
include (GLPI_ROOT."/inc/includes.php");

if(!isset($_REQUEST['itemtype'])) $_REQUEST['itemtype'] = "";
if(!isset($_REQUEST['id'])) $_REQUEST['id'] = 1;
if(!isset($_REQUEST['tabs_id'])) $_REQUEST['tabs_id'] = "default";

$title = PluginMobileItemTab::getTitle($_REQUEST['itemtype'],
                                       $_REQUEST['id'], 
                                       $_REQUEST['tabs_id']);

PluginMobileHtml::header($title, $_SERVER['PHP_SELF']);
PluginMobileItemTab::show($_REQUEST['itemtype'], $_REQUEST['id'], $_REQUEST['tabs_id']);
PluginMobileHtml::footer();
?>
