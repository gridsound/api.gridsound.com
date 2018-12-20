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
$POSTemail = $_POST[ 'email' ] ?? null;
$POSTpass = $_POST[ 'pass' ] ?? null;

if ( !$POSTemail || !$POSTpass ) {
	sendJSON( 400, 'login:fail' );
}

require_once( 'common/connection.php' );
require_once( 'common/getCompositions.php' );

$email = $mysqli->real_escape_string( $POSTemail );
$res = $mysqli->query( "SELECT `id`, `pass`, `email`, `emailpublic`, `status`,
	`firstname`, `lastname`, `username`, `avatar`
	FROM `users` WHERE
	`email` = '$email' OR
	`username` = '$email'" );

if ( $res ) {
	$cmps = null;
	$user = $mysqli->affected_rows > 0
		? $res->fetch_object()
		: null;
	$res->free();
	$authOk = false;
	if ( $user ) {
		$authOk = password_verify( $POSTpass, $user->pass );
		unset( $user->pass );
	}
	if ( $authOk ) {
		$cmps = getCompositions( $mysqli, $user->id, false );
	}
	$mysqli->close();
	if ( !$authOk ) {
		sendJSON( 401, 'login:fail' );
	} else if ( !$res || $cmps === null ) {
		sendJSON( 500, $mysqli->error );
	} else {
		$_SESSION[ 'me' ] = ( object )[
			'user' => $user,
			'compositions' => $cmps,
		];
		sendJSON( 200, $_SESSION[ 'me' ] );
	}
} else {
	sendJSON( 500, $mysqli->error );
}
