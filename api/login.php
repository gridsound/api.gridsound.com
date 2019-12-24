<?php

error_reporting( -1 );

require_once( 'common/sendJSON.php' );
require_once( 'common/enableCors.php' );

enableCors();

session_start();
if ( isset( $_SESSION[ 'me' ] ) ) {
	sendJSON( 200, $_SESSION[ 'me' ] );
}

$_POST = json_decode( file_get_contents( 'php://input' ), true );
$POSTemail = $_POST[ 'email' ] ?? null;
$POSTpass = $_POST[ 'pass' ] ?? null;

if ( !$POSTemail || !$POSTpass ) {
	sendJSON( 400, 'query:bad-format' );
}

require_once( 'common/connection.php' );
require_once( 'common/getUser.php' );

$user = getUser( $mysqli, 'usernameEmail', $POSTemail, true );
$err = $mysqli->error;
$mysqli->close();
$authOk = false;
if ( $user === null ) {
	sendJSON( 500, $err );
}
if ( $user !== false ) {
	$authOk = password_verify( $POSTpass, $user->pass );
	unset( $user->pass );
}
if ( $authOk === false ) {
	sendJSON( 401, 'login:fail' );
}
$_SESSION[ 'me' ] = $user;
sendJSON( 200, $_SESSION[ 'me' ] );
