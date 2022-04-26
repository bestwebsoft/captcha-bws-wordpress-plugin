<?php
/**
 * Display banners on settings page
 * @package Captcha by BestWebSoft
 * @since 4.1.5
 */

/**
 * Show ads for PRO
 * @param     string     $func        function to call
 * @return    void
 */

if ( ! function_exists( 'cptch_pro_block' ) ) {
	function cptch_pro_block( $func, $show_cross = true, $display_always = false ) {
		global $cptch_plugin_info, $wp_version, $cptch_options;
		if ( $display_always || ! bws_hide_premium_options_check( $cptch_options ) ) { ?>
			<div class="bws_pro_version_bloc cptch_pro_block <?php echo $func;?>" title="<?php esc_html_e( 'This options is available in Pro version of plugin', 'captcha-bws' ); ?>">
				<div class="bws_pro_version_table_bloc">
					<?php if ( $show_cross ) { ?>
						<button type="submit" name="bws_hide_premium_options" class="notice-dismiss bws_hide_premium_options" title="<?php esc_html_e( 'Close', 'captcha-bws' ); ?>" value="1"></button>
					<?php } ?>
					<div class="bws_table_bg"></div>
					<div class="bws_pro_version">
						<?php call_user_func( $func ); ?>
					</div>
				</div>
				<div class="bws_pro_version_tooltip">
					<a class="bws_button" href="https://bestwebsoft.com/products/wordpress/plugins/captcha/?k=28d4cf0b4ab6f56e703f46f60d34d039&pn=83&v=<?php echo $cptch_plugin_info["Version"]; ?>&wp_v=<?php echo $wp_version; ?>" target="_blank" title="<?php echo $cptch_plugin_info["Name"]; ?>">
						<?php esc_html_e( 'Upgrade to Pro', 'captcha-bws' ); ?>
					</a>
					<div class="clear"></div>
				</div>
			</div>
		<?php }
	}
}

if ( ! function_exists( 'cptch_packages_banner' ) ) {
	function cptch_packages_banner() { ?>
		<div class="bws_pro_version">
			<button class="button-primary" disabled="disabled"><?php esc_html_e( 'Add New ', 'cptch-bws' ); ?></button>
		</div>
	<?php }
}

if ( ! function_exists( 'cptch_use_limit_attempts_allowlist' ) ) {
	function cptch_use_limit_attempts_allowlist() { ?>
		<table class="form-table bws_pro_version">
			<tr>
				<th scope="row"><?php esc_html_e( 'Allow List', 'captcha-bws' ); ?></th>
				<td>
					<fieldset>
						<label><input type="radio" disabled="disabled" checked="checked" /><?php esc_html_e( 'Default', 'captcha-bws' ); ?></label><br />
						<label><input type="radio" disabled="disabled" />Limit Attempts</label><br />
					</fieldset>
					<span class="bws_info"><?php esc_html_e( 'With a allow list you can hide captcha field for your personal and trusted IP addresses.', 'captcha-bws' ); ?></span>
				</td>
			</tr>
		</table>
	<?php }
}

if ( ! function_exists( 'cptch_allowlist_banner' ) ) {
	function cptch_allowlist_banner() { ?>
		<div class="bws_pro_version">
			<div class="bws_info" style="line-height: 2;"><?php esc_html_e( "Allowed formats", 'captcha-bws' ); ?>:&nbsp;<code>192.168.0.1, 192.168.0., 192.168., 192., 192.168.0.1/8, 123.126.12.243-185.239.34.54</code></div>
			<div class="bws_info" style="line-height: 2;"><?php esc_html_e( "Allowed separators for IPs: a comma", 'captcha-bws' ); ?> (<code>,</code>), <?php esc_html_e( 'semicolon', 'captcha-bws' ); ?> (<code>;</code>), <?php esc_html_e( 'ordinary space, tab, new line or carriage return', 'captcha-bws' ); ?></div>
			<?php esc_html_e( 'Reason', 'captcha-bws' ); ?><br>
			<textarea disabled></textarea>
			<div class="bws_info" style="line-height: 2;"><?php esc_html_e( "Allowed separators for reasons: a comma", 'captcha-bws' ); ?> (<code>,</code>), <?php esc_html_e( 'semicolon', 'captcha-bws' ); ?> (<code>;</code>), <?php esc_html_e( 'tab, new line or carriage return', 'captcha-bws' ); ?></div>
		</div>
	<?php }
}

if ( ! function_exists( 'cptch_display_messages' ) ) {
	function cptch_display_messages() { ?>
	<table class="form-table bws_pro_version">
		<?php $message = array(
				'allowlist_message'	=> array(
				'title'				=> esc_html__( 'Allow Listed IP', 'captcha-bws' ),
				'message'			=> esc_html__( 'Your IP address is allow listed.', 'captcha-bws' ),
				'description'		=> esc_html__( 'This message will be displayed instead of the captcha field.', 'captcha-bws' )
			)
		); ?>
		<tr>
			<th scope="row"><?php echo $message['allowlist_message']['title']; ?></th>
			<td>
				<textarea disabled="disabled"><?php echo $message['allowlist_message']['message']; ?></textarea>
				<div class="bws_info"><?php echo $message['allowlist_message']['description']; ?></div>
			</td>
		</tr>
	</table>
	<?php }
}

if ( ! function_exists( 'cptch_additional_options' ) ) {
	function cptch_additional_options() {
		$src = plugins_url( 'images/package/', dirname( __FILE__ ) ); ?>
		<table class="form-table bws_pro_version">
			<tr>
				<th scope="row"><?php esc_html_e( 'General Settings', 'captcha-bws' ); ?></th>
				<td>
					<input type="checkbox" disabled="disabled" />
					<span class="bws_info"><?php esc_html_e( 'Enable to use general captcha settings.', 'captcha-bws' ); ?></span>
				</td>
			</tr>
			<tr class="cptch_form_option_used_packages">
				<th scope="row"><?php esc_html_e( 'Image Packages', 'captcha-bws' );?></th>
				<td>
					<fieldset>
						<div class="cptch_tabs_package_list cptch_pro_pack_tab">
							<ul class="cptch_tabs_package_list_items">
								<li>
									<span><input type="checkbox" disabled="disabled" /></span>
									<span><img src="<?php echo $src; ?>arabic_bt/0.png"></span>
									<span>Arabic ( black numbers - transparent background )</span>
								</li>
								<li>
									<span><input type="checkbox" disabled="disabled" /></span>
									<span><img src="<?php echo $src; ?>arabic_bw/0.png"></span>
									<span>Arabic ( black numbers - white background )</span>
								</li>
								<li>
									<span><input type="checkbox" disabled="disabled" /></span>
									<span><img src="<?php echo $src; ?>arabic_wb/0.png"></span>
									<span>Arabic ( white numbers - black background )</span>
								</li>
							</ul>
						</div>
					</fieldset>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php esc_html_e( 'Time Limit', 'captcha-bws' ); ?></th>
				<td>
					<input type="checkbox" disabled="disabled">
					<span class="bws_info"><?php esc_html_e( 'Enable to activate a time limit requeired to complete captcha.', 'captcha-bws' ); ?></span>
				</td>
			</tr>
		</table>
	<?php }
}

if ( ! function_exists( 'cptch_use_several_packages' ) ) {
	function cptch_use_several_packages() { ?>
		<table class="form-table cptch_enable_to_use_several_packages bws_pro_version">
			<tr>
				<th scope="row"><?php esc_html_e( 'Use Several Image Packages at The Same Time', 'captcha-bws' );?></th>
				<td><fieldset><input type="checkbox" disabled="disabled" /></fieldset></td>
			</tr>
		</table>
	<?php }
}

/**
 * Function disable's pro-tabs displaing beyond the main settings page
 * @since 4.3.1
 * @param  void
 * @return void
 */
if ( ! function_exists( 'hide_pro_tabs_beyond_settings_page' ) ) {
	function hide_pro_tabs_beyond_settings_page() {
		if ( isset( $_POST['bws_hide_premium_options'] ) ) {
			global $cptch_options;

			/* options changing */
			$result = bws_hide_premium_options( $cptch_options );

			/* return if options had been disabled earlier */
			if ( true === $result )
				return;

			/* changin the globol variable */
			$cptch_options = $result['options'];

			update_option( 'cptch_options', $cptch_options ); ?>

			<div class="updated bws-notice inline"><p><strong><?php if ( ! empty( $result['message'] ) ) echo $result['message']; ?></strong></p></div>
		<?php }
	}
}

if ( ! function_exists( 'cptch_allowlist_block' ) ) {
	function cptch_allowlist_block( $date_format ) { ?>
		<div>
			<input type="submit" name="cptch_load_limit_attempts_allowlist" class="button" value="<?php esc_html_e( 'Load IP Address(-es)', 'captcha-bws' ); ?>" style="float: left;" disabled="disabled" />
			<div class="clear"></div>
		</div>
		<div class="bws_info"><?php esc_html_e( 'Load IP addresses from the "Limit Attempts" allow list.', 'captcha-bws' ); ?></div>
        <form class="form-table cptch_allowlist_form" method="post" action="admin.php?page=captcha-allowlist.php" style="margin: 10px 0;<?php echo ! ( isset( $_REQUEST['cptch_show_allowlist_form'] ) || isset( $_REQUEST['cptch_add_to_allowlist'] ) ) ? 'display: none;': ''; ?>">
            <label><?php esc_html_e( 'IP to allow list', 'captcha-bws' ) ?></label>
            <br />
            <textarea disabled="disabled" rows="2" cols="32" name="cptch_add_to_allowlist"></textarea>
                <br />
                <label>
                    <input disabled="disabled" type="checkbox" name="cptch_add_to_allowlist_my_ip" value="1" />
					<?php esc_html_e( 'My IP', 'captcha-bws' ); ?>
                </label>
            <div class="bws_info">
                <div style="line-height: 2;">
					<?php esc_html_e( "Allowed formats", 'captcha-bws' ); ?>:
                    <code>
                        192.168.0.1, 192.168.0., 192.168., 192., 192.168.0.1/8, 123.126.12.243-185.239.34.54
                    </code>
                </div>
                <div style="line-height: 2;"><?php esc_html_e( "Allowed diapason", 'captcha-bws' ); ?>:<code>0.0.0.0 - 255.255.255.255</code></div>
                <div style="line-height: 2;">
					<?php esc_html_e( "Allowed separators", 'captcha-bws' ); ?>: <?php esc_html_e( "a comma", 'captcha-bws' ); ?> (<code>,</code>), <?php esc_html_e( 'semicolon', 'captcha-bws' ); ?> (<code>;</code>), <?php esc_html_e( 'ordinary space, tab, new line or carriage return.', 'captcha-bws' ); ?>
                </div>
            </div>
            <br />
            <label><?php esc_html_e( 'Reason', 'captcha-bws' ) ?></label>
            <br />
            <textarea disabled="disabled" rows="2" cols="32" name="cptch_add_to_allowlist_reason"></textarea>
            <div class="bws_info">
				<?php esc_html_e( "Allowed separators for reasons", 'captcha-bws' ); ?>: <?php esc_html_e( "a comma", 'captcha-bws' )?> (<code>,</code>), <?php esc_html_e( 'semicolon', 'captcha-bws' ); ?> (<code>;</code>), <?php esc_html_e( 'tab, new line or carriage return.', 'captcha-bws' ); ?>
            </div>
            <p>
                <input disabled="disabled" type="submit" class="button-secondary" value="<?php esc_html_e( 'Add IP to allow list', 'captcha-bws' ); ?>" />
            </p>
        </form>
        <p class="search-box">
			<label class="screen-reader-text" for="pdfprnt-search-input"><?php esc_html_e( 'search', 'captcha-bws' ); ?>:</label>
			<input disabled="disabled" type="search" name="s" />
			<input disabled="disabled" type="submit" id="search-submit" class="button" value="<?php esc_html_e( 'search', 'captcha-bws' ); ?>" />
		</p>
		<div class="tablenav top">
			<div class="alignleft actions bulkactions">
				<select disabled="disabled" name="action" id="bulk-action-selector-top">
					<option value="-1"><?php esc_html_e( 'Bulk Actions', 'captcha-bws' ); ?></option>
					<option value="trash"><?php esc_html_e( 'Trash', 'captcha-bws' ); ?></option>
				</select>
				<input disabled="disabled" type="submit" id="doaction" class="button action" value="<?php esc_html_e( 'Apply', 'captcha-bws' ); ?>" />
			</div>
			<div class="alignleft actions bulkactions">
				<select disabled="disabled" name="action" id="filter-selector-top">
					<option value="-1"><?php esc_html_e( 'All dates', 'captcha-bws' ); ?></option>
					<option value="filter"><?php esc_html_e( 'Filter', 'captcha-bws' ); ?></option>
				</select>
				<input disabled="disabled" type="submit" id="doaction2" class="button action" value="<?php esc_html_e( 'Filter', 'captcha-bws' ); ?>" />
			</div>
			<div class="tablenav-pages one-page">
				<span class="displaying-num">1 <?php esc_html_e( 'items', 'captcha-bws' ); ?></span>
			</div>
			<br class="clear">
		</div>
		<table class="wp-list-table widefat fixed striped allowlist">
			<thead>
				<tr>
					<td class="manage-column column-cb check-column">
						<input disabled="disabled" id="cb-select-all-2" type="checkbox" />
					</td>
					<th scope="col" class="manage-column column-title column-primary sortable desc">
						<a href="#">
							<span><?php esc_html_e( 'IP Address', 'captcha-bws' ); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
					<th scope="col" class="manage-column column-title column-primary sortable desc">
						<a href="#">
							<span><?php esc_html_e( 'Range from/to', 'captcha-bws' ); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
					<th scope="col" class="manage-column column-title"><?php esc_html_e( 'Reason', 'captcha-bws' ); ?></th>
					<th scope="col" class="manage-column column-date sortable desc">
						<a href="#">
							<span><?php esc_html_e( 'Date Added', 'captcha-bws' ); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
			</thead>
			<tbody id="the-list" data-wp-lists="list:allowlist">
				<tr>
					<th scope="row" class="check-column">
						<input disabled="disabled" id="cb_19" type="checkbox" />
					</th>
					<td class="title column-title has-row-actions column-primary" data-colname="Title">
						<span>127.0.0.1</span>
						<div class="row-actions">
							<span class="edit"><a href="#"><?php esc_html_e( 'Edit', 'captcha-bws' ); ?></a> | </span>
							<span class="trash"><a class="submitdelete" href="#"><?php esc_html_e( 'Trash', 'captcha-bws' ); ?></a></span>
						</div>
						<button type="button" class="toggle-row"></button>
						<button type="button" class="toggle-row"></button>
					</td>
					<td class="column-range" data-colname="range">
						<p> - </p>
					</td>
					<td class=" column-reason" data-colname="reason">
						<p> Lorem Ipsum dolor sit amet</p>
					</td>
					<td class="date column-date" data-colname="<?php esc_html_e( 'Date Added', 'captcha-bws' ); ?>"><?php echo date_i18n( $date_format, strtotime( 'May 3, 2017' ) ); ?></td>
				</tr>
			</tbody>
			<tfoot>
				<tr>
					<td class="manage-column column-cb check-column">
						<input disabled="disabled" id="cb-select-all-2" type="checkbox" />
					</td>
					<th scope="col" class="manage-column column-title column-primary sortable desc">
						<a href="#">
							<span><?php esc_html_e( 'IP Address', 'captcha-bws' ); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
					<th scope="col" class="manage-column column-title column-primary sortable desc">
						<a href="#">
							<span><?php esc_html_e( 'Range from/to', 'captcha-bws' ); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
					<th scope="col" class="manage-column column-print"><?php esc_html_e( 'Reason', 'captcha-bws' ); ?></th>
					<th scope="col" class="manage-column column-date sortable desc">
						<a href="#">
							<span><?php esc_html_e( 'Date Added', 'captcha-bws' ); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
				</tr>
			</tfoot>
		</table>
		<div class="tablenav bottom">
			<div class="alignleft actions bulkactions">
				<select disabled="disabled" name="action2" id="bulk-action-selector-bottom">
					<option value="-1"><?php esc_html_e( 'Bulk Actions', 'captcha-bws' ); ?></option>
					<option value="trash"><?php esc_html_e( 'Trash', 'captcha-bws' ); ?></option>
				</select>
				<input disabled="disabled" type="submit" id="doaction3" class="button action" value="<?php esc_html_e( 'Apply', 'captcha-bws' ); ?>" />
			</div>
			<div class="alignleft actions bulkactions">
				<select disabled="disabled" name="action" id="filter-selector-bottom">
					<option value="-1"><?php esc_html_e( 'All dates', 'captcha-bws' ); ?></option>
					<option value="filter"><?php esc_html_e( 'Filter', 'captcha-bws' ); ?></option>
				</select>
				<input disabled="disabled" type="submit" id="doaction4" class="button action" value="<?php esc_html_e( 'Filter', 'captcha-bws' ); ?>" />
			</div>
			<div class="tablenav-pages one-page">
				<span class="displaying-num">1 <?php esc_html_e( 'items', 'captcha-bws' ); ?></span>
			</div>
			<br class="clear">
		</div>
	<?php }
}

if ( ! function_exists( 'cptch_slide_pro_block' ) ) {
	function cptch_slide_pro_block() { ?>
		<div class="bws_pro_version_table_bloc">
			<button type="submit" name="bws_hide_premium_options" class="notice-dismiss bws_hide_premium_options" title="<?php esc_html_e( 'Close', 'captcha-bws' ); ?>"></button>
			<div class="bws_table_bg"></div>
			<table class="form-table bws_pro_version">
				<tr>
					<th scope="row" class="cptch_settings_form"><?php esc_html_e( 'Slider Color', 'captcha-bws' ); ?></th>
					<td>
						<fieldset>
							<label for="cptch_color_start_slide">
								<input type="text"
									id="cptch_color_start_slide"
									name="cptch_color_start_slide"
									value="#1888F8"
									class="cptch_color_field" 
									data-default-color="#1888F8" />
							</label>						
						</fieldset>								
					</td>
				</tr>
				<tr>
					<th scope="row" ><?php esc_html_e( 'Successfull Slider Color', 'captcha-bws' ); ?></th>
					<td>
						<fieldset>
							<label for="cptch_color_end_slide">
								<input type="text"
									id="cptch_color_end_slide"
									name="cptch_color_end_slide"
									value="#43b309"
									class="cptch_color_field" 
									data-default-color="#43b309" />
							</label>
						</fieldset>
					</td>
				</tr>
				<tr>
					<th scope="row" class="cptch_settings_form"><?php esc_html_e( 'Slide Container Color', 'captcha-bws' ); ?></th>
					<td>
						<fieldset>
							<label for="cptch_color_container_slide">
								<input type="text"
									id="cptch_color_container_slide"
									name="cptch_color_container_slide"
									value="#E7E7E7"
									class="cptch_color_field" 
									data-default-color="#E7E7E7" />
							</label>
						</fieldset>
					</td>
				</tr>
				<tr>
					<th scope="row" class="cptch_settings_form"><?php esc_html_e( 'Slide Title Color', 'captcha-bws' ); ?></th>
					<td>
						<fieldset>
							<label for="cptch_color_text_slide">
								<input type="text"
									id="cptch_color_text_slide"
									name="cptch_color_text_slide"
									value="#000000"
									class="cptch_color_field" 
									data-default-color="#000000" />
							</label>
						</fieldset>
					</td>
				</tr>
				<tr>
					<th scope="row" class="cptch_settings_form"><?php esc_html_e( 'Slide Title Size', 'captcha-bws' ); ?></th>
					<td>
						<fieldset>
							<label for="cptch_font_size_text_slide">
								<input type="number"
									step="1"
									min="1"
									max="100"
									id="cptch_font_size_text_slide"
									name="cptch_font_size_text_slide"
									value="14" />&nbsp;px
							</label>
						</fieldset>
						<span class="bws_info cptch_settings_form"><?php esc_html_e( 'Set a font-size for title.', 'captcha-bws' ); ?></span>
					</td>
				</tr>
			</table>
		</div>		
	<?php }
}