<?php

error_reporting( -1 );

require_once( 'common/sendJSON.php' );
require_once( 'common/enableCors.php' );

enableCors();

session_start();
$me = $_SESSION[ 'me' ] ?? null;

if ( $me ) {
	sendJSON( 200, $me );
} else {
	sendJSON( 401, 'user:not-connected' );
}
