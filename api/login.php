<?php

error_reporting( -1 );

require_once( 'common/sendJSON.php' );

session_start();
if ( isset( $_SESSION[ 'me' ] ) ) {
	sendJSON( 200, $_SESSION[ 'me' ] );
}

$POSTemail = $_POST[ 'email' ] ?? null;
$POSTpass = $_POST[ 'pass' ] ?? null;

if ( !$POSTemail || !$POSTpass ) {
	sendJSON( 400 );
}

require_once( 'common/connection.php' );

$email = $mysqli->real_escape_string( $POSTemail );
$res = $mysqli->query( "SELECT `id`, `pass`, `email`, `firstname`, `lastname`, `username`
	FROM `users` WHERE
	`email` = '$email' OR
	`username` = '$email'" );

if ( $res ) {
	$ret = $mysqli->affected_rows > 0
		? $res->fetch_object()
		: null;
	$res->free();
	$mysqli->close();
	if ( $ret && password_verify( $POSTpass, $ret->pass ) ) {
		unset( $ret->pass );
		$_SESSION[ 'me' ] = $ret;
		sendJSON( 200, $ret );
	} else {
		sendJSON( 401 );
	}
} else {
	sendJSON( 500, $mysqli->error );
}
