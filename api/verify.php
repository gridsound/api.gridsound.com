<?php

error_reporting( -1 );

require_once( 'common/sendJSON.php' );
require_once( 'common/enableCors.php' );
require_once( 'common/connectOrDie.php' );

enableCors();

$GETid = $_GET[ 'id' ] ?? null;
$GETdata = $_GET[ 'data' ] ?? null;
$GETcode = $_GET[ 'code' ] ?? null;

if ( !$GETid || !$GETdata || !$GETcode ) {
	sendJSON( 400, 'query:bad-format' );
}

$mysqli = connectOrDie();
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
			$res = $mysqli->query( "UPDATE `users` SET `emailchecked`='1' WHERE `id`='$id'" );
		}
		if ( $res ) {
			session_start();
			$me = $_SESSION[ 'me' ] ?? null;
			if ( $me ) {
				$me->emailchecked = '1';
			}
			header( 'Location: https://gridsound.com' );
		}
	} else {
		sendJSON( 404, "No match for '$data' with code '$code' (the code could be expired)" );
	}
}
if ( !$res ) {
	sendJSON( 500, $mysqli->error );
}
