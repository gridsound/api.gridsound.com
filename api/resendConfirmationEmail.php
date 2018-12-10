<?php

error_reporting( -1 );

require_once( 'common/sendJSON.php' );
require_once( 'common/enableCors.php' );

enableCors();
session_start();

$_POST = json_decode( file_get_contents( 'php://input' ), true );
$POSTemail = $_POST[ 'email' ] ?? null;
$me = $_SESSION[ 'me' ] ?? null;

     if ( !$me ) { sendJSON( 401, "no-auth" ); }
else if ( $POSTemail !== $me->email ) { sendJSON( 400, "email-not-match" ); }
else if ( $me->status !== 'EMAIL_TO_VERIFY' ) { sendJSON( 400, "email-verified" ); }

require_once( 'common/connection.php' );
require_once( 'common/sendEmail.php' );
require_once( 'common/sendEmailConfirmation.php' );
require_once( 'common/addThingToVerify.php' );

$code = addThingToVerify( $mysqli, $me->id, $me->email );
if ( $code ) {
	$code = sendEmailConfirmation( $me->id, $me->username, $me->email, $code );
}
if ( $code ) {
	sendJSON( 200 );
} else {
	sendJSON( 500, 'email:failed' );
}
