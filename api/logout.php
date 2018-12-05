<?php

error_reporting( -1 );

require_once( 'common/sendJSON.php' );

$POSTconfirm = $_POST[ 'confirm' ] ?? null;

if ( $POSTconfirm !== 'true' ) {
	sendJSON( 400 );
}

session_start();

$_SESSION = array();
if ( ini_get( 'session.use_cookies' ) ) {
	$p = session_get_cookie_params();
	setcookie( session_name(), '', time() - 42000,
		$p[ 'path' ], $p[ 'domain' ],
		$p[ 'secure' ], $p[ 'httponly' ] );
}
if ( session_status() === PHP_SESSION_ACTIVE ) {
	session_destroy();
}
sendJSON( 200 );
