<?php

namespace KB_Mailer;

class Emails {
	private static $emails = array();

	public static function add( $id, $email ) {
		self::$emails[ $id ] = $email;
	}

	public static function get( $id ) {
		return self::$emails[ $id ] ?? false;
	}

	public static function send( $id, $to, $content_variables = array(), $subject = false ) {
		$email = self::get( $id );
		if ( $email ) {
			$email->send( $to, $content_variables, $subject );
		}

		return false;
	}
}
