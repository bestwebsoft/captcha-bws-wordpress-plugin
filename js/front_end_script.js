( function( $ ) {
	var cptch_reload_events = {};

	$( document ).on( "touchstart", function( event ) {
		event = event || window.event;
		var item = $( event.target );
		if ( item.attr( 'name' ) == 'ac_form_submit' ) {
			cptch_reload( item, false, 'ac_form_submit' );
			cptch_reload_events['ac_form_submit'] = setInterval( cptch_reload, ( cptch_vars.time_limit*1000 ), item, false, 'ac_form_submit' );
		}
	}).ready( function() {
		$( '.cptch_ajax_wrap' ).each( function( index ) {
			cptch_reload( $( this ), true, index );
			cptch_reload_events[ index ] = setInterval( cptch_reload, ( cptch_vars.time_limit*1000 ), $( this ), true, index );
		});
	});

	function cptch_reload( object, is_ajax_load, index ) {
		is_ajax_load = is_ajax_load || false;
		if  ( is_ajax_load ) {
			var captcha = object;
		} else {
			var captcha = object.closest( 'form' ).find( '.cptch_wrap' );
		}
		if ( ! captcha.length )
			return false;

		var captcha_block = captcha.parent(),
			input_class   = captcha.attr( 'data-cptch-class' ),
			form_slug     = captcha.attr( 'data-cptch-form' );
		$.ajax({
			type: 'POST',
			url: cptch_vars.ajaxurl,
			data: {
				action: 'cptch_reload',
				cptch_nonce: cptch_vars.nonce,
				cptch_input_class: input_class,
				cptch_form_slug: form_slug
			},
			success: function( result ) {
				if ( input_class === '' )
					captcha.replaceWith( result ); /* for default forms */
				else
					captcha_block.replaceWith( result ); /* for custom forms */
			},
			error : function ( xhr, ajaxOptions, thrownError ) {
				clearInterval( cptch_reload_events[ index ] );
				alert( xhr.status + ': ' + thrownError );
			}
		});
	}	
})(jQuery);