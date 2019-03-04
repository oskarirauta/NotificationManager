<?php

global $ROOT_DIR;

require_once $ROOT_DIR . '/inc/generic.php';

class MyDB extends SQLite3 {

  function __construct() {

    global $ROOT_DIR;

    $db_path = $ROOT_DIR . '/tokens.db';

    $this -> open($db_path, SQLITE3_OPEN_CREATE | SQLITE3_OPEN_READWRITE);
    $this -> busyTimeout(12500);

    $sql = <<<EOF
CREATE TABLE IF NOT EXISTS entries (
  id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
  timestamp REAL,
  product TEXT,
  version TEXT,
  build INTEGER,
  debug INTEGER,
  uuid TEXT,
  token TEXT,
  invalid INTEGER
);
EOF;

    $sql2 = <<<EOF
CREATE TABLE IF NOT EXISTS sessions (
  id integer PRIMARY KEY AUTOINCREMENT NOT NULL,
  timestamp REAL,
  uuid TEXT,
  name TEXT,
  product TEXT
);
EOF;

    $this -> exec('BEGIN;');
    if (!$this -> exec($sql)) {
      error($this -> lastErrorMsg());
    }
    if (!$this -> exec($sql2)) {
      error($this -> lastErrorMsg());
    }

    $this -> exec("DELETE FROM entries WHERE timestamp < julianday('now', '-4 months') or invalid = 1;");
    $this -> exec("DELETE FROM sessions WHERE timestamp < julianday('now', '-18 minutes');");
    $this -> exec("DELETE FROM entries WHERE id NOT IN (SELECT MAX(id) FROM entries GROUP BY product, debug, uuid, token, invalid);");
    $this -> exec("DELETE FROM sessions WHERE id NOT IN (SELECT MAX(id) FROM sessions GROUP BY uuid, name, product);");

    $this -> exec('COMMIT;');
  }

  function create_session($product, $name) {

    $uuid = newuuid();
    while ( true ) {
      $count = $this -> querySingle("SELECT COUNT(*) FROM sessions AS id WHERE uuid = '" . $uuid . "';");
      if ( $count == 0 ) {
        break;
      } else {
        $old_uuid = $uuid;
        while ( $uuid == $old_uuid ) { $uuid = newuuid(); }
      }
    }

    $this -> exec("INSERT INTO sessions (timestamp, uuid, name, product) VALUES (julianday('now'), '" . $uuid . "', '" . $name . "', '" . $product . "');");
    return $uuid;
  }

  function validate_session($uuid) {

    $count = $this -> querySingle("SELECT COUNT(*) FROM sessions AS id WHERE uuid = '" . $uuid . "';");
    if ( $count != 0 ) {
      $this -> exec("UPDATE sessions SET timestamp = julianday('now') WHERE uuid = '" . $uuid . "';");
      return true;
    } else return false;
  }

  function logout($uuid) {
    $this -> exec("DELETE FROM sessions WHERE uuid = '" . $uuid . "';");
  }

  function validate_name($uuid) {
    $name = $this -> querySingle("SELECT name from sessions WHERE uuid = '" . $uuid . "';");
    return $name;
  }

  function validate_product($uuid) {
    $product = $this -> querySingle("SELECT product FROM sessions WHERE uuid = '" . $uuid . "';");
    return $product;
  }

  function addToken($uuid, $product, $token, $version, $build = 0, $debugmode = 0) {
    $ret = 'failure';
    $invalid = 0;
    if ( $this -> idExists($product, $uuid, $debugmode)) {
        $ret = 'updated';
        $this -> exec("UPDATE entries SET debug = " . $debugmode . ", uuid = '" . $uuid . "', token = '" . $token . "', version = '" . $version . "', build = " . $build . ", timestamp = julianday('now') WHERE uuid = '" . $uuid . "';");
    } else {
        $ret = 'inserted';
        $this -> exec("INSERT INTO entries (timestamp, product, debug, uuid, token, version, build, invalid) VALUES (julianday('now'), '" . $product . "', " . $debugmode . ", '" . $uuid . "', '" . $token . "', '" . $version . "', " . $build . ", " . $invalid . ");");
    }
    return $ret;
  }

  function idExists($product, $uuid, $debugmode) {
    return $this -> querySingle("SELECT COUNT(*) FROM entries AS uuid WHERE uuid = '" . $uuid . "' AND product = '" . $product . "' AND debug = " . $debugmode . ";") > 0 ? true : false;
  }

  function amountOf($product, $debugmode) {
    $ret = 0;
    $ret = $this -> querySingle("SELECT COUNT(*) FROM entries AS uuid WHERE product = '" . $product . "' AND debug = " . $debugmode . ";");
    return $ret;
  }

  function getAllTokens($product, $debugmode) {
    $tokens = $this -> query("SELECT *, date(timestamp), time(timestamp) FROM entries WHERE product = '" . $product . "' AND debug = " . $debugmode . " ORDER BY build DESC, timestamp DESC, id ASC;");
    $ret = [];
    while ( $row = $tokens -> fetchArray())
      array_push($ret, [
        'id' => $row['id'],
        'timestamp' => $row[9] . " " . $row[10],
        'product' => $row['product'],
        'version' => $row['version'],
        'build' => $row['build'],
        'debug' => $row['debug'] == 0 ? false : true,
        'uuid' => $row['uuid'],
        'token' => $row['token'],
        'invalid' => $row['invalid'] == 0 ? false : true
      ]);
    return $ret;
  }

  function getToken($id) {
    $tokens = $this -> query("SELECT *, date(timestamp), time(timestamp) FROM entries WHERE id = " . $id . " ORDER BY build DESC, timestamp DESC, id ASC;");
    $ret = [];
    while ( $row = $tokens -> fetchArray())
      array_push($ret, [
        'id' => $row['id'],
        'timestamp' => $row[9] . " " . $row[10],
        'product' => $row['product'],
        'version' => $row['version'],
        'build' => $row['build'],
        'debug' => $row['debug'] == 0 ? false : true,
        'uuid' => $row['uuid'],
        'token' => $row['token'],
        'invalid' => $row['invalid'] == 0 ? false : true
      ]);
    return $ret;
  }

  function getTokens($product, $debugmode, $pageIndex = 0) {
    $tokens = $this -> query("SELECT *, date(timestamp), time(timestamp) FROM entries WHERE product = '" . $product . "' AND debug = " . $debugmode . " ORDER BY build DESC, timestamp DESC, id ASC LIMIT 25" . ( $pageIndex == 0 ? "" : ( " OFFSET " . strval( $pageIndex * 25 ))) . ";");
    $ret = [];
    while ( $row = $tokens -> fetchArray())
      array_push($ret, [
        'id' => $row['id'],
        'timestamp' => $row[9] . " " . $row[10],
        'product' => $row['product'],
        'version' => $row['version'],
        'build' => $row['build'],
        'debug' => $row['debug'] == 0 ? false : true,
        'uuid' => $row['uuid'],
        'token' => $row['token'],
        'invalid' => $row['invalid'] == 0 ? false : true
      ]);
    return $ret;
  }

  function pageCount($product, $debugmode) {
    return ceil($this -> amountOf($product, $debugmode) / 25);
  }

  function markInvalid($id) {
    $this -> exec("UPDATE entries SET invalid = 1 WHERE id = '" . $id . "';");
  }

  function deleteToken($id) {
    $this -> exec("DELETE FROM entries WHERE id = '" . $id . "';");
  }

}

function closeDB() {
  global $db;
  if ( isset($db))
    $db -> close();
}

register_shutdown_function('closeDB');

?>
