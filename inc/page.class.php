<?php

if (!defined('GLPI_ROOT')) {
   die("Sorry. You can't access directly to this file");
}

class PluginMobilePage extends Html {

   static function show($request_url) {
      global $CFG_GLPI;

      $mobile_url = 'http'. (isset($_SERVER['HTTPS']) ? 's' : null) .'://'.
         $_SERVER["SERVER_NAME"].$_SERVER["SCRIPT_NAME"];

      //remove base from url
      $glpi_url = substr($mobile_url, 0, strpos($mobile_url, "plugins/mobile"));

      echo $url =  $glpi_url.$request_url;


   }

   static function getTitle($url) {
      return "page";
   }
}