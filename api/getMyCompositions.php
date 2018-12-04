<?php

error_reporting( -1 );

session_start();
$me = $_SESSION[ 'me' ] ?? null;

if ( !$me ) {
	http_response_code( 401 );
	die();
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
	header( 'Content-Type: application/json' );
	echo json_encode( $arr );
} else {
	http_response_code( 500 );
	die( $mysqli->error );
}
