<?php

error_reporting( -1 );

require_once( 'common/sendJSON.php' );

$GETid = $_GET[ 'id' ] ?? null;
$GETdata = $_GET[ 'data' ] ?? null;
$GETcode = $_GET[ 'code' ] ?? null;

if ( !$GETid || !$GETdata || !$GETcode ) {
	sendJSON( 400 );
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
		sendJSON( 404, "No match for '$data' with code '$code' (the code could be expired)" );
	}
}
if ( !$res ) {
	sendJSON( 500, $mysqli->error );
}
