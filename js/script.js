/*!
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/
 */
( function( $ ) {
	$( document ).ready( function() {
		/*pls */
		/* include color-picker */
		if ( $.fn.wpColorPicker ) {
			$( '.cptch_color_field' ).wpColorPicker();
		}
		/* pls*/
		/**
		 * Handle the styling of the "Settings" tab on the plugin settings page
		 * @since 4.2.3
		 */
		var imageFormat	= $( '#cptch_operand_format_images' );

		/*
		* Hide "time limit thershold" field under unchecked "time limit" field
		*/
		function cptch_time_limit() {
			if ( ! $( '#cptch_enable_time_limit' ).is( ':checked' ) ) {
				$( '.cptch_time_limit' ).hide();
			} else {
				$( '.cptch_time_limit' ).show();
			}
		}

		cptch_time_limit();
		$( $( '#cptch_enable_time_limit' ) ).on( 'click', function() {
			cptch_time_limit();
		} );

		/*
		 * Hide all unused related forms on settings page
		 */
		$.each( $( "input[name*='[enable]']" ), function() {
			var formName	= '.' + $( this ).attr( 'id' ).replace( 'enable', 'related_form' ),
				formBlock	= $( formName );

			$( this ).is( ':checked' ) ? formBlock.show() : formBlock.hide();

			$( this ).on( 'click', function() {
				if ( $( this ).is( ':checked' ) ) {
					formBlock.show();
				} else {
					formBlock.hide();
				}
			} );
		} );

		// hide/show whitelist "add new form"
		$( 'button[name="cptch_show_allowlist_form"]' ).on( 'click', function() {
			$( this ).parent( 'form' ).hide();
			$( '.cptch_allowlist_form' ).show();
			return false;
		} );

		function cptch_type() {

			var cptchType = $( 'input[name="cptch_type"]:checked' ).val();

			if ( 'recognition' === cptchType ) {
				$( '.cptch_for_math_actions, .cptch_for_slide' ).hide();
				$( '.cptch_for_recognition' ).show();
				imageFormat.attr( 'checked', 'checked' );
				cptchImageOptions();
				cptch_time_limit();
			} else if ( 'slide' === cptchType ) {
				$( '.cptch_for_recognition, .cptch_for_math_actions, .cptch_time_limit' ).hide();
				$( '.cptch_for_slide' ).show();
				imageFormat.removeAttr('checked' );
				cptchImageOptions();
			} else if ( 'invisible' === cptchType ){
				$( '.cptch_for_recognition, .cptch_for_math_actions, .cptch_time_limit, .cptch_for_slide' ).hide();
				imageFormat.removeAttr( 'checked' );
				cptchImageOptions();
			} else {
				$( '.cptch_for_recognition, .cptch_for_slide' ).hide();
				$( '.cptch_for_math_actions' ).show();
				cptch_time_limit();
			}
		}

		cptch_type();
		$( 'input[name="cptch_type"]' ).on( 'click', function() {
			cptch_type();
		} );

		/* Handle the displaying of notice message above lists of image packages */
		function cptchImageOptions() {
			var isChecked = imageFormat.is( ':checked' );
			if ( isChecked ) {
				$( '.cptch_images_options' ).show();
				$( '.cptch_enable_to_use_several_packages' ).closest( '.bws_pro_version_bloc' ).show();
			} else {
				$( '.cptch_images_options' ).hide();
				$( '.cptch_enable_to_use_several_packages' ).closest( '.bws_pro_version_bloc' ).hide();
			}
		}
		cptchImageOptions();
		imageFormat.on( 'click', function() { cptchImageOptions(); } );

		/* Open/hide packages pro tab */
		$( '#cptch_show_packages_pro_tab_open' ).on( 'click', function() {
			$( '#cptch_show_packages_pro_tab' ).toggle();
		} );
	} );
} )( jQuery );
