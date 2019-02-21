<?php

global $ROOT_DIR;

require_once $ROOT_DIR . '/inc/generic.php';
require_once $ROOT_DIR . '/inc/db.php';

function generate_page() {

  global $session, $page, $SCRIPT_FILE;
  if ( !$session['ssl'] ) {
    $page = notSSL();
    return;
  }

  $page['title'] = empty($session['error']) ? 'Login' : 'Error';
  $page['body'] .= "<FORM ID='login' ACTION='" . $SCRIPT_FILE . "' METHOD='POST'>\r\n";

  $page['body'] .= "<div class='container'>\r\n";

  $page['body'] .= "<div class='row nomargin'>\r\n";
  $page['body'] .= "<div class='fullwidth center-align title'>Please login</div>\r\n";
  $page['body'] .= "</div>\r\n";

  if ( !empty($session['error'])) {
    $page['body'] .= "<div class='row nomargin'>\r\n";
    $page['body'] .= "<div class='fullwidth center-align error'>" . $session['error'] . "</div>\r\n";
    $page['body'] .= "</div>\r\n";
  }

  $page['body'] .= "<div class='row'>\r\n";
  $page['body'] .= "<div class='label'>User ID:</div>\r\n";
  $page['body'] .= "<div class='field'>" . inputType('text', 'userid', '', [ 'placeholder' => 'user id', 'class' => 'loginField', required => true ]) . "</div>\r\n";
  $page['body'] .= "<div class='req'>&nbsp;*</div>\r\n";
  $page['body'] .= "</div>\r\n";

  $page['body'] .= "<div class='row'>\r\n";
  $page['body'] .= "<div class='label'>Password:</div>\r\n";
  $page['body'] .= "<div class='field'>" . inputType('password', 'passwd', '', [ 'placeholder' => 'password', 'class' => 'loginField', 'required' => true ]) . "</div>\r\n";
  $page['body'] .= "<div class='req'>&nbsp;*</div>\r\n";
  $page['body'] .= "</div>\r\n";

  $page['body'] .= "<div class='row'>\r\n";
  $page['body'] .= "<div class='label'>Product:</div>\r\n";
  $page['body'] .= "<div class='field'>" . inputType('text', 'product', '', [ 'placeholder' => 'product code', 'class' => 'loginField', 'required' => true ]) . "</div>\r\n";
  $page['body'] .= "<div class='req'>&nbsp;*</div>\r\n";
  $page['body'] .= "</div>\r\n";

  $page['body'] .= "<div class='row'>\r\n";
  $page['body'] .= "<div class='fullwidth right-align'>\r\n";
  $page['body'] .= inputType('reset', 'reset', 'Reset') . "\r\n";
  $page['body'] .= inputType('submit', 'login', 'Login', [ 'class' => 'loginBtn' ]) . "\r\n";
  $page['body'] .= "</div>\r\n";
  $page['body'] .= "</div>\r\n";

  $page['body'] .= "</div>\r\n";
  $page['body'] .= "</div>\r\n";

  // $page['body'] .= inputType('hidden', 'uuid', $session['uuid'], [ 'placeholder' => 'uuid', 'required' => '1']);
  
  $page['body'] .= "</FORM>";

}

?>
