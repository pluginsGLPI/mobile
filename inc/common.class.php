<?php
if (!defined('GLPI_ROOT')) {
   die("Sorry. You can't access directly to this file");
}

class PluginMobileCommon {

   static function checkLogin() {

      //check glpi login && redirect to plugin mobile
      if (!isset ($_SESSION["glpiactiveprofile"])
      || $_SESSION["glpiactiveprofile"]["interface"] != "central") {
         // Gestion timeout session
         if (!Session::getLoginUserID()) {

            if (strpos($_SERVER['PHP_SELF'], 'index.php') === false
            && strpos($_SERVER['PHP_SELF'], 'login.php') === false
            && strpos($_SERVER['PHP_SELF'], 'logout.php') === false
            && strpos($_SERVER['PHP_SELF'], 'recoverpassword.form.php') === false
            ) {
               Html::Redirect(GLPI_ROOT . "/plugins/mobile/index.php");
               exit ();
            }
         }
      }
   }

   static function checkParams() {
      if (!isset($_SESSION['plugin_mobile']) && isset($_SESSION['glpiID'])) {
         $navigator = self::navigatorDetect();
      }
   }


   static function navigatorDetect() {
      if (isset($_SERVER['HTTP_USER_AGENT'])) {

         if (stripos($_SERVER['HTTP_USER_AGENT'], 'iPad')) return "iPad";
         elseif (stripos($_SERVER['HTTP_USER_AGENT'], 'iPhone')) return "iPhone";
         elseif (stripos($_SERVER['HTTP_USER_AGENT'], 'Android')) return "Android";
         elseif (stripos($_SERVER['HTTP_USER_AGENT'], 'Firefox')) return "Desktop";
         elseif (stripos($_SERVER['HTTP_USER_AGENT'], 'Chrome')) return "Desktop";
         else return "default";
      }
      return;
   }


   static function getOsVersion() {
      $version = "";
      if (isset($_SERVER['HTTP_USER_AGENT'])) {
         $agent = $_SERVER['HTTP_USER_AGENT'];

         if(stripos($agent,'Android') !== false ) {
            $result = explode(' ',stristr($agent,'Android'));
            if(isset($result[1])) $version = substr($result[1], 0, -1);

         } elseif(stripos($agent,'iPhone') !== false ) {
            $result = explode('/',stristr($agent,'Version'));
            if(isset($result[1])) {
               $aversion = explode(' ',$result[1]);
               $version = $aversion[0];
            }

         } elseif( stripos($agent,'iPad') !== false ) {
            $result = explode('/',stristr($agent,'Version'));
            if(isset($aresult[1])) {
               $aversion = explode(' ',$result[1]);
               $version = $aversion[0];
            }
         }
      }

      return $version;
   }

   static function isAndroidTablet() {
      if (isset($_SERVER['HTTP_USER_AGENT'])) {
         if(stripos($_SERVER['HTTP_USER_AGENT'],'mobile') === false) return true;
      }
      return false;
   }

   static function isNavigatorMobile() {
      return in_array(self::navigatorDetect(), array(
         'iPhone',
         'iPad',
         'Android',
         'Fennec'
      ));
   }

   static function redirectMobile() {
      if (!isset($_SESSION['glpiactiveprofile'])
         && strpos($_SERVER['SCRIPT_FILENAME'], 'plugins/mobile') === false
         && strpos($_SERVER['SCRIPT_FILENAME'], 'login.php') === false) {
         //check if alternate auth is available
         Auth::checkAlternateAuthSystems(true, "plugin_mobile_1");

         //else redirect login page
         header("location: ".GLPI_ROOT."/plugins/mobile/index.php");
      }
   }

   static function largeScreen() {
      $navigator = self::navigatorDetect();
      if(in_array($navigator, array('iPad', 'Desktop'))) return true;
      elseif ($navigator == 'Android' && self::isAndroidTablet()) return true;
      else return false;
   }
}