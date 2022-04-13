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
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_option_scripts' ) );
	}

	/**
	 * Enqueues styles and scripts for color picker in wp-admin.
	 * @param string $hook_suffix The current admin page.
	 *
	 * @return void
	 */
	public function enqueue_option_scripts( $hook_suffix ) {
		if ( 'toplevel_page_' . Settings::get( 'admin_page_slug' ) === $hook_suffix ) {
			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_script( 'kbm-options-page-script', KBM_URI . 'assets/options-page-script.js', array( 'wp-color-picker' ), filemtime( KBM_DIR . 'assets/options-page-script.js' ), true );
			wp_localize_script(
				'kbm-options-page-script',
				'kbm_options_page_script',
				array(
					'remove_image_confirm' => __( 'Are you sure you want to remove the logo?', 'kb-mailer' ),
				)
			);
			wp_enqueue_media();
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
			apply_filters( 'kb_mailer_admin_page_capability', 'manage_options' ),
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
			<form method="post" action="options.php">
				<?php
				settings_fields( 'kbm_styling_options_group' );
				do_settings_sections( Settings::get( 'admin_page_slug' ) );
				submit_button();
				?>
			</form>
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
			'logo',
			__( 'Logo', 'kb-mailer' ),
			array( $this, 'logo_callback' ),
			Settings::get( 'admin_page_slug' ),
			'kbm_styling_section'
		);

		add_settings_field(
			'logo_url',
			__( 'Logo URL', 'kb-mailer' ),
			array( $this, 'logo_url_callback' ),
			Settings::get( 'admin_page_slug' ),
			'kbm_styling_section'
		);

		add_settings_field(
			'footer',
			__( 'Footer', 'kb-mailer' ),
			array( $this, 'footer_callback' ),
			Settings::get( 'admin_page_slug' ),
			'kbm_styling_section'
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

		if ( isset( $input['footer'] ) ) {
			$new_input['footer'] = wp_kses_post( $input['footer'] );
		}

		if ( isset( $input['logo'] ) ) {
			$new_input['logo'] = sanitize_text_field( $input['logo'] );
		}

		if ( isset( $input['logo_url'] ) ) {
			$new_input['logo_url'] = esc_url( $input['logo_url'] );
		}

		return $new_input;
	}

	/**
	 * Print the Section text
	 */
	public function print_section_info() {
		esc_html_e( 'Styling and content for all KB Mailer emails', 'kb-mailer' );
	}

	/**
	 * Prints input for main color.
	 * @return void
	 */
	public function main_color_callback() {
		printf(
			'<input type="text" id="main_color" class="kbm-color-pickers" data-default-color="%s" name="kbm_styling_options[main_color]" value="%s" />',
			esc_attr( Settings::get( 'main_color_default' ) ),
			esc_attr( $this->options['main_color'] ?? Settings::get( 'main_color_default' ) )
		);
	}

	/**
	 * Get the settings option array and print one of its values
	 */
	public function footer_callback() {
		$text = $this->options['footer'] ?? '';
		wp_editor(
			$text,
			'kbm-footer',
			array(
				'media_buttons' => false,
				'textarea_name' => 'kbm_styling_options[footer]',
			)
		);
	}

	/**
	 * Get the settings option array and print one of its values
	 */
	public function logo_url_callback() {
		printf(
			'<input type="text" id="logo_url" name="kbm_styling_options[logo_url]" value="%s" />',
			isset( $this->options['logo_url'] ) ? esc_attr( $this->options['logo_url'] ) : ''
		);
	}

	/**
	 * Get the settings option array and print one of its values
	 */
	public function logo_callback() {
		$logo = $this->options['logo'] ?? '';

		if ( ! empty( $logo ) ) {
			$image_attributes = wp_get_attachment_image_src( $logo, 'medium' );
			$src              = $image_attributes[0];
			$value            = $logo;
		} else {
			$value = '';
			$src   = '';
		}

		// Print HTML field
		echo '
        <div class="upload">
            <img src="' . esc_url( $src ) . '" width="200px" style="' . ( empty( $logo ) ? 'display: none;' : '' ) . '"/>
            <div>
                <input type="hidden" name="kbm_styling_options[logo]" id="kbm_styling_options[logo]" value="' . esc_attr( $value ) . '" />
                <button type="submit" class="upload_image_button button">' . esc_html__( 'Upload', 'kb-mailer' ) . '</button>
                <button type="submit" class="remove_image_button button"style="' . ( empty( $logo ) ? 'display: none;' : '' ) . '">&times;</button>
            </div>
        </div>
    ';
	}
}
