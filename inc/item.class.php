<?php
class PluginMobileItem extends CommonDBTM {

   static function getTitle($itemtype, $id) {
      $obj = new $itemtype();
      $obj->getFromDB($id);

      $name = $obj->fields['name'];
      if (isset($obj->fields['completename'])) $name = $obj->fields['completename'];

      return "$name ($id)";
   }


   static function showItem($itemtype, $id) {
      $obj = new $itemtype;
      $obj->getFromDB($id);
      $table = $obj->getTable();
      $searchOptions = Search::getOptions($itemtype);

      //remove undisplayable fields
      foreach($obj->fields as $field => $value) {
         if (self::getOptionNumber($searchOptions, $table, $field) === false) {
            unset($obj->fields[$field]);
         }
         
         switch ($field) {
            // Mask Author of last edit
         	case 'date_mod' :
               $msg = sprintf(__('By %s'), getUserName($obj->fields["users_id_lastupdater"]) );
               $obj->fields[$field] .= " " . strtolower($msg);
               break;
            case 'users_id_lastupdater' :
               unset($obj->fields[$field]);
               break;
         }
      }

      //show fields (if large screen, on two columns)
      echo "<div id='mobileItem'>";
      if (PluginMobileCommon::largeScreen()) {
         $nb_items = count($obj->fields);
         
         $tmp = array_chunk($obj->fields, ceil($nb_items/2), true);
         $fields1 = $tmp[1];
         $fields2 = $tmp[0];

         echo "<div class='ui-grid-a' id='tablet-grid'>";
         echo "<div class='ui-block-a'>";
         self::showItemFields($itemtype, $fields1);
         echo "</div>";
         echo "<div class='ui-block-b'>";
         self::showItemFields($itemtype, $fields2);
         echo "</div>";
         echo "</div>";

      }  else {  
         self::showItemFields($itemtype, $obj->fields);
      }
      echo "</div>";
   }


   static function showItemFields($itemtype, $fields) {
      $readonlyFields = array('id', 'date_mod', 'uuid');

      $obj = new $itemtype();
      $table = $obj->getTable();
      $searchOptions = Search::getOptions($itemtype);

      //init list view
      echo "<ul data-role='listview' data-theme='a'>";
      foreach ($fields as $field => $value) {
         //get current search option
         $itemSearchOptions = $searchOptions[self::getOptionNumber($searchOptions, $table, $field)];

         echo "<li data-role='fieldcontain'>";
         echo "<label for='$field' class='select'>".$itemSearchOptions['name']."</label>";

         if (in_array($field, $readonlyFields)) {
            echo $value;
         } else {

            if ($field == "entities_id") {
               echo Dropdown::getDropdownName($itemSearchOptions['table'], $value);
            } elseif($itemSearchOptions['table'] != $table) {
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
         if (
               isset($opt['linkfield'])
               && $opt['linkfield'] == $field 
               && $opt['table'] != $table 
            || 
               isset($opt['field'])
               && $opt['field'] == $field
               && $opt['linkfield'] == $field
         ) {
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