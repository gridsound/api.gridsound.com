<?php

error_reporting( -1 );

$POSTconfirm = $_POST[ 'confirm' ] ?? null;

if ( $POSTconfirm !== 'true' ) {
	http_response_code( 400 );
	die();
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
