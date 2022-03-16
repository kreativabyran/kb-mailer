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
