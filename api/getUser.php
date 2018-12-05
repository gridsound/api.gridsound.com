<?php

error_reporting( -1 );

require_once( 'common/sendJSON.php' );

$GETid = $_GET[ 'id' ] ?? null;

if ( !$GETid ) {
	sendJSON( 400 );
}

require_once( 'common/connection.php' );

$id = $mysqli->real_escape_string( $GETid );
$res = $mysqli->query( "SELECT `id`, `email`, `firstname`, `lastname`, `username`
	FROM `users` WHERE `id`='$id'" );

if ( $res ) {
	$ret = $res->fetch_object();
	$res->free();
	$mysqli->close();
	sendJSON( 200, $ret );
} else {
	sendJSON( 500, $mysqli->error );
}
