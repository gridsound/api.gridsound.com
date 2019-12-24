<?php

error_reporting( -1 );

require_once( 'common/getUser.php' );
require_once( 'common/sendJSON.php' );
require_once( 'common/enableCors.php' );
require_once( 'common/connectOrDie.php' );
require_once( 'common/getComposition.php' );

enableCors();

$cmpId = $_GET[ 'id' ] ?? null;
if ( !$cmpId ) {
	sendJSON( 400, 'query:bad-format' );
}

$mysqli = connectOrDie();
$cmp = getComposition( $mysqli, $cmpId );
if ( $cmp === null ) {
	sendJSON( 500, $mysqli->error );
}
if ( $cmp === false ) {
	sendJSON( 404 );
}

session_start();
$me = $_SESSION[ 'me' ] ?? null;
$mine = $me && $cmp->iduser === $me->id;

if ( $mine ) {
	$user = $me;
} else {
	if ( $cmp->public === '0' ) {
		sendJSON( 403 );
	}
	$user = getUser( $mysqli, 'id', $cmp->iduser );
}

sendJSON( 200, ( object )[
	'user' => $user,
	'composition' => $cmp,
] );
