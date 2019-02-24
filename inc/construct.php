<?php

header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
header("Pragma: no-cache"); // HTTP 1.0.
header("Expires: 0"); // Proxies.

global $ROOT_DIR;

require_once $ROOT_DIR . '/inc/settings.php';
require_once $ROOT_DIR . '/inc/generic.php';
require_once $ROOT_DIR . '/inc/db.php';
require_once $ROOT_DIR . '/inc/vars.php';

if ( !$session['ssl']) {
  $page = notSSL();
  print(pageData());
  return;
}

$db = new MyDB();

function getParam($name) {
  if ( isset($_POST[$name]))
    return $_POST[$name];
  elseif ( isset($_REQUEST[$name]))
    return $_REQUEST[$name];
  elseif ( isset($_GET[$name]))
    return $_GET[$name];
  else return null;
}

$session['uuid'] = getParam('uuid') or '';
$session['validated'] = empty($session['uuid']) ? false : $db -> validate_session($session['uuid']);

if ( !empty($session['uuid']) && !$session['validated'] ) {
  $session['uuid'] = '';
  $session['name'] = '';
  $session['product'] = '';
  $session['page'] = 'login';
  $session['pageIndex'] = 0;
  $session['targetId'] = '';
  $session['debugOnly'] = false;
  $session['deviceToken'] = '';
  $session['payloadTitle'] = '';
  $session['payloadMsg'] = '';
  $session['payloadBadge'] = '';
  $session['payloadSound'] = '';
  $session['notifyURL'] = '';
  $session['error'] = 'Invalid session. Timed-out?';
}

$session['name'] = $session['validated'] ? $db -> validate_name($session['uuid']) : '';
$session['product'] = $session['validated'] ? $db -> validate_product($session['uuid']) : '';
$session['page'] = '';
$session['pageIndex'] = getParam('pageIndex') or '';
$session['debugOnly'] = getParam('debugOnly') == '1' ? true : false;
$session['targetId'] = getParam('targetId') or '';
$session['deviceToken'] = getParam('deviceToken') or '';
$session['payloadTitle'] = getParam('payloadTitle') or '';
$session['payloadMsg'] = getParam('payloadMsg') or '';
$session['payloadBadge'] = getParam('payloadBadge') or '';
$session['payloadSound'] = getParam('payloadSound') or '';
$session['notifyURL'] = getParam('notifyURL') or '';

if ( $session['payloadSound'] == '-' && !empty(getParam('soundName'))) {
  $session['payloadSound'] = getParam('soundName') or 'default';
}

if ( empty($session['uuid']) && empty($session['error'])) {
  $userid = getParam('userid') == null ? '' : strtolower(getParam('userid'));
  $password = getParam('passwd') or '';
  $product = getParam('product') == null ? '' : strtolower(getParam('product'));
  $users = array_map('strtolower', array_keys($credentials));

  if ( in_array($userid, $users) && password_verify($password, $credentials[$userid]['password']) && !empty($product) && in_array($product, array_keys($products))) {
    $session['uuid'] = $db -> create_session($product, $credentials[$userid]['name']);
    $session['name'] = $credentials[$userid]['name'] or $userid;
    $session['product'] = $product;
    $session['validated'] = true;
  } else if ( !in_array($userid, $users)) {
    $session['error'] = 'Invalid user id.';
  } else if ( empty($product) || !in_array($product, array_keys($products))) {
    $session['error'] = 'Invalid product code.';
  } else if ( !password_verify( $password, $credentials[$userid]['password'] )) {
    $session['error'] = 'Invalid password.';
  }
  if ( !empty($session['error'])) {
    $session['validated'] = false;
    $session['uuid'] = '';
    $session['name'] = '';
    $session['product'] = '';
    if ( empty($userid) && empty($password) && empty($product))
      $session['error'] = '';
  }
}

array_push($page['headers'], "<link rel='stylesheet' type='text/css' href='css/style.css' />");

?>
