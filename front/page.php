<?php

define('GLPI_ROOT', '../../..');
include (GLPI_ROOT."/inc/includes.php");

if(!isset($_REQUEST['url'])) $_REQUEST['url'] = "";

PluginMobileHtml::header(PluginMobilePage::getTitle($_REQUEST['url']), $_SERVER['PHP_SELF']);
PluginMobilePage::show($_REQUEST['url']);
PluginMobileHtml::footer();
?>
