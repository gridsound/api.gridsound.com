<?php

error_reporting( -1 );

require_once( 'common/sendJSON.php' );
require_once( 'common/enableCors.php' );

enableCors();
session_start();

if ( $_SERVER[ 'REQUEST_METHOD' ] !== 'POST' ) {
	sendJSON( 405, "Method Not Allowed" );
}

$me = $_SESSION[ 'me' ] ?? null;
$_POST = json_decode( file_get_contents( 'php://input' ), true );
$POSTcmpid = $_POST[ 'composition_id' ] ?? null;
$POSTstatus = $_POST[ 'status' ] ?? null;

if ( !$me ) {
	sendJSON( 401, 'user:not-connected' );
} else if ( $me->user->emailchecked !== '1' ) {
	sendJSON( 403, 'email:not-verified' );
} else if ( !$POSTcmpid ) {
	sendJSON( 400, 'query:bad-format' );
}

require_once( 'common/connection.php' );

$compo_id = $mysqli->real_escape_string( $POSTcmpid );
$user_id = $mysqli->real_escape_string( $me->user->id );
$res = $mysqli->query( "INSERT INTO `likes` (
	`user_id`,  `composition_id`,  `date` ) VALUES (
	'$user_id', '$compo_id', NOW() )" );

if ( $res ) {
	$mysqli->close();
	sendJSON( 200 );
} else {
	$res = $mysqli->query( "DELETE FROM `likes` WHERE 
	`user_id` = '$user_id' AND `composition_id` = '$compo_id'" );
	if ( $res ) {
		$mysqli->close();
		sendJSON( 200 );
	} else {
		sendJSON( 500, $mysqli->error );
	}
}
