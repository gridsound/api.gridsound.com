<?php

error_reporting( -1 );

require_once( 'common/sendJSON.php' );
require_once( 'common/enableCors.php' );

enableCors();

$GETusername = $_GET[ 'username' ] ?? null;

if ( !$GETusername ) {
	sendJSON( 400, 'query:bad-format' );
}

require_once( 'common/connection.php' );
require_once( 'common/getUserCompositions.php' );

$username = $mysqli->real_escape_string( $GETusername );
$res = $mysqli->query( "SELECT
	`id`, `email`, `emailpublic`, `emailchecked`,
	`firstname`, `lastname`, `username`, `avatar`
	FROM `users` WHERE `username`='$username'" );

if ( $res ) {
	$user = $res->fetch_object();
	$userfound = $mysqli->affected_rows > 0;
	$res->free();
	$cmps = $userfound
		? getUserCompositions( $mysqli, $user->id, true )
		: null;
	$error = $mysqli->error;
	$mysqli->close();
	if ( !$userfound ) {
		sendJSON( 404 );
	} else if ( $cmps === null ) {
		sendJSON( 500, $error );
	} else {
		if ( $user->emailpublic === '0' || $user->emailchecked === '0' ) {
			unset( $user->email );
		}
		unset( $user->emailpublic );
		unset( $user->emailchecked );
		sendJSON( 200, ( object )[
			'user' => $user,
			'compositions' => $cmps,
		] );
	}
} else {
	sendJSON( 500, $mysqli->error );
}
