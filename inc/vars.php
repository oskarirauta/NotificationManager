<?php

global $ROOT_DIR;

require_once $ROOT_DIR . '/inc/generic.php';

$first_run = true;

$session = [
  'ssl' => is_ssl(),
  'uuid' => '',
  'validated' => false,
  'error' => '',
  'product' => '',
  'page' => '',
  'pageIndex' => 0,
  'debugOnly' => false,
  'targetId' => '',
  'deviceToken' => '',
  'payloadtitle' => '',
  'payload' => '',
  'badge' => '',
  'sound' => '',
  'notifyurl' => '',
  'version' => '',
  'build' => '',
  'password' => '',
  'deleteToken' => false,
  'invalid' => 0
];

$page = [
  'title' => '',
  'headers' => [],
  'body' => ''
];

?>
