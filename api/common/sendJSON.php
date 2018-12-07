<?php

function sendJSON( $httpcode, $data = null ) {
	$obj = ( object )[
		'ok' => $httpcode >= 200 && $httpcode < 300,
		'code' => $httpcode,
		'data' => $data ?? ( object )[],
	];
	if ( is_string( $data ) ) {
		$obj->msg = $data;
		unset( $obj->data );
	}
	header( 'Content-Type: application/json; charset=utf-8' );
	http_response_code( $httpcode );
	die( json_encode( $obj ) );
}
