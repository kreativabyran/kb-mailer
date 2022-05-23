<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$file_path = dirname( __FILE__ );
$url_path  = str_replace( $_SERVER['DOCUMENT_ROOT'], '', $file_path );
$full_url  = site_url( $url_path ) . '/';

if( 'local' === wp_get_environment_type() && defined( 'WP_IS_LANDO' ) && ! empty( WP_IS_LANDO )  ) {
	$full_url = plugin_dir_url( __FILE__ );
}

define( 'KBM_DIR', $file_path . '/' );
define( 'KBM_URI', $full_url );

new \KB_Mailer\Options_Page();

if ( ! function_exists( 'kbm_register_email' ) ) {
	function kbm_register_email( $id, $name, $content_variables = array(), $subject = null ) {
		new \KB_Mailer\Email(
			$id,
			$name,
			$content_variables,
			$subject
		);
	}
}

if ( ! function_exists( 'kbm_send_email' ) ) {
	function kbm_send_email( $id, $to, $content_variables = array(), $subject = null ) {
		return \KB_Mailer\Emails::send(
			$id,
			$to,
			$content_variables,
			$subject
		);
	}
}