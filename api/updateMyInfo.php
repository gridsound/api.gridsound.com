<?php

error_reporting( -1 );

require_once( 'common/sendJSON.php' );
require_once( 'common/parsePOST.php' );
require_once( 'common/enableCors.php' );
require_once( 'common/connectOrDie.php' );

enableCors();
session_start();

$me = $_SESSION[ 'me' ] ?? null;
if ( !$me ) {
	sendJSON( 401, 'user:not-connected' );
}

$POST = parsePOST();
$POSTemail = strtolower( trim( $POST->email ?? $me->email ) );
$POSTlastname = trim( $POST->lastname ?? $me->lastname );
$POSTfirstname = trim( $POST->firstname ?? $me->firstname );
$POSTemailpublic = $POST->emailpublic ?? null;

if ( !filter_var( $POSTemail, FILTER_VALIDATE_EMAIL ) ) {
	sendJSON( 400, 'email:bad-format' );
}

$mysqli = connectOrDie();
$id = $me->id;
$email = $mysqli->real_escape_string( $POSTemail );
$lastname = $mysqli->real_escape_string( $POSTlastname );
$firstname = $mysqli->real_escape_string( $POSTfirstname );
$emailchanged = $email !== $me->email;
$emailpublic = $POSTemailpublic === null
	? $me->emailpublic
	: ( $POSTemailpublic ? '1' : '0' );

$query = 'UPDATE `users` SET ';
if ( $emailchanged ) {
	$query .= "
		`email` = '$email',
		`emailchecked` = '0',
	";
}
$query .= "
	`lastname` = '$lastname',
	`firstname` = '$firstname',
	`emailpublic` = '$emailpublic'
	WHERE `id` = '$id'
";

$res = $mysqli->query( $query );
$err = $mysqli->error;
$mysqli->close();

if ( $res ) {
	$me->lastname = $lastname;
	$me->firstname = $firstname;
	$me->emailpublic = $emailpublic;
	if ( $emailchanged ) {
		$me->email = $email;
		$me->emailchecked = '0';
	}
	sendJSON( 200, $me );
} else {
	sendJSON( 500, $err );
}
