<?php

error_reporting( -1 );

require_once( 'common/sendJSON.php' );
require_once( 'common/enableCors.php' );

enableCors();
session_start();

$me = $_SESSION[ 'me' ] ?? null;
$_POST = json_decode( file_get_contents( 'php://input' ), true );
$POSTcmp = json_decode( $_POST[ 'composition' ] ?? null );

if ( !$me ) {
	sendJSON( 401 );
} else if ( $me->user->emailchecked !== '1' ) {
	sendJSON( 401, 'email-not-verified' );
} else if ( !$POSTcmp ) {
	sendJSON( 400 );
}

require_once( 'common/connection.php' );

$id = $POSTcmp->id;
$iduser = $me->user->id;
$data = json_encode( $POSTcmp );
$res = $mysqli->query( "INSERT INTO `compositions` (
	`id`,  `iduser`,  `data`, `created`, `updated` ) VALUES (
	'$id', '$iduser', '$data', NOW(),     NOW() )" );

if ( $res ) {
	$mysqli->close();
	sendJSON( 200 );
} else {
	sendJSON( 500, $mysqli->error );
}
