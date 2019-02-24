<?php

global $ROOT_DIR;

require_once $ROOT_DIR . '/inc/generic.php';
require_once $ROOT_DIR . '/inc/db.php';

function tokenList() {

  global $page, $db, $session, $ROOT_DIR, $SCRIPT_FILE;

  $certError = false;
  $certFile = 'certs/' . $session['product'] . ( $session['debugOnly'] ? '_debug' : '') . '.pem';

  $ret = "<div id='tokenlist'>\r\n";

  if ( !file_exists($ROOT_DIR . '/' . $certFile )) {
    $certError = true;
    $ret .= "<div style='text-align: center;'>\r\n";
    $ret .= "<div class='error'>\r\n";
    $ret .= "ERROR: Certificate <i>" . $certFile . "</i> is missing.\r\n";
    $ret .= "</div>\r\n";
    $ret .= "</div>\r\n";
  }

  $ret .= "<table class='controlPanel' cellspacing='0' cellpadding='0' border='0'>\r\n";

  if ( !empty($session['targetId'])) {

    $ret .= "<tr class='pageList'>\r\n";
    $ret .= "<td colspan='2'>\r\n";
    $ret .= "<a href='./" . $SCRIPT_FILE . "?page=main&uuid=" . $session['uuid'] . "&debugOnly=" . ( $session['debugOnly'] ? "1" : "0" ) . "'>Filter off</a>\r\n";
    $ret .= "</td><td colspan='2'>\r\n";
    $ret .= "<a style='margin-left: 16px;' href='./" . $SCRIPT_FILE . "?page=main&uuid=" . $session['uuid'] . "&debugOnly=" .( $session['debugOnly'] ? "1" : "0" ) . "&deleteToken=1&targetId=" . $session['targetId'] . "'>Remove this</a>\r\n";
    $ret .= "</td><td>&nbsp;</td>\r\n";
    $ret .= "</tr>\r\n";

  } else {

    $pages = $db -> pageCount($session['product'], $session['debugOnly'] ? 1 : 0);

    if ( $pages == 0 )
      $pages = 1;

    $ret .= "<form id='paginator' name='paginator' action='" . $SCRIPT_FILE . "' method='post'>\r\n";
    $ret .= inputType('hidden', 'uuid', $session['uuid']) . "\r\n";
    $ret .= inputType('hidden', 'debugOnly', $session['debugOnly'] ? '1' : '0') . "\r\n";

    $ret .= "<tr class='pageList'><td>\r\n";
    $ret .="Page: </td><td colspan='3'><select name='pageIndex' onchange='this.form.submit()'>\r\n";

    $pageNo = 1;
    $thisPage = empty($session['pageIndex']) ? 1 : intval($session['pageIndex']);
    if ( $thisPage < 1 ) $thisPage = 1;
    while ( $pageNo <= $pages ) {
      $ret .= "<option value='" . $pageNo . "'" . ( $pages == 1 ? " disabled" : "" ) . ( $thisPage == $pageNo ? " selected" : "" ) . " />" . $pageNo . "</option>\r\n";
      $pageNo += 1;
    }

    $ret .= "</select>\r\n";
    $ret .= "</td><td>&nbsp;</td></tr>\r\n";
    $ret .= "</form>\r\n";
  }

  $ret .= "<tr><td colspan='5'><hr/></td></tr>\r\n";

  $ret .= "<form id='messagebox' name='messagebox' action='" . $SCRIPT_FILE . "' method='post'>\r\n";
  $ret .= inputType('hidden', 'uuid', $session['uuid']) . "\r\n";
  $ret .= inputType('hidden', 'debugOnly', $session['debugOnly'] ? '1' : '0') . "\r\n";
  $ret .= inputType('hidden', 'page', 'send') . "\r\n";
  if ( !empty($session['targetId']))
    $ret .= inputType('hidden', 'targetId', $session['targetId']) . "\r\n";
  $ret .= "<tr class='radio'>\r\n";
  $ret .= "<td class='label'><label for='sound'>Sound:</label></td>\r\n";
  $ret .= "<td class='radiobtn' colspan='4'><input type='radio' name='payloadSound' value='default' checked='checked'>&nbsp;Default</input>\r\n";
  $ret .= "</tr><tr>\r\n";
  $ret .= "<tr class='radio'><td class='label'>&nbsp;</td><td class='radiobtn' colspan='4'><input type='radio' name='payloadSound' value=''>&nbsp;None</input>\r\n";
  $ret .= "</td></tr>\r\n";
  $ret .= "<tr class='radio'><td class='label'>&nbsp;</td><td class='radiobtn'><input type='radio' name='payloadSound' value='-'>&nbsp;Custom</input>\r\n";
  $ret .= "</td><td class='field' colspan='2'>\r\n";
  $ret .= inputType('text', 'soundName', '', [ 'placeholder' => 'custom sound', 'class' => 'messageField', 'autocomplete' => 'off' ]);
  $ret .= "</td><td>&nbsp;</td></tr>\r\n";
  $ret .= "<tr class='inputField'><td class='label'>\r\n";
  $ret .= "Badge:\r\n";
  $ret .= "</td><td class='field'>\r\n";
  $ret .= inputType('text', 'payloadBadge', '', [ 'placeholder' => 'badge number', 'class' => 'messageField', 'autocomplete' => 'off' ]);
  $ret .= "</td><td colspan='3'>&nbsp;</td></tr>";
  $ret .= "<tr class='inputField'><td class='label'>\r\n";
  $ret .= "Title:\r\n";
  $ret .= "</td><td class='field'>\r\n";
  $ret .= inputType('text', 'payloadTitle', '',  [ 'placeholder' => 'Notification title', 'class' => 'messageField', 'autocomplete' => 'off' ]) . "\r\n";
  $ret .= "</td><td colspan='2'>&nbsp;</td></tr>\r\n";
  $ret .= "<tr class='inputField'><td class='label'>\r\n";
  $ret .= "Message:\r\n";
  $ret .= "</td><td class='field'>\r\n";
  $ret .= inputType('text', 'payloadMsg', '', [ 'placeholder' => 'message', 'class' => 'messageField', 'autocomplete' => 'off', 'required' => true ]) . "\r\n";
  $ret .= "</td><td>&nbsp;</td><td class='submitbtn'>\r\n";

  $tokens = [];
  if ( empty($session['targetId']))
    $tokens = $db -> getTokens($session['product'], $session['debugOnly'] ? 1 : 0, $thisPage - 1);
  else $tokens = $db -> getToken($session['targetId']);

  if ( count($tokens) == 0 || $certError )
    $ret .= inputType('submit', 'send', 'send', [ 'class' => 'sendBtn', 'disabled' => true ]) . "\r\n";
  else
    $ret .= inputType('submit', 'send', 'send', [ 'class' => 'sendBtn' ]) . "\r\n";

  $ret .= "</td></tr>\r\n";
  $ret .= "</form>\r\n";
  $ret .= "</table>\r\n";

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

  $ret .= "</div></div>\r\n";
  return $ret;
}

?>
