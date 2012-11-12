<?php

/*define('GLPI_ROOT', '../../..');
include (GLPI_ROOT."/inc/includes.php");*/

session_start();

$prevdir = getcwd();
chdir("/var/www/glpi/0.84-bugfixes/front");
ob_start();
include "computer.php";
$html = ob_get_contents();
ob_end_clean();
chdir($prevdir);

if(!isset($_REQUEST['url'])) $_REQUEST['url'] = "";

PluginMobileHtml::header(PluginMobilePage::getTitle($_REQUEST['url']), $_SERVER['PHP_SELF']);
PluginMobilePage::show($_REQUEST['url']);
PluginMobileHtml::footer();
?>
