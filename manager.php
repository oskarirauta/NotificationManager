<?php

$ROOT_DIR = realpath(__DIR__);
$SCRIPT_FILE = basename(__FILE__);

require $ROOT_DIR . '/inc/construct.php';
require $ROOT_DIR . '/inc/pageparser.php';

require_once $ROOT_DIR . '/templates/' . $session['page'] . '.php';

generate_page();
print(pageData());

/*
// Debugging output
print("Session: " . $session['uuid'] . "\n");
print("Validated: " . ( $session['validated'] ? "True" : "False" ) . "\n");
print("Product: " . $session['product'] . "\n");
print("Page: " . $session['page'] . "\n");
print("Error: " . $session['error']. "\n");
*/

?>
