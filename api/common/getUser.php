<?php

function getUser( &$mysqli, $idType, $id, $full = false ) {
	$fields = '`id`, `email`, `emailpublic`, `firstname`, `lastname`, `username`, `avatar`';
	if ( $full ) {
		$fields .= ', `pass`, `emailchecked`';
	}
	$id = $mysqli->real_escape_string( $id );
	$idType = $mysqli->real_escape_string( $idType );
	$cond = $idType === 'usernameEmail'
		? "`username` = '$id' OR `email` = '$id'"
		: "`$idType` = '$id'";
	$res = $mysqli->query( "SELECT $fields FROM `users` WHERE $cond" );
	$user = null;
	if ( $res ) {
		if ( $res->num_rows < 1 ) {
			$user = false;
		} else {
			$user = $res->fetch_object();
			if ( !$full && $user->emailpublic === '0' ) {
				unset( $user->email );
				unset( $user->emailpublic );
			}
		}
		$res->free();
	}
	return $user;
}
