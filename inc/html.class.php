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
         <script src='".GLPI_ROOT."/lib/jquery/jquery-1.7.2.min.js'></script>";
         self::echoJqueryCommonScripts();
      echo "
         <script src='".GLPI_ROOT.
            "/plugins/mobile/lib/jquery.mobile-1.2.0/jquery.mobile-1.2.0.min.js'></script>";
      echo "</head>
      <body>";

      echo "
      <div data-role='page' data-theme='a'>

         <a href='central.php' data-role='button' data-inline='true' data-icon='glpi-mobile-home' 
            title='".__('Home')."' ></a>

         <div data-role='header' data-position='inline'>";
            echo "<a href='#menuPanel' data-icon='grid' data-rel='popup' data-role='button' 
               data-inline='true' title='".__("Menu")."'>".__("Menu")."</a>";

            self::showMenu($menu);

            
            echo "<h1>$title</h1>

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

   static function extractMenu($html) {
      //extract menu; search for <div id='c_menu'>
      preg_match("/<ul id='menu.*>(.*)<\/div>/Uism", $html, $matches);
      $menu =  $matches[1];

      //remove unused attributes
      $menu = preg_replace(array(
         "/onmouseover=\".*\"/U",
         "/class=['|\"].*['|\"]/U",
         "/accesskey='.*'/U"
      ), "", $menu);


      //replace 1st levels
      $menu = preg_replace("/<\/li><li id='menu/Uism", "<li id='menu", $menu);
      $menu = preg_replace("/<li id='menu.*'.*>.*<a href.*>(.*)<\/a>.*(<ul.*\/ul>)/Uism", 
         "<div data-role='collapsible' data-inset='false'>\n<h2>$1</h2>\n$2</div>", $menu);

      //add listview attr to ul
      $menu = str_replace("<ul", "<ul data-role='listview'", $menu);

      //replace href for get page in plugin mobile
      $menu = str_replace("href='", "href='page.php?url=", $menu);

      return $menu;
   }

   static function showMenu($menu) {
      echo "
      <div data-role='popup' id='menuPanel' data-theme='none'>
         <div data-role='collapsible-set' data-content-theme='c'
               data-collapsed-icon='arrow-r' data-expanded-icon='arrow-d' 
               style='margin:0; width:250px;'>
            $menu
            </div><!-- /collapsible -->
         </div><!-- /collapsible set -->
      </div><!-- /popup -->
      
      ";
   }

   static function echoJqueryCommonScripts() {

      $JS = <<<JAVASCRIPT
      $(document).bind("mobileinit", function(){
         
         $("#menuPanel").on({popupbeforeposition: function() {
            var h = $(window).height();
            console.log("test");
         
            $("#menuPanel").css("height", h);
         }});

      });
JAVASCRIPT;

      echo "<script type='text/javascript'>$JS</script>";
   }
}