<?php

error_reporting( -1 );

$GETid = $_GET[ 'id' ] ?? null;

if ( !$GETid ) {
	http_response_code( 400 );
	die();
}

require_once( 'common/connection.php' );

$id = $mysqli->real_escape_string( $GETid );
$res = $mysqli->query( "SELECT `id`, `email`, `firstname`, `lastname`, `username`
	FROM `users` WHERE `id`='$id'" );

if ( $res ) {
	$ret = $res->fetch_object();
	$res->free();
	$mysqli->close();
	header( 'Content-Type: application/json' );
	echo json_encode( $ret );
} else {
	http_response_code( 500 );
	die( $mysqli->error );
}
