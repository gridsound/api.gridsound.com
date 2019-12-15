<?php

error_reporting( -1 );

require_once( 'common/sendJSON.php' );
require_once( 'common/enableCors.php' );

enableCors();

$GETid = $_GET[ 'id' ] ?? null;
if ( !$GETid ) {
	sendJSON( 400, 'query:bad-format' );
}

session_start();
$me = $_SESSION[ 'me' ] ?? null;
$itsMe = $me && $GETid === $me->id;

require_once( 'common/connection.php' );
require_once( 'common/getUserCompositions.php' );

$cmps = getUserCompositions( $mysqli, $GETid, !$itsMe );
$err = $mysqli->error;
$mysqli->close();
if ( $cmps === null ) {
	sendJSON( 500, $err );
}
sendJSON( 200, $cmps );
