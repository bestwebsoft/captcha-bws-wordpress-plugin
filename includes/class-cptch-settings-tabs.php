<?php
/**
 * Displays the content on the plugin settings page
 */

require_once( dirname( dirname( __FILE__ ) ) . '/bws_menu/class-bws-settings.php' );

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
				'settings'      => array( 'label' => __( 'Settings', 'captcha-bws' ) ),
				'misc'          => array( 'label' => __( 'Misc', 'captcha-bws' ) ),
				'custom_code'   => array( 'label' => __( 'Custom Code', 'captcha-bws' ) ),
				/*pls */
				'license'       => array( 'label' => __( 'License Key', 'captcha-bws' ) )
				/* pls*/
			);

			if ( ! function_exists( 'cptch_get_default_options' ) )
				require_once( dirname( __FILE__ ) . '/helpers.php' );

			parent::__construct( array(
				'plugin_basename'    => $plugin_basename,
				'plugins_info'       => $cptch_plugin_info,
				'prefix'             => 'cptch',
				'default_options'    => cptch_get_default_options(),
				'options'            => $cptch_options,
				'tabs'               => $tabs,
				/*pls */
				'wp_slug'             => 'captcha-bws',
				'pro_page'           => 'admin.php?page=captcha_pro.php',
				'bws_license_plugin' => 'captcha-pro/captcha_pro.php',
				'link_key'           => '9701bbd97e61e52baa79c58c3caacf6d',
				'link_pn'            => '75'
				/* pls*/			
			) );

			$this->all_plugins = get_plugins();

			$this->forms = array(
				'wp_login'         			=> array( 'name' => __( 'Login form', 'captcha-bws' ) ),
				'wp_register'      			=> array( 'name' => __( 'Registration form', 'captcha-bws' ) ),
				'wp_lost_password' 			=> array( 'name' => __( 'Reset password form', 'captcha-bws' ) ),
				'wp_comments'      			=> array( 'name' => __( 'Comments form', 'captcha-bws' ) ),
			//	'bws_contact'      			=> array( 'name' => 'Contact Form' ),
			);

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
						if ( empty( $this->options['forms'][ $new_form ] ) )
							$this->options['forms'][ $new_form ] = $this->default_options['forms'][ $new_form ];
					}
				}
			}

			/**
			* form categories are used when compatible plugins are displayed
			*/
			$this->form_categories = array(
				'wp_default' => array(
					'title' => __( 'WordPress default', 'captcha-bws' ),
					'forms' => array(
						'wp_login',
						'wp_register',
						'wp_lost_password',
						'wp_comments'
					)
				),
				'external' => array(
					'title' => __( 'External plugins', 'captcha-bws' ),
					'forms' => array(
					//	'bws_contact'
					)
				),
			);

			/**
			* create list with default compatible forms
			*/
			$this->registered_forms = array_merge(
				$this->form_categories['wp_default']['forms'],
				$this->form_categories['external']['forms']
			);

			$user_forms = array_diff( array_keys( $this->forms ), $this->registered_forms );
			if ( ! empty( $user_forms) )
				$this->form_categories['external']['forms'] = array_merge( $this->form_categories['external']['forms'], $user_forms );

			/**
			* get ralated plugins info
			*/
			$this->options = $this->get_related_plugins_info( $this->options );
		}

		/**
		 * Save plugin options to the database
		 * @access public
		 * @param  void
		 * @return array    The action results
		 */
		public function save_options() {
			/*
			 * Prepare forms options
			 */
			$forms     = array_keys( $this->forms );
			$form_bool = array( 'enable', 'hide_from_registered' );
			foreach ( $forms as $form_slug ) {
				foreach ( $form_bool as $option ) {
					$this->options['forms'][ $form_slug ][ $option ] = isset( $_REQUEST['cptch']['forms'][ $form_slug ][ $option ] );
				}
			}

			update_option( 'cptch_options', $this->options );
			$message = __( "Settings saved.", 'captcha-bws' );

			return compact( 'message', 'notice', 'error' );
		}

		/**
		 * Displays 'settings' menu-tab
		 * @access public
		 * @param void
		 * @return void
		 */
		public function tab_settings() { ?>
			<h3 class="bws_tab_label"><?php _e( 'Captcha Settings', 'captcha-bws' ); ?></h3>
			<?php $this->help_phrase(); ?>
			<hr>
			<table class="form-table">
				<tr>
					<th scope="row"><?php _e( 'Enable Captcha for', 'captcha-bws' ); ?></th>
					<td>
						<?php foreach ( $this->form_categories as $fieldset_name => $fieldset_data ) { 
							if ( ! empty( $fieldset_data['forms'] ) ) { ?>
								<p><i><?php echo $fieldset_data['title']; ?></i></p>
								<br>
								<fieldset id="<?php echo $fieldset_name; ?>">
									<?php foreach ( $fieldset_data['forms'] as $form_name ) { ?>
										<label class="cptch_related">
											<?php /**
											* if plugin is external and it is not active, it's checkbox should be disabled
											*/
											$disabled = in_array( $form_name, $this->registered_forms ) && (
													( isset( $this->options['related_plugins_info'][ $form_name ] ) &&
													'active' != $this->options['related_plugins_info'][ $form_name ]['status'] ) ||
													( isset( $this->options['related_plugins_info'][ $fieldset_name ] ) &&
													'active' != $this->options['related_plugins_info'][ $fieldset_name ]['status'] )
												);

											$value = $fieldset_name . '_' . $form_name;
											$id = 'cptch_' .  $form_name . '_enable';
											$name = 'cptch[forms][' . $form_name . '][enable]';
											$checked = !! $this->options['forms'][ $form_name ]['enable'];
											$this->add_checkbox_input( compact( 'id', 'name', 'checked', 'value', 'class', 'disabled' ) );

											echo $this->forms[ $form_name ]['name'];

											if ( 'external' == $fieldset_name && $disabled ) {
												echo $this->get_form_message( $form_name ); /* show "instal/activate" mesage */
											} elseif ( 'bws_contact' == $form_name &&
												( is_plugin_active( 'contact-form-multi/contact-form-multi.php' ) ||
												is_plugin_active( 'contact-form-multi-pro/contact-form-multi-pro.php' ) ) ) { ?>
												<span class="bws_info"> <?php _e( 'Enable to add the CAPTCHA to forms on their settings pages.', 'captcha-bws' ); ?></span>
											<?php } ?>
										</label>
										<br />
									<?php } ?>
								</fieldset>
								<hr>
							<?php }
						} ?>
					</td>
				</tr>
			</table>
			<?php 
		}

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
				value="<?php echo ! empty( $args['value'] ) ? $args['value'] : 1; ?>"
				<?php echo ( ! empty( $args['disabled'] ) ) ? ' disabled="disabled"' : '';
				echo $args['checked'] ? ' checked="checked"' : ''; ?> />
		<?php }

		/**
		 * Displays messages 'insall now'/'activate' for not active plugins
		 * @param  string $status
		 * @return string
		 */
		private function get_form_message( $slug ) {
			switch ( $this->options['related_plugins_info'][ $slug ]['status'] ) {
				case 'deactivated':
					return ' <a href="plugins.php">' . __( 'Activate', 'captcha-bws' ) . '</a>';
				case 'not_installed':
					return ' <a href="' . $this->options['related_plugins_info'][ $slug ]['link'] . '" target="_blank">' . __( 'Install Now', 'captcha-bws' ) . '</a>';
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
				'bws_contact' => array( 'contact-form-plugin/contact_form.php', 'contact-form-pro/contact_form_pro.php' )
			);

			foreach ( $compatible_plugins as $plugin_slug => $plugin )
				$options['related_plugins_info'][ $plugin_slug ] = cptch_get_plugin_status( $plugin, $this->all_plugins );

			return $options;
		}
	}
}