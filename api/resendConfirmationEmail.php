<?php

error_reporting( -1 );

require_once( 'common/sendJSON.php' );
require_once( 'common/parsePOST.php' );
require_once( 'common/enableCors.php' );
require_once( 'common/connectOrDie.php' );
require_once( 'common/addThingToVerify.php' );
require_once( 'common/sendEmailConfirmation.php' );

enableCors();
session_start();

$POST = parsePOST();
$POSTemail = $POST->email ?? null;
$me = $_SESSION[ 'me' ] ?? null;

     if ( !$me ) { sendJSON( 401, "no-auth" ); }
else if ( $POSTemail !== $me->email ) { sendJSON( 400, "email-not-match" ); }
else if ( $me->emailchecked === '1' ) { sendJSON( 400, "email-verified" ); }

$mysqli = connectOrDie();
$code = addThingToVerify( $mysqli, $me->id, $me->email );
$err = $mysqli->error;
$mysqli->close();

if ( !$code ) {
	sendJSON( 500, $err );
} else if ( sendEmailConfirmation( $me->id, $me->username, $me->email, $code ) ) {
	sendJSON( 200 );
} else {
	sendJSON( 500, 'email:failed' );
}
