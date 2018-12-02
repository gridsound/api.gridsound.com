<?php

error_reporting( -1 );

$GETid = $_GET[ 'id' ] ?? null;
$GETdata = $_GET[ 'data' ] ?? null;
$GETcode = $_GET[ 'code' ] ?? null;

if ( !$GETid || !$GETdata || !$GETcode ) {
	http_response_code( 400 );
	die();
}

require_once( 'common/connection.php' );

$id = $mysqli->real_escape_string( $GETid );
$data = $mysqli->real_escape_string( $GETdata );
$code = $mysqli->real_escape_string( $GETcode );
$res = $mysqli->query( "DELETE FROM `thingsNotVerified` WHERE
	`iduser` = '$id' AND
	`data` = '$data' AND
	`code` = '$code' AND
	`expire` > NOW()" );

if ( $res ) {
	if ( $mysqli->affected_rows > 0 ) {
		if ( mb_strpos( $data, '@' ) !== false ) {
			$res = $mysqli->query( "UPDATE `users` SET `status`='NORMAL' WHERE `id`='$id'" );
		}
		if ( $res ) {
			header( 'Location: https://gridsound.com' );
		}
	} else {
		http_response_code( 404 );
		die( "No match for '$data' with code '$code' (the code could be expired)" );
	}
}
if ( !$res ) {
	http_response_code( 500 );
	die( $mysqli->error );
}
