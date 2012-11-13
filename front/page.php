<?php

//chdir("/var/www/glpi/0.84-bugfixes/front");
//ob_start();
//include "helpdesk.faq.php";
// $html = ob_get_contents();
// ob_end_clean();


define('GLPI_ROOT', '../../..');
include (GLPI_ROOT."/inc/includes.php");

if(!isset($_REQUEST['url'])) $_REQUEST['url'] = "";

PluginMobileHtml::header(PluginMobilePage::getTitle($_REQUEST['url']), $_SERVER['PHP_SELF']);
PluginMobileSearch::show('Ticket');
PluginMobileHtml::footer();
?>
