<?php

error_reporting( -1 );

require_once( 'common/sendJSON.php' );
require_once( 'common/enableCors.php' );

enableCors();

session_start();
if ( isset( $_SESSION[ 'me' ] ) ) {
	sendJSON( 200, $_SESSION[ 'me' ] );
}

$_POST = json_decode( file_get_contents( 'php://input' ), true );
$POSTpass = $_POST[ 'pass' ] ?? '';
$POSTemail = $_POST[ 'email' ] ?? '';
$POSTusername = $_POST[ 'username' ] ?? '';

if (
	mb_strlen( $POSTpass ) < 6 ||
	mb_strlen( $POSTusername ) < 4 ||
	!filter_var( $POSTemail, FILTER_VALIDATE_EMAIL )
) {
	sendJSON( 400 );
}

require_once( 'common/connection.php' );
require_once( 'common/addThingToVerify.php' );
require_once( 'common/sendEmail.php' );
require_once( 'common/uuid.php' );

$id = uuid();
$pass = password_hash( $POSTpass, PASSWORD_BCRYPT );
$email = $mysqli->real_escape_string( $POSTemail );
$username = $mysqli->real_escape_string( $POSTusername );
$res = $mysqli->query( "INSERT INTO `users` (
	`id`,  `status`,          `email`,  `pass`,  `username`, `created` ) VALUES (
	'$id', 'EMAIL_TO_VERIFY', '$email', '$pass', '$username', NOW() )" );

if ( $res ) {
	$code = addThingToVerify( $mysqli, $id, $email );
	$mysqli->close();
	if ( $code ) {
		sendEmail( $email, 'Email confirmation',
			"Hi $username,\r\n\r\n" .
			"Welcome to GridSound !\r\n" .
			"Clicking that link will confirm your email :\r\n" .
			"https://api.gridsound.com/verify?id=$id&data=$email&code=$code"
		);
	}
	$_SESSION[ 'me' ] = ( object )[
		'id' => $id,
		'email' => $email,
		'username' => $username,
	];
	sendJSON( 201, $_SESSION[ 'me' ] );
} else {
	sendJSON( 500, $mysqli->error );
}
