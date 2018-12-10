<?php

function sendEmail( $email, $title, $msg ) {
	return mail( $email, $title, $msg, array(
		'From' => 'GridSound <contact@gridsound.com>',
		'Reply-To' => 'contact@gridsound.com',
		'X-Mailer' => 'PHP/' . phpversion(),
	) );
}
