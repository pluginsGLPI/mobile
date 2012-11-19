<?php

//Load php libraries
require_once GLPI_ROOT."/plugins/mobile/lib/QueryPath-2.1.2-minimal/QueryPath.php";

// Init the hooks of the plugins -Needed
function plugin_init_mobile() {
   global $PLUGIN_HOOKS, $LANG;
   
   $PLUGIN_HOOKS['csrf_compliant']['mobile'] = true;
   
   $menu_entry = 'front/central.php';
   $PLUGIN_HOOKS['menu_entry']['mobile']     = $menu_entry;

   //if mobile navigator detected, redirect to plugin mobile
   $plug = new Plugin;
   if ($plug->isInstalled('mobile') && $plug->isActivated('mobile')) {
      if (PluginMobileCommon::isNavigatorMobile()) {
         PluginMobileCommon::redirectMobile();
      }
   }
}


// Get the name and the version of the plugin - Needed
function plugin_version_mobile() {

   return array('name'           => "Mobile",
                'version'        => "2.0",
                'author'         => "<a href='mailto:adelaunay@teclib.com'>Alexandre DELAUNAY</a>",
                'homepage'       => "http://www.teclib.com/",
                'minGlpiVersion' => "0.84");
}


// Optional : check prerequisites before install : may print errors or add to message after redirect
function plugin_mobile_check_prerequisites() {

   if (version_compare(GLPI_VERSION,'0.84','>=')) {
      return true;
   } else {
      echo "GLPI version not compatible need 0.84 min";
   }
}


// Check configuration process for plugin : need to return true if succeeded
// Can display a message only if failure and $verbose is true
function plugin_mobile_check_config($verbose=false) {
   global $LANG;

   if (true) { // Your configuration check
      return true;
   }
   if ($verbose) {
      echo $LANG['plugins'][2];
   }
   return false;
}


?>
