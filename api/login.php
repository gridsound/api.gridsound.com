<?php

error_reporting( -1 );

session_start();
if ( isset( $_SESSION[ 'me' ] ) ) {
	header( 'Content-Type: application/json' );
	die( json_encode( $_SESSION[ 'me' ] ) );
}

$POSTusername = $_POST[ 'username' ] ?? null;
$POSTpass = $_POST[ 'pass' ] ?? null;

if ( !$POSTusername || !$POSTpass ) {
	http_response_code( 400 );
	die();
}

require_once( 'common/connection.php' );

$username = $mysqli->real_escape_string( $POSTusername );
$res = $mysqli->query( "SELECT `id`, `pass`, `email`, `firstname`, `lastname`, `username`
	FROM `users` WHERE `username`='$username'" );

if ( $res ) {
	$ret = $res->fetch_object();
	$res->free();
	$mysqli->close();
	if ( password_verify( $POSTpass, $ret->pass ) ) {
		unset( $ret->pass );
		$_SESSION[ 'me' ] = $ret;
		header( 'Content-Type: application/json' );
		echo json_encode( $ret );
	}
} else {
	http_response_code( 500 );
	die( $mysqli->error );
}
