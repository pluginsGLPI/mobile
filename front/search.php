<?php
define('GLPI_ROOT', '../../..');
include (GLPI_ROOT."/inc/includes.php");

if(!isset($_REQUEST['itemtype'])) $_REQUEST['itemtype'] = "";

$title = $_REQUEST['itemtype']::getTypeName(2);

PluginMobileHtml::header($title, $_SERVER['PHP_SELF']);
PluginMobileSearch::show($_REQUEST['itemtype']);
PluginMobileHtml::footer();
?>
