<?php

error_reporting( -1 );

require_once( 'common/sendJSON.php' );
require_once( 'common/enableCors.php' );

enableCors();

$GETusername = $_GET[ 'username' ] ?? null;
if ( !$GETusername ) {
	sendJSON( 400, 'query:bad-format' );
}

require_once( 'common/connection.php' );
require_once( 'common/getUser.php' );

$user = getUser( $mysqli, 'username', $GETusername );
$err = $mysqli->error;
$mysqli->close();
if ( $user === null ) {
	sendJSON( 500, $err );
}
if ( $user === false ) {
	sendJSON( 404 );
}
sendJSON( 200, $user );
