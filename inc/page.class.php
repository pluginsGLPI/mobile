<?php

class PluginMobilePage extends CommonDBTM {

   static function showItem($itemtype, $id) {
      $readonlyFields = array('id', 'date_mod', 'uuid');

      $obj = new $itemtype;
      $obj->getFromDB($id);
      $table = $obj->getTable();

      $searchOptions = Search::getOptions($itemtype);

      echo "<ul data-role='listview' data-theme='a'>";
      foreach($obj->fields as $field => $value) {
         $opt_num = self::getOptionNumber($searchOptions, $table, $field);
         if ($opt_num === false) continue; 
         $itemSearchOptions = $searchOptions[$opt_num];

         echo "<li data-role='fieldcontain'>";
         echo "<label for='$field' class='select'>".$itemSearchOptions['name']."</label>";

         if (in_array($field, $readonlyFields)) {
            echo $value;
         } else {

            if ($itemSearchOptions['table'] != $table) {
               Dropdown::show(getItemTypeForTable($itemSearchOptions['table']),
                                 array('value'     => $value,
                                       'name'      => $field,
                                       'comments'  => false));
            } elseif(isset($itemSearchOptions['searchtype']) 
                     && $itemSearchOptions['searchtype'] == "equals") {
               self::showEquals($itemSearchOptions, $value);
            } elseif($itemSearchOptions['datatype'] == "bool") {
               Dropdown::showYesNo($itemSearchOptions['linkfield'], $value);
            } elseif($itemSearchOptions['datatype'] == "date") {
                  echo "<input type='text' data-role='date' name='$field' value='$value' />";
            } elseif($itemSearchOptions['datatype'] == "text") {
                  echo "<textarea name='$field' rows='4'>$value</textarea>";
            } else {
               echo "<input type='text' name='$field' id='$field' value='$value' />";
            }
         }

         echo "</li>";
      }
      echo "</ul>";
   }

   static function getOptionNumber($opts, $table, $field) {
      foreach ($opts as $num => $opt) {
         if ($opt['linkfield'] == $field 
            && $opt['table'] != $table 
            || 
            $opt['field'] == $field
            && $opt['linkfield'] == $field ) {
            return $num;
         }
      }
      return false;
   }
   

   static function showEquals($searchopt, $value) {
      $inputname = $searchopt['linkfield'];
      
      switch ($searchopt['table'].".".$searchopt['linkfield']) {
         case "glpi_tickets.type" :
            Ticket::dropdownType($inputname, array('value' => $value));
            break;
         case "glpi_tickets.status" :
            Ticket::dropdownStatus(array('name'=> $inputname, 'value' => $value));
            break;
         case "glpi_tickets.priority" :
            Ticket::dropdownPriority(array('name'=> $inputname, 'value' => $value));
            break;
         case "glpi_tickets.impact" :
            Ticket::dropdownImpact(array('name'=> $inputname, 'value' => $value));
            break;
         case "glpi_tickets.urgency" :
            Ticket::dropdownUrgency(array('name'=> $inputname, 'value' => $value));
            break;
         case "glpi_tickets.global_validation" :
            TicketValidation::dropdownStatus($inputname,array('value'=>$value,'all'=>1));
            break;
         case "glpi_users.name":
            User::dropdown(array('name'      => $inputname,
                                 'value'     => $value,
                                 'comments'  => false,
                                 'all'       => -1,
                                 'right'     => 'all'));
            break;
         case "glpi_ticketvalidations.status" :
            TicketValidation::dropdownStatus($inputname,array('value'=>$value,'all'=>1));
            break;
      }
   }
}

