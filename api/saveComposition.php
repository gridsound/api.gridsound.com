<?php

error_reporting( -1 );

require_once( 'common/sendJSON.php' );
require_once( 'common/enableCors.php' );

enableCors();
session_start();

$me = $_SESSION[ 'me' ] ?? null;
$_POST = json_decode( file_get_contents( 'php://input' ), true );
$POSTcmp = json_decode( $_POST[ 'composition' ] ?? null );
$data = json_encode( $POSTcmp );

if ( !$me ) {
	sendJSON( 401 );
} else if ( $me->user->emailchecked !== '1' ) {
	sendJSON( 401, 'email-not-verified' );
} else if ( !$POSTcmp || !$data ) {
	sendJSON( 400 );
}

require_once( 'common/connection.php' );

$id = $mysqli->real_escape_string( $POSTcmp->id );
$iduser = $mysqli->real_escape_string( $me->user->id );
$data64 = base64_encode( $data );
$res = $mysqli->query( "UPDATE `compositions` SET
	`data` = '$data64',
	`updated` = NOW()
	WHERE `id` = '$id'
	AND `iduser` = '$iduser'" );

if ( $res ) {
	if ( $mysqli->affected_rows === 0 ) {
		$res = $mysqli->query( "INSERT INTO `compositions` (
			`id`,  `iduser`,  `data`,   `created`, `updated` ) VALUES (
			'$id', '$iduser', '$data64', NOW(),     NOW() )" );
		$me->compositions[] = ( object )[
			'id' => $id,
			'data' => $data,
			'public' => '1',
		];
	} else {
		$ind = array_search( $id, array_column( $me->compositions, 'id' ) );
		$me->compositions[ $ind ]->data = $data;
	}
}

if ( $res ) {
	$mysqli->close();
	sendJSON( 200 );
} else {
	sendJSON( 500, $mysqli->error );
}
