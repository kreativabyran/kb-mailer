<?php
/**
 * Container for all emails.
 *
 * @package kb-mailer
 */

namespace KB_Mailer;

class Emails {
	/**
	 * @var Email[] All registered emails.
	 */
	private static $emails = array();

	/**
	 * @param string $id   ID of added Email.
	 * @param Email $email Instance of class Email.
	 *
	 * @return void
	 */
	public static function add( $id, $email ) {
		self::$emails[ $id ] = $email;
	}

	/**
	 * @param string $id ID of Email to get. Or null to get all emails.
	 *
	 * @return false|Email|Email[]
	 */
	public static function get( $id = null ) {
		if ( $id ) {
			return self::$emails[ $id ] ?? false;
		} else {
			return self::$emails;
		}
	}

	/**
	 * @param string   $id                 ID of email to send.
	 * @param string   $to                 Receiver email address.
	 * @param string[] $content_variables  Variables used for dynamic content in emails.
	 * Array element key is an id for the variable, used for replacement.
	 * Element value is what should be inserted as dynamic content.
	 * @param string   $subject Optional email subject. Defaults to Email name.
	 *
	 * @return bool Whether the email was sent successfully.
	 */
	public static function send( $id, $to, $content_variables = array(), $subject = null ) {
		$email = self::get( $id );
		if ( $email ) {
			return $email->send( $to, $content_variables, $subject );
		}

		return false;
	}
}
