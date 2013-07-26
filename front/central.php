<?php

define('GLPI_ROOT', '../../..');
include (GLPI_ROOT."/inc/includes.php");

// Change profile system
if (isset($_REQUEST['newprofile'])) {
   if (isset($_SESSION["glpiprofiles"][$_REQUEST['newprofile']])) {
      Session::changeProfile($_REQUEST['newprofile']);
      Html::redirect($_SERVER['PHP_SELF']);
   } else {
      Html::redirect(preg_replace("/entities_id=.*/","",$_SERVER['HTTP_REFERER']));
   }
}

// Manage entity change
if (isset($_REQUEST["active_entity"])) {
   if (!isset($_REQUEST["is_recursive"])) {
      $_REQUEST["is_recursive"] = 0;
   }
   if (Session::changeActiveEntities($_REQUEST["active_entity"],$_REQUEST["is_recursive"])) {
      if ($_REQUEST["active_entity"] == $_SESSION["glpiactive_entity"]) {
         Html::redirect(preg_replace("/entities_id.*/","",$_SERVER['HTTP_REFERER']));
      }
   }
}

// Redirect management
if (isset($_GET["redirect"])) {
   Toolbox::manageRedirect($_GET["redirect"]);
}




PluginMobileHtml::header(Central::getTypeName(1), $_SERVER['PHP_SELF']);
PluginMobileCentral::showForMobile();
PluginMobileHtml::footer();
?>
