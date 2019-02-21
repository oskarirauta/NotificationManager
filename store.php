<?php

header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
header("Pragma: no-cache"); // HTTP 1.0.
header("Expires: 0"); // Proxies.

$ROOT_DIR = realpath(__DIR__);
$SCRIPT_FILE = basename(__FILE__);

require_once $ROOT_DIR . '/inc/settings.php';
require_once $ROOT_DIR . '/inc/generic.php';
require_once $ROOT_DIR . '/inc/db.php';
require_once $ROOT_DIR . '/inc/vars.php';

// Fake ssl for debugging purposes
//$session['ssl'] = true;

if ( !$session['ssl']) {
  $page = notSSL();
  print(pageData());
  return;
}

function getParam($name) {
  if ( isset($_POST[$name]))
    return $_POST[$name];
  elseif ( isset($_REQUEST[$name]))
    return $_REQUEST[$name];
  elseif ( isset($_GET[$name]))
    return $_GET[$name];
  else return null;
}

$session['product'] = getParam('product') or '';
$session['debugOnly'] = !empty(getParam('debugOnly') or '') ? true : false;
$session['uuid'] = getParam('uuid') or '';
$session['deviceToken'] = getParam('deviceToken') or '';
$session['version'] = getParam('version') or '';
$session['build'] = getParam('build') or '0';
$session['invalid'] = 0;

/*
// Debugging test
$session['product'] = 'myproduct';
$session['debugOnly'] = false;
$session['uuid'] = 2074;
$session['deviceToken'] = 9802;
$session['version'] = '1.2';
$session['build'] = 102;
*/

if ( empty($session['product']) || empty($session['uuid']) || empty($session['deviceToken']) || empty($session['version']) || $session['build'] < 1 ) {
  print('ERROR: parameter mismatch');
  return;
} elseif ( !in_array($session['product'], $products)) {
  print('ERROR: invalid product code');
  return;
}

$db = new MyDB();
$ret = $db -> addToken($session['uuid'], $session['product'], $session['deviceToken'], $session['version'], $session['build'], $session['debugOnly'] ? 1 : 0);

print($ret);

?>
