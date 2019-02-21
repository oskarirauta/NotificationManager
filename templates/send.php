<?php

global $ROOT_DIR;

require_once $ROOT_DIR . '/inc/generic.php';
require_once $ROOT_DIR . '/inc/db.php';
require_once $ROOT_DIR . '/templates/progress.php';

function generate_script($tokens) {

  global $page, $session, $SCRIPT_FILE;

  $script  = "<script type='text/javascript'>\r\n";
  $script .= "var sessionId = '" . $session['uuid'] . "';\r\n";
  $script .= "var reqId = " . time() . ";\r\n";
  $script .= "var debugMode = " . ( $session['debugOnly'] ? "1" : "0" ) . ";\r\n";
  $script .= "var tokenCount = " . count($tokens) . ";\r\n";
  $script .= "var msg = '" . $session['payload'] . "';\r\n";
  $script .= "var tokenList = [\r\n";

  $lastId = end($tokens)['id'];

  foreach ( $tokens as $token ) {
    $script .= " [" . $token['id'] . ", " . $token['token'] . "]";
    $script .= $token['id'] == $lastId ? "];\r\n\n" : ",\r\n";
  }

  $script .= <<<EOF

var progressEl = document.getElementById('progress');
var percentageEl = document.getElementById('percentage');
var captionEl = document.getElementById('caption');
var doneBtnEl = document.getElementById('doneBtn');

function beginSending() {
  captionEl.style.display = "block";
  doneBtnEl.style.display = "none";
  progressEl.value = 0;
  progressEl.max = tokenCount;
  percentageEl.innerHtml = "0%";
  send();
}

function endSending() {
  captionEl.style.display = "none";
  doneBtnEl.style.display = "block";
}

/*
async function delay(delayInms) {
  return new Promise(resolve  => {
    setTimeout(() => {
      resolve(2);
    }, delayInms);
  });
}

async function send() {
*/

function send() {

  if ( tokenList.length > 0 ) {
    var thisToken = tokenList.shift();
    //let delayres = await delay(100);
    
    nanoajax.ajax({url:'./msg_push.php?uuid=' + sessionId + '&debugOnly=' + debugMode + '&targetId=' + thisToken['0'] + '&debugMode=' + debugMode + '&deviceToken=' + thisToken[1] + '&payload=' + msg}, function (code, responseText) {

      progressEl.value = progressEl.value + 1;
      percentageEl.innerHTML = Math.floor(( progressEl.value / progressEl.max ) * 100 ) + "%";

      if ( code == 200 ) {
        send();
      } else { // Report errors?
        send();
      }
    });
  } else {
    endSending();
  }
}

var interval = setInterval(function() {
  if ( document.readyState === 'complete' ) {
    clearInterval(interval);

    progressEl = document.getElementById('progress');
    percentageEl = document.getElementById('percentage');
    captionEl = document.getElementById('caption');
    doneBtnEl = document.getElementById('doneBtn');

    progressEl.value = 0;
    progressEl.max = tokenCount;
    percentageEl.innerHTML = "0%";
    beginSending();
  }
}, 100);

EOF;

  $script .= "</script>";

  array_push($page['headers'], "<script src='./js/nanoajax.min.js' type='text/javascript'></script>");
  array_push($page['headers'], $script);
}

function generate_page() {

  global $session, $page, $db;
  if ( !$session['ssl'] ) {
    $page = notSSL();
    return;
  }

  $tokens = empty($session['targetId']) ? $db -> getAllTokens($session['product'], $session['debugOnly'] ? 1 : 0) : [ $db -> getToken($session['targetId']) ];

  $idCount = $db -> amountOf($session['product'], $session['debugOnly'] ? 1 : 0);
  $page['title'] = 'Notification system - Sending';

  generate_script($tokens);

  $page['body'] .= "<div class='sendui'>\r\n";
  $page['body'] .= "<div class='title'>Sending message.</div>\r\n";
  $page['body'] .= "<div class='productname'>Product: <b>" . $session['product'] . "</b>";
  $page['body'] .= "<small style='margin-left: 6px;'>" . ( $session['debugOnly'] ? "[<u>DEBUG</u>]" : "[<u>PRODUCTION</u>]" ) . "</small>";
  $page['body'] .= "</div>\r\n";
  $page['body'] .= "<div class='count'>Sending to " . ( empty($session['targetId']) ? strval($idCount) : "1" ) . " devices.</div>\r\n";
  $page['body'] .= "<div class='msgpreview'>Message: " . $session['payload'] . "</div>\r\n";

  $page['body'] .= "</div>";

  $page['body'] .= progressView();

}

?>
