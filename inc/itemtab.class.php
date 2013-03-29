<?php

class PluginMobileItemTab {

   static function getTitle($itemtype, $id, $tabs_id) {
      $item = new $itemtype;
      $item->getFromDB($id);

      $tabs = $item->defineTabs();

      if ($tabs_id === "default") {
         $name = array_shift($tabs);
      } else  {
         $name = $tabs[$tabs_id];
      }

      return $name;
   }

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
         $params_str = "tabs_id=".urlencode($tabs_id)."&itemtype=$itemtype&id=$id";
         $out.= "   <li><a href='itemtab.php?$params_str'>$tab_name</a></li>";
      }
      $out.= "</ul>";
      $out.= "</div>";

      return $out;
   }


   static function show($itemtype, $id, $tabs_id) {
      $item = new $itemtype;
      $item->getFromDB($id);

      ob_start();
      CommonGLPI::displayStandardTab($item, $tabs_id);
      $html = utf8_decode(ob_get_contents());
      ob_end_clean();

      //init querypath lib
      $top = "";
      $options = array('ignore_parser_warnings' => TRUE);
      @$qp = qp($html, NULL, $options);

      //remove tooltips
      $qp->top($top)->find("span.x-hidden")->remove();
      $qp->top($top)->find("a img")->parent()->remove();
      $qp->top($top)->find("img[title=".__('Add')."]")->remove();

      //init responsive table 
      $table_responsive = ".tab_cadre_fixe";
      $qp->top($table_responsive)
         ->Attr('id', "mobileTable")
         ->Attr("data-role", "table")
         ->Attr("data-mode", "columntoggle")
         //->Attr("data-mode", "reflow")
         //->AddClass("table-stroke")
         ->AddClass("table-stripe");

      /*$qp->top($table_responsive)->find("tr:first-child th")->parent()->remove();

      //add priority to th (from 1 to 6) for responive mode
      $i = 0;
      $nb_th = count($qp->top($table_responsive)->find("tr th"));
      foreach ($qp->top($table_responsive)->find("tr th") as $th) {
         $i++;
         if ($i <= 1) {
            $th->Attr('data-priority', 'critical');
            continue;
         }
         //if (strpos($th->attr("class"), "th-active")) continue;
         $priority = round($i * 6 / $nb_th);
         $th->Attr('data-priority', $priority);
         
      }  

      //place th in thead
      $qp->top($table_responsive)->append("<thead class='ui-bar-b'></thead>");
      $thead = $qp->top($table_responsive)->find("tr:first-child")->html();
      $qp->top($table_responsive)->find("tr:first-child")->remove();
      $tbody = "";
      foreach ($qp->top($table_responsive)->find("tr") as $tr) {
         $tbody.= $tr->html();
      }
      $qp->top($table_responsive)->find("tr")->remove();
      $qp->top($table_responsive)->find("th")->remove();
      $qp->top($table_responsive)->find("thead")->append($thead);
      $qp->top($table_responsive)->append($tbody);*/

      echo $qp->top($top)->html();
   }
}

?>