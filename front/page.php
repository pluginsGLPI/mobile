<?php

define('GLPI_ROOT', '../../..');
include (GLPI_ROOT."/inc/includes.php");

PluginMobileHtml::header(PluginMobilePage::getTitle($url), $_SERVER['PHP_SELF']);

if(!isset($_REQUEST['url'])) $_REQUEST['url'] = "";
PluginMobilePage::show($_REQUEST['url']);

PluginMobileHtml::footer();
?>
