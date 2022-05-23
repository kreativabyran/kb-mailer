<?php
/**
 * Class for each registered email.
 *
 * @package kb-mailer
 */

namespace KB_Mailer;

class Email {
	/**
	 * @var string Email identifier. Used for getting email to send or otherwise use.
	 */
	private $id;

	/**
	 * @var string Name of email. Displayed in wp-admin, and used as email subject if not explicitly set.
	 */
	private $name;

	/**
	 * @var string Email subject.
	 */
	private $subject;

	/**
	 * @var string[] Variables used for dynamic content in emails.
	 * Array element key is an id for the variable, used for replacement.
	 * Element value is name of the variable, displayed in wp-admin.
	 */
	private $content_variables;

	/**
	 * @var Email_Options_Page Options page class for current email.
	 */
	private $options_page;

	/**
	 * @var Email_Test_Page Test page class for current email.
	 */
	private $test_page;

	public function __construct( $id, $name, $content_variables = array(), $subject = null ) {
		$this->id                = sanitize_title( $id );
		$this->name              = sanitize_text_field( $name );
		$this->subject           = sanitize_text_field( $subject );
		$this->content_variables = $content_variables;

		if ( is_admin() ) {
			$this->options_page = new Email_Options_Page( $this->id, $this->name, $this->content_variables );
			$this->test_page    = new Email_Test_Page( $this->id, $this->name, $this->content_variables );
		}

		Emails::add( $this->id, $this );
	}

	/**
	 * @return string
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * @return string
	 */
	public function get_name() {
		return $this->name;
	}

	/**
	 * @return string
	 */
	public function get_default_subject() {
		if ( isset( $this->subject ) && ! empty( $this->subject ) ) {
			return $this->subject;
		}

		return $this->name;
	}

	/**
	 * @param string $to                    Receiver email address.
	 * @param string[] $content_variables   Variables to use as replacement in email content.
	 * @param string                        Optional email subject. Defaults to email name.
	 *
	 * @return bool Whether the email was sent successfully.
	 */
	public function send( $to, $content_variables = array(), $subject = null ) {
		$message = $this->render_email_content( $content_variables );
		$subject = $subject ?? $this->get_default_subject();
		return wp_mail( $to, $subject, $message, array( 'Content-Type: text/html; charset=UTF-8' ) );
	}

	/**
	 * @param array $content_variables Variables to use as replacement in email content.
	 *
	 * @return string
	 */
	private function render_email_content( $content_variables ) {
		$kbm_options = get_option( 'kbm_styling_options' );
		$main_color  = $kbm_options['main_color'] ?? Settings::get( 'main_color_default' );
		$logo        = $kbm_options['logo'] ?? false;
		$logo_url    = $kbm_options['logo_url'] ?? false;
		$footer      = $kbm_options['footer'] ?? false;

		if ( ! empty( $logo ) ) {
			$image_attributes = wp_get_attachment_image_src( $logo, 'medium' );
			$logo             = $image_attributes[0];
		}

		$email_options = get_option( 'kbm_options_' . $this->id );
		$email_content = array(
			'header' => $email_options['header'] ?? '',
			'body'   => wpautop( $email_options['body'] ) ?? '',
		);

		if ( ! empty( $content_variables ) ) {
			foreach ( $content_variables as $key => $variable ) {
				$email_content = str_replace( apply_filters( 'kb_mailer_content_variable_before', '%' ) . $key . apply_filters( 'kb_mailer_content_variable_after', '%' ), $variable, $email_content );
			}
		}

		$email = new Template(
			'default-email',
			array(
				'main_color' => $main_color,
				'header'     => $email_content['header'],
				'body'       => $email_content['body'],
				'footer'     => $footer,
				'logo'       => $logo,
				'logo_url'   => $logo_url,
			)
		);

		return $email->get();
	}
}
