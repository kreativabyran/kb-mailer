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
	add_meta_box( 'postfunctiondiv', __( 'Function' ), 'kb_mailer_mail_metaboxes_html', 'kb_mailer_mail', 'normal', 'high' );
}
add_action( 'add_meta_boxes_kbmails', 'kb_mailer_mail_metaboxes' );


function kb_mailer_mail_metaboxes_html() {
	global $post;
	$custom = get_post_custom( $post->ID );
	$body   = $custom[ 'body' ][ 0 ] ?? '';
	wp_editor( htmlspecialchars( $body ), 'kb-mailer-body' );
}
