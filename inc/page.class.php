<?php

if (!defined('GLPI_ROOT')) {
   die("Sorry. You can't access directly to this file");
}

class PluginMobilePage extends Html {

   static function showPage($url) {
      echo $url;

   }

   static function getTitle($url) {
      return "page";
   }
}