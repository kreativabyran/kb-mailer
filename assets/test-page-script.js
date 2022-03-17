/* global test_page_script_data */
jQuery( document ).on(
	'submit',
	'#kbm-mail-test-form',
	function ( event ) {
		event.preventDefault();

		const $submit_btn              = jQuery( '#kbm-send-test' );
		const $sending_message         = jQuery( '#kbm-sending' );
		const $sending_message_success = jQuery( '#kbm-sending-success' );
		const $sending_message_error   = jQuery( '#kbm-sending-error' );
		$submit_btn.prop( 'disabled', true );
		$sending_message.slideDown();
		$sending_message_error.slideUp();
		$sending_message_success.slideUp();

		let cvars = {};
		const to  = jQuery( '#kbm-email' ).val();

		jQuery( '.kbm-cvar-input' ).each(
			function () {
				const cvar = jQuery( this );

				cvars[ cvar.data( 'cvar' ) ] = cvar.val();
			}
		)

		console.log( to );
		console.log( cvars );
		jQuery.ajax(
			{
				url: test_page_script_data.ajax_url,
				type: 'post',
				data: {
					action: 'kbm_send_test_mail',
					wp_sec: test_page_script_data.nonce,
					to: to,
					cvars: cvars,
				}
			}
		)
			.done(
				function ( response ) {
					$submit_btn.prop( 'disabled', false );
					$sending_message.slideUp();
					$sending_message_success.slideDown();
				}
			)
			.fail(
				function ( response ) {
					$submit_btn.prop( 'disabled', false );
					$sending_message.slideUp();
					$sending_message_error.slideDown();
				}
			);
	}
)
