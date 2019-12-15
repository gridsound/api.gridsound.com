<?php

error_reporting( -1 );

require_once( 'common/sendJSON.php' );
require_once( 'common/enableCors.php' );

enableCors();
session_start();

$me = $_SESSION[ 'me' ] ?? null;
$_POST = json_decode( file_get_contents( 'php://input' ), true );
$POSTcmp = $_POST[ 'composition' ] ?? null;
$dataDecoded = json_decode( $POSTcmp );

if ( !$me ) {
	sendJSON( 401, 'user:not-connected' );
} else if ( $me->emailchecked !== '1' ) {
	sendJSON( 403, 'email:not-verified' );
} else if ( !$POSTcmp || !$data ) {
	sendJSON( 400, 'query:bad-format' );
}

require_once( 'common/connection.php' );

$id = $mysqli->real_escape_string( $dataDecoded->id );
$data = $mysqli->real_escape_string( $POSTcmp );
$iduser = $mysqli->real_escape_string( $me->id );
$res = $mysqli->query( "UPDATE `compositions` SET
	`data` = '$data',
	`updated` = NOW()
	WHERE `id` = '$id'
	AND `iduser` = '$iduser'" );

if ( $res && $mysqli->affected_rows === 0 ) {
	$res = $mysqli->query( "INSERT INTO `compositions` (
		`id`,  `iduser`,  `data`, `created`, `updated` ) VALUES (
		'$id', '$iduser', '$data', NOW(),     NOW() )" );
}

if ( $res ) {
	$mysqli->close();
	sendJSON( 200 );
} else {
	sendJSON( 500, $mysqli->error );
}
