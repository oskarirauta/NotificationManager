<?php

global $ROOT_DIR/*, $SCRIPT_FILE*/;

require_once $ROOT_DIR . '/inc/generic.php';
require_once $ROOT_DIR . '/inc/db.php';

function generate_page() {

  global $session, $page, $SCRIPT_FILE, $db;
  if ( !$session['ssl'] ) {
    $page = notSSL();
    return;
  }

  $page['title'] = empty($session['error']) ? 'Login' : 'Error';
  $page['body'] .= "<form id='login' action='" . $SCRIPT_FILE . "' method='post'>\r\n";

  $page['body'] .= "<div class='container'>\r\n";

  $page['body'] .= "<div class='row nomargin'>\r\n";
  $page['body'] .= "<div class='fullwidth center-align title'>Logout</div>\r\n";
  $page['body'] .= "</div>\r\n";

  $page['body'] .= "<div class='row'>\r\n";
  $page['body'] .= "<div class='fullwidth center-align'>You have logged out.</div>\r\n";
  $page['body'] .= "</div>\r\n";
  $page['body'] .= "<div class='row'>\r\n";
  $page['body'] .= "<div class='fullwidth center-align'>\r\n";
  $page['body'] .= inputType('submit', 'login', 'Login', [ 'class' => 'loginBtn' ]) . "\r\n";
  $page['body'] .= "</div>\r\n";
  $page['body'] .= "</div>\r\n";  
  $page['body'] .= "</form>";

  $db -> logout($session['uuid']);
}

?>
