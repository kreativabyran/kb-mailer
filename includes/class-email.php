<?php

namespace KB_Mailer;

class Email {
	private $name;
	private $id;
	private $content_variables;
	private $options_page;

	public function __construct( $id, $name, $content_variables = array() ) {
		$this->id                = sanitize_title( $id );
		$this->name              = sanitize_text_field( $name );
		$this->content_variables = $content_variables;

		if ( is_admin() ) {
			$this->options_page = new Options_Page( $this->id, $this->name, $this->content_variables );
		}

		Emails::add( $this->id, $this );
	}

	public function send( $to, $content_variables = array(), $subject = false ) {
		$email_options = get_option( 'kbm_options_' . $this->id );

		$email_content = array(
			'header' => $email_options['header'],
			'body'   => $email_options['body'],
			'footer' => $email_options['footer'],
		);

		if ( ! empty( $content_variables ) ) {
			foreach ( $content_variables as $key => $variable ) {
				$email_content = str_replace( '%' . $key . '%', $variable, $email_content );
			}
		}

		$subject  = $subject ?? $this->name;
		$message  = '<h1>' . $email_content['header'] . '</h1>';
		$message .= $email_content['body'];
		$message .= '<h3>' . $email_content['footer'] . '</h3>';
		wp_mail( $to, $subject, $message, array( 'Content-Type: text/html; charset=UTF-8' ) );
	}
}
