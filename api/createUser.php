<?php

error_reporting( -1 );

require_once( 'common/uuid.php' );
require_once( 'common/sendJSON.php' );
require_once( 'common/parsePOST.php' );
require_once( 'common/enableCors.php' );
require_once( 'common/connectOrDie.php' );
require_once( 'common/addThingToVerify.php' );
require_once( 'common/sendEmailConfirmation.php' );

enableCors();

session_start();
if ( isset( $_SESSION[ 'me' ] ) ) {
	sendJSON( 200, $_SESSION[ 'me' ] );
}

$POST = parsePOST();
$POSTpass = $POST->pass ?? '';
$POSTemail = strtolower( trim( $POST->email ?? '' ) );
$POSTusername = trim( $POST->username ?? '' );

$errMsg = null;
$POSTpassLen = mb_strlen( $POSTpass );
$POSTemailLen = mb_strlen( $POSTemail );
$POSTusernameLen = mb_strlen( $POSTusername );

     if ( $POSTpassLen < 6 ) { $errMsg = 'pass:too-short'; }
else if ( $POSTemailLen > 128 ) { $errMsg = 'email:too-long'; }
else if ( $POSTusernameLen < 4 ) { $errMsg = 'username:too-short'; }
else if ( $POSTusernameLen > 32 ) { $errMsg = 'username:too-long'; }
else if ( !preg_match( '/^\w*$/', $POSTusername ) ) { $errMsg = 'username:bad-format'; }
else if ( !filter_var( $POSTemail, FILTER_VALIDATE_EMAIL ) ) { $errMsg = 'email:bad-format'; }
if ( $errMsg ) {
	sendJSON( 400, $errMsg );
}

$mysqli = connectOrDie();
$id = uuid();
$pass = password_hash( $POSTpass, PASSWORD_BCRYPT );
$email = $mysqli->real_escape_string( $POSTemail );
$username = $mysqli->real_escape_string( $POSTusername );
$avatar = 'https://www.gravatar.com/avatar/' . md5( $email );
$res = $mysqli->query( "INSERT INTO `users` (
	`id`,  `email`,  `pass`,  `username`,  `avatar`, `created` ) VALUES (
	'$id', '$email', '$pass', '$username', '$avatar', NOW() )" );
$err = $mysqli->error;
$code = $res ? addThingToVerify( $mysqli, $id, $email ) : null;
$mysqli->close();

if ( $res ) {
	if ( $code ) {
		sendEmailConfirmation( $id, $username, $email, $code );
	}
	$me = ( object )[
		'id' => $id,
		'email' => $email,
		'emailpublic' => '0',
		'emailchecked' => '0',
		'username' => $username,
		'avatar' => $avatar,
	];
	$_SESSION[ 'me' ] = $me;
	sendJSON( 201, $me );
} else {
	if ( strpos( $err, 'Duplicate' ) === 0 ) {
		$err = explode( '\'', $err )[ 3 ] . ':duplicate';
	}
	sendJSON( 500, $err );
}
