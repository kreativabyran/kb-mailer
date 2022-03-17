<?php
/**
 * Plugin main options page.
 *
 * @package kb-mailer
 */

namespace KB_Mailer;

class Options_Page {
	/**
	 * Holds the values to be used in the fields callbacks
	 */
	private $options;

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_menu_page' ) );
		add_action( 'admin_init', array( $this, 'page_init' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_color_picker' ) );
	}

	/**
	 * Enqueues styles and scripts for color picker in wp-admin.
	 * @param string $hook_suffix The current admin page.
	 *
	 * @return void
	 */
	public function enqueue_color_picker( $hook_suffix ) {
		if ( 'toplevel_page_' . Settings::get( 'admin_page_slug' ) === $hook_suffix ) {
			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_script( 'kbm-options-page-script', KBM_URI . 'assets/options-page-script.js', array( 'wp-color-picker' ), filemtime( KBM_DIR . 'assets/options-page-script.js' ), true );
		}
	}

	/**
	 * Registers menu page.
	 * @return void
	 */
	public function add_menu_page() {
		\add_menu_page(
			'KB Mailer',
			'KB Mailer',
			'manage_options',
			Settings::get( 'admin_page_slug' ),
			array( $this, 'render_menu_page' ),
			'dashicons-email-alt',
			120
		);
	}

	/**
	 * Renders menu page.
	 * @return void
	 */
	public function render_menu_page() {
		$this->options = get_option( 'kbm_styling_options' );
		$emails        = Emails::get();
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'KB Mailer', 'kb-mailer' ); ?></h1>
			<form method="post" action="options.php">
				<?php
				settings_fields( 'kbm_styling_options_group' );
				do_settings_sections( Settings::get( 'admin_page_slug' ) );
				submit_button();
				?>
			</form>
			<h3><?php esc_html_e( 'Emails', 'kb-mailer' ); ?></h3>
			<?php
			if ( ! empty( $emails ) ) {
				?>
					<table class="widefat fixed striped">
						<thead>
							<tr>
								<th><?php esc_html_e( 'Name', 'kb-mailer' ); ?></th>
								<th><?php esc_html_e( 'Edit', 'kb-mailer' ); ?></th>
								<th><?php esc_html_e( 'Test', 'kb-mailer' ); ?></th>
							</tr>
						</thead>
						<tbody>
							<?php
							foreach ( $emails as $id => $email ) {
								?>
								<tbody>
									<tr>
										<td><?php echo esc_html( $email->get_name() ); ?></td>
										<td>
											<a href="<?php echo esc_url( admin_url( 'admin.php' ) . '?page=' . Settings::get( 'admin_page_slug' ) . '-' . $id ); ?>">
												<span class="dashicons dashicons-edit"></span>
											</a>
										</td>
										<td>
											<a href="<?php echo esc_url( admin_url( 'admin.php' ) . '?page=' . Settings::get( 'admin_page_slug' ) . '-test-' . $id ); ?>">
												<span class="dashicons dashicons-email-alt2"></span>
											</a>
										</td>
									</tr>
								</tbody>
								<?php
							}
							?>
						</tbody>
					</table>
				<?php
			} else {
				echo '<p>' . esc_html__( 'You have not added any emails', 'kb-mailer' ) . '</p>';
			}
			?>
		</div>
		<?php
	}

	/**
	 * Register and add settings
	 */
	public function page_init() {
		register_setting(
			'kbm_styling_options_group', // Option group
			'kbm_styling_options', // Option name
			array( $this, 'sanitize' ) // Sanitize
		);

		add_settings_section(
			'kbm_styling_section', // ID
			__( 'Styling', 'kb-mailer' ), // Title
			array( $this, 'print_section_info' ), // Callback
			Settings::get( 'admin_page_slug' ) // Page
		);

		add_settings_field(
			'main_color', // ID
			__( 'Main color', 'kb-mailer' ), // Title
			array( $this, 'main_color_callback' ), // Callback
			Settings::get( 'admin_page_slug' ), // Page
			'kbm_styling_section' // Section
		);

		add_settings_field(
			'secondary_color', // ID
			__( 'Second color', 'kb-mailer' ), // Title
			array( $this, 'secondary_color_callback' ), // Callback
			Settings::get( 'admin_page_slug' ), // Page
			'kbm_styling_section' // Section
		);

	}

	/**
	 * Sanitize each setting field as needed
	 *
	 * @param array $input Contains all settings fields as array keys.
	 *
	 * @return array Sanitized input.
	 */
	public function sanitize( $input ) {
		$new_input = array();

		if ( isset( $input['main_color'] ) ) {
			$new_input['main_color'] = sanitize_text_field( $input['main_color'] );
		}

		if ( isset( $input['secondary_color'] ) ) {
			$new_input['secondary_color'] = sanitize_text_field( $input['secondary_color'] );
		}

		return $new_input;
	}

	/**
	 * Print the Section text
	 */
	public function print_section_info() {
		esc_html_e( 'Styling for all KB Mailer emails', 'kb-mailer' );
	}

	/**
	 * Prints input for main color.
	 * @return void
	 */
	public function main_color_callback() {
		printf(
			'<input type="text" id="main_color" class="kbm-color-pickers" data-default-color="' . Settings::get( 'main_color_default' ) . '" name="kbm_styling_options[main_color]" value="%s" />',
			isset( $this->options['main_color'] ) ? esc_attr( $this->options['main_color'] ) : ''
		);
	}

	/**
	 * Prints input for secondary color.
	 * @return void
	 */
	public function secondary_color_callback() {
		printf(
			'<input type="text" id="secondary_color" class="kbm-color-pickers" data-default-color="' . Settings::get( 'secondary_color_default' ) . '" name="kbm_styling_options[secondary_color]" value="%s" />',
			isset( $this->options['secondary_color'] ) ? esc_attr( $this->options['secondary_color'] ) : ''
		);
	}
}
