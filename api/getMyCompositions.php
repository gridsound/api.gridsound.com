<?php

error_reporting( -1 );

require_once( 'common/sendJSON.php' );

session_start();
$me = $_SESSION[ 'me' ] ?? null;

if ( !$me ) {
	sendJSON( 401 );
}

require_once( 'common/connection.php' );

$res = $mysqli->query( "SELECT `id`, `public`, `data`
	FROM `compositions`
	WHERE `iduser` = '$me->id'" );

if ( $res ) {
	$arr = array();
	while ( $row = $res->fetch_object() ) {
		$arr[] = $row;
	}
	$res->free();
	$mysqli->close();
	sendJSON( 200, $arr );
} else {
	sendJSON( 500, $mysqli->error );
}
