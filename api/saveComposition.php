<?php

error_reporting( -1 );

require_once( 'common/sendJSON.php' );
require_once( 'common/parsePOST.php' );
require_once( 'common/enableCors.php' );
require_once( 'common/connectOrDie.php' );

enableCors();
session_start();

$me = $_SESSION[ 'me' ] ?? null;
$POST = parsePOST();
$POSTcmp = $POST->composition ?? null;
$dataDecoded = json_decode( $POSTcmp );

if ( !$me ) {
	sendJSON( 401, 'user:not-connected' );
} else if ( $me->emailchecked !== '1' ) {
	sendJSON( 403, 'email:not-verified' );
} else if ( !$POSTcmp || !$dataDecoded ) {
	sendJSON( 400, 'query:bad-format' );
}

$mysqli = connectOrDie();
$id = $mysqli->real_escape_string( $dataDecoded->id );
$data = $mysqli->real_escape_string( $POSTcmp );
$iduser = $mysqli->real_escape_string( $me->id );
$name = $mysqli->real_escape_string( $dataDecoded->name );
$bpm = $mysqli->real_escape_string( $dataDecoded->bpm );
$duration = $mysqli->real_escape_string( $dataDecoded->duration );
$beatsPerMeasure = $mysqli->real_escape_string( $dataDecoded->beatsPerMeasure );
$stepsPerBeat = $mysqli->real_escape_string( $dataDecoded->stepsPerBeat );
$res = $mysqli->query( "UPDATE `compositions` SET
	`name` = '$name',
	`bpm` = '$bpm',
	`duration` = '$duration',
	`beatsPerMeasure` = '$beatsPerMeasure',
	`stepsPerBeat` = '$stepsPerBeat',
	`data` = '$data',
	`updated` = NOW()
	WHERE `id` = '$id' AND `iduser` = '$iduser'" );

if ( $res && $mysqli->affected_rows === 0 ) {
	$res = $mysqli->query( "INSERT INTO `compositions` (
		`id`,  `iduser`,  `name`,  `bpm`,  `duration`,  `beatsPerMeasure`,  `stepsPerBeat`,  `data`, `created`, `updated` ) VALUES (
		'$id', '$iduser', '$name', '$bpm', '$duration', '$beatsPerMeasure', '$stepsPerBeat', '$data', NOW(),     NOW() )" );
}

$err = $mysqli->error;
$mysqli->close();
if ( $res ) {
	sendJSON( 200 );
} else {
	sendJSON( 500, $err );
}
