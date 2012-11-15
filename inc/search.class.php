<?php

class PluginMobileSearch extends Search {

   static function show($itemtype) {
      self::manageGetValues($itemtype);
      self::showGenericSearch($itemtype, $_GET);
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
      $qp = qp($html, NULL, $options);
      
      //remove unused tags
      $qp->remove('input[type=checkbox]');
      $qp->remove('script');

      //get pager content
      $pager = $qp->find(".tab_cadre_pager td:last-child")->html();
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

      //remove img in th and replace it by border (TODO : filter by up/down img)
      $qp->top($top)->find("tr:first-child img")
         ->parent()->AddClass("th-active-up");
      $qp->top($top)->find("tr:first-child img")->remove();

      //remove tooltips
      $qp->top($top)->find("span.x-hidden")->remove();
      $qp->top($top)->find("tr a img")->parent()->remove();

      //init table 
      $qp->top($top)
         ->Attr('id', "mobileTable")
         ->Attr("data-role", "table")
         ->Attr("data-mode", "columntoggle")
         ->AddClass("table-stripe")
         ->AddClass("table-stripe");


      echo $qp->html();

      
      

      self::displayFooterNavBar($numrows);
   }

   static function showGenericSearch($itemtype, array $params) {

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
      $last = floor($numrows / $step) * $step;

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
         $_GET['start']+1, 
         $_GET['start']+$step, 
         $numrows);
      echo "<span id='nav_position'>$position</span>";
      echo "<div data-role='navbar'>";
      echo "<ul>";

         echo "<li><a href='".$CFG_GLPI["root_doc"]."/plugins/mobile/front/searchbox.php?itemtype="
               .$_GET['itemtype']."' data-icon='search' data-rel='dialog'>"
               .__("Search")."</a></li>";

         echo "<li><a ";
         if (!$disable_first) echo "href='".$url."&".$get_str.$start_str.$first."' rel='external'";
         else echo "class='ui-disabled'";
         echo " data-icon='back'>".__("First")."</a></li>";

         echo "<li><a ";
         if (!$disable_prev) echo "href='".$url."&".$get_str.$start_str.$prev."' rel='external'";
         else echo "class='ui-disabled'";
         echo " data-icon='arrow-l'>".__("Previous")."</a></li>";

         echo "<li><a ";
         if (!$disable_next) echo "href='".$url."&".$get_str.$start_str.$next."' rel='external'";
         else echo "class='ui-disabled'";
         echo " data-icon='arrow-r'>".__("Next")."</a></li>";

         echo "<li><a ";
         if (!$disable_end) echo "href='".$url."&".$get_str.$start_str.$last."' rel='external'";
         else echo "class='ui-disabled'";
         echo " data-icon='forward'>".__("Last")."</a></li>";

      echo "</ul>";
      echo "</div>";
      echo "</div>";
   }
}