<?php
/**
 * This functions are used for adding captcha in Formidable Contact Form
 **/

if ( ! function_exists( 'cptch_frm_add_basic_field' ) ) {
	/**
	 * Add the field to the top section of the fields
	 *
	 * @param array $fields All fields for Formidable.
	 * @return array $fields Return fields array.
	 */
	function cptch_frm_add_basic_field( $fields ) {

		$fields['captcha-bws'] = array(
			'name' => 'Captcha BWS',
			'icon' => 'frm_icon_font frm_shield_check_icon',
		);

		return $fields;
	}
}

if ( ! function_exists( 'cptch_frm_set_defaults' ) ) {
	/**
	 * Set default settings for new field.
	 *
	 * @param array $field_data All fields for Formidable.
	 * @return array $field_data Return fields array.
	 */
	function cptch_frm_set_defaults( $field_data ) {
		global $cptch_options;
		if ( 'captcha-bws' === $field_data['type'] ) {
			$field_data['required']                 = 1;
			$field_data['field_options']['blank']   = $cptch_options['no_answer'];
			$field_data['field_options']['invalid'] = $cptch_options['wrong_answer'];
		}

		return $field_data;
	}
}

if ( ! function_exists( 'cptch_frm_show_the_admin_field' ) ) {
	/**
	 * Show the field in the builder page.
	 *
	 * @param array $field reCaptcha field for display in admin.
	 */
	function cptch_frm_show_the_admin_field( $field ) {
		if ( 'captcha-bws' !== $field['type'] ) {
			return;
		}
		?>
		[bws_captcha]
		<?php
	}
}

if ( ! function_exists( 'cptch_frm_show_front_field' ) ) {
	/**
	 * Show the field in form.
	 *
	 * @param array $field reCaptcha field for display in front.
	 * @param array $field_name reCaptcha field name.
	 * @param array $atts reCaptcha atts.
	 */
	function cptch_frm_show_front_field( $field, $field_name, $atts ) {
		global $cptch_options;
		if ( 'captcha-bws' !== $field['type'] ) {
			return;
		}

		echo cptch_display_captcha_shortcode( array( 'form_slug' => 'frm_contact_form' ) );
	}
}

if ( ! function_exists( 'cptch_frm_custom_validation' ) ) {
	/**
	 * Add custom validation.
	 *
	 * @param array $errors Errors array for form.
	 * @param array $posted_field Current posted field.
	 * @param array $posted_value Current posted value.
	 */
	function cptch_frm_custom_validation( $errors, $posted_field, $posted_value ) {
		global $cptch_options;

		if ( 'captcha-bws' === $posted_field->type ) {
			if ( isset( $errors[ 'field' . $posted_field->id ] ) ) {
				unset( $errors[ 'field' . $posted_field->id ] );
			}
			if ( isset( $_POST ) && ( isset( $_POST['cptch_number'] ) || isset( $_POST['cptch_result'] ) ) ) {
				$cptch_check = cptch_check_custom_form( true, 'string', 'frm_contact_form' );
			}
			if ( ! empty( $cptch_check ) && true !== $cptch_check ) {
				$errors[ 'field' . $posted_field->id ] = esc_html( $cptch_check );
			}
		}
		return $errors;
	}
}

add_filter( 'frm_available_fields', 'cptch_frm_add_basic_field' );
//add_filter( 'frm_pro_available_fields', 'cptch_frm_add_basic_field' );
add_filter( 'frm_before_field_created', 'cptch_frm_set_defaults' );
add_action( 'frm_display_added_fields', 'cptch_frm_show_the_admin_field' );
add_action( 'frm_form_fields', 'cptch_frm_show_front_field', 10, 3 );
add_filter( 'frm_validate_field_entry', 'cptch_frm_custom_validation', 10, 3 );
