<?php

error_reporting( -1 );

require_once( 'common/sendJSON.php' );
require_once( 'common/sendEmail.php' );
require_once( 'common/parsePOST.php' );
require_once( 'common/enableCors.php' );
require_once( 'common/connectOrDie.php' );

enableCors();

$POST = parsePOST();
$POSTcode = $POST->code ?? null;
$POSTpass = $POST->pass ?? '';
$POSTemail = $POST->email ?? null;

if ( !$POSTcode ) {
	sendJSON( 400, 'query:bad-format' );
} if ( !filter_var( $POSTemail, FILTER_VALIDATE_EMAIL ) ) {
	sendJSON( 400, 'email:bad-format' );
} if ( mb_strlen( $POSTpass ) < 6 ) {
	sendJSON( 400, 'pass:too-short' );
}

$mysqli = connectOrDie();
$code = $mysqli->real_escape_string( $POSTcode );
$email = $mysqli->real_escape_string( $POSTemail );
$res = $mysqli->query( "DELETE FROM `passwordForgotten` WHERE
	`email` = '$email' AND
	`code` = '$code'" );

if ( !$res ) {
	sendJSON( 500, $mysqli->error );
} if ( $mysqli->affected_rows < 1 ) {
	sendJSON( 403, 'password:bad-code' );
}

$pass = password_hash( $POSTpass, PASSWORD_BCRYPT );
$res = $mysqli->query( "UPDATE `users` SET `pass` = '$pass' WHERE `email` = '$email'" );

if ( !$res ) {
	sendJSON( 500, $mysqli->error );
} if ( $mysqli->affected_rows > 0 ) {
	sendEmail( $email, 'Password changed',
		"Hi,\r\n\r\n" .
		"Your password has been reset, you can now log in again on " .
		"https://gridsound.com/#/auth"
	);
	sendJSON( 200 );
}
