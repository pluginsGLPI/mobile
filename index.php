<?php
define('GLPI_ROOT', '../..'); 
include (GLPI_ROOT . "/inc/includes.php"); 

PluginMobileHtml::includeHeader(__('Authentication'));
PluginMobileHtml::showLoginBox();
PluginMobileHtml::footer();
