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
			'plugin_option_version'        => $cptch_plugin_info["Version"],
			'display_settings_notice'	=> 1,
			'suggest_feature_banner'	=> 1,
			'forms'						=> array(),
			'time_limit'				=> 120
		);

		$forms = cptch_get_default_forms();

		foreach ( $forms as $form ) {
			$default_options['forms'][ $form ] = array(
				'enable'	=> in_array( $form, array( 'wp_login', 'wp_register', 'wp_lost_password', 'wp_comments' ) ),
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
			'wp_login', 'wp_register',
			'wp_lost_password', 'wp_comments',
			'bws_contact'
		);

		/*
		 * Add user forms to defaults
		 */
		$new_forms = apply_filters( 'cptch_add_form', array() );

		if ( ! is_array( $new_forms ) || empty( $new_forms ) )
			return $defaults;

		$new = array_filter( array_map( 'esc_attr', array_keys( $new_forms ) ) );

		return array_unique( array_merge( $defaults, $new ) );
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
			'status'      => '',
			'plugin'      => $plugins,
			'plugin_info' => array(),
		);
		foreach ( (array)$plugins as $plugin ) {
			if ( array_key_exists( $plugin, $all_plugins ) ) {
				if ( is_plugin_active( $plugin ) ) {
					$result['status']      = 'active';
					$result['compatible']  = cptch_is_compatible( $plugin, $all_plugins[ $plugin ]['Version'] );
					$result['plugin_info'] = $all_plugins[$plugin];
					break;
				} else {
					$result['status']      = 'deactivated';
					$result['compatible']  = cptch_is_compatible( $plugin, $all_plugins[ $plugin ]['Version'] );
					$result['plugin_info'] = $all_plugins[$plugin];
				}
			}
		}

		if ( empty( $result['status'] ) )
			$result['status'] = 'not_installed';

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
				$min_version = false;
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
		$wp_link  = 'http://wordpress.org/plugins/%s/';
		switch ( $plugin ) {
			case 'contact-form-plugin/contact_form.php':
			case 'contact-form-pro/contact_form_pro.php':
				return sprintf( $bws_link, 'contact-form', '9ab9d358ad3a23b8a99a8328595ede2e' );
			case 'limit-attempts/limit-attempts.php':
			case 'limit-attempts-pro/limit-attempts-pro.php':
				return sprintf( $bws_link, 'limit-attempts', 'c5ba37f86ebfc2754a71c759a5907888' );
			default:
				return '#';
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
			default:
				return 'unknown';
		}
	}
}