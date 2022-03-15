<?php
/*
Plugin Name: KB Mailer
Plugin URI: https://kreatiabyran.se
Description: Provides a simple way of building emails.
Version: 1.0
Author: @oskarmodig
Author URI: https://kreatiabyran.se
*/

function custom_post_type() {

	// Set UI labels for Custom Post Type
	$labels = array(
		'name'               => _x( 'KB Mail', 'Post Type General Name', 'kb-mailer' ),
		'singular_name'      => _x( 'Mail', 'Post Type Singular Name', 'kb-mailer' ),
		'menu_name'          => __( 'KB Mailer', 'kb-mailer' ),
		'all_items'          => __( 'All Mail', 'kb-mailer' ),
		'view_item'          => __( 'View Mail', 'kb-mailer' ),
		'add_new_item'       => __( 'Add New Mail', 'kb-mailer' ),
		'add_new'            => __( 'Add New', 'kb-mailer' ),
		'edit_item'          => __( 'Edit Mail', 'kb-mailer' ),
		'update_item'        => __( 'Update Mail', 'kb-mailer' ),
		'search_items'       => __( 'Search Mail', 'kb-mailer' ),
		'not_found'          => __( 'No mail', 'kb-mailer' ),
		'not_found_in_trash' => __( 'Not found in Trash', 'kb-mailer' ),
	);

	// Set other options for Custom Post Type

	$args = array(
		'label'               => __( 'Mails', 'kb-mailer' ),
		'description'         => __( 'Custom emails', 'kb-mailer' ),
		'labels'              => $labels,
		// Features this CPT supports in Post Editor
		'supports'            => array( 'title', 'revisions', 'custom-fields' ),
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_nav_menus'   => false,
		'show_in_admin_bar'   => true,
		'menu_position'       => 80,
		'menu_icon'           => 'dashicons-email-alt',
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => true,
		'publicly_queryable'  => false,
		'capability_type'     => 'post',
		'show_in_rest'        => false,

	);

	// Registering your Custom Post Type
	register_post_type( 'kbmails', $args );
}
// Hooking up our function to theme setup
add_action( 'init', 'custom_post_type' );


function kb_mailer_mail_metaboxes() {
	global $wp_meta_boxes;
	add_meta_box( 'kb-mailer-meta', __( 'Mail Body', 'kb-mailer' ), 'kb_mailer_mail_metaboxes_html', 'kb_mailer_mail', 'normal', 'high' );
}
add_action( 'add_meta_boxes_kbmails', 'kb_mailer_mail_metaboxes' );



function kb_mailer_mail_metaboxes_html( $post ) {
	if ( 'kbmails' === $post->post_type ) {
		$text = get_post_meta( $post->ID, 'kbm_body', true );
		wp_editor(
			$text,
			'kbm-body',
			array(
				'media_buttons' => false,
				'textarea_name' => 'kbm-body-input',
			)
		);
	}
}
//add_action( 'edit_form_advanced', 'kb_mailer_mail_metaboxes_html' );

add_action(
	'save_post',
	function( $post_id ) {
		if ( ! empty( $_POST['kbm-body-input'] ) ) {
			$data = wp_kses_post( $_POST['kbm-body-input'] );
			update_post_meta( $post_id, 'kbm_body', $data );
		}
	}
);

function kbm_tiny_mce_editor( $in, $editor_id ) {

	if ( 'kbm-body' === $editor_id ) { // Remove read more-button.
		$toolbar1 = explode( ',', $in['toolbar1'] );

		$key = array_search( 'wp_more', $toolbar1, true );
		if ( false !== $key ) {
			unset( $toolbar1[ $key ] );
		}
		$in['toolbar1'] = implode( ',', $toolbar1 );
	}

	return $in;
}
//add_filter( 'tiny_mce_before_init', 'kbm_tiny_mce_editor', 10, 2 );
