<?php

error_reporting( -1 );

require_once( 'common/sendJSON.php' );
require_once( 'common/enableCors.php' );

enableCors();

$GETusername = $_GET[ 'username' ] ?? null;

if ( !$GETusername ) {
	sendJSON( 400 );
}

require_once( 'common/connection.php' );
require_once( 'common/getCompositions.php' );

$username = $mysqli->real_escape_string( $GETusername );
$res = $mysqli->query( "SELECT `id`, `emailpublic`,
	`firstname`, `lastname`, `username`, `avatar`
	FROM `users` WHERE `username`='$username'" );

if ( $res ) {
	$user = $res->fetch_object();
	$userfound = $mysqli->affected_rows > 0;
	$res->free();
	$cmps = $userfound
		? getCompositions( $mysqli, $user->id, true )
		: null;
	$error = $mysqli->error;
	$mysqli->close();
	if ( !$userfound ) {
		sendJSON( 404 );
	} else if ( $cmps === null ) {
		sendJSON( 500, $error );
	} else {
		sendJSON( 200, ( object )[
			'user' => $user,
			'compositions' => $cmps,
		] );
	}
} else {
	sendJSON( 500, $mysqli->error );
}
