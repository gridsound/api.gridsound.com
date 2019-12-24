<?php

error_reporting( -1 );

require_once( 'common/sendJSON.php' );
require_once( 'common/parsePOST.php' );
require_once( 'common/enableCors.php' );
require_once( 'common/connectOrDie.php' );

enableCors();
session_start();

$POST = parsePOST();
$POSTid = $POST->id ?? null;
$me = $_SESSION[ 'me' ] ?? null;

if ( !$me ) {
	sendJSON( 401, 'user:not-connected' );
} else if ( $me->emailchecked !== '1' ) {
	sendJSON( 403, 'email:not-verified' );
} else if ( !$POSTid ) {
	sendJSON( 400, 'query:bad-format' );
}

$mysqli = connectOrDie();
$id = $mysqli->real_escape_string( $POSTid );
$iduser = $mysqli->real_escape_string( $me->id );
$res = $mysqli->query( "DELETE FROM `compositions` WHERE `id` = '$id' AND `iduser` = '$iduser'" );
$err = $mysqli->error;
$deleted = $mysqli->affected_rows > 0;
$mysqli->close();

if ( !$res ) {
	sendJSON( 500, $err );
}
sendJSON( $deleted ? 200 : 404 );
