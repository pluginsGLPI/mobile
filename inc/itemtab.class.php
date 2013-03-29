<?php

class PluginMobileItemTab {

   static function getIcon() {
      return "<a href='#tabPanel' data-role='button' data-icon='bars'
                data-theme='a'>".__("Tabs")."</a>";
   }

   static function showPanelForItem($itemtype, $id) {
      $item = new $itemtype;
      $item->getFromDB($id);

      $tabs = $item->defineTabs();

      $out = "<div data-role='panel' id='tabPanel' data-position='right'>";
      $out.= "<ul data-role='listview'>";
      foreach ($tabs as $tabs_id => $tab_name) {
         $out.= "   <li><a href='#$tabs_id'>$tab_name</a></li>";
      }
      $out.= "</ul>";
      $out.= "</div>";

      return $out;
   }
}

?>