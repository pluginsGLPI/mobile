<?php
define('GLPI_ROOT', '../../..');
include (GLPI_ROOT."/inc/includes.php");

if(!isset($_REQUEST['itemtype'])) $_REQUEST['itemtype'] = "";

PluginMobileHtml::header("", $_SERVER['PHP_SELF']);
PluginMobileSearch::show($_REQUEST['itemtype']);
PluginMobileHtml::footer();
?>
