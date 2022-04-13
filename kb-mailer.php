<?php
/*
Plugin Name: KB Mailer
Plugin URI: https://kreatiabyran.se
Description: Provides a simple way of building and sending templated emails in WordPress.
Version: 2.0.0
Author: Kreativa Byrån
Author URI: https://kreatiabyran.se
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'KBM_DIR', plugin_dir_path( __FILE__ ) );
define( 'KBM_URI', plugin_dir_url( __FILE__ ) );

require_once KBM_DIR . 'vendor/autoload.php';

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
