<?php

define('GLPI_ROOT', '../../..');
include (GLPI_ROOT."/inc/includes.php");

PluginMobileHtml::header(Central::getTypeName(1), $_SERVER['PHP_SELF']);

PluginMobileHtml::footer();
?>
