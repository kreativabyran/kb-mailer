/* global kbm_options_page_script */
jQuery( document ).ready(
	function( $ ) {
		$( '.kbm-color-pickers' ).wpColorPicker();

		$( '.upload_image_button' ).click(
			function() {
				const send_attachment_bkp       = wp.media.editor.send.attachment;
				const button                    = $( this );
				wp.media.editor.send.attachment = function(props, attachment) {
					$( button ).parent().prev().attr( 'src', attachment.url ).show();
					$( button ).next().show();
					$( button ).prev().val( attachment.id );
					wp.media.editor.send.attachment = send_attachment_bkp;
				}
				wp.media.editor.open( button );
				return false;
			}
		);

		// The "Remove" button (remove the value from input type='hidden')
		$( '.remove_image_button' ).click(
			function() {
				const answer = confirm( kbm_options_page_script.remove_image_confirm );
				if ( true === answer) {
					$( this ).parent().prev().hide();
					$( this ).hide();
					$( this ).prev().prev().val( '' );
				}
				return false;
			}
		);
	}
);
