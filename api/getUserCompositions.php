<?php

error_reporting( -1 );

require_once( 'common/sendJSON.php' );

$GETid = $_GET[ 'id' ] ?? null;

if ( !$GETid ) {
	sendJSON( 400 );
}

require_once( 'common/connection.php' );

$id = $mysqli->real_escape_string( $GETid );
$res = $mysqli->query( "SELECT `id`, `data`
	FROM `compositions` WHERE
	`iduser` = '$id' AND
	`public` = 1" );

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
