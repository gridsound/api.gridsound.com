<?php

error_reporting( -1 );

require_once( 'common/sendJSON.php' );
require_once( 'common/enableCors.php' );

enableCors();
session_start();

$me = $_SESSION[ 'me' ]->user ?? null;
if ( !$me ) {
	sendJSON( 401 );
}

$_POST = json_decode( file_get_contents( 'php://input' ), true );
$POSTemail = strtolower( trim( $_POST[ 'email' ] ?? $me->email ) );
$POSTlastname = trim( $_POST[ 'lastname' ] ?? $me->lastname );
$POSTfirstname = trim( $_POST[ 'firstname' ] ?? $me->firstname );
$POSTemailpublic = $_POST[ 'emailpublic' ] ?? null;

if ( !filter_var( $POSTemail, FILTER_VALIDATE_EMAIL ) ) {
	sendJSON( 400, 'email:bad-format' );
}

require_once( 'common/connection.php' );

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

if ( $res ) {
	$mysqli->close();
	$me->lastname = $lastname;
	$me->firstname = $firstname;
	$me->emailpublic = $emailpublic;
	if ( $emailchanged ) {
		$me->email = $email;
		$me->emailchecked = '0';
	}
	sendJSON( 200, ( object )[ 'user' => $me ] );
} else {
	sendJSON( 500, $mysqli->error );
}
