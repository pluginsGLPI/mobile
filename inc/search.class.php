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
      $top = "#massformTicket table.tab_cadrehov";
      $options = array('ignore_parser_warnings' => TRUE);
      $qp = qp($html, NULL, $options);
      
      //remove unused tags
      $qp->remove('input[type=checkbox]');
      $qp->remove('script');

      //clean table items
      $qp->find("tr")
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
         ->Attr('id', "search-table")
         ->Attr("data-role", "table")
         ->Attr("data-mode", "columntoggle")
         ->AddClass("table-stripe")
         ->AddClass("table-stripe");


      echo $qp->html();
   }

   static function showGenericSearch($itemtype, array $params) {

   }
}