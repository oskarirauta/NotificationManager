<?php

$ROOT_DIR = realpath(__DIR__);
$SCRIPT_FILE = basename(__FILE__);

require $ROOT_DIR . '/inc/construct.php';

/*
 * Read Error Response when sending Apple Enhanced Push Notification
 *
 * This assumes your iOS devices have the proper code to add their device tokens
 * to the db and also the proper code to receive push notifications when sent.
 *
 */

if ( empty($session['targetId']) || empty($session['deviceToken']) || empty($session['payloadMsg']) || empty($session['product'])) {
  print("target ID: " . $session['targetId'] . "\r\n");
  print("device Token: " . $session['deviceToken'] . "\r\n");
  print("payloadMsg: " . $session['payloadMsg'] . "\r\n");
  print("product: " . $session['product'] . "\r\n");
  print("Error code -1: Incorrect parameters\r\n");
  return;
}

$certFile = $ROOT_DIR . '/certs/' . $session['product'] . ( $session['debugOnly'] ? '_debug' : '' ) . '.pem';
$certPassword = $products[$session['product']]['cert'];
$server = 'ssl://gateway.' . ( $session['debugOnly'] ? 'sandbox.' : '' ) . 'push.apple.com:2195';

// Setup notification message
$body['aps'] = array(
  'alert' => array(
    'title' => '',
    'body' => $session['payloadMsg']),
  'sound' => 'default'
);

if ( !empty($session['payloadTitle'])) $body['aps']['alert']['title'] = $session['payloadTitle'];
if ( !empty($session['notifyURL'])) $body['aps']['notifyurl'] = $session['notifyURL'];
if ( !empty($session['payloadBadge'])) $body['aps']['badge'] = $session['payloadBadge'];
if ( !empty($session['payloadSound'])) $body['aps']['sound'] = $session['payloadSound'];

// Setup stream (connect to Apple Push Server)
$ctx = stream_context_create();
stream_context_set_option($ctx, 'ssl', 'passphrase', $certPassword);
stream_context_set_option($ctx, 'ssl', 'local_cert', $certFile);
$fp = stream_socket_client($server, $err, $errstr, 60, STREAM_CLIENT_CONNECT, $ctx);

if ( !$fp ) { // ERROR
  print("Error code -2: Failed to connect (stream_socket_client)\r\n");
} else {
  stream_set_blocking ($fp, 0); //This allows fread() to return right away when there are no errors. But it can also miss errors during last seconds of sending, as there is a delay before error is returned. Workaround is to pause briefly AFTER sending last notification, and then do one more fread() to see if anything else is there.
  $apple_expiry = time() + (8 * 24 * 60 * 60); //Keep push alive (waiting for delivery) for 8 days

  $apple_identifier = 1;
  $payload = json_encode($body);

  // Enhanced Notification
  $msg = pack("C", 1) . pack("N", $apple_identifier) . pack("N", $apple_expiry) . pack("n", 32) . pack('H*', str_replace(' ', '', $session['deviceToken'])) . pack("n", strlen($payload)) . $payload;

  // SEND PUSH
  fwrite($fp, $msg);

  // We can check if an error has been returned while we are sending, but we also need to check once more after we are done sending in case there was a delay with error response.
  $error1 = checkAppleErrorResponse($fp, $session['targetId']);

  // Workaround to check if there were any errors during the last seconds of sending.
  usleep(500000); //Pause for half a second. Note I tested this with up to a 5 minute pause, and the error message was still available to be retrieved
  $error2 = checkAppleErrorResponse($fp, $session['targetId']);

  if ( empty($error1) && empty($error2))
    print("OK");
  else
    print(empty($error1) ? $error2 : $error1);

  print("\r\n<br/>\r\n");
  fclose($fp);
}

// FUNCTION to check if there is an error response from Apple
// Returns empty string if no error. Otherwise string contains error message.
function checkAppleErrorResponse($fp, $row_id) {

  global $db;
  $retval = '';

  // byte1=always 8, byte2=StatusCode, bytes3,4,5,6=identifier(rowID). Should return nothing if OK.
  $apple_error_response = fread($fp, 6);
  // NOTE: Make sure you set stream_set_blocking($fp, 0) or else fread will pause your script and wait forever when there is no response to be sent.

  if ( $apple_error_response ) { // unpack the error response (first byte 'command" should always be 8)
    $error_response = unpack('Ccommand/Cstatus_code/Nidentifier', $apple_error_response);

    $retval = 'Error code ';

    switch ( $error_response['status_code'] ) {
      case 0:
        $retval .= '0: No errors encountered';
        break;
      case 1:
        $retval .= '1: Processing error';
        break;
      case 2:
        $retval .= '2: Missing device token';
        break;
      case 3:
        $retval .= '3: Missing topic';
        break;
      case 4:
        $retval .= '4: Missing payload';
        break;
      case 5:
        $retval .= '5: Invalid token size';
        break;
      case 6:
        $retval .= '6: Invalid topic size';
        break;
      case 7:
        $retval .= '7: Invalid payload size';
        break;
      case 8:
        $db -> markInvalid($row_id);
        $retval .= '8: Invalid token';
        break;
      case 255:
        $retval .= '255: None / Unknown';
        break;
      default:
        $retval .= $error_response['status_code'] . ': Not listed';
    }
    $retval .= ' [row: ' . $row_id . ']';
  }
  return $retval;
}

?>
