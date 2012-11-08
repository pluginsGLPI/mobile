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
         <link rel='stylesheet' href='".GLPI_ROOT."/plugins/mobile/mobile.css' /> 
         <script src='".GLPI_ROOT."/lib/jquery/jquery-1.7.2.min.js'></script> 
         <script src='".GLPI_ROOT.
            "/plugins/mobile/lib/jquery.mobile-1.2.0/jquery.mobile-1.2.0.min.js'></script> 

      </head>
      <body>";

      echo "<div data-role='page' data-theme='a'>
         <a data-role='button' data-inline='true' data-icon='glpi-mobile-home' title='".__('Home').
            "' href='central.php'>
         </a>
         <div data-role='header' data-position='inline'>
            <a href='#menuPanel' data-icon='grid' title='".__("Menu").
               "' data-rel='popup' data-transition='slide' data-position-to='window' ".
               "data-role='button'>".__("Menu")."</a>";
      self::showMenu();
      echo "   <h1>$title</h1>
            <a data-icon='back'  data-back='true' title='".__('Back')."'>".__('Back')."</a>
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

   static function showMenu() {
      echo "
      <div data-role='popup' id='menuPanel' data-corners='false' data-theme='none' 
            data-shadow='false' data-tolerance='0,0'>
         <button data-theme='a' data-icon='back' data-mini='true'>Back</button>
         <button data-theme='a' data-icon='grid' data-mini='true'>Menu</button>
         <button data-theme='a' data-icon='search' data-mini='true'>Search</button>
      </div>
      ";
   }

   static function extractMenu($html) {

   }
}