<?php

class PluginMobileCentral extends Central {

   static function showForMobile() {

      echo "<div data-role='collapsible-set' data-content-theme='a'>

         <div data-role='collapsible' data-collapsed='true'>
         <h3>".__("Personal View")."</h3>
         <p>";
      self::showMyView();
      echo "</p>
         </div>
         
         <div data-role='collapsible'>
         <h3>".__("Group View")."</h3>
         <p>";
      self::showGroupView();
      echo "</p>
         </div>
         
         <div data-role='collapsible'>
         <h3>".__("Global View")."</h3>
         <p>";
      self::showGlobalView();
      echo "</p>
         </div>

         <div data-role='collapsible'>
         <h3>".__("RSS feed")."</h3>
         <p>";
      self::showRSSView();
      echo "</p>
         </div>
         
      </div>";

   }

   static function showMyView() {
      ob_start();
      parent::showMyView();
      $html = utf8_decode(ob_get_contents());
      ob_end_clean();

      echo self::cleanHTML($html);
   }

   static function showGlobalView() {
      ob_start();
      parent::showGlobalView();
      $html = utf8_decode(ob_get_contents());
      ob_end_clean();

      echo self::cleanHTML($html);
   }

   static function showRSSView() {
      ob_start();
      parent::showRSSView();
      $html = utf8_decode(ob_get_contents());
      ob_end_clean();

      echo self::cleanHTML($html);
   }

   static function showGroupView() {
      ob_start();
      parent::showGroupView();
      $html = utf8_decode(ob_get_contents());
      ob_end_clean();

      echo self::cleanHTML($html);
   }

   static function cleanHTML($html) {
      //init querypath lib
      $top = "table.tab_cadre_central";
      $options = array('ignore_parser_warnings' => TRUE);
      $qp = qp($html, NULL, $options);
      
      //remove unused tags
      $qp->remove('input[type=checkbox]');
      $qp->remove('script');

      //clean table items
      $qp->top($top)->find("tr")
         ->removeClass("tab_bg_1")
         ->removeClass("tab_bg_2");
      $qp->find("td")
         ->removeAttr("valign")
         ->removeAttr("width")
         ->removeClass("tab_bg_2_2");

      //compute link
      //$qp->top($top)->find("a").attr("href");

      //init table 
      $qp->top($top)
         ->Attr('id', "searchTable")
         ->Attr("data-role", "table")
         ->Attr("data-mode", "columntoggle")
         ->AddClass("table-stripe");

      //remove tooltips
      $qp->top($top)->find("span.x-hidden")->remove();
      $qp->top($top)->find("tr a img")->parent()->remove();

      return $qp->top($top)->html();
   }
}