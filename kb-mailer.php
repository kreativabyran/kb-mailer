<?php
/*
Plugin Name: KB Mailer
Plugin URI: https://kreatiabyran.se
Description: Provides a simple way of building emails.
Version: 1.0
Author: @oskarmodig
Author URI: https://kreatiabyran.se
*/

namespace KB_Mailer;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'KBM_DIR', plugin_dir_path( __FILE__ ) . '/' );
define( 'KBM_URI', plugin_dir_url( __FILE__ ) . '/' );

require_once KBM_DIR . 'vendor/autoload.php';

$autoloader = new \Pablo_Pacheco\WP_Namespace_Autoloader\WP_Namespace_Autoloader(
	array(
		'directory'        => __DIR__,     // Directory of your project. It can be your theme or plugin. Defaults to __DIR__ (probably your best bet).
		'namespace_prefix' => 'KB_Mailer',      // Main namespace of your project. E.g My_Project\Admin\Tests should be My_Project. Defaults to the namespace of the instantiating file.
		'classes_dir'      => 'includes',  // (optional). It is where your namespaced classes are located inside your project. If your classes are in the root level, leave this empty. If they are located on 'src' folder, write 'src' here
		'lowercase'        => array( 'file', 'folders' ),
	)
);
$autoloader->init();

add_action(
	'admin_menu',
	function () {
		\add_menu_page(
			'KB Mailer',
			'KB Mailer',
			'manage_options',
			'kb-mailer',
			function() {
				echo 'KBMAILER!';
			},
			'',
			120
		);
	}
);

new Email(
	'triss',
	'Trisslott-mail',
	array(
		'name'  => __( 'Namn på mottagare', 'kbm' ),
		'triss' => __( 'Trisslottskoden', 'kbm' ),
	)
);
