<?php
/**
 * Displays the content on the plugin settings page
 */

if ( ! class_exists( 'Cptch_Settings_Tabs' ) ) {
	class Cptch_Settings_Tabs extends Bws_Settings_Tabs {
		private $forms, $form_categories, $registered_forms;

		/**
		 * Constructor.
		 *
		 * @access public
		 *
		 * @see Bws_Settings_Tabs::__construct() for more information on default arguments.
		 *
		 * @param string $plugin_basename
		 */
		public function __construct( $plugin_basename ) {
			global $cptch_options, $cptch_plugin_info;

			$tabs = array(
				'settings'		=> array( 'label' => esc_html__( 'Settings', 'captcha-bws' ) ),
				'messages'		=> array( 'label' => esc_html__( 'Messages', 'captcha-bws' ) ),
				'misc'			=> array( 'label' => esc_html__( 'Misc', 'captcha-bws' ) ),
				'custom_code'	=> array( 'label' => esc_html__( 'Custom Code', 'captcha-bws' ) ),
				/*pls */
				'license'		=> array( 'label' => esc_html__( 'License Key', 'captcha-bws' ) )
				/* pls*/
			);

			if ( ! function_exists( 'cptch_get_default_options' ) ) {
				require_once( dirname( __FILE__ ) . '/helpers.php' );
			}

			parent::__construct( array(
				'plugin_basename'		=> $plugin_basename,
				'plugins_info'			=> $cptch_plugin_info,
				'prefix'				=> 'cptch',
				'default_options'		=> cptch_get_default_options(),
				'options'				=> $cptch_options,
				'tabs'					=> $tabs,
				/*pls */
				'wp_slug'				=> 'captcha-bws',
				'link_key'				=> '9701bbd97e61e52baa79c58c3caacf6d',
				'link_pn'				=> '75',
				/* pls*/
				'doc_link'				=> 'https://bestwebsoft.com/documentation/captcha/captcha-user-guide/',
				'doc_video_link'		=> 'https://www.youtube.com/watch?v=5UyK8tS3oqM'
			) );

			$this->all_plugins = get_plugins();

			$this->forms = array(
				'wp_login'				=> array( 'name' => esc_html__( 'Login form', 'captcha-bws' ) ),
				'wp_register'			=> array( 'name' => esc_html__( 'Registration form', 'captcha-bws' ) ),
				'wp_lost_password'		=> array( 'name' => esc_html__( 'Reset password form', 'captcha-bws' ) ),
				'wp_comments'			=> array( 'name' => esc_html__( 'Comments form', 'captcha-bws' ) ),
				'bws_contact'			=> array( 'name' => 'Contact Form' ),
				'bws_booking'			=> array( 'name' => 'Car Rental V2 Pro' ),
				/*pls */
				'bws_subscriber'			=> array( 'name' => 'Subscriber', 'for_pro' => 1 ),
				'cf7_contact'				=> array( 'name' => 'Contact Form 7', 'for_pro' => 1 ),
				'mailchimp'					=> array( 'name' => 'Mailchimp for Wordpress', 'for_pro' => 1 ),
				'ninja_form'				=> array( 'name' => 'Ninja Forms', 'for_pro' => 1 ),
				'gravity_form'				=> array( 'name' => 'Gravity Forms', 'for_pro' => 1 ),
                'wpforms'				    => array( 'name' => 'WPForms', 'for_pro' => 1 ),
				'buddypress_register'		=> array( 'name' => esc_html__( 'Registration form', 'captcha-bws' ), 'for_pro' => 1 ),
				'buddypress_comments'		=> array( 'name' => esc_html__( 'Comments form', 'captcha-bws' ), 'for_pro' => 1 ),
				'buddypress_group'			=> array( 'name' => esc_html__( 'Create a Group form', 'captcha-bws' ), 'for_pro' => 1 ),
				'woocommerce_login'			=> array( 'name' => esc_html__( 'Login form', 'captcha-bws' ), 'for_pro' => 1 ),
				'woocommerce_register'		=> array( 'name' => esc_html__( 'Registration form', 'captcha-bws' ), 'for_pro' => 1 ),
				'woocommerce_lost_password'	=> array( 'name' => esc_html__( 'Forgot password form', 'captcha-bws' ), 'for_pro' => 1 ),
				'woocommerce_checkout'		=> array( 'name' => esc_html__( 'Checkout form', 'captcha-bws' ), 'for_pro' => 1 ),
				'jetpack_contact_form'		=> array( 'name' => esc_html__( 'Jetpack Contact Form', 'captcha-bws' ), 'for_pro' => 1 ),
				'bbpress_new_topic_form'	=> array( 'name' => esc_html__( 'bbPress New Topic form', 'captcha-bws' ), 'for_pro' => 1 ),
				'bbpress_reply_form'		=> array( 'name' => esc_html__( 'bbPress Reply form', 'captcha-bws' ), 'for_pro' => 1 ),
				'wpforo_login_form'			=> array( 'name' => esc_html__( 'wpForo Login form', 'captcha-bws' ), 'for_pro' => 1 ),
				'wpforo_register_form'		=> array( 'name' => esc_html__( 'wpForo Registration form', 'captcha-bws' ), 'for_pro' => 1 ),
				'wpforo_new_topic_form'		=> array( 'name' => esc_html__( 'wpForo New Topic form', 'captcha-bws' ), 'for_pro' => 1 ),
				'wpforo_reply_form'			=> array( 'name' => esc_html__( 'wpForo Reply form', 'captcha-bws' ), 'for_pro' => 1 ),

				/* pls*/
			);

			$this->forms = apply_filters( 'cptch_get_additional_forms', $this->forms );
			/*
			 * Add users forms to the forms lists
			 */
			$user_forms = apply_filters( 'cptch_add_form', array() );
			if ( ! empty( $user_forms ) ) {
				/*
				 * Get default form slugs from defaults
				 * which have been added by hook "cptch_add_default_form" */
				$new_default_forms = array_diff( cptch_get_default_forms(), array_keys( $this->forms ) );
				/*
				 * Remove forms slugs form from the newly added
				 * which have not been added to defaults previously
				 */
				$new_forms = array_intersect( $new_default_forms, array_keys( $user_forms ) );
				/* Get the sub array with new form labels */
				$new_forms_fields = array_intersect_key( $user_forms, array_flip( $new_forms ) );
				$new_forms_fields = array_map( array( $this, 'sanitize_new_form_data' ), $new_forms_fields );
				if ( ! empty( $new_forms_fields ) ) {
					/* Add new forms labels to the registered */
					$this->forms = array_merge( $this->forms, $new_forms_fields );
					/* Add default settings in case if new forms settings have not been saved yet */
					foreach ( $new_forms as $new_form ) {
						if ( empty( $this->options['forms'][ $new_form ] ) ) {
							$this->options['forms'][ $new_form ] = $this->default_options['forms'][ $new_form ];
						}
					}
				}
			}

			/**
			* form categories are used when compatible plugins are displayed
			*/
			$this->form_categories = array(
				'wp_default' => array(
					'title' => esc_html__( 'WordPress default', 'captcha-bws' ),
					'forms' => array(
						'wp_login',
						'wp_register',
						'wp_lost_password',
						'wp_comments'
					)
				),
				'external' => array(
					'title' => esc_html__( 'External plugins', 'captcha-bws' ),
					'forms' => array(
						'bws_contact',
                        'bws_booking'
					)
				),
				/*pls */
				'other_for_pro' => array(
					'external' => array(
						'title' => esc_html__( 'External plugins', 'captcha-bws' ),
						'forms' => array(
							'bws_subscriber',
							'cf7_contact',
							'jetpack_contact_form',
							'mailchimp',
							'ninja_form',
							'gravity_form',
                            'wpforms'
						)
					),
					'bbpress' => array(
						'title' => 'BbPress',
						'forms' => array(
							'bbpress_new_topic_form',
							'bbpress_reply_form'
						)
					),
					'buddypress' => array(
						'title' => 'BuddyPress',
						'forms' => array(
							'buddypress_register',
							'buddypress_comments',
							'buddypress_group'
						)
					),
					'woocommerce' => array(
						'title' => 'WooCommerce',
						'forms' => array(
							'woocommerce_login',
							'woocommerce_register',
							'woocommerce_lost_password',
							'woocommerce_checkout'
						)
					),
					'wpforo' => array(
						'title' => 'Forums - wpForo',
						'forms' => array(
							'wpforo_login_form',
							'wpforo_register_form',
							'wpforo_new_topic_form',
							'wpforo_reply_form'
						)
					)
				)
				/* pls*/
			);
			$this->form_categories['external'] = apply_filters( 'cptch_get_additional_forms_slugs', $this->form_categories['external'] );
			/**
			* create list with default compatible forms
			*/

			$this->registered_forms = array_merge(
				$this->form_categories['wp_default']['forms'],
				$this->form_categories['external']['forms'] /*pls */,
				$this->form_categories['other_for_pro']['external']['forms'],
				$this->form_categories['other_for_pro']['buddypress']['forms'],
				$this->form_categories['other_for_pro']['woocommerce']['forms'],
				$this->form_categories['other_for_pro']['bbpress']['forms'],
				$this->form_categories['other_for_pro']['wpforo']['forms']
				/* pls*/
			);

			$user_forms = array_diff( array_keys( $this->forms ), $this->registered_forms );
			if ( ! empty( $user_forms ) ) {
				$this->form_categories['external']['forms'] = array_merge( $this->form_categories['external']['forms'], $user_forms );
			}

			/**
			* get ralated plugins info
			*/
			$this->options = $this->get_related_plugins_info( $this->options );

			/**
			* The option restoring have place later then $this->__constuct
			* so related plugins info will be lost without this add_filter
			*/
			add_action( get_parent_class( $this ) . '_additional_misc_options', array( $this, 'additional_misc_options' ) );
			add_filter( get_parent_class( $this ) . '_additional_restore_options', array( $this, 'additional_restore_options' ) );
		}

		/**
		 * Save plugin options to the database
		 * @access public
		 * @param  void
		 * @return array    The action results
		 */
		public function save_options() {
			$message = $notice = $error = '';
			$notices = array();

			do_action( 'cptch_notice_cf7', $this->options );

			/*
			 * Prepare general options
			 */
			$general_arrays = array(
				'math_actions'		=> esc_html__( 'Arithmetic Actions', 'captcha-bws' ),
				'operand_format'	=> esc_html__( 'Complexity', 'captcha-bws' ),
				'used_packages'		=> esc_html__( 'Image Packages', 'captcha-bws' )
			);
			$general_bool		= array( 'load_via_ajax', 'display_reload_button', 'enlarge_images' );
			$general_strings	= array( 'type', 'title', 'required_symbol', 'no_answer', 'wrong_answer', 'time_limit_off', 'time_limit_off_notice', 'text_start_slide', 'text_end_slide' );

			$general_bool = apply_filters( 'cptch_get_additional_bool_vars', $general_bool );
			$general_strings = apply_filters( 'cptch_get_additional_strings_vars', $general_strings );

			foreach ( $general_bool as $option ) {
				$this->options[ $option ] = ! empty( $_REQUEST["cptch_{$option}"] );
			}
            $this->options['forms']['general']['enable_time_limit'] = ! empty( $_REQUEST["cptch_enable_time_limit"] );

			foreach ( $general_strings as $option ) {
				$value = isset( $_REQUEST["cptch_{$option}"] ) ? stripslashes( sanitize_text_field( $_REQUEST["cptch_{$option}"] ) ) : '';

				if ( ! in_array( $option, array( 'title', 'required_symbol' ) ) && empty( $value ) ) {
					/* The index has been added in order to prevent the displaying of this message more than once */
					$notices['a'] = esc_html__( 'Text fields in the "Messages" tab must not be empty.', 'captcha-bws' );
				} else {
					$this->options[ $option ] = $value;
				}
			}

			if ( 'slide' == $this->options['type'] ) {
				$this->options['load_via_ajax'] = 0;
			}

			foreach ( $general_arrays as $option => $option_name ) {
				$value = isset( $_REQUEST["cptch_{$option}"] ) && is_array( $_REQUEST["cptch_{$option}"] ) ? array_map( 'esc_html', $_REQUEST["cptch_{$option}"] ) : array();

				/* "Arithmetic actions" and "Complexity" must not be empty */
				if ( empty( $value ) && 'used_packages' != $option && 'recognition' != $this->options['type'] && 'slide' != $this->options['type'] ) {
					$notices[] = sprintf( esc_html__( '"%s" option must not be fully disabled.', 'captcha-bws' ), $option_name );
				} else {
					$this->options[ $option ] = $value;
				}
			}
            $this->options['forms']['general']['used_packages'] = $this->options['used_packages'];
			$this->options['images_count']	= isset( $_REQUEST['cptch_images_count'] ) ? absint( $_REQUEST['cptch_images_count'] ) : 4;
            $this->options['forms']['general']['time_limit']	= isset( $_REQUEST['cptch_time_limit'] ) ? absint( $_REQUEST['cptch_time_limit'] ) : 120;

			/*
			 * Prepare forms options
			 */
			$forms = array_keys( $this->forms );
			$form_bool = array( 'enable', 'hide_from_registered' );
			foreach ( $forms as $form_slug ) {
				foreach ( $form_bool as $option ) {
					$this->options['forms'][ $form_slug ][ $option ] = isset( $_REQUEST['cptch']['forms'][ $form_slug ][ $option ] );
				}
			}

			/*
			 * If the user has selected images for the CAPTCHA
			 * it is necessary that at least one of the images packages was selected on the General Options tab
			 */
			if (
				( $this->images_enabled() || 'recognition' == $this->options['type'] ) &&
				empty( $this->options['forms']['general']['used_packages'] )
			) {
				if ( 'recognition' == $this->options['type'] ) {
					$notices[] = esc_html__( 'In order to use "Optical Character Recognition" type, please select at least one of the items in the option "Image Packages".', 'captcha-bws' );
					$this->options['type'] = 'math_actions';
				} else {
					$notices[] = esc_html__( 'In order to use images in the CAPTCHA, please select at least one of the items in the option "Image Packages". The "Images" checkbox in "Complexity" option has been disabled.', 'captcha-bws' );
				}
				$key = array_keys( $this->options['operand_format'], 'images' );
				unset( $this->options['operand_format'][ $key[0] ] );
				if ( empty( $this->options['operand_format'] ) )
					$this->options['operand_format'] = array( 'numbers', 'words' );
			}

			$this->options = apply_filters( 'cptch_before_save_options', $this->options );
			update_option( 'cptch_options', $this->options );
			$notice  = implode( '<br />', $notices );
			$message = esc_html__( "Settings saved.", 'captcha-bws' );

			return compact( 'message', 'notice' );
		}

		/**
		 * Displays 'settings' menu-tab
		 * @access public
		 * @param void
		 * @return void
		 */
		public function tab_settings() {

			$options = apply_filters( 'cptch_get_additional_options', array(), $this->get_form_message( 'limit_attempts' ) );
			$options += array(
				'type'	=> array(
					'type'				=> 'radio',
					'title'				=> esc_html__( 'Captcha Type', 'captcha-bws' ),
					'array_options'		=> array(
						'math_actions'		=> array( esc_html__( 'Arithmetic actions', 'captcha-bws' ) ),
						'recognition'		=> array( esc_html__( 'Optical Character Recognition (OCR)', 'captcha-bws' ) ),
						'invisible'			=> array( esc_html__( 'Invisible', 'captcha-bws' ) ),
						'slide'			    => array( esc_html__( 'Slide captcha', 'captcha-bws' ) )
					)
				),
				'math_actions'	=> array(
					'type'			=> 'checkbox',
					'title'			=> esc_html__( 'Arithmetic Actions', 'captcha-bws' ),
					'array_options'	=> array(
						'plus'				=> array( esc_html__( 'Addition', 'captcha-bws' ) . '&nbsp;(+)' ),
						'minus'				=> array( esc_html__( 'Subtraction', 'captcha-bws' ) . '&nbsp;(-)' ),
						'multiplications'	=> array( esc_html__( 'Multiplication', 'captcha-bws' ) . '&nbsp;(x)' )
					),
					'class'			=> 'cptch_for_math_actions'
				),
				'operand_format'	=> array(
					'type'				=> 'checkbox',
					'title'				=> esc_html__( 'Complexity', 'captcha-bws' ),
					'array_options'		=> array(
						'numbers'		=> array( esc_html__( 'Numbers (1, 2, 3, etc.)', 'captcha-bws' ) ),
						'words'			=> array( esc_html__( 'Words (one, two, three, etc.)', 'captcha-bws' ) ),
						'images'		=> array( esc_html__( 'Images', 'captcha-bws' ) )
					),
					'class'				=> 'cptch_for_math_actions'
				),
				'images_count'	=> array(
					'type'		=> 'number',
					'title'				=> esc_html__( 'Number of Images', 'captcha-bws' ),
					'min'				=> 1,
					'max'				=> 10,
					'block_description'	=> esc_html__( 'Set a number of images to display simultaneously as a captcha question.', 'captcha-bws' ),
					'class'				=> 'cptch_for_recognition'
				),
				'used_packages'	=> array(
					'type'				=> 'pack_list',
					'title'				=> esc_html__( 'Image Packages', 'captcha-bws' ),
					'class'				=> 'cptch_images_options cptch_for_math_actions cptch_for_recognition'
				),
				/*pls */
				'use_several_packages'	=> array(
					'type'		=> 'checkbox',
					'title'					=> esc_html__( 'Use several image packages at the same time', 'captcha-bws' ),
					'class'					=> 'cptch_images_options cptch_enable_to_use_several_packages.'
				),
				/* pls*/
				'enlarge_images'	=> array(
					'type'					=> 'checkbox',
					'title'					=> esc_html__( 'Enlarge Images', 'captcha-bws' ),
					'inline_description'	=> esc_html__( 'Enable to enlarge captcha images on mouseover.', 'captcha-bws' ),
					'class'					=> 'cptch_images_options cptch_for_math_actions cptch_for_recognition'
				),
				'display_reload_button'	=> array(
					'type'					=> 'checkbox',
					'title'					=> esc_html__( 'Reload Button', 'captcha-bws' ),
					'inline_description'	=> esc_html__( 'Enable to display reload button for captcha.', 'captcha-bws' ),
					'class'					=> 'cptch_for_math_actions cptch_for_recognition'
				 ),
				'title'	=> array(
					'type'					=> 'text',
					'title'					=> esc_html__( 'Captcha Title', 'captcha-bws' ) ),
				'required_symbol'			=> array(
					'type'					=> 'text',
					'title'					=> esc_html__( 'Required Symbol', 'captcha-bws' ) ),
				'load_via_ajax'	=> array(
					'type'					=> 'checkbox',
					'title'					=> esc_html__( 'Advanced Protection', 'captcha-bws' ),
					'inline_description'	=> esc_html__( 'Enable to display captcha when the website page is loaded.', 'captcha-bws' ),
					'class'					=> 'cptch_for_math_actions cptch_for_recognition'
				)
			); ?>
			<h3 class="bws_tab_label"><?php esc_html_e( 'Captcha Settings', 'captcha-bws' ); ?></h3>
			<?php $this->help_phrase(); ?>
			<hr>
			<div class="bws_tab_sub_label"><?php esc_html_e( 'General', 'captcha-bws' ); ?></div>
			<table class="form-table">
				<tr>
					<th scope="row"><?php esc_html_e( 'Enable Captcha for', 'captcha-bws' ); ?></th>
					<td>
						<?php foreach ( $this->form_categories as $fieldset_name => $fieldset_data ) {
							/*pls */
							/**
							* All missed forms will be displayed later in pro blocks
							*/
							if ( 'other_for_pro' == $fieldset_name ) {
								continue;
							} 
							/* pls*/ ?>
							<p><i><?php echo $fieldset_data['title']; ?></i></p>
							<br>
							<fieldset id="<?php echo $fieldset_name; ?>">
								<?php foreach ( $fieldset_data['forms'] as $form_name ) {
									/**
									* if plugin is external and it is not active, it's checkbox should be disabled
									*/
									$disabled = in_array( $form_name, $this->registered_forms ) && (
																				( isset( $this->options['related_plugins_info'][ $form_name ] ) &&
																						'active' != $this->options['related_plugins_info'][ $form_name ]['status'] ) ||
																				( isset( $this->options['related_plugins_info'][ $fieldset_name ] ) &&
																						'active' != $this->options['related_plugins_info'][ $fieldset_name ]['status'] )
																		); ?>
									<label class="cptch_related">
										<?php $value = $fieldset_name . '_' . $form_name;
										$id = 'cptch_' . $form_name . '_enable';
										$class = '';
										$name = 'cptch[forms][' . $form_name . '][enable]';
										if ( isset ( $this->options['forms'][ $form_name ]['enable'] ) ) {
											$checked = !! $this->options['forms'][ $form_name ]['enable'];
										} else {
											$checked = 0;
										}

										$this->add_checkbox_input( compact( 'id', 'name', 'checked', 'value', 'class', 'disabled' ) );

										echo $this->forms[ $form_name ]['name']; ?>
																		</label>
										<?php if ( 'external' == $fieldset_name && $disabled ) {
											echo $this->get_form_message( $form_name ); /* show "instal/activate" mesage */
										} elseif ( 'bws_contact' == $form_name && ( is_plugin_active( 'contact-form-multi/contact-form-multi.php' ) || is_plugin_active( 'contact-form-multi-pro/contact-form-multi-pro.php' ) ) ) { ?>
																						<br /><span class="bws_info"> <?php esc_html_e( 'Enable to add the CAPTCHA to forms on their settings pages.', 'captcha-bws' ); ?></span>
										<?php } ?>
									<br />
								<?php } ?>
							</fieldset>
							<hr>
						<?php } ?>
					</td>
				</tr>
				<!-- pls -->
			</table>
			<?php if ( ! $this->hide_pro_tabs ) { ?>
				<div class="bws_pro_version_bloc">
					<div class="bws_pro_version_table_bloc">
						<button type="submit" name="bws_hide_premium_options" class="notice-dismiss bws_hide_premium_options" title="<?php esc_html_e( 'Close', 'captcha-bws' ); ?>"></button>
						<div class="bws_table_bg"></div>
						<table class="form-table bws_pro_version">
							<tr>
								<th></th>
								<td>
									<?php foreach ( $this->form_categories['other_for_pro'] as $fieldset_name => $fieldset_data ) { ?>
										<p><?php echo $fieldset_data['title']; ?></p>
										<br>
										<fieldset id="<?php echo $fieldset_name; ?>">
											<?php foreach ( $fieldset_data['forms'] as $form_name => $form_data ) { ?>
												<label>
													<input type="checkbox" disabled="disabled">
													<?php echo $this->forms[ $form_data ]['name']; ?>
												</label>
												<br />
											<?php } ?>
										</fieldset>
										<hr>
									<?php } ?>
								</td>
							</tr>
						</table>
						<?php cptch_use_limit_attempts_allowlist(); ?>
					</div>
					<?php $this->bws_pro_block_links(); ?>
				</div>
			<?php } ?>
			<table class="form-table">
				<!-- end pls -->
				<?php foreach ( $options as $key => $data ) {
					/*pls */
					if ( 'use_several_packages' == $key ) {
						if ( ! $this->hide_pro_tabs ) { ?>
							</table>
							<div class="bws_pro_version_bloc">
								<div class="bws_pro_version_table_bloc">
									<button type="submit" name="bws_hide_premium_options" class="notice-dismiss bws_hide_premium_options" title="<?php esc_html_e( 'Close', 'captcha-bws' ); ?>"></button>
									<div class="bws_table_bg"></div>
									<?php cptch_use_several_packages(); ?>
								</div>
								<?php $this->bws_pro_block_links(); ?>
							</div>
							<table class="form-table">
						<?php }
						continue;
					} 
					/* pls*/ ?>
					<tr<?php if ( ! empty( $data['class'] ) ) echo ' class="' . $data['class'] . '"'; ?>>
						<th scope="row"><?php echo ucwords( $data['title'] ); ?></th>
						<td>
							<fieldset>
								<?php $func = "add_{$data['type']}_input";
								if ( isset( $data['array_options'] ) ) {
									$name = 'radio' == $data['type'] ? 'cptch_' . $key : 'cptch_' . $key . '[]';
									foreach ( $data['array_options'] as $slug => $sub_data ) {
										$id = "cptch_{$key}_{$slug}"; ?>
										<label for="<?php echo $id; ?>">
											<?php if (
												'use_limit_attempts_allowlist' == $key &&
												$slug &&
												'active' != $this->options['related_plugins_info']['limit_attempts']['status']
											) { ?>
												<input type="radio" id="<?php echo $id; ?>" name="<?php echo $name; ?>" disabled="disabled" />
											<?php } else {
												$checked = 'radio' == $data['type'] ? ( $slug == $this->options[ $key ] ) : in_array( $slug, $this->options[ $key ] );
												$value	= $slug;
												$this->$func( compact( 'id', 'name', 'value', 'checked' ) );
											}
											echo $sub_data[0]; ?>
										</label>
										<br />
									<?php }
								} else {
									$id = isset( $data['array_options'] ) ? '' : ( isset( $this->options[ $key ] ) ? "cptch_{$key}" : "cptch_form_general_{$key}" );
                                    if ( ( $this->options[ $key ] != $this->options['used_packages'] ) ) {
                                        $name    = $id;
                                        $value   = $this->options[ $key ];
                                    } else {
                                        $name    = $id;
                                        $value   = $this->options['forms']['general']['used_packages'];
                                    }
									$checked	= !! $value;
									if ( 'used_packages' == $key ) {
										$open_tag = $close_tag = "";
									} else {
										$open_tag = "<label for=\"{$id}\">";
										$close_tag = "</label>";
									}
									if ( isset( $data['min'] ) )
										$min = $data['min'];
									if ( isset( $data['max'] ) )
										$max = $data['max'];
									echo $open_tag;
									$this->$func( compact( 'id', 'name', 'value', 'checked', 'min', 'max' ) );
									echo $close_tag;
									if ( isset( $data['inline_description'] ) ) { ?>
										<span class="bws_info"><?php echo $data['inline_description']; ?></span>
									<?php }
								} ?>
							</fieldset>
							<?php if ( isset( $data['block_description'] ) ) { ?>
								<span class="bws_info"><?php echo $data['block_description']; ?></span>
							<?php } ?>
						</td>
					</tr>
				<?php }
				$options = array(
					array(
						'id'					=> "cptch_enable_time_limit",
                        'name'					=> "cptch_enable_time_limit",
                        'checked'				=> $this->options['forms']['general']['enable_time_limit'],
                        'inline_description'	=> esc_html__( 'Enable to activate a time limit required to complete captcha.', 'captcha-bws' )
                    ),
					array(
						'id'	=> "cptch_time_limit",
                        'name'		=> "cptch_time_limit",
                        'value'		=> ! empty( $this->options['forms']['general']['time_limit'] ) && 10 <= $this->options['forms']['general']['time_limit'] ? $this->options['forms']['general']['time_limit'] : 120,
                        'min'		=> 10,
                        'max'		=> 9999
                    )
                );?>
				<tr class="cptch_for_math_actions cptch_for_recognition">
					<th scope="row"><?php esc_html_e( 'Time Limit', 'captcha-bws' ); ?></th>
					<td>
						<?php $this->add_checkbox_input( $options[0] ); ?>
						<span class="bws_info"><?php echo $options[0][ 'inline_description' ]; ?></span>
					</td>
				</tr>
				<tr class="cptch_time_limit" <?php echo $options[0]['checked'] ? '' : ' style="display: none"'; ?>>
					<th scope="row"><?php esc_html_e( 'Time Limit Threshold', 'captcha-bws' ); ?></th>
					<td>
						<span class="cptch_time_limit">
							<?php $this->add_number_input( $options[1] ); echo '&nbsp;' . esc_html_e( 'sec', 'captcha-bws' ); ?>
						</span>
					</td>
				</tr>
			</table>
			<?php
			if ( ! $this->hide_pro_tabs ) { ?>
				<div class="bws_pro_version_bloc">
					<?php cptch_slide_pro_block(); 
					$this->bws_pro_block_links(); ?>
				</div>
			<?php 
			} ?>
			<?php foreach ( $this->forms as $form_slug => $data ) {
				if ( /*pls */ isset( $data['for_pro'] ) || ( /* pls*/ 'wp_comments' != $form_slug /*pls */ && $this->hide_pro_tabs ) /* pls*/ )
					continue;

				foreach ( $this->form_categories as $category_name => $category_data ) {
					if ( in_array( $form_slug, $category_data['forms'] ) ) {
						if ( 'wp_default' == $category_name ) {
							$category_title = 'WordPress - ';
						} elseif ( 'external' == $category_name ) {
							$category_title = '';
						} else {
							$category_title = $category_data['title'] . ' - ';
						}
						break;
					}
				} ?>
				<div class="bws_tab_sub_label cptch_<?php echo $form_slug; ?>_related_form"><?php echo $category_title . $data['name']; ?></div>
				<?php if ( 'wp_comments' == $form_slug ) { ?>
						<?php $id	= "cptch_form_{$form_slug}_hide_from_registered";
						$name		= "cptch[forms][{$form_slug}][hide_from_registered]";
						$checked	= !! $this->options['forms'][ $form_slug ]['hide_from_registered'];
						$style		= $info = $readonly = '';

					/* Multisite uses common "register" and "lostpassword" forms all sub-sites */
					if (
						$this->is_multisite &&
						in_array( $form_slug, array( 'wp_register', 'wp_lost_password' ) ) &&
						! in_array( get_current_blog_id(), array( 0, 1 ) )
					) {
						$info		= esc_html__( 'This option is available only for network or for main blog', 'captcha-bws' );
						$readonly	= ' readonly="readonly" disabled="disabled"';
					} elseif ( ! $this->options['forms'][ $form_slug ]['enable'] ) {
						$style = ' style="display: none;"';
					} ?>
					<table class="form-table cptch_<?php echo $form_slug; ?>_related_form cptch_related_form_bloc">
						<tr class="cptch_form_option_hide_from_registered"<?php echo $style; ?>>
							<th scope="row"><?php esc_html_e( 'Hide from Registered Users', 'captcha-bws' ); ?></th>
							<td>
								<?php $this->add_checkbox_input( compact( 'id', 'name', 'checked', 'readonly' ) ); ?> <span class="bws_info"><?php esc_html_e( 'Enable to hide captcha for registered users.', 'captcha-bws' ); ?></span>
							</td>
						</tr>
					</table><!-- .cptch_$form_slug --><!-- pls -->
					<?php if ( ! $this->hide_pro_tabs ) { ?>
						<div class="bws_pro_version_bloc cptch_<?php echo $form_slug; ?>_related_form cptch_related_form_bloc">
							<div class="bws_pro_version_table_bloc">
								<button type="submit" name="bws_hide_premium_options" class="notice-dismiss bws_hide_premium_options" title="<?php esc_html_e( 'Close', 'captcha-bws' ); ?>"></button>
								<div class="bws_table_bg"></div>
								<?php cptch_additional_options(); ?>
							</div> <!-- .bws_pro_version_table_bloc -->
							<?php $this->bws_pro_block_links(); ?>
						</div>
					<?php } ?>
				<?php } else { ?>
					<div class="bws_pro_version_bloc cptch_<?php echo $form_slug; ?>_related_form cptch_related_form_bloc">
						<div class="bws_pro_version_table_bloc">
							<button type="submit" name="bws_hide_premium_options" class="notice-dismiss bws_hide_premium_options" title="<?php esc_html_e( 'Close', 'captcha-bws' ); ?>"></button>
							<div class="bws_table_bg"></div>
							<?php $plugin = cptch_get_plugin( $form_slug );

							if ( ! empty( $plugin ) ) {
								/* Don't display form options if there is to old plugin version */
								if ( 'active' == $this->options['related_plugins_info'][ $plugin ]['status'] &&
									! $this->options['related_plugins_info'][ $plugin ]['compatible']
								) {
									$link			= $this->options['related_plugins_info'][ $plugin ]['link'];
									$plugin_name	= $this->options['related_plugins_info'][ $plugin ]['plugin_info']['Name'];
									$recommended	= esc_html__( 'update', 'captcha-bws' );
									$to_current		= esc_html__( 'to the current version', 'captcha-bws' );
								/* Don't display form options for deactivated or not installed plugins */
								} else {
									switch ( $this->options['related_plugins_info'][ $plugin ]['status'] ) {
										case 'not_installed':
											$link			= $this->options['related_plugins_info'][ $plugin ]['link'];
											$plugin_name	= cptch_get_plugin_name( $plugin );
											$recommended	= esc_html__( 'install', 'captcha-bws' );
											break;
										case 'deactivated':
											$link			= admin_url( '/plugins.php' );
											$plugin_name	= $this->options['related_plugins_info'][ $plugin ]['plugin_info']['Name'];
											$recommended	= esc_html__( 'activate', 'captcha-bws' );
											break;
										default:
											break;
									}
								}
							}

							if ( ! empty( $recommended ) ) { ?>
								<table class="form-table bws_pro_version">
									<tr>
										<td colspan="2">
											<?php echo esc_html__( 'You should', 'captcha-bws' ) .
											"&nbsp;<a href=\"{$link}\" target=\"_blank\">{$recommended}&nbsp;{$plugin_name}</a>&nbsp;" .
											( empty( $to_current ) ? '' : $to_current . '&nbsp;' ) .
											esc_html__( 'to use this functionality.', 'captcha-bws' ); ?>
										</td>
									</tr>
								</table>
								<?php unset( $recommended );
							} else {
								cptch_additional_options();
							} ?>
						</div><!-- .bws_pro_version_table_bloc -->
						<?php $this->bws_pro_block_links(); ?>
					</div><!-- .bws_pro_version_bloc -->
					<!-- end pls -->
				<?php } 
			}
		}

		/**
		 * Displays 'messages' menu-tab
		 * @access public
		 * @param void
		 * @return void
		 */
		public function tab_messages() { ?>
			<h3 class="bws_tab_label"><?php esc_html_e( 'Messages Settings', 'captcha-bws' ); ?></h3>
			<?php $this->help_phrase(); ?>
			<hr>
			<table class="form-table">
				<?php $messages = array(
					'no_answer'				=> array(
						'title'			=> esc_html__( 'Captcha Field is Empty', 'captcha-bws' ),
						'message'		=> esc_html__( 'Please complete the captcha.', 'captcha-bws' ),
						'class'			=> 'cptch_for_math_actions cptch_for_recognition'
					),
					'wrong_answer'			=> array(
						'title'			=> esc_html__( 'Captcha is Incorrect', 'captcha-bws' ),
						'message'		=> esc_html__( 'Please enter correct captcha value.', 'captcha-bws' ),
						'class'			=> 'cptch_for_math_actions cptch_for_recognition'
					),
					'time_limit_off'		=> array(
						'title'			=> esc_html__( 'Captcha Time Limit Exceeded', 'captcha-bws' ),
						'message'		=> esc_html__( 'Time limit exceeded. Please complete the captcha once again.', 'captcha-bws' ),
						'class'			=> 'cptch_for_math_actions cptch_for_recognition'
					),
					'time_limit_off_notice'	=> array(
						'title'			=> esc_html__( 'Answer Time Limit Exceeded', 'captcha-bws' ),
						'message'		=> esc_html__( 'Time limit exceeded. Please complete the captcha once again.', 'captcha-bws' ),
						'description'	=> esc_html__( 'This message will be displayed above the captcha field.', 'captcha-bws' ),
						'class'			=> 'cptch_for_math_actions cptch_for_recognition'
					),
					'text_start_slide'	=> array(
						'title'			=> esc_html__( 'Slide Title', 'captcha-bws' ),
						'class'			=> 'cptch_for_slide'
					),
					'text_end_slide'	=> array(
						'title'			=> esc_html__( 'Successfull Verification', 'captcha-bws' ),
						'class'			=> 'cptch_for_slide' 
					)
				);
				$messages = apply_filters( 'cptch_get_additional_messages', $messages );
				foreach ( $messages as $message_name => $data ) { ?>
					<tr <?php if ( ! empty( $data['class'] ) ) echo ' class="' . $data['class'] . '"'; ?>>
						<th scope="row"><?php echo $data['title']; ?></th>
						<td>
							<textarea <?php echo 'id="cptch_' . $message_name . '" name="cptch_' . $message_name . '"'; ?>><?php echo trim( $this->options[ $message_name ] ); ?></textarea>
							<?php if ( isset( $data['description'] ) ) { ?>
								<div class="bws_info"><?php echo $data['description']; ?></div>
							<?php } ?>
						</td>
					</tr>
				<?php } ?>
			</table><!-- pls -->
			<?php if ( ! $this->hide_pro_tabs ) { ?>
				<div class="bws_pro_version_bloc">
					<div class="bws_pro_version_table_bloc">
						<button type="submit" name="bws_hide_premium_options" class="notice-dismiss bws_hide_premium_options" title="<?php esc_html_e( 'Close', 'captcha-bws' ); ?>"></button>
						<div class="bws_table_bg"></div>
							<?php cptch_display_messages(); ?>
							<?php $this->bws_pro_block_links(); ?>
					</div>
				</div>
			<?php } ?>
			<!-- end pls -->
		<?php }

		/**
		 * Display custom options on the 'misc' tab
		 * @access public
		 */
		public function additional_misc_options() {
			do_action( 'cptch_settings_page_misc_action', $this->options );
		}

		/**
		 * Displays the HTML radiobutton with the specified attributes
		 * @access private
		 * @param  array  $args   An array of HTML attributes
		 * @return void
		 */
		private function add_radio_input( $args ) { ?>
			<input
				type="radio"
				id="<?php echo $args['id']; ?>"
				name="<?php echo $args['name']; ?>"
				value="<?php echo $args['value']; ?>"
				<?php echo $args['checked'] ? ' checked="checked"' : ''; ?> />
		<?php }

		/**
		 * Displays the HTML checkbox with the specified attributes
		 * @access private
		 * @param  array  $args   An array of HTML attributes
		 * @return void
		 */
		private function add_checkbox_input( $args ) { ?>
			<input
				type="checkbox"
				id="<?php echo $args['id']; ?>"
				name="<?php echo $args['name']; ?>"
				value="<?php ! empty( $args['value'] ) ? print_r( $args['value'] ) : print_r( 1 ) ; ?>"
				<?php echo ( ! empty( $args['disabled'] ) ) ? ' disabled="disabled"' : '';
				echo $args['checked'] ? ' checked="checked"' : ''; ?> />
		<?php }

		/**
		 * Displays the HTML number field with the specified attributes
		 * @access private
		 * @param  array  $args   An array of HTML attributes
		 * @return void
		 */
		private function add_number_input( $args ) { ?>
			<input
				type="number"
				step="1"
				min="<?php echo $args['min']; ?>"
				max="<?php echo $args['max']; ?>"
				id="<?php echo $args['id']; ?>"
				name="<?php echo $args['name']; ?>"
				value="<?php echo $args['value']; ?>" />
		<?php }

		/**
		 * Displays the HTML text field with the specified attributes
		 * @access private
		 * @param  array  $args   An array of HTML attributes
		 * @return void
		 */
		private function add_text_input( $args ) { ?>
			<input
				type="text"
				id="<?php echo $args['id']; ?>"
				name="<?php echo $args['name']; ?>"
				value="<?php echo $args['value']; ?>" />
		<?php }

		/**
		 * Displays the list of available package list on the form options tabs
		 * @access private
		 * @param  array  $args   An array of HTML attributes
		 * @return boolean
		 */
		private function add_pack_list_input( $args ) {
			global $wpdb;

			$package_list = $wpdb->get_results(
				"SELECT
					`{$wpdb->base_prefix}cptch_packages`.`id`,
					`{$wpdb->base_prefix}cptch_packages`.`name`,
					`{$wpdb->base_prefix}cptch_packages`.`folder`,
					`{$wpdb->base_prefix}cptch_packages`.`settings`,
					`{$wpdb->base_prefix}cptch_images`.`name` AS `image`
				FROM
					`{$wpdb->base_prefix}cptch_packages`
				LEFT JOIN
					`{$wpdb->base_prefix}cptch_images`
				ON
					`{$wpdb->base_prefix}cptch_images`.`package_id`=`{$wpdb->base_prefix}cptch_packages`.`id`
				GROUP BY `{$wpdb->base_prefix}cptch_packages`.`id`
				ORDER BY `name` ASC;",
				ARRAY_A
			);

			if ( empty( $package_list ) ) { ?>
				<span><?php esc_html_e( 'The image packages list is empty. Please restore default settings or re-install the plugin to fix this error.', 'captcha-bws' ); ?></span>
				<?php return false;
			}

			if ( $this->is_multisite ) {
				switch_to_blog( 1 );
				$upload_dir = wp_upload_dir();
				restore_current_blog();
			} else {
				$upload_dir = wp_upload_dir();
			}
			$packages_url = $upload_dir['baseurl'] . '/bws_captcha_images'; ?>
			<div class="cptch_tabs_package_list">
				<ul class="cptch_tabs_package_list_items">
				<?php foreach ( $package_list as $pack ) {
					$styles = '';
					if ( ! empty( $pack['settings'] ) ) {
						$settings = unserialize( $pack['settings'] );
						if ( is_array( $settings ) ) {
							$styles = ' style="';
							foreach ( $settings as $propery => $value )
								$styles .= "{$propery}: {$value};";
							$styles .= '"';
						}
					}
					$id			= "{$args['id']}_{$pack['id']}";
					$name		= "{$args['name']}[]";
					$value		= $pack['id'];
					$checked	= isset($args['value']) && in_array( $pack['id'], $args['value'] ); ?>
					<li>
						<span><?php $this->add_checkbox_input( compact( 'id', 'name', 'value', 'checked' ) ); ?></span>
						<span><label for="<?php echo $id; ?>"><img src="<?php echo "{$packages_url}/{$pack['folder']}/{$pack['image']}"; ?>" title="<?php echo $pack['name']; ?>"<?php echo $styles; ?>/></label></span>
						<span><label for="<?php echo $id; ?>"><?php echo $pack['name']; ?></label></span>
					</li>
				<?php } ?>
				</ul>
			</div>
			<?php return true;
		}

		/**
		 * Displays messages 'insall now'/'activate' for not active plugins
		 * @param  string $status
		 * @return string
		 */
		private function get_form_message( $slug ) {
			switch ( $this->options['related_plugins_info'][ $slug ]['status'] ) {
				case 'deactivated':
					return ' <a href="plugins.php">' . esc_html__( 'Activate', 'captcha-bws' ) . '</a>';
				case 'not_installed':
					return ' <a href="' . $this->options['related_plugins_info'][ $slug ]['link'] . '" target="_blank">' . esc_html__( 'Install Now', 'captcha-bws' ) . '</a>';
				default:
					return '';
			}
		}

		/**
		 * Form data from the user call function for the "cptch_add_form_tab" hook
		 * @access private
		 * @param  string|array   $form_data   Each new form data
		 * @return array                       Sanitized label
		 */
		private function sanitize_new_form_data( $form_data ) {
			$form_data = (array)$form_data;
			/**
			 * Return an array with the one element only
			 * to prevent the processing of potentially dangerous data
			 * @see self::_construct()
			 */
			return array( 'name' => esc_html( trim( $form_data[0] ) ) );
		}

		/**
		 * Whether the images are enabled for the CAPTCHA
		 * @access private
		 * @param  void
		 * @return boolean
		 */
		private function images_enabled() {
			return in_array( 'images', $this->options['operand_format'] );
		}

		/**
		 * Custom functions for "Restore plugin options to defaults"
		 * @access public
		 */
		public function additional_restore_options( $default_options ) {
			$default_options = $this->get_related_plugins_info( $default_options );

			/* do not update package selection */
			$default_options['forms']['general']['used_packages'] = $this->options['forms']['general']['used_packages'];

			return $default_options;
		}

		/**
		 * Using for adding related plugin's info during the restoring or creating this class
		 * @access public
		 * @param  array
		 * @return array
		 */
		public function get_related_plugins_info( $options ) {
			/**
			* default compatible plugins
			*/
			$compatible_plugins = array(
				'bws_contact' => array( 'contact-form-plugin/contact_form.php', 'contact-form-pro/contact_form_pro.php' ),
				'bws_booking' => 'bws-car-rental-pro/bws-car-rental-pro.php',
				'limit_attempts' => array( 'limit-attempts/limit-attempts.php', 'limit-attempts-pro/limit-attempts-pro.php' )
			);

			$compatible_plugins = apply_filters( 'cptch_get_additional_plugins', $compatible_plugins );			

			foreach ( $compatible_plugins as $plugin_slug => $plugin )
				$options['related_plugins_info'][ $plugin_slug ] = cptch_get_plugin_status( $plugin, $this->all_plugins );

			return $options;
		}
	}
}