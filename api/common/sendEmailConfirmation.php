<?php

require_once( 'sendEmail.php' );

function sendEmailConfirmation( $id, $username, $email, $code ) {
	return sendEmail( $email, 'Email confirmation',
		"Hi $username,\r\n\r\n" .
		"Welcome to GridSound !\r\n" .
		"Clicking that link will confirm your email :\r\n" .
		"https://api.gridsound.com/verify?id=$id&data=$email&code=$code"
	);
}
