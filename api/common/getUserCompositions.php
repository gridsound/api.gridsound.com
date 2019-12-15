<?php

function getUserCompositions( &$mysqli, $iduser, $onlyPublic ) {
	$cmps = null;
	$iduser = $mysqli->real_escape_string( $iduser );
	$query = "SELECT `id`, `public`, `data`
		FROM `compositions` WHERE `iduser` = '$iduser'";
	if ( $onlyPublic ) {
		$query .= ' AND `public` = 1';
	}
	$res = $mysqli->query( $query );
	if ( $res ) {
		$cmps = array();
		while ( $row = $res->fetch_object() ) {
			if ( $onlyPublic ) {
				unset( $row->public );
			}
			$cmps[] = $row;
		}
		$res->free();
	}
	return $cmps;
}
