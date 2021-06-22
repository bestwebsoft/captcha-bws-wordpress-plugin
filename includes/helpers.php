<?php
/**
 * Contains the functions which are use on plugin admin pages
 * @package Captcha by BestWebSoft
 * @since   5.0.0
 */

/**
 * Fetch plugin default options
 * @param  void
 * @return array
 */
if ( ! function_exists( 'cptch_get_default_options' ) ) {
	function cptch_get_default_options() {
		global $cptch_plugin_info;

		$default_options = array(
			'plugin_option_version'			=> $cptch_plugin_info["Version"],
			'str_key'						=> array( 'time' => '', 'key' => '' ),
			'type'							=> 'invisible',
			'math_actions'					=> array( 'plus', 'minus', 'multiplications' ),
			'operand_format'				=> array( 'numbers', 'words', 'images' ),
			'images_count'					=> 5,
			'title'							=> '',
			'required_symbol'				=> '*',
			'text_start_slide'              => esc_html__( 'Slide to verify', 'captcha-bws' ),
			'text_end_slide'                => esc_html__( 'Verification passed', 'captcha-bws' ),
			'display_reload_button'			=> true,
			'enlarge_images'				=> false,
			'used_packages'					=> array(),
			'enable_time_limit'				=> false,
			'time_limit'					=> 120,
			'no_answer'						=> esc_html__( 'Please complete the captcha.', 'captcha-bws' ),
			'wrong_answer'					=> esc_html__( 'Please enter correct captcha value.', 'captcha-bws' ),
			'time_limit_off'				=> esc_html__( 'Time limit exceeded. Please complete the captcha once again.', 'captcha-bws' ),
			'time_limit_off_notice'			=> esc_html__( 'Time limit exceeded. Please complete the captcha once again.', 'captcha-bws' ),
			'allowlist_message'				=> esc_html__( 'Your IP address is allow listed.', 'captcha-bws' ),
			'load_via_ajax'					=> false,
			'use_limit_attempts_allowlist'	=> false,
			'display_settings_notice'		=> 1,
			'suggest_feature_banner'		=> 1,
			'forms'							=> array(),
		);

		$forms = cptch_get_default_forms();

		foreach ( $forms as $form ) {
			$default_options['forms'][ $form ] = array(
				'enable'				=> in_array( $form, array( 'wp_login', 'wp_register', 'wp_lost_password', 'wp_comments' ) ),
				'hide_from_registered'	=> 'wp_comments' == $form,
                'used_packages'			=> array(),
                'time_limit'			 => 120,
                'enable_time_limit'		=> false
			);
		}

		return $default_options;
	}
}

/**
 * Fetch the list of forms which are compatible with the plugin
 * @param  void
 * @return array
 */
if ( ! function_exists( 'cptch_get_default_forms' ) ) {
	function cptch_get_default_forms() {
		$defaults = array(
			'general', 'wp_login', 'wp_register',
			'wp_lost_password', 'wp_comments',
			'bws_contact', 'bws_booking'
		);

		$defaults = apply_filters( 'cptch_get_additional_forms_slugs', $defaults );
		/*
		 * Add user forms to defaults
		 */
		$new_forms = apply_filters( 'cptch_add_form', array() );

		if ( ! is_array( $new_forms ) || empty( $new_forms ) ) {
			return $defaults;
		}

		$new = array_filter( array_map( 'esc_attr', array_keys( $new_forms ) ) );

		return array_unique( array_merge( $defaults, $new ) );
	}
}

/**
 * Updates the plugin options if there was any changes in the new plugin version
 * @see    register_cptch_settings()
 * @param  array  $old_options
 * @param  array  $default_options
 * @return array
 */
if ( ! function_exists( 'cptch_parse_options' ) ) {
	function cptch_parse_options( $old_options, $default_options ) {
		global $cptch_plugin_info;

		$new_options = cptch_merge_recursive( $default_options, $old_options );

		/* Replace old option fields names by new */
		$args = array(
			'str_key'				=> array( 'cptchpls_str_key', 'cptch_str_key' ),
			'required_symbol'		=> array( 'cptchpls_required_symbol', 'cptch_required_symbol' ),
			'title'					=> array( 'cptchpls_label_form', 'cptch_label_form' ),
			'no_answer'				=> array( 'cptchpls_error_empty_value', 'cptch_error_empty_value' ),
			'wrong_answer'			=> array( 'cptchpls_error_incorrect_value', 'cptch_error_incorrect_value' ),
			'time_limit_off'		=> array( 'cptchpls_error_time_limit', 'cptch_error_time_limit' ),
			'time_limit_off_notice'	=> array( 'time_limit_notice' ),
			'enable_time_limit'		=> array( 'use_time_limit' ),
		);

		foreach ( $args as $new_field => $old_fields ) {
			foreach ( $old_fields as $old_field ) {
				if ( isset( $old_options[ $old_field ] ) ) {
					$new_options[ $new_field ] = $old_options[ $old_field ];
					if ( isset( $new_options[ $old_field ] ) ) {
						unset( $new_options[ $old_field ] );
					}
					break;
				}
			}
		}

		/* Change the old settings structure by new one with the storing of the old values */
		$args = array(
			'math_actions'		=> array(
				'plus'				=> array( 'cptchpls_math_action_plus', 'cptch_math_action_plus' ),
				'minus'				=> array( 'cptchpls_math_action_minus', 'cptch_math_action_minus' ),
				'multiplications'	=> array( 'cptchpls_math_action_increase', 'cptch_math_action_increase' )
			),
			'operand_format'	=> array(
				'numbers'			=> array( 'cptchpls_difficulty_number', 'cptch_difficulty_number' ),
				'words'				=> array( 'cptchpls_difficulty_word', 'cptch_difficulty_word' ),
				'images'			=> array( 'cptchpls_difficulty_image', 'cptch_difficulty_image' )
			)
		);

		foreach ( $args as $new_field => $old_args ) {

			if ( empty( $new_options[ $new_field ] ) || ! is_array( $new_options[ $new_field ] ) ) {
				$new_options[ $new_field ] = array();
			}

			foreach( $old_args as $new_field_value => $old_fields ) {
				foreach( $old_fields as $old_field ) {
					if ( ! isset( $old_options[ $old_field ] ) ) {
						continue;
					}

					if (
						!! $old_options[ $old_field ] &&
						! in_array( $new_field_value, $new_options[ $new_field ] )
					)
						$new_options[ $new_field ][] = $new_field_value;

					if ( isset( $new_options[ $old_field ] ) ) {
						unset( $new_options[ $old_field ] );
					}
				}
			}
		}

		/* Forming the options for the each of the form which are compatible with the plugin */
		$args = array(
			'wp_login'		=> array(
				'enable'	=> array( 'cptchpls_login_form', 'cptch_login_form' )
			),
			'wp_register'	=> array(
				'enable'	=> array( 'cptchpls_register_form', 'cptch_register_form' )
			),
			'wp_lost_password'	=> array(
				'enable'		=> array( 'cptchpls_lost_password_form', 'cptch_lost_password_form' )
			),
			'wp_comments'		=> array(
				'enable'				=> array( 'cptchpls_comments_form', 'cptch_comments_form' ),
				'hide_from_registered'	=> array( 'cptchpls_hide_register', 'cptch_hide_register' )
			),
			'bws_contact'		=> array(
				'enable'		=> array( 'cptchpls_contact_form', 'cptch_contact_form' )
			),
			'bws_booking'		=> array(
				'enable'		=> array( 'cptchpls_booking_form', 'cptch_booking_form' )
			)
		);

		$args = apply_filters( 'cptch_get_additional_plugins_options', $args );

		foreach( $args as $form => $options ) {
			foreach( $options as $new_fields => $old_fields ) {
				foreach( (array)$old_fields as $old_field ) {
					if ( isset( $old_options[ $old_field ] ) ) {
						$new_options['forms'][ $form ][ $new_fields ] = $old_options[ $old_field ];

						if ( isset( $new_options[ $old_field ] ) ) {
							unset( $new_options[ $old_field ] );
						}
					}
				}
			}
		}

		/* Update plugin version */
		$new_options['plugin_option_version']	= $cptch_plugin_info["Version"];
		$new_options['display_settings_notice']	= 0;

		return $new_options;
	}
}

/**
 * Replaces values of the base array by values form appropriate fields of the replacement array or
 * joins not existed fields in base from the replacement recursively
 * @see    cptch_parse_options()
 * @param  array  $base          The initial array
 * @param  array  $replacement   The array to merge
 * @return array
 */
if ( ! function_exists( 'cptch_merge_recursive' ) ) {
	function cptch_merge_recursive( $base, $replacement ) {

		/* array_keys( $replacement ) == range( 0, count( $replacement ) - 1 ) - checking if array is numerical */
		if ( ! is_array( $base ) || empty( $replacement ) || array_keys( $replacement ) == range( 0, count( $replacement ) - 1 ) ) {
			return $replacement;
		}

		foreach ( $replacement as $key => $value ) {
			$base[ $key ] =
					! empty( $base[ $key ] ) &&
					is_array( $base[ $key ] )
				?
					cptch_merge_recursive( $base[ $key ], $value )
				:
					$value;
		}

		return $base;
	}
}

/**
 * Fethch the plugin data
 * @param  string|array  $plugins       The string or array of strings in the format {plugin_folder}/{plugin_file}
 * @param  array         $all_plugins   The list of all installed plugins
 * @param  boolean       $is_network    Whether the multisite is installed
 * @return array                        The plugins data
 */
if ( ! function_exists( 'cptch_get_plugin_status' ) ) {
	function cptch_get_plugin_status( $plugins, $all_plugins ) {
		$result = array(
			'status'		=> '',
			'plugin'		=> $plugins,
			'plugin_info'	=> array(),
		);
		foreach ( (array)$plugins as $plugin ) {
			if ( array_key_exists( $plugin, $all_plugins ) ) {
				if ( is_plugin_active( $plugin ) ) {
					$result['status']		= 'active';
					$result['compatible']	= cptch_is_compatible( $plugin, $all_plugins[ $plugin ]['Version'] );
					$result['plugin_info']	= $all_plugins[ $plugin ];
					break;
				} else {
					$result['status']		= 'deactivated';
					$result['compatible']	= cptch_is_compatible( $plugin, $all_plugins[ $plugin ]['Version'] );
					$result['plugin_info']	= $all_plugins[ $plugin ];
				}
			}
		}

		if ( empty( $result['status'] ) ) {
			$result['status'] = 'not_installed';
		}

		$result['link'] = cptch_get_plugin_link( $plugin );

		return $result;
	}
}

/**
 * Checks whether the BWS CAPTCHA is compatible with the specified plugin version
 * @param  string   $plugin     The string in the format {plugin_folder}/{plugin_file}
 * @param  string   $version    The plugin version that is checked
 * @return boolean
 */
if ( ! function_exists( 'cptch_is_compatible' ) ) {
	function cptch_is_compatible( $plugin, $version ) {
		switch ( $plugin ) {
			case 'contact-form-plugin/contact_form.php':
				$min_version = '3.95';
				break;
			case 'contact-form-pro/contact_form_pro.php':
				$min_version = '2.0.6';
				break;
			default:
				$min_version = apply_filters( 'cptch_is_compatible_additional_plugins', false, $plugin );
				break;
		}

		return $min_version ? version_compare( $version, $min_version, '>' ) : true;
	}
}

/**
* Fetch the plugin slug by the specified form slug
* @param   string  $form_slug   The form slug
* @return  string               The plugin slug
*/
if ( ! function_exists( 'cptch_get_plugin' ) ) {
	function cptch_get_plugin( $form_slug ) {
		switch( $form_slug ) {
			case 'general':
			case 'wp_login':
			case 'wp_register':
			case 'wp_lost_password':
			case 'wp_comments':
			default:
				return '';
			case 'bws_contact':
			case 'bws_booking':
				return $form_slug;
		}
		if ( in_array( $form_slug, apply_filters( 'cptch_get_additional_forms_slugs', array() ) ) ) {
			return $form_slug;
		}
	}
}

/**
 * Fetch the plugin download link
 * @param  string   $plugin     The string in the format {plugin_folder}/{plugin_file}
 * @return string               The plugin download link
 */
if ( ! function_exists( 'cptch_get_plugin_link' ) ) {
	function cptch_get_plugin_link( $plugin ) {
		global $wp_version, $cptch_plugin_info;
		$bws_link = "https://bestwebsoft.com/products/wordpress/plugins/%1s/?k=%2s&pn=72&v={$cptch_plugin_info["Version"]}&wp_v={$wp_version}/";
		switch ( $plugin ) {
			case 'contact-form-plugin/contact_form.php':
			case 'contact-form-pro/contact_form_pro.php':
				return sprintf( $bws_link, 'contact-form', '9ab9d358ad3a23b8a99a8328595ede2e' );
			case 'bws-car-rental-pro/bws-car-rental-pro.php':
				return sprintf( $bws_link, 'car-rental-v2', '3c0c792876c76187abe0381d85d907c1' );
			case 'limit-attempts/limit-attempts.php':
			case 'limit-attempts-pro/limit-attempts-pro.php':
				return sprintf( $bws_link, 'limit-attempts', 'c5ba37f86ebfc2754a71c759a5907888' );
			default:
				return apply_filters( 'cptch_get_links_of_additional_plugins', '#', $bws_link, $plugin );
		}
	}
}

/**
 * Fetch the plugin name
 * @param  string   $plugin_slug     The plugin slug
 * @return string                   The plugin name
 */
if ( ! function_exists( 'cptch_get_plugin_name' ) ) {
	function cptch_get_plugin_name( $plugin_slug ) {
		switch( $plugin_slug ) {
			case 'bws_contact':
				return 'Contact Form by BestwebSoft';
			case 'bws_booking':
				return 'Car Rental V2 by BestWebSoft';
			default:
				return apply_filters( 'cptch_get_additional_plugin_name', 'unknown',  $plugin_slug );
		}
	}
}
