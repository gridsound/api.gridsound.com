<?php

function sendEmail( $email, $title, $msg ) {
	$head = "From: GridSound <contact@gridsound.com>\r\n" .
		"Reply-To: contact@gridsound.com\r\n" .
		'X-Mailer: PHP/' . phpversion();

	return mail( $email, $title, $msg, $head );
}
