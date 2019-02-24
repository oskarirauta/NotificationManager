<?php

global $ROOT_DIR;

require_once $ROOT_DIR . '/inc/generic.php';
require_once $ROOT_DIR . '/inc/db.php';

require_once $ROOT_DIR . '/templates/tokenList.php';

function generate_page() {

  global $session, $page, $db, $SCRIPT_FILE;
  if ( !$session['ssl'] ) {
    $page = notSSL();
    return;
  }

  $idCount = $db -> amountOf($session['product'], $session['debugOnly'] ? 1 : 0);
  $page['title'] = 'Notification system';

  $page['body'] .= "<div class='mainui'>\r\n";
  $page['body'] .= "<div class='title'>Welcome to notification messenger, " . $session['name'] . ".</div>\r\n";
  $page['body'] .= "<div class='productname'>Product: <b>" . $session['product'] . "</b>";
  $page['body'] .= "<small style='margin-left: 6px;'>" . ( $session['debugOnly'] ? "[<u>DEBUG</u>]" : "[<u>PRODUCTION</u>]" ) . "</small>";
  $page['body'] .= "</div>\r\n";
  $page['body'] .= "<div class='count'>" . strval($idCount) . " tokens stored in database.</div>";
  $page['body'] .= "<div class='modeselector'><a href='./" . $SCRIPT_FILE . "?page=main&uuid=" . $session['uuid'] . "&debugOnly=" . ( $session['debugOnly'] ? "0" : "1" ) . "'>" . ( $session['debugOnly'] ? "Switch to production tokens" : "Switch to debug tokens" ) . "</a></div>\r\n";
  $page['body'] .= "<div class='logout'><a href='./" . $SCRIPT_FILE . "?page=logout&uuid=" . $session['uuid'] . "&debugOnly=0'>Logout</a></div>\r\n";

  $page['body'] .= tokenList();
  $page['body'] .= "</div>";
}

?>
