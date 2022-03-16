<?php

namespace KB_Mailer;

class Settings {
	private static $settings = array(
		'admin_page_slug'         => 'kb-mailer',
		'content_variable_before' => '%',
		'content_variable_after'  => '%',
	);

	public static function get( $key = null ) {
		if ( ! empty( $key ) ) {
			return self::$settings[ $key ] ?? false;
		} else {
			return self::$settings;
		}
	}

	/**
	 * @param string $key
	 * @param mixed $value
	 */
	public static function set( $key, $value ) {
		self::$settings[ $key ] = $value;
	}
}
