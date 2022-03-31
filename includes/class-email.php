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

	public function __construct( $id, $name, $content_variables = array() ) {
		$this->id                = sanitize_title( $id );
		$this->name              = sanitize_text_field( $name );
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
	public function get_id(): string {
		return $this->id;
	}

	/**
	 * @return string
	 */
	public function get_name(): string {
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
		$subject = $subject ?? $this->name;
		return wp_mail( $to, $subject, $message, array( 'Content-Type: text/html; charset=UTF-8' ) );
	}

	/**
	 * @param $content_variables Variables to use as replacement in email content.
	 *
	 * @return string
	 */
	private function render_email_content( $content_variables ) {
		$kbm_options     = get_option( 'kbm_styling_options' );
		$main_color      = $kbm_options['main_color'] ?? Settings::get( 'main_color_default' );
		$secondary_color = $kbm_options['secondary_color'] ?? Settings::get( 'secondary_color_default' );

		$email_options = get_option( 'kbm_options_' . $this->id );
		$email_content = array(
			'header' => $email_options['header'] ?? '',
			'body'   => wpautop( $email_options['body'] ) ?? '',
			'footer' => $email_options['footer'] ?? '',
		);

		if ( ! empty( $content_variables ) ) {
			foreach ( $content_variables as $key => $variable ) {
				$email_content = str_replace( apply_filters( 'kb_mailer_content_variable_before', '%' ) . $key . apply_filters( 'kb_mailer_content_variable_after', '%' ), $variable, $email_content );
			}
		}

		ob_start();
		?>
		<div style="background-color:<?php echo esc_attr( $main_color ); ?>;margin:0;padding:20px 0;width:100%">
			<table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%">
				<tbody>
					<tr>
						<td align="center" valign="top">
							<table border="0" cellpadding="0" cellspacing="0" width="600" style="background-color:#ffffff;border-radius:2px">
								<tbody>
									<tr>
										<td align="center" valign="top">
											<table border="0" cellpadding="0" cellspacing="0" width="100%"  style="background-color:<?php echo esc_attr( $main_color ); ?>;border-bottom:0;font-weight:bold;line-height:100%;vertical-align:middle;font-family:'Helvetica Neue',Helvetica,Roboto,Arial,sans-serif;border-radius:2px 2px 0 0;padding:0 32px;">
												<tbody>
													<tr>
														<td style="display:block">
															<h1 style="font-family:'Helvetica Neue',Helvetica,Roboto,Arial,sans-serif;font-size:30px;font-weight:400;line-height:150%;margin:0;text-align:left;padding:32px 0 20px;border-bottom: 1px solid #eee;"><?php echo esc_html( $email_content['header'] ); ?></h1>
														</td>
													</tr>
												</tbody>
											</table>
										</td>
									</tr>
									<tr>
										<td>
											<table border="0" cellpadding="0" cellspacing="0" width="600">
												<tbody>
													<tr>
														<td valign="top" style="background-color:#ffffff;font-family:'Helvetica Neue',Helvetica,Roboto,Arial,sans-serif;font-weight:400;">
															<table border="0" cellpadding="20" cellspacing="0" width="100%">
																<tbody>
																	<tr>
																		<td valign="top" style="padding:32px">
																			<?php echo wp_kses_post( $email_content['body'] ); ?>
																		</td>
																	</tr>
																</tbody>
															</table>
														</td>
													</tr>
												</tbody>
											</table>
										</td>
									</tr>
									<tr>
										<td valign="top" style="padding:0;">
											<table border="0" cellpadding="10" cellspacing="0" width="100%">
												<tbody>
													<tr>
														<td colspan="2" valign="middle" style="text-align:center;padding:24px 0 0 0">
															<img src="KB-logo.png" alt="logotyp" width="200">
														</td>
													</tr>
												</tbody>
											</table>
										</td>
									</tr>
									<tr>
										<td valign="top" style="padding:0;">
											<table border="0" cellpadding="10" cellspacing="0" width="100%">
												<tbody>
													<tr>
														<td colspan="2" valign="middle" style="border-radius:6px;border:0;color:#858585;font-family:'Helvetica Neue',Helvetica,Roboto,Arial,sans-serif;font-size:12px;line-height:150%;text-align:center;padding:24px 0">
															<?php echo esc_html( $email_content['footer'] ); ?>
														</td>
													</tr>
												</tbody>
											</table>
										</td>
									</tr>
								</tbody>
							</table>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<?php
		return ob_get_clean();
	}
}
