<?php

function ask_hidden( $prompt ) {
	echo $prompt;
	echo "\033[30;40m";  // black text on black background
	$input = fgets( STDIN );
	echo "\033[0m";      // reset
	return rtrim( $input, "\n" );
}

$password1 = ask_hidden("Enter password: ");
$password2 = ask_hidden("Re-enter password: ");

if ( $password1 != $password2 ) {
  print("Error: password's do not match.\r\n");
  return -1;
} elseif ( strlen($password1) == 0 ) {
  print("Error: password cannot be empty.\r\n");
  return -1;
} elseif ( strlen($password1) < 6 ) {
  print("Warning: password should have atleast 6 letters.\r\n");
}

$hash = password_hash($password1, PASSWORD_DEFAULT);
print("Hash for entered password is: " . $hash . "\r\n");

?>
