<?php

global $ROOT_DIR;

require_once $ROOT_DIR . '/inc/generic.php';
require_once $ROOT_DIR . '/inc/db.php';

function tokenList() {

  global $page, $db, $session, $SCRIPT_FILE;

  $ret = "<div id='tokenlist'>\r\n";

  if ( !empty($session['targetId'])) {

    $ret .= "<div class='pagelist'>";
    $ret .= "<a href='./" . $SCRIPT_FILE . "?page=main&uuid=" . $session['uuid'] . "&debugOnly=" . ( $session['debugOnly'] ? "1" : "0" ) . "'>Exit single token mode</a>";
    $ret .= "<a style='margin-left: 16px;' href='./" . $SCRIPT_FILE . "?page=main&uuid=" . $session['uuid'] . "&debugOnly=" . ( $session['debugOnly'] ? "1" : "0" ) . "&deleteToken=1&targetId=" . $session['targetId'] . "'>Remove this token</a>";
    $ret .= "</div>";

  } else {

    $pages = $db -> pageCount($session['product'], $session['debugOnly'] ? 1 : 0);

    if ( $pages == 0 )
      $pages = 1;

    $ret .= "<div class='pagelist'>\r\n";
    $ret .= "<form id='paginator' action='" . $SCRIPT_FILE . "' method='post'>\r\n";
    $ret .= inputType('hidden', 'uuid', $session['uuid']) . "\r\n";
    $ret .= inputType('hidden', 'debugOnly', $session['debugOnly'] ? '1' : '0') . "\r\n";
    $ret .= "Page: <select name='pageIndex' onchange='this.form.submit()'>\r\n";

    $pageNo = 1;
    $thisPage = empty($session['pageIndex']) ? 1 : intval($session['pageIndex']);
    if ( $thisPage < 1 ) $thisPage = 1;
    while ( $pageNo <= $pages ) {
      $ret .= "<option value='" . $pageNo . "'" . ( $pages == 1 ? " disabled" : "" ) . ( $thisPage == $pageNo ? " selected" : "" ) . " />" . $pageNo . "</option>\r\n";
      $pageNo += 1;
    }

    $ret .= "</select>\r\n";
    $ret .= "</form>\r\n";
    $ret .= "</div>\r\n";
  }

  $ret .= "<div class='messagebox'>\r\n";
  $ret .= "<form id='paginator' action='" . $SCRIPT_FILE . "' method='post'>\r\n";
  $ret .= inputType('hidden', 'uuid', $session['uuid']) . "\r\n";
  $ret .= inputType('hidden', 'debugOnly', $session['debugOnly'] ? '1' : '0') . "\r\n";
  $ret .= inputType('hidden', 'page', 'send') . "\r\n";
  if ( !empty($session['targetId']))
    $ret .= inputType('hidden', 'targetId', $session['targetId']) . "\r\n";
  $ret .= "Message: " . inputType('text', 'payload', '', [ 'placeholder' => 'message', 'class' => 'messageField', 'required' => true ]) . "\r\n";
  $ret .= inputType('submit', 'send', 'send', [ 'class' => 'sendBtn' ]) . "\r\n";
  $ret .= "</form>\r\n";
  $ret .= "</div>\r\n";

  $tokens = [];
  if ( empty($session['targetId']))
    $tokens = $db -> getTokens($session['product'], $session['debugOnly'] ? 1 : 0, $thisPage - 1);
  else $tokens = $db -> getToken($session['targetId']);

  $ret .= "<div class='tokenContainer'>\r\n";

  $ret .= "<div class='tokenBr'>\r\n";
  $ret .= "<div class='id'>ID</div>\r\n";
  $ret .= "<div class='timestamp'>Timestamp</div>\r\n";
  $ret .= "<div class='version'>Version</div>\r\n";
  $ret .= "<div class='build'>Build</div>\r\n";
  $ret .= "<div class='uuid'>UUID</div>\r\n";
  $ret .= "<div class='token'>Token</div>\r\n";
  $ret .= "</div>\r\n";

  foreach ( $tokens as $token ) {
    $ret .= "<div class='tokenEntry'>";
    $ret .= "<div class='id'>";
    if ( empty($session['targetId']))
      $ret .= "<a href='./" . $SCRIPT_FILE . "?page=main&uuid=" . $session['uuid'] . "&debugOnly=" . ( $session['debugOnly'] ? "1" : "0" ) . "&targetId=" . strval($token['id']) . "'>";
    $ret .= ( $token['id'] < 10 ? "&nbsp;&nbsp;" : ( $token['id'] < 100 ? "&nbsp;" : "" )) . strval($token['id']);
    if ( empty($session['targetId']))
      $ret .= "</a>";
    $ret .= "</div>\r\n";
    $ret .= "<div class='timestamp'>" . $token['timestamp'] . "</div>\r\n";
    $ret .= "<div class='version'>" . $token['version'] . "</div>\r\n";
    $ret .= "<div class='build'>" . strval($token['build']) . "</div>\r\n";
    $ret .= "<div class='uuid'>" . $token['uuid'] . "</div>\r\n";
    $ret .= "<div class='token'>" . $token['token'] . "</div>\r\n";
    $ret .= "</div>\r\n";
  }

  if ( count($tokens) == 0 ) {
    $ret .= "<div class='tokenEntry'>\r\n";
    $ret .= "<div class='id'>&nbsp;</div>\r\n";
    $ret .= "<div class='timestamp'>&nbsp;</div>\r\n";
    $ret .= "<div class='version'>&nbsp;</div>\r\n";
    $ret .= "<div class='build'>&nbsp;</div>\r\n";
    $ret .= "<div class='uuid'>&nbsp;</div>\r\n";
    $ret .= "<div class='token'>&nbsp;</div>\r\n";
    $ret .= "</div>\r\n";
  }

  $ret .= "</div>\r\n";
  $ret .= "<div class='tokenlist'>\r\n";


  $ret .= "</div>\r\n";

  $ret .= "</div>\r\n";

  return $ret;
}

?>
