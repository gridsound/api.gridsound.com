<?php

error_reporting( -1 );

require_once( 'common/sendJSON.php' );
require_once( 'common/enableCors.php' );

enableCors();
session_start();

$_POST = json_decode( file_get_contents( 'php://input' ), true );
$POSTid = $_POST[ 'id' ] ?? null;
$me = $_SESSION[ 'me' ] ?? null;

if ( !$me ) {
	sendJSON( 401, 'user:not-connected' );
} else if ( $me->user->emailchecked !== '1' ) {
	sendJSON( 403, 'email:not-verified' );
} else if ( !$POSTid ) {
	sendJSON( 400, 'query:bad-format' );
}

require_once( 'common/connection.php' );

$id = $mysqli->real_escape_string( $POSTid );
$iduser = $mysqli->real_escape_string( $me->user->id );
$res = $mysqli->query( "DELETE FROM `compositions`
	WHERE `id` = '$id'
	AND `iduser` = '$iduser'" );

if ( $res ) {
	$deleted = $mysqli->affected_rows > 0;
	$mysqli->close();
	if ( $deleted ) {
		$ind = array_search( $id, array_column( $me->compositions, 'id' ), true );
		array_splice( $me->compositions, $ind );
		sendJSON( 200 );
	} else {
		sendJSON( 404 );
	}
} else {
	sendJSON( 500, $mysqli->error );
}
