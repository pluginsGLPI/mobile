<?php

if (!defined('GLPI_ROOT')) {
   die("Sorry. You can't access directly to this file");
}

class PluginMobileHtml extends Html {

   /**
    * Print a nice HTML head for every page
    *
    * @param $title     title of the page
    * @param $url       not used anymore (default '')
    * @param $sector    sector in which the page displayed is (default 'none')
    * @param $item      item corresponding to the page displayed (default 'none')
    * @param $option    option corresponding to the page displayed (default '')
   **/
   static function header($title, $url='', $sector="none", $item="none", $option="") {
      ob_start();
      parent::header($title, $url, $sector, $item, $option);
      $html = ob_get_contents();
      ob_end_clean();

      $menu = self::extractMenu($html);

      echo "<!DOCTYPE html>
      <html>
      <head>

         <title>$title</title>
         <meta charset='utf-8' />
         <meta name='viewport' content='width=device-width, initial-scale=1'>
         <link rel='stylesheet' href='".GLPI_ROOT.
            "/plugins/mobile/themes/default/glpi-mobile.min.css' />
         <link rel='stylesheet' href='".GLPI_ROOT.
            "/plugins/mobile/lib/jquery.mobile-1.2.0/jquery.mobile.structure-1.2.0.min.css' /> 
         <script src='".GLPI_ROOT."/lib/jquery/jquery-1.7.2.min.js'></script> 
         <script src='".GLPI_ROOT.
            "/plugins/mobile/lib/jquery.mobile-1.2.0/jquery.mobile-1.2.0.min.js'></script> 

      </head>
      <body>";

      echo "<div data-role='page' data-theme='a'>
         <a data-icon='glpi-mobile-home' title='".__('Home').
            "' data-iconpos='notext' href='central.php'>
         </a>
         <div data-role='header' data-position='inline'>
            <a data-icon='grid' title='".__("Menu")."'>".__("Menu")."</a>
            <h1>$title</h1>
            <a data-icon='back' title='".__('Back')."'>".__('Back')."</a>
         </div>
         <div data-role='content' data-theme='a'>";
   
   }

   static function footer($keepDB=false) {
      ob_start();
      parent::footer($keepDB);
      $html = ob_get_contents();
      ob_end_clean();



      echo "</div></div></body></html>";
   }

   static function extractMenu($html) {

   }
}