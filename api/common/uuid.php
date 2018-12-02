<?php

function uuid() {
	$s = bin2hex( random_bytes( 18 ) );
	$s[ 14 ] = '4';
	$s[ 19 ] = dechex( 8 + random_int( 0, 3 ) );
	$s[ 8 ] =
	$s[ 13 ] =
	$s[ 18 ] =
	$s[ 23 ] = '-';
	return $s;
}
