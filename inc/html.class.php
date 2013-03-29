<?php

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
      //verify profile
      PluginMobileCommon::checkLogin();
      
      //include header (css, js)
      self::includeHeader($title, $url, $sector, $item, $option);

      $screen = (PluginMobileCommon::largeScreen() ? "mobileLargeScreen" : "mobileMiniScreen");

      //csontruct base header html
      //echo "<a href='central.php'><img src='../pics/logo.png' title='".__('Home')."' /></a>";"

      PluginMobileMenu::showPanel();

      echo "<div data-role='header'>";
            
            PluginMobileMenu::showIcon();

            echo "<h1>$title</h1>";

            //echo "<a data-icon='back' data-back='true' title='".__('Back')."'>".__('Back')."</a>";

         echo "</div>
         <div data-role='content' data-theme='a' class='$screen'>";
   
   }

   static function includeHeader($title = '', $url='', $sector="none", $item="none", $option="") {
      echo "<!DOCTYPE html><html><head>";

      echo "<title>$title</title>";
      echo "<meta charset='utf-8' />";
      echo "<meta name='viewport' content='width=device-width, initial-scale=1'>";
      
      // FAV & APPLE DEVICE ICON
      echo "<link rel='apple-touch-icon' type='image/png' href='".
         GLPI_ROOT."/plugins/mobile/pics/apple-touch-icon.png' />";
      echo "<link rel='icon' type='image/png' href='".
         GLPI_ROOT."/plugins/mobile/pics/favicon.png' />";

      // GLPI MOBILE THEME CSS
      echo "<link rel='stylesheet' href='".GLPI_ROOT.
            "/plugins/mobile/themes/default/glpi-mobile.min.css' />";

      // COMMON JQUERY MOBILE CSS
      echo "<link rel='stylesheet' href='".GLPI_ROOT.
            "/plugins/mobile/lib/jquery.mobile/jquery.mobile.structure-1.3.0.css' />";
         
      //GENERAL CSS
      echo "<link rel='stylesheet' href='".GLPI_ROOT."/plugins/mobile/mobile.css' />";

      //JQUERY JS
      echo "<script src='".GLPI_ROOT."/plugins/mobile/lib/jquery-1.8.3.min.js'></script>";

      // COMMON JS
      self::displayJqueryCommonScripts();

      //JQUERY MOBILE JS
      echo "<script src='".GLPI_ROOT.
            "/plugins/mobile/lib/jquery.mobile/jquery.mobile-1.3.0.min.js'></script>";

      //EXTJS loading (to be removed in 0.85)
      echo "<script type=\"text/javascript\" src='".
             GLPI_ROOT."/lib/extjs/adapter/ext/ext-base.js'></script>\n";
      if ($_SESSION['glpi_use_mode'] == Session::DEBUG_MODE) {
         echo "<script type='text/javascript' src='".
                GLPI_ROOT."/lib/extjs/ext-all-debug.js'></script>\n";
      } else {
         echo "<script type='text/javascript' src='".
                GLPI_ROOT."/lib/extjs/ext-all.js'></script>\n";
      }

      echo "</head><body>";
      echo "<div data-role='page' data-theme='a' class='$sector $item'>";
   }

   static function footer($keepDB=false) {
      /*ob_start();
      parent::footer($keepDB);
      $html = ob_get_contents();
      ob_end_clean();*/

      echo "</div></div></body></html>";
   }


   static function displayJqueryCommonScripts() {
      
      $JS = <<<JAVASCRIPT
      //jquery mobile init
      $(document).bind("mobileinit", function(){
         $("#menuPanel").on({popupbeforeposition: function() {
            var h = $(window).height();
         
            $("#menuPanel").css("height", h);
         }});
      });

      //jquery init
      $(document).ready(function() {

      });
JAVASCRIPT;

      echo "<script type='text/javascript'>$JS</script>";
   }

   


   static function showLoginBox($error = '', $REDIRECT = "") {

      $_SESSION["glpicookietest"] = 'testcookie';

      // For compatibility reason
      if (isset($_GET["noCAS"])) {
         $_GET["noAUTO"] = $_GET["noCAS"];
      }

      Auth::checkAlternateAuthSystems(true, isset($_GET["redirect"])?$_GET["redirect"]:"");

      echo "<a href='#'><img src='".GLPI_ROOT."/plugins/mobile/pics/logo.png' alt='Logo' /></a>";
      echo "<div data-role='header'  data-position='inline'>";
      echo "<h1>".__('Authentication')."</h1>";
      echo "</div>";

      echo "<div data-role='content' class='login-box'>";
      if (!empty($error)) {
         echo "<div class='center b'>";
         echo "<noscript><p>";
         _e('You must activate the JavaScript function of your navigator');
         echo "</p></noscript>";
         echo $error;
         echo "<br><br>";
      }

      echo "<form action='".GLPI_ROOT."/plugins/mobile/login.php' method='post'>";
      echo "<fieldset>";

      echo "<div data-role='fieldcontain'>";
      echo "<label for='login_name'>".__('Login').":</label>";
      echo "<input type='text' name='login_name' id='login_name' value=''  />";
      echo "</div>";

      echo "<div data-role='fieldcontain'>";
      echo "<label for='login_password'>".__('Password').":</label>";
      echo "<input type='password' name='login_password' id='login_password' value='' />";
      echo "</div>";

      echo "<button type='submit' data-icon='check' data-theme='d'>".__('Post')."</button>";

      echo "</fieldset>";

      if (isset($_GET["noAUTO"])) {
         echo "<input type='hidden' name='noAUTO' value='1'/>";
      }

      // redirect to ticket
      if (isset($_GET["redirect"])) {
         Toolbox::manageRedirect($_GET["redirect"]);
         echo '<input type="hidden" name="redirect" value="'.$_GET['redirect'].'">';
      }

      Html::closeForm();

      echo "</div>";

      echo "<script type='text/javascript' >\n";
      echo "document.getElementById('login_name').focus();";
      echo "</script>";
   }


   
}