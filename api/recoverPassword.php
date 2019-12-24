<?php

error_reporting( -1 );

require_once( 'common/uuid.php' );
require_once( 'common/sendJSON.php' );
require_once( 'common/sendEmail.php' );
require_once( 'common/parsePOST.php' );
require_once( 'common/enableCors.php' );
require_once( 'common/connectOrDie.php' );

enableCors();

$POST = parsePOST();
$POSTemail = $POST->email ?? null;
if ( !filter_var( $POSTemail, FILTER_VALIDATE_EMAIL ) ) {
	sendJSON( 400, 'email:bad-format' );
}

$mysqli = connectOrDie();
$email = $mysqli->real_escape_string( $POSTemail );
$res = $mysqli->query( "SELECT `username` FROM `users` WHERE `email` = '$email'" );
$err = $mysqli->error;

if ( !$res ) {
	$mysqli->close();
	sendJSON( 500, $err );
} else if ( $res->num_rows < 1 ) {
	$res->free();
	$mysqli->close();
	sendJSON( 404, 'email:not-found' );
}

$user = $res->fetch_object();
$res->free();

$res = $mysqli->query( "SELECT `id` FROM `passwordForgotten` WHERE
	`email` = '$email' AND
	`expire` > NOW() - INTERVAL 1 DAY" );
$err = $mysqli->error;

if ( !$res ) {
	$mysqli->close();
	sendJSON( 500, $err );
} else if ( $res->num_rows > 0 ) {
	$res->free();
	$mysqli->close();
	sendJSON( 409, 'password:already-recovering' );
} else {
	$res->free();
}

$res = $mysqli->query( "DELETE FROM `passwordForgotten` WHERE `email` = '$email'" );
$err = $mysqli->error;
if ( !$res ) {
	$mysqli->close();
	sendJSON( 500, $err );
}

$code = uuid();
$res = $mysqli->query( "INSERT INTO `passwordForgotten`(
	`email`,  `code`, `expire` ) VALUES (
	'$email', '$code', NOW() + INTERVAL 1 DAY )" );
if ( !$res ) {
	sendJSON( 500, $mysqli->error );
}

$username = $user->username;
sendEmail( $email, 'Password recovering',
	"Hi $username,\r\n\r\n" .
	"Clicking that link will let you set a new password :\r\n" .
	"https://gridsound.com/#/resetPassword/$email/$code\r\n\r\n" .
	"If you didn't ask to recover your password then just ignore this email.\r\n" .
	"But this mean that somebody else has entered your email on this page " .
	"https://gridsound.com/#/forgotPassword"
);
sendJSON( 200 );
