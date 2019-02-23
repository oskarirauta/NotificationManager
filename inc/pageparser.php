<?php

global $session, $ROOT_DIR;

if ( $session['validated'] && empty($session['error'])) {
  $session['page'] = getParam('page') == null ? '' : strtolower(getParam('page'));
  if ( $session['page'] != 'main' && !file_exists($ROOT_DIR . '/templates/' . $session['page'] . '.php' ))
    $session['page'] = 'main';
} else {
  $session['page'] = 'login';
  $session['pageIndex'] = 0;
  $session['targetId'] = '';
  $session['debugOnly'] = false;
}

if ( $session['page'] == 'main' ) {
  $session['deleteToken'] = getParam('deleteToken') == '1' ? true : false;
  if ( $session['deleteToken'] && !empty($session['targetId'])) {
    $db -> deleteToken($session['targetId']);
    $session['targetId'] = '';
  }
}

?>
