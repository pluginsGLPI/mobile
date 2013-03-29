<?php

class PluginMobileSearch extends Search {

   static function show($itemtype) {
      self::manageGetValues($itemtype);
      //self::showGenericSearch($itemtype, $_GET);
      self::showList($itemtype, $_GET);
   }


   static function showList($itemtype, array $params) {
      ob_start();
      parent::showList($itemtype, $params);
      $html = utf8_decode(ob_get_contents());
      ob_end_clean();


      //init querypath lib
      $top = ".tab_cadrehov";
      $options = array('ignore_parser_warnings' => TRUE);
      @$qp = qp($html, NULL, $options);
      
      //remove unused tags
      $qp->remove('input[type=checkbox]');
      $qp->remove('script');

      //get pager content
      $pager = trim(Html::clean($qp->find(".tab_cadre_pager td:last-child")->html()));
      $tmp = explode(" ", $pager);
      $numrows = array_pop($tmp);

      //clean table items
      $qp->top($top)->find("tr")
         ->removeClass("tab_bg_1")
         ->removeClass("tab_bg_2");
      $qp->find("td")
         ->removeAttr("valign")
         ->removeAttr("width")
         ->removeClass("tab_bg_2_2")
         /*->AddClass("ui-body-a")*/;
      $qp->top($top)->find("th:first-child:empty")->remove();
      $qp->top($top)->find("td:first-child:empty")->remove();

      //add priority to th (from 1 to 6)
      $i = 0;
      $nb_th = count($qp->top($top)->find("tr:first-child th"));
      foreach ($qp->top($top)->find("tr:first-child th") as $th) {
         $i++;
         if ($i <= 1) continue;
         //if (strpos($th->attr("class"), "th-active")) continue;
         $priority = round($i * 6 / $nb_th);
         $th->Attr('data-priority', $priority);
         
      }      

      //remove img in th and replace it by border (TODO : filter by up/down img)
      $th = $qp->top($top)->find("tr:first-child img");
      if (!isset($_REQUEST['order']) || $_REQUEST['order'] == "DESC") {
         $th->parent()->AddClass("th-active-down")->Attr('data-priority', "critical");
      } else {
         $th->parent()->AddClass("th-active-up")->Attr('data-priority', "critical");
      }
      $qp->top($top)->find("tr:first-child img")->remove();

      //remove tooltips
      $qp->top($top)->find("span.x-hidden")->remove();
      $qp->top($top)->find("tr a img")->parent()->remove();

      //replace links
      foreach ($qp->top($top)->find("td a") as $a) {
         $href = explode("=", $a->attr("href"));
         $id = array_pop($href);
         $a->attr("href", "item.php?itemtype=$itemtype&id=$id");
      }

      //replace head links
      foreach ($qp->top($top)->find("th a") as $a) {
         $href = explode("?", $a->attr("href"));
         $a->attr("href", "search.php?".$href[1]);
      }

      
      //place th in thead
      $qp->top($top)->append("<thead class='ui-bar-b'></thead>");
      $thead = $qp->top($top)->find("tr:first-child")->html();
      $qp->top($top)->find("tr:first-child")->remove();
      $tbody = "";
      foreach ($qp->top($top)->find("tr") as $tr) {
         $tbody.= $tr->html();
      }
      $qp->top($top)->find("tr")->remove();
      $qp->top($top)->find("th")->remove();
      $qp->top($top)->find("thead")->append($thead);
      $qp->top($top)->append($tbody);

      //init table 
      $qp->top($top)
         ->Attr('id', "mobileTable")
         ->Attr("data-role", "table")
         ->Attr("data-mode", "columntoggle")
         //->Attr("data-mode", "reflow")
         //->AddClass("table-stroke")
         ->AddClass("table-stripe");

      echo $qp->html();

      self::displayFooterNavBar($numrows);
   }

   static function showGenericSearch($itemtype, array $params) {
      ob_start();
      parent::showGenericSearch($itemtype, $params);
      //$html = utf8_decode(ob_get_contents());
      $html = ob_get_contents();
      ob_end_clean();

      //init querypath lib
      $top = "form[name=searchformComputer]";
      $options = array(
         'ignore_parser_warnings' => TRUE,
          'convert_from_encoding' => 'utf-8'
      );
      $qp = qp($html, NULL, $options);

      //replace form action
      //$qp->top($top)->find("form")->attr('action', 
      // GLPI_ROOT."/plugins/mobile/front/searchbox.php");
      

      //remove table, replace it with listview
      $listview = "<ul data-role='listview'>";
      foreach ($qp->top($top)->find(".tab_cadre_fixe td:first-child table tr") as $tr) {
         $listview.= "<li data-role='fieldcontain'>";
         $listview.= str_replace("&nbsp;", "", $tr->html());
         $listview.= "</li>";
      }
      $listview.= "</ul>";
      $qp->top($top)->find(".tab_cadre_fixe")->remove();
      $qp->top($top)->find("#searchcriterias")->append($listview);
      


      echo $qp->top($top)->html();
   }

   public static function displayFooterNavBar($numrows) {
      global $LANG, $CFG_GLPI;

      $url = "search.php?itemtype=".$_REQUEST['itemtype'];
      $step = $_SESSION['glpilist_limit'];

      if (!isset($_GET['start'])) $start = 0;
      else $start = $_GET['start'];

      $get_str = $_SERVER['QUERY_STRING'];
      $get_str = substr($get_str, 0, strpos($get_str, '&start='));

      $first = 0;
      $prev = $start - $step;
      if ($prev < 0) $prev = 0;
      $next = $start + $step;
      if ($next > $numrows) $next = $numrows;
      $last = $numrows - $step;

      $disable_first = false;
      $disable_prev = false;
      $disable_next = false;
      $disable_end = false;

      $start_str = "start=";
      if (strlen(trim($get_str)) > 0) $start_str = "&".$start_str;

      //disable unnecessary navigation element
      if ($start == 0) {
         $disable_first = true;
         $disable_prev = true;
      }

      if (($numrows - $start) <= $step) {
         $disable_next = true;
         $disable_end = true;
      }

      //display footer navigation bar
      echo "<div data-role='footer' data-position='fixed' data-theme='a'>";
      // display navigation position
      $position = sprintf(__('From %1$d to %2$d on %3$d'), 
         $start + 1, 
         $next, 
         $numrows);
      echo "<span id='nav_position'>$position</span>";
      echo "<div data-role='navbar'>";
      echo "<ul>";

         echo "<li><a href='".$CFG_GLPI["root_doc"]."/plugins/mobile/front/searchbox.php?itemtype="
               .$_GET['itemtype']."' data-icon='search' data-rel='dialog'>"
               .__("Search")."</a></li>";

         echo "<li><a ";
         if (!$disable_first) echo "href='".$url."&".$get_str.$start_str.$first."'";
         else echo "class='ui-disabled'";
         echo " data-icon='back'>".__("First")."</a></li>";

         echo "<li><a ";
         if (!$disable_prev) echo "href='".$url."&".$get_str.$start_str.$prev."'";
         else echo "class='ui-disabled'";
         echo " data-icon='arrow-l'>".__("Previous")."</a></li>";

         echo "<li><a ";
         if (!$disable_next) echo "href='".$url."&".$get_str.$start_str.$next."'";
         else echo "class='ui-disabled'";
         echo " data-icon='arrow-r'>".__("Next")."</a></li>";

         echo "<li><a ";
         if (!$disable_end) echo "href='".$url."&".$get_str.$start_str.$last."'";
         else echo "class='ui-disabled'";
         echo " data-icon='forward'>".__("Last")."</a></li>";

      echo "</ul>";
      echo "</div>"; // end navbar
      echo "</div>"; // end footer
   }
}