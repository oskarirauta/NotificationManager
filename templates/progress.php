<?php

global $ROOT_DIR;

require_once $ROOT_DIR . '/inc/generic.php';
require_once $ROOT_DIR . '/inc/db.php';

function progressView() {

  global $session, $page, $db, $SCRIPT_FILE;
  $ret = "";

  $ret .= "<div class='overlay'>\r\n";
  $ret .= "<div class='pframe'>\r\n";
  $ret .= "<div class='pcontainer'>\r\n";
  $ret .= "<div class='title'>Sending</div>\r\n";
  $ret .= "<progress id='progress' class='progress' value='0'></progress>\r\n";
  $ret .= "<div id='percentage' class='percentage'>0%</div>\r\n";
  $ret .= "<div id='caption' class='caption'>Please wait until finished..</div>\r\n";
  $ret .= "<div id='doneBtn' class='doneBtn' onClick=\"location.href='./" . $SCRIPT_FILE . "?page=main&uuid=" . $session['uuid'] . "&debugOnly=" . ( $session['debugOnly'] ? "1" : "0" ) . "'\">DONE</div>\r\n";
  $ret .= "</div>\r\n";
  $ret .= "</div>\r\n";
  $ret .= "</div>";

  return $ret;
}

?>
