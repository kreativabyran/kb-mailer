<?php
/**
 * Plugin settings.
 *
 * @package kb-mailer
 */

namespace KB_Mailer;

class Settings {
	/**
	 * @var array KB_Mailer settings
	 */
	private static $settings = array(
		'admin_page_slug'         => 'kb-mailer',
		'content_variable_before' => '%',
		'content_variable_after'  => '%',
		'main_color_default'      => '#221f47',
		'secondary_color_default' => '#ec6f6f',
	);

	/**
	 * @param string $key Key of setting to get. Or null to get all settings.
	 *
	 * @return mixed If $key is set, the value for that key is returned.
	 * If $key doesn't exist null is returned.
	 * If $key isn't set all settings are returned.
	 */
	public static function get( $key = null ) {
		if ( ! empty( $key ) ) {
			return self::$settings[ $key ] ?? null;
		} else {
			return self::$settings;
		}
	}

	/**
	 * @param string $key  Key of setting to set.
	 * @param mixed $value Value to set.
	 */
	public static function set( $key, $value ) {
		self::$settings[ $key ] = $value;
	}
}
