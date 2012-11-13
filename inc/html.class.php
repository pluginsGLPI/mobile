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
      ob_start();
      parent::header($title, $url, $sector, $item, $option);
      $html = ob_get_contents();
      ob_end_clean();

      $menu = self::extractMenu($html);
      
      self::includeHeader($title);

      echo "<img src='../pics/logo.png' title='".__('Home')."' />

         <div data-role='header'>";
            
            self::showMenu($menu);

            echo "<h1>$title</h1>";

            //echo "<a data-icon='back' data-back='true' title='".__('Back')."'>".__('Back')."</a>";

         echo "</div>
         <div data-role='content' data-theme='a'>";
   
   }

   static function includeHeader($title = '') {
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
      // echo "<script src='".GLPI_ROOT.
      //       "/plugins/mobile/lib/jquery.mobile-1.2.0/jquery.mobile-tables.js'></script>";
      echo "<script src='".GLPI_ROOT.
            "/plugins/mobile/lib/jquery.mobile-1.2.0/jquery.mobile-1.2.0.min.js'></script>";
      echo "</head>
      <body><div data-role='page' data-theme='a'>";
   }

   static function footer($keepDB=false) {
      ob_start();
      parent::footer($keepDB);
      $html = ob_get_contents();
      ob_end_clean();



      echo "</div></div></body></html>";
   }

   static function extractMenu($html) {
      global $CFG_GLPI;

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

      //remove base glpi from url
      $menu = str_replace($CFG_GLPI['root_doc']."/", "", $menu);

      return $menu;
   }

   static function showMenu($menu) {
      global $CFG_GLPI;

      echo "<a href='#menuPanel' data-icon='grid' data-rel='popup' data-role='button' 
                title='".__("Menu")."'>&nbsp;</a>";

      echo "
      <div data-role='popup' id='menuPanel'>
         <div data-role='controlgroup' data-type='horizontal' style='margin:5px;'>
            <a href='central.php' data-role='button' data-icon='home'".
               "data-iconpos='notext' data-theme='a'>".__("Home")."</a>
            <a href='#' data-role='button' data-icon='star' ".
               "data-iconpos='notext'>".__("Bookmark")."</a>
            <a href='preferences.php' data-role='button' data-icon='gear' ".
               "data-iconpos='notext'>".__("Settings")."</a>
            <a href='../logout.php' data-role='button' data-icon='delete' ".
               "data-iconpos='notext'>".__("Logout")."</a>
         </div>";
      self::showProfileSelecter($CFG_GLPI["root_doc"]."/front/helpdesk.public.php");
      echo"<div data-role='header'><center>".__("Menu")."</center></div>
         <div data-role='collapsible-set' data-content-theme='c'
               data-collapsed-icon='arrow-r' data-expanded-icon='arrow-d' 
               style='margin:0; width:250px;'>        
            $menu
            </div><!-- /collapsible -->
         </div><!-- /collapsible set -->
      </div><!-- /popup -->
      
      ";
   }

   /**
    * Print the form used to select profile if several are available
    *
    * @param $target target of the form
    *
    * @return nothing
   **/
   static function showProfileSelecter($target) {
      global $CFG_GLPI;

      echo"<div data-role='header'><center>".__("Profile")."</center></div>";
      echo "<fieldset data-role='controlgroup' data-type='horizontal' 
         data-mini='true' style='margin-left:5px'>";

      if (count($_SESSION["glpiprofiles"])>1) {
         echo "<form name='form' method='post' action='".$target."' style='float:left'>";
         echo "<select name='newprofile' id='newprofile' onChange='submit()'>";

         foreach ($_SESSION["glpiprofiles"] as $key => $val) {
            echo "<option value='".$key."' ".
                   (($_SESSION["glpiactiveprofile"]["id"] == $key) ?"selected":"").">".$val['name'].
                 "</option>";
         }
         echo "</select>";
         echo "<label for='newprofile'>".__("Profile")."</label>";
         Html::closeForm();
      }

      if (Session::isMultiEntitiesMode()) {
         /*ob_start();
         include $CFG_GLPI['root_doc']."/ajax/entitytree.php";
         $html = ob_get_contents();
         ob_end_clean();

         echo $html;*/

         echo "<a href='#' data-role='button' data-icon='check' data-iconpos='notext'>".
            __("Entity")."</a>";
      }
      echo "</fieldset>";
   }


   static function echoJqueryCommonScripts() {

      $JS = <<<JAVASCRIPT
      $(document).bind("mobileinit", function(){
         
         $("#menuPanel").on({popupbeforeposition: function() {
            var h = $(window).height();
            console.log("test");
         
            $("#menuPanel").css("height", h);

            $("#search-table").scrollview();
         }});

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

      echo "<button type='submit' data-icon='check'>".__('Post')."</button>";

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