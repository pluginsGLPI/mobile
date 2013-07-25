<?php

class PluginMobileCentral extends Central {

   static function showForMobile() {

      //self service interface
      if ($_SESSION['glpiactiveprofile']['interface'] === 'helpdesk') {
         return self::showHelpdeskPublic();
      }

      //normal interface
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
      //self::showGroupView();
      echo "</p>
         </div>
         
         <div data-role='collapsible'>
         <h3>".__("Global View")."</h3>
         <p>";
      //self::showGlobalView();
      echo "</p>
         </div>

         <div data-role='collapsible'>
         <h3>".__("RSS feed")."</h3>
         <p>";
      //self::showRSSView();
      echo "</p>
         </div>
         
      </div>";

   }

   static function showMyView() {
      $showticket = (Session::haveRight("show_all_ticket", "1")
                     || Session::haveRight("show_assign_ticket", "1"));

      $showproblem = (Session::haveRight("show_all_problem", "1")
                      || Session::haveRight("show_my_problem", "1"));

      if (Session::haveRight('validate_request',1)
         || Session::haveRight('validate_incident',1)) {
         ob_start();
         Ticket::showCentralList(0,"tovalidate",false);
         $html = utf8_decode(ob_get_contents());
         ob_end_clean();
         echo self::cleanHTML($html, "table.tab_cadrehov");
         if (!empty($html)) echo "<br />";
      }

      ob_start();
      Ticket::showCentralList(0, "toapprove", false);
      $html = utf8_decode(ob_get_contents());
      ob_end_clean();
      echo self::cleanHTML($html, "table.tab_cadrehov");
      if (!empty($html)) echo "<br />";

      ob_start();
      Ticket::showCentralList(0, "rejected", false);
      $html = utf8_decode(ob_get_contents());
      ob_end_clean();
      echo self::cleanHTML($html, "table.tab_cadrehov");
      if (!empty($html)) echo "<br />";

      ob_start();
      Ticket::showCentralList(0, "requestbyself", false);
      $html = utf8_decode(ob_get_contents());
      ob_end_clean();
      echo self::cleanHTML($html, "table.tab_cadrehov");
      if (!empty($html)) echo "<br />";

      if ($showticket) {
         ob_start();
         Ticket::showCentralList(0, "process", false);
         $html = utf8_decode(ob_get_contents());
         ob_end_clean();
         echo self::cleanHTML($html, "table.tab_cadrehov");
         if (!empty($html)) echo "<br />";

         ob_start();
         Ticket::showCentralList(0, "waiting", false);
         $html = utf8_decode(ob_get_contents());
         ob_end_clean();
         echo self::cleanHTML($html, "table.tab_cadrehov");
         if (!empty($html)) echo "<br />";
      }
      if ($showproblem) {
         ob_start();
         Problem::showCentralList(0, "process", false);
         $html = utf8_decode(ob_get_contents());
         ob_end_clean();
         echo self::cleanHTML($html, "table.tab_cadrehov");
         if (!empty($html)) echo "<br />";
      }

      ob_start();
      Planning::showCentral(Session::getLoginUserID());
      $html = utf8_decode(ob_get_contents());
      ob_end_clean();
      echo self::cleanHTML($html);
      if (!empty($html)) echo "<br />";

      ob_start();
      Reminder::showListForCentral();
      $html = utf8_decode(ob_get_contents());
      ob_end_clean();
      echo self::cleanHTML($html);
      if (!empty($html)) echo "<br />";
      
      if (Session::haveRight("reminder_public","r")) {
         ob_start();
         Reminder::showListForCentral(false);
         $html = utf8_decode(ob_get_contents());
         ob_end_clean();
         echo self::cleanHTML($html);
      }

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

   static function cleanHTML($html, $top = "table.tab_cadre_central", $show_title = true) {
      //init querypath lib
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
         ->removeClass("tab_bg_2_2")
         ->removeClass("center");

      //clean title
      if (!$show_title) {
         $qp->top($top)->remove('th');
      } else {
         //place th in thead
         $qp->top($top)->append("<thead class='ui-bar-b'></thead>");
         $thead = "";
         foreach ($qp->top($top)->find("th")->parent() as $trth) {
            $thead.= $trth->html();
         }
         $qp->top($top)->find("th")->parent()->remove();
         $tbody = "";
         foreach ($qp->top($top)->find("tr") as $trtd) {
            $tbody.= $trtd->html();
         }
         $qp->top($top)->find("tr")->remove();
         $qp->top($top)->find("th")->remove();
         $qp->top($top)->find("thead")->append($thead);
         $qp->top($top)->append($tbody);
      }

      //compute link
      foreach ($qp->top($top)->find("a") as $a) {
         //search shortcut
         if (strpos($a->attr("href"), "ticket.php") !== false) {
            $tmp = explode("?", $a->attr("href"));
            $params = $tmp[1];
            $a->attr("href", "search.php?itemtype=Ticket&$params");
         }         

         if (strpos($a->attr("href"), "ticket.form.php") !== false) {
            foreach ($qp->top($top)->find("td a") as $a) {
               $href = explode("=", $a->attr("href"));
               $id = array_pop($href);
               $a->attr("href", "item.php?itemtype=Ticket&id=$id");
            }
         }
      }

      //init table 
      $qp->top($top)
         ->Attr('id', "mobileTable")
         ->Attr("data-role", "table")
         ->Attr("data-mode", "columntoggle")
         ->AddClass("table-stripe");

      //remove tooltips
      $qp->top($top)->find("span.x-hidden")->remove();
      $qp->top($top)->find("tr a img")->parent()->remove();

      return $qp->top($top)->html();
   }

   static function showHelpdeskPublic() {
      if (isset($_GET['create_ticket'])) {
         $ticket = new Ticket();
         $ticket->showFormHelpdesk(Session::getLoginUserID());
      } else {
         
         if (Session::haveRight('create_ticket',1)) {
            ob_start();
            Ticket::showCentralCount(true);
            $html = utf8_decode(ob_get_contents());
            ob_end_clean();
            echo self::cleanHTML($html, "table.tab_cadrehov");
         }

         echo "<div data-role='collapsible-set' data-content-theme='a'>";
         if (Session::haveRight("reminder_public","r")) {
            echo "<div data-role='collapsible' data-collapsed='true'>";
            echo"<h3>"._n('Public reminder', 'Public reminders', 2)."</h3>";
            echo "<p>";
            ob_start();
            Reminder::showListForCentral(false);
            $html = utf8_decode(ob_get_contents());
            ob_end_clean();
            echo self::cleanHTML($html, "table.tab_cadrehov", false);
            echo "</p></div>";
         }

         if (Session::haveRight("rssfeed_public","r")) {
            echo "<div data-role='collapsible' data-collapsed='true'>";
            echo"<h3>".__("RSS feed")."</h3>";
            echo "<p>";
            ob_start();
            RSSFeed::showListForCentral(false);
            $html = utf8_decode(ob_get_contents());
            ob_end_clean();
            echo self::cleanHTML($html, "table.tab_cadrehov", false);
            echo "</p></div>";
         }

         // Show KB items
         if (Session::haveRight("faq","r")) {
            echo "<div data-role='collapsible' data-collapsed='true'>";
            echo"<h3>".__('Most popular questions')."</h3>";
            echo "<p>";
            ob_start();
            KnowbaseItem::showRecentPopular("popular");
            $html = utf8_decode(ob_get_contents());
            ob_end_clean();
            echo self::cleanHTML($html, "table.tab_cadrehov", false);
            echo "</p></div>";


            echo "<div data-role='collapsible' data-collapsed='true'>";
            echo"<h3>".__("Recent entries")."</h3>";
            echo "<p>";
            ob_start();
            KnowbaseItem::showRecentPopular("recent");
            $html = utf8_decode(ob_get_contents());
            ob_end_clean();
            echo self::cleanHTML($html, "table.tab_cadrehov", false);
            echo "</p></div>";


            echo "<div data-role='collapsible' data-collapsed='true'>";
            echo"<h3>".__("Last updated entries")."</h3>";
            echo "<p>";
            ob_start();
            KnowbaseItem::showRecentPopular("lastupdate");
            $html = utf8_decode(ob_get_contents());
            ob_end_clean();
            echo self::cleanHTML($html, "table.tab_cadrehov", false);
            echo "</p></div>";
         }
         echo "</p></div></div>";
      }
   }
}