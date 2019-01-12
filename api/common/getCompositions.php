<?php

function getCompositions( &$mysqli, $iduser, $onlyPublic ) {
	$query = "SELECT `id`, `public`, `data`
		FROM `compositions`
		WHERE `iduser` = '$iduser'";
	if ( $onlyPublic ) {
		$query .= ' AND `public` = 1';
	}
	$res = $mysqli->query( $query );
	if ( $res ) {
		$cmps = array();
		while ( $row = $res->fetch_object() ) {
			$row->data = base64_decode( $row->data, true );
			$cmps[] = $row;
		}
		$res->free();
		return $cmps;
	}
	return null;
}
