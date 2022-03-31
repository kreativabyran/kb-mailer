<?php

namespace KB_Mailer;

class Email_Test_Page {
	/**
	 * @var string Email identifier. For identifying options for specific email.
	 */
	private $id;

	/**
	 * @var string Name of email. Displayed in wp-admin.
	 */
	private $name;

	/**
	 * @var string[] Variables used for dynamic content in emails.
	 * Array element key is an id for the variable, used for replacement.
	 * Element value is name of the variable, displayed in wp-admin.
	 */
	private $content_variables;

	/**
	 * @var mixed $options Holds the values to be used in the fields callbacks
	 */
	private $options;

	/**
	 * Start up
	 */
	public function __construct( $id, $name, $content_variables ) {
		$this->id                = $id;
		$this->name              = $name;
		$this->content_variables = $content_variables;

		add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'wp_ajax_kbm_send_test_mail', array( $this, 'ajax_test_mail' ) );
	}

	/**
	 * Enqueues styles and scripts for color picker in wp-admin.
	 * @param string $hook_suffix The current admin page.
	 *
	 * @return void
	 */
	public function enqueue_scripts( $hook_suffix ) {
		if ( 'admin_page_' . Settings::get( 'admin_page_slug' ) . '-test-' . $this->id === $hook_suffix ) {
			wp_enqueue_script( 'kbm-test-page-script', KBM_URI . 'assets/test-page-script.js', array( 'wp-color-picker' ), filemtime( KBM_DIR . 'assets/test-page-script.js' ), true );
			wp_localize_script(
				'kbm-test-page-script',
				'test_page_script_data',
				array(
					'ajax_url' => admin_url( 'admin-ajax.php' ),
					'nonce'    => wp_create_nonce( 'kb_test_mail_' . $this->id ),
				)
			);
		}
	}

	/**
	 * Add options page
	 */
	public function add_plugin_page() {

		add_submenu_page(
			null,
			$this->name . ' - Test - KB Mailer',
			$this->name,
			apply_filters( 'kb_mailer_admin_page_capability', 'manage_options' ),
			Settings::get( 'admin_page_slug' ) . '-test-' . $this->id,
			array( $this, 'create_admin_page' )
		);
	}

	/**
	 * Options page callback
	 */
	public function create_admin_page() {
		// Set class property
		$this->options = get_option( 'kbm_options_test_' . $this->id );
		?>
		<div class="wrap">
			<a href="<?php echo esc_url( admin_url( 'admin.php' ) . '?page=' . Settings::get( 'admin_page_slug' ) ); ?>"><?php esc_html_e( 'Go back', 'kb-mailer' ); ?></a>
			<h1><?php echo 'KB Mailer - Test - ' . esc_html( $this->name ); ?></h1>
			<form method="post" id="kbm-mail-test-form">
				<table class="form-table">
					<tbody>
						<tr>
							<th>
								<?php esc_html_e( 'Send test email to*', 'kb-mailer' ); ?>
							</th>
							<td>
								<?php
								printf(
									'<input type="text" required id="kbm-email" value="%s" />',
									esc_attr( get_option( 'admin_email' ) )
								);
								?>
							</td>
						</tr>
					<?php
					if ( ! empty( $this->content_variables ) ) {
						foreach ( $this->content_variables as $key => $desc ) {
							?>
								<tr>
									<th>
										<?php echo esc_html( $desc ); ?>
									</th>
									<td>
										<input class="kbm-cvar-input" type="text" data-cvar="<?php echo esc_attr( $key ); ?>" />
									</td>
								</tr>
							<?php
						}
					}
					?>
					</tbody>
				</table>
				<button id="kbm-send-test" type="submit" class="button button-primary"><?php esc_html_e( 'Send', 'kb-mailer' ); ?></button>
				<p id="kbm-sending" style="display: none;"><?php esc_html_e( 'Sending...', 'kb-mailer' ); ?></p>
				<p id="kbm-sending-success" style="display: none;"><?php esc_html_e( 'Test email sent successfully!', 'kb-mailer' ); ?></p>
				<p id="kbm-sending-error" style="display: none;"><?php esc_html_e( 'Failed sending message. Please try again.', 'kb-mailer' ); ?></p>
			</form>
			<div style="margin-top: 20px;">
				<a href="<?php echo esc_url( admin_url( 'admin.php' ) . '?page=' . Settings::get( 'admin_page_slug' ) ); ?>"><?php esc_html_e( 'Go back', 'kb-mailer' ); ?></a>
			</div>
		</div>
		<?php
	}

	public function ajax_test_mail() {
		check_ajax_referer( 'kb_test_mail_' . $this->id, 'wp_sec' );
		$sent = Emails::send( $this->id, $_POST['to'], (array) $_POST['cvars'] );
		if ( $sent ) {
			wp_send_json_success( 'success!' );
		} else {
			wp_send_json_error( 'oh no!', 500 );
		}
	}
}
