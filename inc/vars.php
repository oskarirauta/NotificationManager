<?php

global $ROOT_DIR;

require_once $ROOT_DIR . '/inc/generic.php';

$session = [
  'ssl' => /*is_ssl()*/true,
  'uuid' => '',
  'name' => '',
  'validated' => false,
  'error' => '',
  'product' => '',
  'page' => '',
  'pageIndex' => 0,
  'debugOnly' => false,
  'targetId' => '',
  'deviceToken' => '',
  'version' => '',
  'build' => '',
  'password' => '',
  'deleteToken' => false,
  'invalid' => 0,
  'payloadTitle' => '',
  'payloadMsg' => '',
  'payloadBadge' => '',
  'payloadSound' => '',
  'notifyURL' => ''
];

$page = [
  'title' => '',
  'headers' => [],
  'body' => ''
];

?>
