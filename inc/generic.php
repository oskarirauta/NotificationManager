<?php

function is_ssl() {
  global $_SERVER;

  if ( isset($_SERVER['HTTPS']) ) {
    if ( 'on' == strtolower($_SERVER['HTTPS']) or '1' == $_SERVER['HTTPS'] )
      return true;
  } elseif ( isset($_SERVER['SERVER_PORT']) && ( '443' == $_SERVER['SERVER_PORT'] ) )
    return true;
  return false;
}

function guidv4($data) {

  assert(strlen($data) == 16);
  $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
  $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10
  return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}

function newuuid() {
  return guidv4(random_bytes(16));
}

function notSSL() {
  return [
    'title' => 'Error',
    'headers' => [],
    'body' => 'SSL connection is required.'
  ];
}

function pageData() {
  global $page;
  return "<HTML>\r\n<HEAD>\r\n<TITLE>" . $page['title'] . "</TITLE>\r\n" . join("\r\n", $page['headers']) . "\r\n</HEAD><BODY>\r\n" . $page['body'] . "\r\n</BODY>\n</HTML>";
}

function inputType($type, $name, $value = '', $attributes = []) {
  $required = false;
  $ret = "<INPUT TYPE='" . $type . "' name='" . $name . "' value='" . $value . "'";
  foreach ( $attributes as $key => $val ) {
    if ( $key == 'required' ) $required = true;
    else $ret .= " " . $key . "='" . $val . "'";
  }
  return $ret . ( $required ? ' required' : '' ) . " />";
}

function submitButton($name = '', $value = '', $attributes = []) {
  $ret = "<BUTTON TYPE='submit'";
  if ( !empty($name)) $ret .= " name='" . $name . "'";
  if ( !empty($value)) $ret .= " value='" . $value . "'";
  foreach ( $attributes as $key => $val )
    $ret .= " " . $key .= "='" . $val . "'";
  return $ret . " />";
}

?>
