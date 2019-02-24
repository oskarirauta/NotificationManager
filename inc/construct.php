<?php

header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
header("Pragma: no-cache"); // HTTP 1.0.
header("Expires: 0"); // Proxies.

global $ROOT_DIR;

require_once $ROOT_DIR . '/inc/settings.php';
require_once $ROOT_DIR . '/inc/generic.php';
require_once $ROOT_DIR . '/inc/db.php';
require_once $ROOT_DIR . '/inc/vars.php';

// Fake ssl for debugging purposes
// $session['ssl'] = true;

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
  $session['payload'] = '';
  $session['deviceToken'] = '';
  $session['badge'] = '';
  $session['sound'] = '';
  $session['notifyurl'] = '';
  $session['error'] = 'Invalid session. Timed-out?';
}

$session['name'] = $session['validated'] ? $db -> validate_name($session['uuid']) : '';
$session['product'] = $session['validated'] ? $db -> validate_product($session['uuid']) : '';
$session['page'] = '';
$session['pageIndex'] = getParam('pageIndex') or '';
$session['debugOnly'] = getParam('debugOnly') == '1' ? true : false;
$session['targetId'] = getParam('targetId') or '';
$session['deviceToken'] = getParam('deviceToken') or '';
$session['payload'] = getParam('payload') or '';
$session['badge'] = getParam('badge') or '';
$session['sound'] = getParam('sound') or '';
$session['notifyurl'] = getParam('notifyurl') or '';

if /*( empty($session['uuid']) && empty($session['error']))*/ (true) {
  $userid = getParam('userid') == null ? '' : strtolower(getParam('userid'));
  $password = getParam('passwd') or '';
  $product = getParam('product') == null ? '' : strtolower(getParam('product'));
  $users = array_map('strtolower', array_keys($credentials));

/*
//Entrypoint for debugging issues after login.
  $userid = 'myuser';
  $password = 'mypassword';
  $product = 'myproduct';
*/

  if ( in_array($userid, $users) && password_verify($password, $credentials[$userid]) && !empty($product) && in_array($product, array_keys($products))) {
    $session['uuid'] = $db -> create_session($product);
    $session['name'] = $userid;
    $session['product'] = $product;
    $session['validated'] = true;
  } else if ( !in_array($userid, $users)) {
    $session['error'] = 'Invalid user id.';
  } else if ( empty($product) || !in_array($product, array_keys($products))) {
    $session['error'] = 'Invalid product code.';
  } else if ( !password_verify( $password, $credentials[$userid] )) {
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
