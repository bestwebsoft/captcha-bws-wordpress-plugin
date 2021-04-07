( function( $ ) {
	var cptch_reload_events = {};

	$( document ).on( "click", '.cptch_reload_button', function( event ) {
		cptch_reload( $( this ) );
	} ).on( "mouseup", 'input[name="ac_form_submit"]', function( event ) {
		cptch_reload( $( this ) );
	} ).on( "touchstart", function( event ) {
		event = event || window.event;
		var item = $( event.target );
		if ( '1' === cptch_vars.enlarge ) {
			var element = item.hasClass( 'cptch_img' ) ? item : ( item.closest( '.cptch_img' ).length ? item.closest( '.cptch_img' ) : null );
			if ( element && element.length ) {
				event.preventDefault();
				element.toggleClass( 'cptch_reduce' );
				$( '.cptch_img' ).not( element ).removeClass( 'cptch_reduce' );
			} else {
				$( '.cptch_img' ).removeClass( 'cptch_reduce' );
			}
		}
		if ( item.hasClass( 'cptch_reload_button' ) || 'ac_form_submit' === item.attr( 'name' ) ) {
			if ( 'invisible' === item.data( 'cptch-type' ) ) {
				cptch_reload( item, true, 'ac_form_submit' );
				cptch_reload_events['ac_form_submit'] = setInterval( cptch_reload, ( cptch_vars.time_limit*1000 ), item, true, 'ac_form_submit' );
			} else {
				cptch_reload( item );
			}
		}
	} ).ready( function() {
		if ( $( '.cptch_recognition' ).length ) {
			$( '.cptch_recognition' ).each( function() {
				$( this ).find( '.cptch_input' ).css( 'width', $( this ).find( '.cptch_images_wrap' ).css( 'width' ) );
			} );
		}

		var ajax_containers = $( '.cptch_ajax_wrap' );

		if ( ! ajax_containers.length ) {
			return;
		}

		ajax_containers.each( function( index ) {
			if ( 'invisible' === $( this ).data( 'cptch-type' ) ) {
				cptch_reload( $( this ), true, index );
				cptch_reload_events[ index ] = setInterval( cptch_reload, ( cptch_vars.time_limit*1000 ), $( this ), true, index );
			} else {
				cptch_reload( $( this ), true );
			}
		} );
	} );

	/**
	 * Reload captcha
	 */
	function cptch_reload( object, is_ajax_load, index ) {
		is_ajax_load = is_ajax_load || false;
		if ( is_ajax_load ) {
			var captcha = object;
		} else {
			var captcha = object.hasClass( 'cptch_reload_button' ) ? object.parent().parent( '.cptch_wrap' ) : object.closest( 'form' ).find( '.cptch_wrap' );
		}
		var button  = captcha.find( '.cptch_reload_button' );
		if ( ! captcha.length || button.hasClass( 'cptch_active' ) ) {
			return false;
		}
		button.addClass( 'cptch_active' );
		var captcha_block	= captcha.parent(),
			input			= captcha.find( 'input:text' ),
			input_name		= is_ajax_load ? captcha.attr( 'data-cptch-input' ) : input.attr( 'name' ),
			input_class		= is_ajax_load || ! input.length ? captcha.attr( 'data-cptch-class' ) : input.attr( 'class' ).replace( /cptch_input/, '' ).replace( /^\s+|\s+$/g, '' ),
			form_slug		= is_ajax_load ? captcha.attr( 'data-cptch-form' ) : captcha_block.find( 'input[name="cptch_form"]' ).val();

		$.ajax( {
			type: 'POST',
			url: cptch_vars.ajaxurl,
			data: {
				action:				'cptch_reload',
				cptch_nonce:		cptch_vars.nonce,
				cptch_input_name:	input_name,
				cptch_input_class:	input_class,
				cptch_form_slug:	form_slug,
			},
			success: function( result ) {
				var result = $.parseJSON( result );
				captcha_block.find( '.cptch_to_remove' ).remove();
				if ( '' === input_class ) {
					captcha.replaceWith( result['display'] ); /* for default forms */
				} else {
					captcha_block.replaceWith(result['display']); /* for custom forms */
				}

				if ( $( result['display'] ).hasClass( 'cptch_recognition' ) || $( result['display'] ).find( '.cptch_wrap' ).hasClass( 'cptch_recognition' ) ) {
					$( '.cptch_recognition' ).each( function() {
						$( this ).find( '.cptch_input' ).css( 'width', $( this ).find( '.cptch_images_wrap' ).css( 'width' ) );
					} );
				}

				var id = "cptch_time_limit_notice_" + result['id_postfix'];
	            setTimeout(
	                function() {
	                    var notice = document.getElementById( id );
	                    if ( notice )
	                        notice.style.display = "block";
	                },
	                result['limit_time'] + '000'
	            ); 
			},
			error : function ( xhr, ajaxOptions, thrownError ) {
				clearInterval( cptch_reload_events[ index ] );
				alert( xhr.status + ': ' + thrownError );
			}
		} );
	}
} )( jQuery );