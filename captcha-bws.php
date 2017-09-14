<?php
/*
Plugin Name: Captcha by BestWebSoft
Plugin URI: https://bestwebsoft.com/products/wordpress/plugins/captcha/
Description: #1 super security anti-spam captcha plugin for WordPress forms.
Author: BestWebSoft
Text Domain: captcha-bws
Domain Path: /languages
Version: 5.0.0
Author URI: https://bestwebsoft.com/
License: GPLv2 or later
*/

/*  © Copyright 2017  BestWebSoft  ( https://support.bestwebsoft.com )

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

require_once( dirname( __FILE__ ) . '/includes/invisible.php' );

if ( ! function_exists( 'cptch_admin_menu' ) ) {
	function cptch_admin_menu() {
		global $submenu, $wp_version, $cptch_plugin_info;

		$settings_page = add_menu_page( __( 'Captcha Settings', 'captcha-bws' ), 'Captcha', 'manage_options', 'captcha.php', 'cptch_settings_page', 'none' );

		add_submenu_page( 'captcha.php', __( 'Captcha Settings', 'captcha-bws' ), __( 'Settings', 'captcha-bws' ), 'manage_options', 'captcha.php', 'cptch_settings_page' );

		add_submenu_page( 'captcha.php', 'BWS Panel', 'BWS Panel', 'manage_options', 'cptch-bws-panel', 'bws_add_menu_render' );

		/*pls */
		if ( isset( $submenu['captcha.php'] ) )
			$submenu['captcha.php'][] = array(
				'<span style="color:#d86463"> ' . __( 'Upgrade to Pro', 'captcha-bws' ) . '</span>',
				'manage_options',
				'https://bestwebsoft.com/products/wordpress/plugins/captcha/?k=28d4cf0b4ab6f56e703f46f60d34d039&pn=83&v=' . $cptch_plugin_info["Version"] . '&wp_v=' . $wp_version );
		/* pls*/

		add_action( "load-{$settings_page}", 'cptch_add_tabs' );
	}
}

/* add help tab */
if ( ! function_exists( 'cptch_add_tabs' ) ) {
	function cptch_add_tabs() {
		$args = array(
			'id'      => 'cptch',
			'section' => '200538879'
		);
		bws_help_tab( get_current_screen(), $args );
	}
}

if ( ! function_exists( 'cptch_plugins_loaded' ) ) {
	function cptch_plugins_loaded() {
		/* Internationalization */
		load_plugin_textdomain( 'captcha-bws', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}
}

if ( ! function_exists ( 'cptch_init' ) ) {
	function cptch_init() {
		global $cptch_plugin_info, $cptch_options;

		require_once( dirname( __FILE__ ) . '/bws_menu/bws_include.php' );
		bws_include_init( plugin_basename( __FILE__ ) );

		if ( ! $cptch_plugin_info ) {
			if ( ! function_exists( 'get_plugin_data' ) )
				require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			$cptch_plugin_info = get_plugin_data( __FILE__ );
		}

		/* Function check if plugin is compatible with current WP version */
		bws_wp_min_version_check( plugin_basename( __FILE__ ), $cptch_plugin_info, '3.9' );

		$is_admin = is_admin() && ! defined( 'DOING_AJAX' );

		/* Call register settings function */
		$pages = array(
			'captcha.php',
		);

		if ( ! $is_admin || ( isset( $_GET['page'] ) && in_array( $_GET['page'], $pages ) ) )
			cptch_settings();

		if ( $is_admin )
			return;

		/*
		 * Add the CAPTCHA to the WP login form
		 */
		if ( $cptch_options['forms']['wp_login']['enable'] ) {
			add_action( 'login_form', 'cptch_login_form' );
			add_filter( 'authenticate', 'cptch_login_check', 21, 1 );
		}

		/*
		 * Add the CAPTCHA to the WP register form
		 */
		if ( $cptch_options['forms']['wp_register']['enable'] ) {
			add_action( 'register_form', 'cptch_register_form' );
			add_action( 'signup_extra_fields', 'wpmu_cptch_register_form' );
			add_action( 'signup_blogform', 'wpmu_cptch_register_form' );

			add_filter( 'registration_errors', 'cptch_register_check', 9, 1 );
			if ( is_multisite() ) {
				add_filter( 'wpmu_validate_user_signup', 'cptch_register_validate' );
				add_filter( 'wpmu_validate_blog_signup', 'cptch_register_validate' );
			}
		}

		/*
		 * Add the CAPTCHA into the WP lost password form
		 */
		if ( $cptch_options['forms']['wp_lost_password']['enable'] ) {
			add_action( 'lostpassword_form', 'cptch_lostpassword_form' );
			add_filter( 'allow_password_reset', 'cptch_lostpassword_check' );
		}

		/*
		 * Add the CAPTCHA to the WP comments form
		 */
		if ( $cptch_options['forms']['wp_comments']['enable'] ) {
			/*
			 * Common hooks to add necessary actions for the WP comment form,
			 * but some themes don't contain these hooks in their comments form templates
			 */
			add_action( 'comment_form_after_fields', 'cptch_comment_form_wp3', 1 );
			add_action( 'comment_form_logged_in_after', 'cptch_comment_form_wp3', 1 );
			/*
			 * Try to display the CAPTCHA before the close tag </form>
			 * in case if hooks 'comment_form_after_fields' or 'comment_form_logged_in_after'
			 * are not included to the theme comments form template
			 */
			add_action( 'comment_form', 'cptch_comment_form' );
			add_filter( 'preprocess_comment', 'cptch_comment_post' );
		}

		/*
		 * Add the CAPTCHA to the Contact Form by BestWebSoft plugin forms
		 */
		if ( $cptch_options['forms']['bws_contact']['enable'] ) {
			add_filter( 'cntctfrm_display_captcha', 'cptch_custom_form', 10, 2 );
			add_filter( 'cntctfrm_check_form', 'cptch_check_bws_contact_form' );
		}
	}
}

if ( ! function_exists ( 'cptch_admin_init' ) ) {
	function cptch_admin_init() {
		global $bws_plugin_info, $cptch_plugin_info;
		/* Add variable for bws_menu */
		if ( empty( $bws_plugin_info ) )
			$bws_plugin_info = array( 'id' => '75', 'version' => $cptch_plugin_info["Version"] );
	}
}

/**
 * Activation plugin function
 */
if ( ! function_exists( 'cptch_plugin_activate' ) ) {
	function cptch_plugin_activate( $networkwide ) {
		global $wpdb;
		/* Activation function for network, check if it is a network activation - if so, run the activation function for each blog id */
		if ( function_exists( 'is_multisite' ) && is_multisite() && $networkwide ) {
			$old_blog = $wpdb->blogid;
			/* Get all blog ids */
			$blogids = $wpdb->get_col( "SELECT `blog_id` FROM $wpdb->blogs" );
			foreach ( $blogids as $blog_id ) {
				switch_to_blog( $blog_id );
				cptch_settings();
			}
			switch_to_blog( $old_blog );
			return;
		}
		cptch_settings();

		register_uninstall_hook( __FILE__, 'cptch_delete_options' );
	}
}

/* Register settings function */
if ( ! function_exists( 'cptch_settings' ) ) {
	function cptch_settings() {
		global $cptch_options, $cptch_plugin_info, $wpdb;

		if ( empty( $cptch_plugin_info ) ) {
			if ( ! function_exists( 'get_plugin_data' ) )
				require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			$cptch_plugin_info = get_plugin_data( __FILE__ );
		}

		$need_update = false;

		$cptch_options = get_option( 'cptch_options' );

		if ( empty( $cptch_options ) ) {
			require_once( dirname( __FILE__ ) . '/includes/helpers.php' );
			$cptch_options = cptch_get_default_options();
			update_option( 'cptch_options', $cptch_options );
		}

		if ( empty( $cptch_options['plugin_option_version'] ) || $cptch_options['plugin_option_version'] != $cptch_plugin_info["Version"] ) {
			$need_update = true;

			require_once( dirname( __FILE__ ) . '/includes/helpers.php' );
			
			$default_options = cptch_get_default_options();
			$cptch_options = cptch_merge_recursive( $cptch_options, $default_options );
		}

		if ( $need_update )
			update_option( 'cptch_options', $cptch_options );
	}
}

/**
 * Function displays captcha admin-pages
 * @return void
 */
if ( ! function_exists( 'cptch_settings_page' ) ) {
	function cptch_settings_page() { ?>
		<div class="wrap">
			<?php require_once( dirname( __FILE__ ) . '/includes/class-cptch-settings-tabs.php' );
			$page = new Cptch_Settings_Tabs( plugin_basename( __FILE__ ) ); ?>
			<h1><?php _e( 'Captcha Settings', 'captcha-bws' ); ?></h1>
			<?php $page->display_content(); ?>
		</div>
	<?php }
}

/************** WP LOGIN FORM HOOKS ********************/
if ( ! function_exists( 'cptch_login_form' ) ) {
	function cptch_login_form() {
		if ( "" == session_id() )
			@session_start();

		if ( isset( $_SESSION["cptch_login"] ) )
			unset( $_SESSION["cptch_login"] );

		echo cptch_display_captcha( 'wp_login', 'cptch_wp_login' ) . '<br />';
		return true;
	}
}

if ( ! function_exists( 'cptch_login_check' ) ) {
	function cptch_login_check( $user ) {
		if ( ! isset( $_POST['wp-submit'] ) )
			return $user;

		if ( "" == session_id() )
			@session_start();

		if ( isset( $_SESSION["cptch_login"] ) && true === $_SESSION["cptch_login"] )
			return $user;

		/* Delete errors, if they set */
		if ( isset( $_SESSION['cptch_error'] ) )
			unset( $_SESSION['cptch_error'] );

		$captcha = new Cptch_Invisible();
		$captcha->check();
		if ( $captcha->is_errors() ) {
			$_SESSION['cptch_login'] = false;
			wp_clear_auth_cookie();
			return $captcha->get_errors();
		}

		/* Captcha was matched */
		$_SESSION['cptch_login'] = true;
		return $user;
	}
}

/************** WP REGISTER FORM HOOKS ********************/
if ( ! function_exists( 'cptch_register_form' ) ) {
	function cptch_register_form() {
		echo cptch_display_captcha( 'wp_register', 'cptch_wp_register' ) . '<br />';
		return true;
	}
}

if ( ! function_exists ( 'wpmu_cptch_register_form' ) ) {
	function wpmu_cptch_register_form( $errors ) {
		/* the captcha html - register form */
		echo '<div class="cptch_block">';
		if ( is_wp_error( $errors ) ) {
			$error_codes = $errors->get_error_codes();
			if ( is_array( $error_codes ) && ! empty( $error_codes ) ) {
				foreach ( $error_codes as $error_code ) {
					if ( "cptch" == substr( $error_code, 0, 5 ) ) {
						$error_message = $errors->get_error_message( $error_code );
						echo '<p class="error">' . $error_message . '</p>';
					}
				}
			}
		}
		echo cptch_display_captcha( 'wp_register' );
		echo '</div><br />';
	}
}

if ( ! function_exists ( 'cptch_register_check' ) ) {
	function cptch_register_check( $error ) {
		$captcha = new Cptch_Invisible();
		$captcha->check();
		if ( $captcha->is_errors() ) {
			return $captcha->get_errors();
		}
		return $error;
	}
}

if ( ! function_exists( 'cptch_register_validate' ) ) {
	function cptch_register_validate( $results ) {
		$captcha = new Cptch_Invisible();
		$captcha->check();

		if ( $captcha->is_errors() ) {
			$results['errors'] = $captcha->get_errors();
		}		
		return $results;
	}
}

/************** WP LOST PASSWORD FORM HOOKS ********************/
if ( ! function_exists ( 'cptch_lostpassword_form' ) ) {
	function cptch_lostpassword_form() {
		echo cptch_display_captcha( 'wp_lost_password', 'cptch_wp_lost_password' ) . '<br />';
		return true;
	}
}

if ( ! function_exists ( 'cptch_lostpassword_check' ) ) {
	function cptch_lostpassword_check( $allow ) {
		$captcha = new Cptch_Invisible();
		$captcha->check();
		if ( $captcha->is_errors() ) {
			return $captcha->get_errors();
		}
		return $allow;
	}
}

/************** WP COMMENT FORM HOOKS ********************/
if ( ! function_exists( 'cptch_comment_form' ) ) {
	function cptch_comment_form() {
		echo cptch_display_captcha( 'wp_comments', 'cptch_wp_comments' );
		return true;
	}
}

if ( ! function_exists( 'cptch_comment_form_wp3' ) ) {
	function cptch_comment_form_wp3() {
		remove_action( 'comment_form', 'cptch_comment_form' );
		echo cptch_display_captcha( 'wp_comments', 'cptch_wp_comments' );
		return true;
	}
}

if ( ! function_exists( 'cptch_comment_post' ) ) {
	function cptch_comment_post( $comment ) {
		/* Added for compatibility with WP Wall plugin. This does NOT add CAPTCHA to WP Wall plugin, It just prevents the "Error: You did not enter a Captcha phrase." when submitting a WP Wall comment */
		if ( function_exists( 'WPWall_Widget' ) && isset( $_REQUEST['wpwall_comment'] ) ) {
			/* Skip capthca */
			return $comment;
		}

		/* Skip captcha for comment replies from the admin menu */
		if ( isset( $_REQUEST['action'] ) && 'replyto-comment' == $_REQUEST['action'] &&
		( check_ajax_referer( 'replyto-comment', '_ajax_nonce', false ) || check_ajax_referer( 'replyto-comment', '_ajax_nonce-replyto-comment', false ) ) ) {
			return $comment;
		}

		/* Skip captcha for trackback or pingback */
		if ( '' != $comment['comment_type'] && 'comment' != $comment['comment_type'] ) {
			return $comment;
		}

		$captcha = new Cptch_Invisible();
		$captcha->check();
		if ( $captcha->is_errors() ) {
			$error = $captcha->get_errors();
			wp_die( $error->get_error_message() . ( ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ? '' : ' ' . __( "Click the BACK button on your browser, and try again.", 'captcha-bws' ) ) );
		}

		/* Captcha was matched */
		return $comment;
	}
}

/************** BWS CONTACT FORM ********************/
if ( ! function_exists ( 'cptch_custom_form' ) ) {
	function cptch_custom_form( $content = "", $form_slug = 'general' ) {
		return
			( is_string( $content ) ? $content : '' ) .
			cptch_display_captcha( $form_slug );
	}
}

if ( ! function_exists( 'cptch_check_bws_contact_form' ) ) {
	function cptch_check_bws_contact_form( $allow ) {
		if ( true !== $allow )
			return $allow;
		return cptch_check_custom_form( true, 'wp_error' );
	}
}

/************** DISPLAY CAPTCHA VIA FILTER HOOK ********************/
/**
 *
 * @since 4.2.3
 */
if ( ! function_exists( 'cptch_display_filter' ) ) {
	function cptch_display_filter( $content = '', $form_slug = 'general', $class_name = "" ) {
		$args = array(
			'form_slug'  => $form_slug,
			'class_name' => $class_name
		);
		if ( 'general' == $form_slug || $cptch_options['forms'][ $form_slug ]['enable'] ) {
			return $content . cptch_display_captcha_shortcode( $args );
		}
		return $content;
	}
}

/**
 *
 * @since 4.2.3
 */
if ( ! function_exists( 'cptch_verify_filter' ) ) {
	function cptch_verify_filter( $allow = true, $return_format = 'string', $form_slug = 'general' ) {

		if ( true !== $allow )
			return $allow;

		if ( ! in_array( $return_format, array( 'string', 'wp_error' ) ) )
			$return_format = 'string';

		if ( 'general' == $form_slug || $cptch_options['forms'][ $form_slug ]['enable'] ) {

			return cptch_check_custom_form( true, $return_format, $form_slug );
		}

		return $allow;
	}
}

if ( ! function_exists( 'cptch_display_captcha' ) ) {
	function cptch_display_captcha( $form_slug = 'general', $class_name = "" ) {
		global $cptch_options;

		/**
		 * Escaping function parameters
		 */
		$form_slug  = esc_attr( $form_slug );
		$form_slug  = empty( $form_slug ) ? 'general' : $form_slug;
		$class_name = esc_attr( $class_name );

		if ( empty( $class_name ) ) {
			$tag_open = $tag_close = '';
		} else {
			$tag_open  = '<div class="cptch_block">';
			$tag_close = '</div>';
		}

		/**
		 * In case when the CAPTCHA uses in the custom form and there is no saved settings for this form making an attempt to get default settings
		 */
		if ( ! array_key_exists( $form_slug, $cptch_options['forms'] ) ) {
			require_once( dirname( __FILE__ ) . '/includes/helpers.php' );
			$default_options = cptch_get_default_options();
			/* prevent the need to get default settings on the next displaying of the CAPTCHA */
			if ( array_key_exists( $form_slug, $default_options['forms'] ) ) {
				$cptch_options['forms'][ $form_slug ] = $default_options['forms'][ $form_slug ];
				update_option( 'cptch_options' );
			} else {
				$form_slug = 'general';
			}
		}		

		/**
		 * Display only the CAPTCHA container to replace it with the CAPTCHA after the whole page loading via AJAX
		 */
		return $tag_open . 
			cptch_add_scripts() .
			'<span class="cptch_wrap cptch_ajax_wrap" data-cptch-form="' . $form_slug . '" data-cptch-class="' . $class_name . '">' .
				__( 'Captcha loading...', 'captcha-bws' ) .			
				'<noscript>' .
				__( 'In order to pass the captcha please enable JavaScript.', 'captcha-bws' ) .
				'</noscript>
			</span>' .
		$tag_close;
	}
}

/**
 * Checks the answer for the CAPTCHA
 * @param  mixed   $allow          The result of the pevious checking
 * @param  string  $return_format  The type of the cheking result. Can be set as 'string' or 'wp_error'
 * @param  string  $form_slug
 * @return mixed                   boolean(true) - in case when the CAPTCHA answer is right, or user`s IP is in the whitelist,
 *                                 string or WP_Error object ( depending on the $return_format variable ) - in case when the CAPTCHA answer is wrong
 */
if ( ! function_exists( 'cptch_check_custom_form' ) ) {
	function cptch_check_custom_form( $allow = true, $return_format = 'string', $form_slug = 'general' ) {
		$captcha = new Cptch_Invisible();
		$captcha->check();
		if ( $captcha->is_errors() ) {
			$error = $captcha->get_errors();
			if ( 'string' == $return_format ) {						
				return $error->get_error_message();
			} else {
				return $error;
			}
		} else {
			return $allow;
		}
	}
}

/**
 * Add necessary js scripts
 * @uses     for including necessary scripts on the pages witn the CAPTCHA only
 * @param    void
 * @return   string   empty string - if the form has been loaded by PHP or the CAPTCHA has been reloaded, inline javascript - if the form has been loaded by AJAX
 */
if ( ! function_exists( 'cptch_add_scripts' ) ) {
	function cptch_add_scripts () {
		global $cptch_options;

		if ( ! wp_script_is( 'cptch_front_end_script', 'registered' ) ) {
			wp_register_script( 'cptch_front_end_script', plugins_url( 'js/front_end_script.js' , __FILE__ ), array( 'jquery' ), false, $cptch_options['plugin_option_version'] );
			add_action( 'wp_footer', 'cptch_front_end_scripts' );
			if (
				$cptch_options['forms']['wp_login']['enable'] ||
				$cptch_options['forms']['wp_register']['enable'] ||
				$cptch_options['forms']['wp_lost_password']['enable']
			)
				add_action( 'login_footer', 'cptch_front_end_scripts' );
		}
		return '';
	}
}

if ( ! function_exists( 'cptch_reload' ) ) {
	function cptch_reload() {
		check_ajax_referer( 'cptch', 'cptch_nonce' );

		$form_slug  = isset( $_REQUEST['cptch_form_slug'] )   ? esc_attr( $_REQUEST['cptch_form_slug'] )   : 'general';
		$class      = isset( $_REQUEST['cptch_input_class'] ) ? esc_attr( $_REQUEST['cptch_input_class'] ) : '';

		if ( empty( $class ) ) {
			$tag_open = $tag_close = '';
		} else {
			$tag_open  = '<div class="cptch_block">';
			$tag_close = '</div>';
		}

		$captcha = new Cptch_Invisible();
		
		if ( ! $captcha->is_errors() ) {
			echo $tag_open .
				'<span class="cptch_wrap cptch_ajax_wrap" data-cptch-form="' . $form_slug . '" data-cptch-class="' . $class . '">'
					. $captcha->get_content() .
				'</span>' .
			$tag_close;
		}
		
		die();
	}
}

if ( ! function_exists( 'cptch_front_end_scripts' ) ) {
	function cptch_front_end_scripts() {
		global $cptch_options;
		if (
			wp_script_is( 'cptch_front_end_script', 'registered' ) &&
			! wp_script_is( 'cptch_front_end_script', 'enqueued' )
		) {
			wp_enqueue_script( 'cptch_front_end_script' );
			$args = array(
				'nonce'   		=> wp_create_nonce( 'cptch', 'cptch_nonce' ),
				'ajaxurl' 		=> admin_url( 'admin-ajax.php' ),
				'time_limit'	=> $cptch_options['time_limit']
			);
			wp_localize_script( 'cptch_front_end_script', 'cptch_vars', $args );
		}
	}
}

if ( ! function_exists ( 'cptch_admin_head' ) ) {
	function cptch_admin_head() {
		global $cptch_options;

		/* css for displaing an icon */
		wp_enqueue_style( 'cptch_admin_page_stylesheet', plugins_url( 'css/admin_page.css', __FILE__ ), array(), $cptch_options['plugin_option_version'] );

		$pages = array(
			'captcha.php',
		);

		if ( isset( $_REQUEST['page'] ) && in_array( $_REQUEST['page'], $pages ) ) {
			bws_enqueue_settings_scripts();
			bws_plugins_include_codemirror();
		}
	}
}

if ( ! function_exists( 'cptch_plugin_action_links' ) ) {
	function cptch_plugin_action_links( $links, $file ) {
		if ( ! is_network_admin() ) {
			static $this_plugin;
			if ( ! $this_plugin ) $this_plugin = plugin_basename(__FILE__);

			if ( $file == $this_plugin ) {
				$settings_link = '<a href="admin.php?page=captcha.php">' . __( 'Settings', 'captcha-bws' ) . '</a>';
				array_unshift( $links, $settings_link );
			}
		}
		return $links;
	}
}

if ( ! function_exists( 'cptch_register_plugin_links' ) ) {
	function cptch_register_plugin_links( $links, $file ) {
		$base = plugin_basename( __FILE__ );
		if ( $file == $base ) {
			if ( ! is_network_admin() )
				$links[]	=	'<a href="admin.php?page=captcha.php">' . __( 'Settings', 'captcha-bws' ) . '</a>';
			$links[]	=	'<a href="https://support.bestwebsoft.com/hc/en-us/" target="_blank">' . __( 'FAQ', 'captcha-bws' ) . '</a>';
			$links[]	=	'<a href="https://support.bestwebsoft.com">' . __( 'Support', 'captcha-bws' ) . '</a>';
		}
		return $links;
	}
}

if ( ! function_exists ( 'cptch_plugin_banner' ) ) {
	function cptch_plugin_banner() {
		global $hook_suffix, $cptch_plugin_info;

		if ( 'plugins.php' == $hook_suffix )
			bws_plugin_banner_to_settings( $cptch_plugin_info, 'cptch_options', 'captcha-bws', 'admin.php?page=captcha.php' );

		if ( isset( $_GET['page'] ) && 'captcha.php' == $_GET['page'] )
			bws_plugin_suggest_feature_banner( $cptch_plugin_info, 'cptch_options', 'captcha-bws' );
	}
}

/* Function for delete delete options */
if ( ! function_exists ( 'cptch_delete_options' ) ) {
	function cptch_delete_options() {
		global $wpdb;

		$all_plugins        = get_plugins();
		$is_another_captcha = array_key_exists( 'captcha-plus/captcha-plus.php', $all_plugins ) || array_key_exists( 'captcha-pro/captcha_pro.php', $all_plugins );

		require_once( dirname( __FILE__ ) . '/bws_menu/bws_include.php' );
		bws_include_init( plugin_basename( __FILE__ ) );
		bws_delete_plugin( plugin_basename( __FILE__ ) );

		/* do nothing more if Plus or Pro BWS CAPTCHA are installed */
		if ( $is_another_captcha )
			return;

		if ( is_multisite() ) {
			$old_blog = $wpdb->blogid;
			/* Get all blog ids */
			$blogids = $wpdb->get_col( "SELECT `blog_id` FROM $wpdb->blogs" );
			foreach ( $blogids as $blog_id ) {
				switch_to_blog( $blog_id );
				delete_option( 'cptch_options' );
			}
			switch_to_blog( $old_blog );
		} else {
			delete_option( 'cptch_options' );
		}

		require_once( dirname( __FILE__ ) . '/bws_menu/bws_include.php' );
		bws_include_init( plugin_basename( __FILE__ ) );
		bws_delete_plugin( plugin_basename( __FILE__ ) );
	}
}

register_activation_hook( __FILE__, 'cptch_plugin_activate' );

add_action( 'admin_menu', 'cptch_admin_menu' );

add_action( 'init', 'cptch_init' );
add_action( 'admin_init', 'cptch_admin_init' );

add_action( 'plugins_loaded', 'cptch_plugins_loaded' );

/* Additional links on the plugin page */
add_filter( 'plugin_action_links', 'cptch_plugin_action_links', 10, 2 );
add_filter( 'plugin_row_meta', 'cptch_register_plugin_links', 10, 2 );

add_action( 'admin_notices', 'cptch_plugin_banner' );

add_action( 'admin_enqueue_scripts', 'cptch_admin_head' );

add_filter( 'cptch_display', 'cptch_display_filter', 10, 3 );
add_filter( 'cptch_verify', 'cptch_verify_filter', 10, 3 );

add_action( 'wp_ajax_cptch_reload', 'cptch_reload' );
add_action( 'wp_ajax_nopriv_cptch_reload', 'cptch_reload' );