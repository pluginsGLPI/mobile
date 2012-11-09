<?php

define('GLPI_ROOT', '../..'); 
include (GLPI_ROOT . "/inc/includes.php"); 


Session::destroy();

Html::redirect(GLPI_ROOT . "/plugins/mobile/index.php");

$common->displayFooter();
?>
