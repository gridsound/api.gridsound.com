<?php

error_reporting( -1 );

require_once( 'common/sendJSON.php' );

$GETusername = $_GET[ 'username' ] ?? null;

if ( !$GETusername ) {
	sendJSON( 400 );
}

require_once( 'common/connection.php' );

$username = $mysqli->real_escape_string( $GETusername );
$res = $mysqli->query( "SELECT `id`, `emailpublic`, `firstname`, `lastname`, `username`
	FROM `users` WHERE `username`='$username'" );

if ( $res ) {
	$ret = $res->fetch_object();
	$res->free();
	$mysqli->close();
	sendJSON( 200, $ret );
} else {
	sendJSON( 500, $mysqli->error );
}
