<?php
define('GLPI_ROOT', '../../..');
include (GLPI_ROOT."/inc/includes.php");

if(!isset($_REQUEST['itemtype'])) $_REQUEST['itemtype'] = "";
if(!isset($_REQUEST['id'])) $_REQUEST['id'] = 1;

$title = PluginMobileItem::getTitle($_REQUEST['itemtype'], $_REQUEST['id']);

PluginMobileHtml::header($title, $_SERVER['PHP_SELF'], "none", "none", array(
                         'right_button' => PluginMobileItemTab::getIcon(),
                         'right_panel'  => PluginMobileItemTab::showPanelForItem(
                              $_REQUEST['itemtype'], $_REQUEST['id'])
                         ));
PluginMobileItem::showItem($_REQUEST['itemtype'], $_REQUEST['id']);
PluginMobileHtml::footer();
?>
