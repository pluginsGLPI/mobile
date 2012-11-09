<?php

define('GLPI_ROOT', '../..'); 
include (GLPI_ROOT . "/inc/includes.php"); 
PluginMobileHtml::includeHeader(__('Authentication'));
   

if (!isset($_SESSION["glpicookietest"]) || ($_SESSION["glpicookietest"] != 'testcookie')) {
   if (!is_writable(GLPI_SESSION_DIR)) {
      Html::redirect($CFG_GLPI['root_doc'] . "/index.php?error=2");
   } else {
      Html::redirect($CFG_GLPI['root_doc'] . "/index.php?error=1");
   }
}

$_POST = array_map('stripslashes', $_POST);

//Do login and checks
//$user_present = 1;
if (!isset($_POST['login_name'])) {
   $_POST['login_name'] = '';
}

if (isset($_POST['login_password'])) {
   $_POST['login_password'] = Toolbox::unclean_cross_side_scripting_deep($_POST['login_password']);
} else {
   $_POST['login_password'] = '';
}


// Redirect management
$REDIRECT = "";
if (isset($_POST['redirect']) && (strlen($_POST['redirect']) > 0)) {
   $REDIRECT = "?redirect=" .$_POST['redirect'];

} else if (isset($_GET['redirect']) && strlen($_GET['redirect'])>0) {
   $REDIRECT = "?redirect=" .$_GET['redirect'];
}

$auth = new Auth();

// now we can continue with the process...
if ($auth->Login($_POST['login_name'], $_POST['login_password'],
                 (isset($_REQUEST["noAUTO"])?$_REQUEST["noAUTO"]:false))) {

   // Redirect to Command Central if not post-only
   if ($_SESSION["glpiactiveprofile"]["interface"] == "helpdesk") {
      if ($_SESSION['glpiactiveprofile']['create_ticket_on_login']
          && empty($REDIRECT)) {
         Html::redirect($CFG_GLPI['root_doc'].
            "/plugins/mobile/front/helpdesk.public.php?create_ticket=1");
      }
      Html::redirect($CFG_GLPI['root_doc'] . "/plugins/mobile/front/helpdesk.public.php$REDIRECT");

   } else {
      if ($_SESSION['glpiactiveprofile']['create_ticket_on_login']
          && empty($REDIRECT)) {
         Html::redirect($CFG_GLPI['root_doc'] . "/plugins/mobile/front/ticket.form.php");
      }
      Html::redirect($CFG_GLPI['root_doc'] . "/plugins/mobile/front/central.php$REDIRECT");
   }

} else {
   // we have done at least a good login? No, we exit.
   PluginMobileHtml::showLoginBox($auth->getErr(), $REDIRECT);
}
PluginMobileHtml::footer();