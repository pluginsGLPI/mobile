<?php

define('GLPI_ROOT', '../..'); 
include (GLPI_ROOT . "/inc/includes.php"); 


Session::destroy();

header("location: ".GLPI_ROOT . "/plugins/mobile/index.php");

?>
