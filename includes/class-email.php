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
			$this->options_page = new Email_Options_Page( $this->id, $this->name, $this->content_variables );
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

	public function send( $to, $content_variables = array(), $subject = false ) {
		$message = $this->render_email_content( $content_variables );
		$subject = $subject ?? $this->name;
		wp_mail( $to, $subject, $message, array( 'Content-Type: text/html; charset=UTF-8' ) );
	}

	private function render_email_content( $content_variables ) {
		$kbm_options   = get_option( 'kbm_styling_options' );
		$email_options = get_option( 'kbm_options_' . $this->id );

		$email_content = array(
			'header' => $email_options['header'],
			'body'   => $email_options['body'],
			'footer' => $email_options['footer'],
		);

		if ( ! empty( $content_variables ) ) {
			foreach ( $content_variables as $key => $variable ) {
				$email_content = str_replace( Settings::get( 'content_variable_before' ) . $key . Settings::get( 'content_variable_after' ), $variable, $email_content );
			}
		}

		ob_start();
		?>
		<div style="background-color:#f7f7f7;margin:0;padding:70px 0;width:100%">
			<table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%">
				<tbody>
					<tr>
						<td align="center" valign="top">
							<table border="0" cellpadding="0" cellspacing="0" width="600" style="background-color:#ffffff;border:1px solid #dedede;border-radius:3px">
								<tbody>
									<tr>
										<td align="center" valign="top">
											<table border="0" cellpadding="0" cellspacing="0" width="100%"  style="background-color:<?php echo esc_attr( $kbm_options['main_color'] ); ?>;color:<?php echo esc_attr( $kbm_options['secondary_color'] ); ?>;border-bottom:0;font-weight:bold;line-height:100%;vertical-align:middle;font-family:'Helvetica Neue',Helvetica,Roboto,Arial,sans-serif;border-radius:3px 3px 0 0">
												<tbody>
													<tr>
														<td style="padding:36px 48px;display:block">
															<h1 style="font-family:'Helvetica Neue',Helvetica,Roboto,Arial,sans-serif;font-size:30px;font-weight:300;line-height:150%;margin:0;text-align:left;color:<?php echo esc_attr( $kbm_options['secondary_color'] ); ?>;background-color:inherit"><?php echo esc_html( $email_content['header'] ); ?></h1>
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
														<td valign="top" style="background-color:#ffffff">
															<table border="0" cellpadding="20" cellspacing="0" width="100%">
																<tbody>
																	<tr>
																		<td valign="top" style="padding:48px 48px 32px">
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
								</tbody>
							</table>
						</td>
					</tr>
					<tr>
						<td align="center" valign="top">
							<table border="0" cellpadding="10" cellspacing="0" width="600">
								<tbody>
									<tr>
										<td valign="top" style="padding:0;border-radius:6px">
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

		$message = ob_get_clean();

		return $message;
	}
}