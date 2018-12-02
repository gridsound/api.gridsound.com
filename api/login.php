<?php

error_reporting( -1 );

session_start();
if ( isset( $_SESSION[ 'me' ] ) ) {
	header( 'Content-Type: application/json' );
	die( json_encode( $_SESSION[ 'me' ] ) );
}

$POSTemail = $_POST[ 'email' ] ?? null;
$POSTpass = $_POST[ 'pass' ] ?? null;

if ( !$POSTemail || !$POSTpass ) {
	http_response_code( 400 );
	die();
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
		header( 'Content-Type: application/json' );
		echo json_encode( $ret );
	} else {
		http_response_code( 401 );
		die();
	}
} else {
	http_response_code( 500 );
	die( $mysqli->error );
}
