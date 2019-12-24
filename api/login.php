<?php

error_reporting( -1 );

require_once( 'common/getUser.php' );
require_once( 'common/sendJSON.php' );
require_once( 'common/parsePOST.php' );
require_once( 'common/enableCors.php' );
require_once( 'common/connectOrDie.php' );

enableCors();

session_start();
if ( isset( $_SESSION[ 'me' ] ) ) {
	sendJSON( 200, $_SESSION[ 'me' ] );
}

$POST = parsePOST();
$POSTemail = $POST->email ?? null;
$POSTpass = $POST->pass ?? null;

if ( !$POSTemail || !$POSTpass ) {
	sendJSON( 400, 'query:bad-format' );
}

$mysqli = connectOrDie();
$user = getUser( $mysqli, 'usernameEmail', $POSTemail, true );
$err = $mysqli->error;
$mysqli->close();

if ( $user === null ) {
	sendJSON( 500, $err );
}

$authOk = false;
if ( $user !== false ) {
	$authOk = password_verify( $POSTpass, $user->pass );
	unset( $user->pass );
}
if ( $authOk === false ) {
	sendJSON( 401, 'login:fail' );
}

$_SESSION[ 'me' ] = $user;
sendJSON( 200, $user );
