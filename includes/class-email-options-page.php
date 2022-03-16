<?php
/**
 * Options page for each registered email.
 *
 * @package kb-mailer
 */

namespace KB_Mailer;

class Email_Options_Page {
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
		add_action( 'admin_init', array( $this, 'page_init' ) );
	}

	/**
	 * Add options page
	 */
	public function add_plugin_page() {

		add_submenu_page(
			null,
			$this->name . ' - KB Mailer',
			$this->name,
			'manage_options',
			Settings::get( 'admin_page_slug' ) . '-' . $this->id,
			array( $this, 'create_admin_page' )
		);
	}

	/**
	 * Options page callback
	 */
	public function create_admin_page() {
		// Set class property
		$this->options = get_option( 'kbm_options_' . $this->id );
		?>
		<div class="wrap">
			<h1><?php echo 'KB Mailer - ' . esc_html( $this->name ); ?></h1>
			<form method="post" action="options.php">
				<?php
				// This prints out all hidden setting fields
				settings_fields( 'kbm_option_group_' . $this->id );
				do_settings_sections( Settings::get( 'admin_page_slug' ) . '-' . $this->id );
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
			'kbm_option_group_' . $this->id, // Option group
			'kbm_options_' . $this->id, // Option name
			array( $this, 'sanitize' ) // Sanitize
		);

		add_settings_section(
			'kbm_section_content_' . $this->id, // ID
			__( 'Email Content', 'kb-mailer' ), // Title
			array( $this, 'print_section_info' ), // Callback
			Settings::get( 'admin_page_slug' ) . '-' . $this->id // Page
		);

		add_settings_field(
			'header', // ID
			__( 'Header', 'kb-mailer' ), // Title
			array( $this, 'header_callback' ), // Callback
			Settings::get( 'admin_page_slug' ) . '-' . $this->id, // Page
			'kbm_section_content_' . $this->id // Section
		);

		add_settings_field(
			'body', // ID
			__( 'Body', 'kb-mailer' ), // Title
			array( $this, 'body_callback' ), // Callback
			Settings::get( 'admin_page_slug' ) . '-' . $this->id, // Page
			'kbm_section_content_' . $this->id // Section
		);

		add_settings_field(
			'footer',
			__( 'Footer', 'kb-mailer' ),
			array( $this, 'footer_callback' ),
			Settings::get( 'admin_page_slug' ) . '-' . $this->id,
			'kbm_section_content_' . $this->id
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

		if ( isset( $input['header'] ) ) {
			$new_input['header'] = sanitize_text_field( $input['header'] );
		}

		if ( isset( $input['body'] ) ) {
			$new_input['body'] = wp_kses_post( $input['body'] );
		}

		if ( isset( $input['footer'] ) ) {
			$new_input['footer'] = sanitize_text_field( $input['footer'] );
		}

		return $new_input;
	}

	/**
	 * Print the Section text
	 */
	public function print_section_info() {
		if ( ! empty( $this->content_variables ) ) {
			esc_html_e( 'The following content variables are available for use in the email:', 'kb-mailer' );
			?>
			<table>
				<thead>
					<tr>
						<th><?php esc_html_e( 'Variable ID', 'kb-mailer' ); ?></th>
						<th><?php esc_html_e( 'Description', 'kb-mailer' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php
					foreach ( $this->content_variables as $key => $content_variable ) {
						?>
							<tr>
								<td><?php echo esc_html( Settings::get( 'content_variable_before' ) . $key . Settings::get( 'content_variable_after' ) ); ?></td>
								<td><?php echo esc_html( $content_variable ); ?></td>
							</tr>
						<?php
					}
					?>
				</tbody>
			</table>
			<?php
		}
	}

	/**
	 * Get the settings option array and print one of its values
	 */
	public function header_callback() {
		printf(
			'<input type="text" id="email_header" name="kbm_options_' . $this->id . '[header]" value="%s" />',
			isset( $this->options['header'] ) ? esc_attr( $this->options['header'] ) : ''
		);
	}

	/**
	 * Get the settings option array and print one of its values
	 */
	public function body_callback() {
		$text = $this->options['body'] ?? '';
		wp_editor(
			$text,
			'kbm-body',
			array(
				'media_buttons' => false,
				'textarea_name' => 'kbm_options_' . $this->id . '[body]',
			)
		);
	}

	/**
	 * Get the settings option array and print one of its values
	 */
	public function footer_callback() {
		printf(
			'<input type="text" id="email_footer" name="kbm_options_' . $this->id . '[footer]" value="%s" />',
			isset( $this->options['footer'] ) ? esc_attr( $this->options['footer'] ) : ''
		);
	}

}
