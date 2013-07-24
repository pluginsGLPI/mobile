<?php

class PluginMobileMenu {

   static function showIcon() {
      echo "<a href='#menuPanel' id='menuIcon' data-icon='arrow-l' data-rel='popup' 
               data-role='button' data-theme='a' title='".__("Menu")."'><img src='../pics/logo2.png'
               title='".__('Home')."' /></a>";

   }

   static function showPanel() {
      global $CFG_GLPI;

      $m = self::getArray();

      echo "
      <div data-role='panel' id='menuPanel'>
         <div data-role='controlgroup' data-type='horizontal' style='margin:5px;'>
            <a href='central.php' data-role='button' data-icon='home'".
               "data-iconpos='notext' data-theme='d'>".__("Home")."</a>
            <a href='#' data-role='button' data-icon='star' ".
               "data-iconpos='notext' data-theme='d'>".__("Bookmark")."</a>
            <a href='preferences.php' data-role='button' data-icon='gear' ".
               "data-iconpos='notext' data-theme='d'>".__("Settings")."</a>
            <a href='../logout.php' data-role='button' data-icon='delete' ".
               "data-iconpos='notext' data-theme='d'>".__("Logout")."</a>
         </div>";
      self::showProfileSelecter($CFG_GLPI["root_doc"]."/plugins/mobile/front/central.php");
      echo"<div data-role='header'><center>".__("Menu")."</center></div>
         <div data-role='collapsible-set' data-content-theme='c'
               data-collapsed-icon='arrow-r' data-expanded-icon='arrow-d'> ";       



      // Get object-variables and build the navigation-elements
      $i = 1;
      foreach ($m as $part => $data) {
         if (isset($data['content']) && count($data['content'])) {
            echo "<div data-role='collapsible' data-inset='false'>";
            $link = "#";

            if (isset($data['default']) && !empty($data['default'])) {
               $link = $CFG_GLPI["root_doc"].$data['default'];
            }

            if (Toolbox::strlen($data['title']) > 14) {
               $data['title'] = Toolbox::substr($data['title'], 0, 14)."...";
            }
            echo "<h2>".$data['title']."</h2>";
            echo "<ul data-role='listview'>";



            // list menu item
            foreach ($data['content'] as $key => $val) {
               if (isset($val['page'])
                   && isset($val['title'])) {
                  echo "<li><a href='".$val['page']."'>".$val['title']."</a></li>\n";
               }
            }
            echo "</ul></div>";
            $i++;
         }
      }

            
      echo "</div><!-- /collapsible -->
      </div><!-- /panel -->
      
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
         echo "<form method='post' action='".$target."' style='float:left'>";
         echo "<select name='newprofile' id='newprofile' onChange='submit()'>";

         foreach ($_SESSION["glpiprofiles"] as $key => $val) {
            $selected = "";
            if (($_SESSION["glpiactiveprofile"]["id"] == $key)) {
               $selected = "selected";
            }
            echo "<option value='".$key."' $selected>".$val['name'].
                 "</option>";
         }
         echo "</select>";
         echo "<label for='newprofile'>".__("Profile")."</label>";
         Html::closeForm();
      }

      if (Session::isMultiEntitiesMode()) {
         $entity_name = $_SESSION['glpiactive_entity_shortname'];
         if (strpos($entity_name, "(".__("tree structure").")") !== "false") {
            $entity_name = str_replace("(".__("tree structure").")", "", $entity_name);
         }
         if (strlen($entity_name) > 15) {
            $entity_name = substr($entity_name, 0, 15)."...";
         }
         if (count($_SESSION['glpiactiveentities']) > 1) {
            $entity_name .= "<img src='".GLPI_ROOT."/pics/entity_all.png' />";
         }

         echo "<a href='#popupEntity' data-rel='popup' data-position-to='window' data-role='button'
               data-inline='true' data-icon='check' data-theme='a' data-transition='pop'>".
               $entity_name."</a>";
         echo "<div data-role='popup' id='popupMenu' data-theme='a'>";
         echo "<div data-role='popup' id='popupEntity' data-theme='a' class='ui-content'>";
         echo "<span class='b'>".__('Select the desired entity')."<br></span>";
         echo "<a data-ajax='false' data-role='button' href='".$target."?active_entity=all' ".
            "title=\"".__s('Show all')."\">".str_replace(" ","&nbsp;",__('Show all'))."</a>";
         echo "<hr /><br />";
         echo "<form data-ajax='false' method='get' action='".$target."'>";
         Dropdown::show("Entity", array('value'       => $_SESSION['glpiactive_entity'], 
                                        'comments'    => false,
                                        'name'        => 'active_entity',
                                        'entity'      => 0, 
                                        'entity_sons' => true, 
                                        'rand'        => "\" class=\"ui-button-corner-all")); 
         echo "<input type='checkbox' name='is_recursive' id='is_recursive' value='1' />";
         echo "<label for='is_recursive'>".__("Child entities")."</label>";
         echo "<input type='submit' value='"._sx('button','Post')."' data-theme='d'/>";
         Html::closeForm();
         echo "</div></div>";
      }
      echo "</fieldset>";
   }

   static function getArray() {
      global $CFG_GLPI;

      // INVENTORY
      $showallassets              = false;
      $m['inventory']['title'] = __('Assets');

      if (Session::haveRight("computer","r")) {
         $m['inventory']['content']['computer']['title']           = _n('Computer', 'Computers', 2);
         $m['inventory']['content']['computer']['page']            = 'search.php?itemtype=Computer';
      }


      if (Session::haveRight("monitor","r")) {
         $m['inventory']['content']['monitor']['title']           = _n('Monitor', 'Monitors', 2);
         $m['inventory']['content']['monitor']['page']            = 'search.php?itemtype=Monitor';
      }


      if (Session::haveRight("software","r")) {
         $m['inventory']['content']['software']['title']           = _n('Software', 'Software', 2);
         $m['inventory']['content']['software']['page']            = 'search.php?itemtype=Software';
      }


      if (Session::haveRight("networking","r")) {
         $m['inventory']['content']['networking']['title'] = _n('Network', 'Networks', 2);
         $m['inventory']['content']['networking']['page']  = 'search.php?itemtype=Networkequipment';

      }


      if (Session::haveRight("peripheral","r")) {
         $m['inventory']['content']['peripheral']['title']       = _n('Device', 'Devices', 2);
         $m['inventory']['content']['peripheral']['page']        = 'search.php?itemtype=Peripheral';
      }


      if (Session::haveRight("printer","r")) {
         $m['inventory']['content']['printer']['title']           = _n('Printer', 'Printers', 2);
         $m['inventory']['content']['printer']['page']            = 'search.php?itemtype=Printer';
      }


      if (Session::haveRight("cartridge","r")) {
         $m['inventory']['content']['cartridge']['title']     = _n('Cartridge', 'Cartridges', 2);
         $m['inventory']['content']['cartridge']['page']      = 'search.php?itemtype=Cartridgeitem';
      }


      if (Session::haveRight("consumable","r")) {
         $m['inventory']['content']['consumable']['title']           = _n('Consumable',
                                                                             'Consumables', 2);
         $m['inventory']['content']['consumable']['page']   = 'search.php?itemtype=Consumableitem';
      }


      if (Session::haveRight("phone","r")) {
         $m['inventory']['content']['phone']['title']           = _n('Phone', 'Phones', 2);
         $m['inventory']['content']['phone']['page']            = 'search.php?itemtype=Phone';
      }

      // ASSISTANCE
      $m['maintain']['title'] = __('Assistance');

      if (Session::haveRight("observe_ticket","1")
          || Session::haveRight("show_all_ticket","1")
          || Session::haveRight("create_ticket","1")) {

         $m['maintain']['content']['ticket']['title']           = _n('Ticket', 'Tickets', 2);
         $m['maintain']['content']['ticket']['page']            = 'search.php?itemtype=Ticket';
      }

      if (Session::haveRight("show_all_problem","1")
          || Session::haveRight("show_my_problem","1")) {
         $m['maintain']['content']['problem']['title']           = _n('Problem', 'Problems', 2);
         $m['maintain']['content']['problem']['page']            = 'search.php?itemtype=Problem';
      }

      if (Session::haveRight("show_all_change","1")
          || Session::haveRight("show_my_change","1")) {
         $m['maintain']['content']['change']['title']           = _n('Change', 'Changes', 2);
         $m['maintain']['content']['change']['page']            = 'search.php?itemtype=Change';
      }

      if (Session::haveRight("show_planning","1")
         || Session::haveRight("show_all_planning","1")
         || Session::haveRight("show_group_planning","1")) {
         $m['maintain']['content']['planning']['title']           = __('Planning');
         $m['maintain']['content']['planning']['page']            = 'planning.php';
      }

      if (Session::haveRight("statistic","1")) {
         $m['maintain']['content']['stat']['title']    = __('Statistics');
         $m['maintain']['content']['stat']['page']     = 'stat.php';
      }


      if (Session::haveRight("ticketrecurrent","r")) {
         $m['maintain']['content']['ticketrecurrent']['title']    = __('Recurrent tickets');
         $m['maintain']['content']['ticketrecurrent']['page']='search.php?itemtype=Ticketrecurrent';
      }


      // FINANCIAL
      $m['financial']['title'] = __('Management');

      if (Session::haveRight("budget", "r")) {
         $m['financial']['default'] = 'front/budget.php';

         $m['financial']['content']['budget']['title']           = _n('Budget', 'Budgets', 2);
         $m['financial']['content']['budget']['page']            = 'search.php?itemtype=Budget';
      }

      if (Session::haveRight("contact_enterprise", "r")) {
         $m['financial']['content']['supplier']['title']           = _n('Supplier',
                                                                           'Suppliers', 2);
         $m['financial']['content']['supplier']['page']            = 'search.php?itemtype=Supplier';


         $m['financial']['content']['contact']['title']           = _n('Contact', 'Contacts', 2);
         $m['financial']['content']['contact']['page']            = 'search.php?itemtype=Contact';
      }


      if (Session::haveRight("contract", "r")) {
         $m['financial']['content']['contract']['title']           = _n('Contract',
                                                                           'Contracts', 2);
         $m['financial']['content']['contract']['page']            = 'search.php?itemtype=Contract';
      }


      if (Session::haveRight("document", "r")) {
         $m['financial']['content']['document']['title']           = _n('Document',
                                                                           'Documents', 2);
         $m['financial']['content']['document']['page']            = 'search.php?itemtype=Document';
      }



      // UTILS
      $m['utils']['title'] = __('Tools');


      if (Session::haveRight('reminder_public', 'r')) {
         $m['utils']['content']['reminder']['title']        = _n('Reminder', 'Reminders', 2);
      } else {
         $m['utils']['content']['reminder']['title']        = _n('Personal reminder',
                                                                    'Personal reminders', 2);
      }
      $m['utils']['content']['reminder']['page']            = 'front/reminder.php';

      $m['utils']['content']['rssfeed']['title']           = _n('RSS feed', 'RSS feeds', 2);
      $m['utils']['content']['rssfeed']['page']            = 'front/rssfeed.php';

      if (Session::haveRight("knowbase","r") || Session::haveRight("faq","r")) {
         if (Session::haveRight("knowbase","r")) {
            $m['utils']['content']['knowbase']['title']        = __('Knowledge base');
         } else {
            $m['utils']['content']['knowbase']['title']        = __('FAQ');
         }

         $m['utils']['content']['knowbase']['page']            = 'front/knowbaseitem.php';
      }


      if (Session::haveRight("reservation_helpdesk","1")
          || Session::haveRight("reservation_central","r")) {
         $m['utils']['content']['reservation']['title']     = _n('Reservation',
                                                                           'Reservations', 2);
         $m['utils']['content']['reservation']['page']      = 'search.php?itemtype=Reservationitem';
      }


      if (Session::haveRight("reports","r")) {
         $m['utils']['content']['report']['title']    = _n('Report', 'Reports', 2);
         $m['utils']['content']['report']['page']     = 'search.php?itemtype=Report';
      }

      if (!isset($_SESSION['glpishowmigrationcleaner'])) {

         if (TableExists('glpi_networkportmigrations')
             && (countElementsInTable('glpi_networkportmigrations') > 0)) {
            $_SESSION['glpishowmigrationcleaner'] = true;
         } else {
            $_SESSION['glpishowmigrationcleaner'] = false;
         }
      }

      if ($_SESSION['glpishowmigrationcleaner']) {
         $m['utils']['content']['migration']['title']    = __('Migration cleaner');
         $m['utils']['content']['migration']['page']     = 'front/migration_cleaner.php';
      }

      // PLUGINS
      if (isset($PLUGIN_HOOKS["menu_entry"]) && count($PLUGIN_HOOKS["menu_entry"])) {
         $m['plugins']['title'] = __('Plugins');
         $plugins = array();

         foreach  ($PLUGIN_HOOKS["menu_entry"] as $plugin => $active) {
            if ($active) { // true or a string
               $plugins[$plugin] = Plugin::getInfo($plugin);
            }
         }

         if (count($plugins)) {
            $list = array();

            foreach ($plugins as $key => $val) {
               $list[$key] = $val["name"];
            }
            asort($list);

            foreach ($list as $key => $val) {
               $m['plugins']['content'][$key]['title'] = $val;
               $m['plugins']['content'][$key]['page']  = '/plugins/'.$key.'/';

               if (is_string($PLUGIN_HOOKS["menu_entry"][$key])) {
                  $m['plugins']['content'][$key]['page'] .= $PLUGIN_HOOKS["menu_entry"][$key];
               }

               // Set default link for plugins
               if (!isset($m['plugins']['default'])) {
                  $m['plugins']['default'] = $m['plugins']['content'][$key]['page'];
               }

               if (($sector == "plugins")
                   && ($item == $key)) {

                  if (isset($PLUGIN_HOOKS["submenu_entry"][$key])
                      && is_array($PLUGIN_HOOKS["submenu_entry"][$key])) {

                     foreach ($PLUGIN_HOOKS["submenu_entry"][$key] as $name => $link) {
                        // New complete option management
                        if ($name == "options") {
                           $m['plugins']['content'][$key]['options'] = $link;
                        } else { // Keep it for compatibility

                           if (is_array($link)) {
                              // Simple link option
                              if (isset($link[$option])) {
                                 $m['plugins']['content'][$key]['links'][$name]
                                                ='/plugins/'.$key.'/'.$link[$option];
                              }
                           } else {
                              $m['plugins']['content'][$key]['links'][$name]
                                                ='/plugins/'.$key.'/'.$link;
                           }
                        }
                     }
                  }
               }
            }
         }
      }


      /// ADMINISTRATION
      $m['admin']['title'] = __('Administration');

      if (Session::haveRight("user","r")) {
         $m['admin']['content']['user']['title']           = _n('User', 'Users', 2);
         $m['admin']['content']['user']['page']            = 'search.php?itemtype=User';
      }


      if (Session::haveRight("group","r")) {
         $m['admin']['content']['group']['title']           = _n('Group', 'Groups', 2);
         $m['admin']['content']['group']['page']            = 'search.php?itemtype=Group';
      }


      if (Session::haveRight("entity","r")) {
         $m['admin']['content']['entity']['title']           = _n('Entity', 'Entities', 2);
         $m['admin']['content']['entity']['page']            = 'search.php?itemtype=Entity';
      }


      if (Session::haveRight("rule_ldap","r")
          || Session::haveRight("rule_ocs","r")
          || Session::haveRight("entity_rule_ticket","r")
          || Session::haveRight("rule_softwarecategories","r")
          || Session::haveRight("rule_mailcollector","r")) {

         $m['admin']['content']['rule']['title']    = _n('Rule', 'Rules', 2);
         $m['admin']['content']['rule']['page']     = 'front/rule.php';
      }

      if (Session::haveRight("transfer","r" )
          && Session::isMultiEntitiesMode()) {
         $m['admin']['content']['rule']['options']['transfer']['title'] = __('Transfer');
         $m['admin']['content']['rule']['options']['transfer']['links']['search']
                                                                           = "front/transfer.php";
      }


      if (Session::haveRight("rule_dictionnary_dropdown","r")
          || Session::haveRight("rule_dictionnary_software","r")
          || Session::haveRight("rule_dictionnary_printer","r")) {

         $m['admin']['content']['dictionnary']['title']    = __('Dictionaries');
         $m['admin']['content']['dictionnary']['page']     = 'front/dictionnary.php';

      }


      if (Session::haveRight("profile","r")) {
         $m['admin']['content']['profile']['title']           = _n('Profile', 'Profiles', 2);
         $m['admin']['content']['profile']['page']            = 'search.php?itemtype=Profile';
      }

      if (Session::haveRight("backup","w")) {
         $m['admin']['content']['backup']['title']    = __('Maintenance');
         $m['admin']['content']['backup']['page']     = 'front/backup.php';
      }


      if (Session::haveRight("logs","r")) {
         $m['admin']['content']['log']['title']    = _n('Log', 'Logs', 2);
         $m['admin']['content']['log']['page']     = 'front/event.php';
      }



      /// CONFIG
      $config    = array();
      $addconfig = array();
      $m['config']['title'] = __('Setup');

      if (Session::haveRight("dropdown","r")
          || Session::haveRight("entity_dropdown","r")
          || Session::haveRight("internet","r")) {
         $m['config']['content']['dropdowns']['title']    = _n('Dropdown', 'Dropdowns', 2);
         $m['config']['content']['dropdowns']['page']     = 'front/dropdown.php';
      }

      if (Session::haveRight("device","w")) {
         $m['config']['content']['device']['title'] = _n('Component', 'Components', 2);
         $m['config']['content']['device']['page']  = 'front/device.php';
      }


      if (($CFG_GLPI['use_mailing'] && Session::haveRight("notification","r"))
          || Session::haveRight("config","w")) {
         $m['config']['content']['mailing']['title'] = _n('Notification', 'Notifications', 2);
         $m['config']['content']['mailing']['page']  = 'front/setup.notification.php';
      }


      if (Session::haveRight("sla","r")) {
         $m['config']['content']['sla']['title']           = _n('SLA', 'SLA', 2);
         $m['config']['content']['sla']['page']            = 'search.php?itemtype=Sla';
      }

      if (Session::haveRight("config","w")) {

         //TRANS: menu title for "General setup""
         $m['config']['content']['config']['title']   = _x('setup', 'General');
         $m['config']['content']['config']['page']    = 'front/config.form.php';


         $m['config']['content']['control']['title']  = _n('Check', 'Checks', 2);
         $m['config']['content']['control']['page']   = 'front/control.php';


         $m['config']['content']['control']['options']['FieldUnicity']['title']
                                                         = __('Fields unicity');
         $m['config']['content']['control']['options']['FieldUnicity']['page']
                                                         = 'search.php?itemtype=Fieldunicity';


         $m['config']['content']['crontask']['title']           = _n('Automatic action',
                                                                        'Automatic actions', 2);
         $m['config']['content']['crontask']['page']            = 'search.php?itemtype=Crontask';

         $m['config']['content']['mailing']['options']['config']['title'] = __('Email');
         $m['config']['content']['mailing']['options']['config']['page']
                        = 'front/notificationmailsetting.form.php';


         $m['config']['content']['extauth']['title'] = __('Authentication');
         $m['config']['content']['extauth']['page']  = 'front/setup.auth.php';




         $m['config']['content']['mailcollector']['title'] = _n('Receiver', 'Receivers', 2);
         $m['config']['content']['mailcollector']['page']  = 'search.php?itemtype=Mailcollector';

      }

      if (Session::haveRight("link","r")) {
         $m['config']['content']['link']['title']           = _n('External link',
                                                                    'External links', 2);
         $m['config']['content']['link']['page']            = 'search.php?itemtype=Link';

      }


      if (Session::haveRight("config","w")) {
         $m['config']['content']['plugins']['title'] = __('Plugins');
         $m['config']['content']['plugins']['page']  = 'search.php?itemtype=Plugin';
      }





      return $m;
   }
}