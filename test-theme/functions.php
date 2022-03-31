<?php
if ( function_exists( 'kbm_register_email' ) ) {
	kbm_register_email(
		'contact',
		'Contact request',
		array(
			'name'    => 'Name of person requesting contact',
			'message' => 'Message from person',
		)
	);
}

add_filter(
	'kb_mailer_admin_page_capability',
	function( $capability ) {
		return 'edit_posts';
	}
);
add_filter(
	'kb_mailer_content_variable_before',
	function( $capability ) {
		return '|';
	}
);