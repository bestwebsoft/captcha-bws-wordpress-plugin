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
			<div class="bws_pro_version_bloc cptch_pro_block <?php echo $func;?>" title="<?php _e( 'This options is available in Pro version of plugin', 'captcha-bws' ); ?>">
				<div class="bws_pro_version_table_bloc">
					<?php if ( $show_cross ) { ?>
						<button type="submit" name="bws_hide_premium_options" class="notice-dismiss bws_hide_premium_options" title="<?php _e( 'Close', 'captcha-bws' ); ?>" value="1"></button>
					<?php } ?>
					<div class="bws_table_bg"></div>
					<div class="bws_pro_version">
						<?php call_user_func( $func ); ?>
					</div>
				</div>
				<div class="bws_pro_version_tooltip">
					<a class="bws_button" href="https://bestwebsoft.com/products/wordpress/plugins/captcha/?k=28d4cf0b4ab6f56e703f46f60d34d039&pn=83&v=<?php echo $cptch_plugin_info["Version"]; ?>&wp_v=<?php echo $wp_version; ?>" target="_blank" title="Google Captcha Pro (reCAPTCHA)">
						<?php _e( 'Upgrade to Pro', 'captcha-bws' ); ?>
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
			<button class="button-primary" disabled="disabled"><?php _e( 'Add New ', 'cptch-bws' ); ?></button>
		</div>
	<?php }
}

if ( ! function_exists( 'cptch_use_limit_attempts_whitelist' ) ) {
	function cptch_use_limit_attempts_whitelist() { ?>
		<table class="form-table bws_pro_version">
			<tr>
				<th scope="row"><?php _e( 'Whitelist', 'captcha-bws' ); ?></th>
				<td>
					<fieldset>
						<label><input type="radio" disabled="disabled" checked="checked" /><?php _e( 'Default', 'captcha-bws' ); ?></label><br />
						<label><input type="radio" disabled="disabled" /><?php _e( 'Limit Attempts', 'captcha-bws' ); ?></label><br />
					</fieldset>
					<span class="bws_info"><?php _e( 'With a whitelist you can hide captcha field for your personal and trusted IP addresses.', 'captcha-bws' ); ?></span>
				</td>
			</tr>
		</table>
	<?php }
}

if ( ! function_exists( 'cptch_whitelist_banner' ) ) {
	function cptch_whitelist_banner() { ?>
		<div class="bws_pro_version">
			<div class="bws_info" style="line-height: 2;"><?php _e( "Allowed formats", 'captcha-bws' ); ?>:&nbsp;<code>192.168.0.1, 192.168.0., 192.168., 192., 192.168.0.1/8, 123.126.12.243-185.239.34.54</code></div>
			<div class="bws_info" style="line-height: 2;"><?php _e( "Allowed separators for IPs: a comma", 'captcha-bws' ); ?> (<code>,</code>), <?php _e( 'semicolon', 'captcha-bws' ); ?> (<code>;</code>), <?php _e( 'ordinary space, tab, new line or carriage return', 'captcha-bws' ); ?></div>
			<?php _e( 'Reason', 'captcha-bws' ); ?><br>
			<textarea disabled></textarea>
			<div class="bws_info" style="line-height: 2;"><?php _e( "Allowed separators for reasons: a comma", 'captcha-bws' ); ?> (<code>,</code>), <?php _e( 'semicolon', 'captcha-bws' ); ?> (<code>;</code>), <?php _e( 'tab, new line or carriage return', 'captcha-bws' ); ?></div>
		</div>
	<?php }
}

if ( ! function_exists( 'cptch_display_messages' ) ) {
	function cptch_display_messages() { ?>
	<table class="form-table bws_pro_version">
		<?php $message = array(
				'whitelist_message'	=> array(
				'title'				=> __( 'Whitelisted IP', 'captcha-bws' ),
				'message'			=> __( 'Your IP address is Whitelisted.', 'captcha-bws' ),
				'description'		=> __( 'This message will be displayed instead of the captcha field.', 'captcha-bws' )
			)
		); ?>
		<tr>
			<th scope="row"><?php echo $message['whitelist_message']['title']; ?></th>
			<td>
				<textarea disabled="disabled"><?php echo $message['whitelist_message']['message']; ?></textarea>
				<div class="bws_info"><?php echo $message['whitelist_message']['description']; ?></div>
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
				<th scope="row"><?php _e( 'General Settings', 'captcha-bws' ); ?></th>
				<td>
					<input type="checkbox" disabled="disabled" />
					<span class="bws_info"><?php _e( 'Enable to use general captcha settings.', 'captcha-bws' ); ?></span>
				</td>
			</tr>
			<tr class="cptch_form_option_used_packages">
				<th scope="row"><?php _e( 'Image Packages', 'captcha-bws' );?></th>
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
				<th scope="row"><?php _e( 'Time Limit', 'captcha-bws' ); ?></th>
				<td>
					<input type="checkbox" disabled="disabled">
					<span class="bws_info"><?php _e( 'Enable to activate a time limit requeired to complete captcha.', 'captcha-bws' ); ?></span>
				</td>
			</tr>
		</table>
	<?php }
}

if ( ! function_exists( 'cptch_use_several_packages' ) ) {
	function cptch_use_several_packages() { ?>
		<table class="form-table cptch_enable_to_use_several_packages bws_pro_version">
			<tr>
				<th scope="row"><?php _e( 'Use Several Image Packages at The Same Time', 'captcha-bws' );?></th>
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

if ( ! function_exists( 'cptch_whitelist_block' ) ) {
	function cptch_whitelist_block( $date_format ) {
		global $wp_version;
		$old_wp_version = ( version_compare( $wp_version, '4.3', '<' ) ); ?>
		<div>
			<input type="submit" name="cptch_load_limit_attempts_whitelist" class="button" value="<?php _e( 'Load IP Address(-es)', 'captcha-bws' ); ?>" style="float: left;" disabled="disabled" />
			<div class="clear"></div>
		</div>
		<div class="bws_info"><?php _e( 'Load IP addresses from the "Limit Attempts" whitelist.', 'captcha-bws' ); ?></div>
        <form class="form-table cptch_whitelist_form" method="post" action="admin.php?page=captcha-whitelist.php" style="margin: 10px 0;<?php echo ! ( isset( $_REQUEST['cptch_show_whitelist_form'] ) || isset( $_REQUEST['cptch_add_to_whitelist'] ) ) ? 'display: none;': ''; ?>">
            <label><?php _e( 'IP to whitelist', 'captcha-bws' ) ?></label>
            <br />
            <textarea rows="2" cols="32" name="cptch_add_to_whitelist"></textarea>
                <br />
                <label>
                    <input type="checkbox" name="cptch_add_to_whitelist_my_ip" value="1" />
					<?php _e( 'My IP', 'captcha-bws' ); ?>
                </label>
            <div class="bws_info">
                <div style="line-height: 2;">
					<?php _e( "Allowed formats", 'captcha-bws' ); ?>:
                    <code>
                        192.168.0.1, 192.168.0., 192.168., 192., 192.168.0.1/8, 123.126.12.243-185.239.34.54
                    </code>
                </div>
                <div style="line-height: 2;"><?php _e( "Allowed diapason", 'captcha-bws' ); ?>:<code>0.0.0.0 - 255.255.255.255</code></div>
                <div style="line-height: 2;">
					<?php _e( "Allowed separators", 'captcha-bws' ); ?>: <?php _e( "a comma", 'captcha-bws' ); ?> (<code>,</code>), <?php _e( 'semicolon', 'captcha-bws' ); ?> (<code>;</code>), <?php _e( 'ordinary space, tab, new line or carriage return.', 'captcha-bws' ); ?>
                </div>
            </div>
            <br />
            <label><?php _e( 'Reason', 'captcha-bws' ) ?></label>
            <br />
            <textarea rows="2" cols="32" name="cptch_add_to_whitelist_reason"></textarea>
            <div class="bws_info">
				<?php _e( "Allowed separators for reasons", 'captcha-bws' ); ?>: <?php _e( "a comma", 'captcha-bws' )?> (<code>,</code>), <?php _e( 'semicolon', 'captcha-bws' ); ?> (<code>;</code>), <?php _e( 'tab, new line or carriage return.', 'captcha-bws' ); ?>
            </div>
            <p>
                <input type="submit" class="button-secondary" value="<?php _e( 'Add IP to whitelist', 'captcha-bws' ); ?>" />
            </p>
        </form>
        <p class="search-box">
			<label class="screen-reader-text" for="pdfprnt-search-input"><?php _e( 'search', 'captcha-bws' ); ?>:</label>
			<input disabled="disabled" type="search" name="s" />
			<input disabled="disabled" type="submit" id="search-submit" class="button" value="search" />
		</p>
		<div class="tablenav top">
			<div class="alignleft actions bulkactions">
				<select disabled="disabled" name="action" id="bulk-action-selector-top">
					<option value="-1"><?php _e( 'Bulk Actions', 'captcha-bws' ); ?></option>
					<option value="trash"><?php _e( 'Trash', 'captcha-bws' ); ?></option>
				</select>
				<input disabled="disabled" type="submit" id="doaction" class="button action" value="Apply" />
			</div>
			<div class="alignleft actions bulkactions">
				<select disabled="disabled" name="action" id="filter-selector-top">
					<option value="-1"><?php _e( 'All dates', 'captcha-bws' ); ?></option>
					<option value="filter"><?php _e( 'Filter', 'captcha-bws' ); ?></option>
				</select>
				<input disabled="disabled" type="submit" id="doaction2" class="button action" value="Filter" />
			</div>
			<div class="tablenav-pages one-page">
				<span class="displaying-num">1 <?php _e( 'items', 'captcha-bws' ); ?></span>
			</div>
			<br class="clear">
		</div>
		<table class="wp-list-table widefat fixed striped whitelist<?php if ( $old_wp_version ) echo ' whitelist_old_wp'; ?>">
			<thead>
				<tr>
					<?php printf( '<%s class="manage-column column-cb check-column">', ( $old_wp_version ? 'th' : 'td' ) ); ?>
						<input disabled="disabled" id="cb-select-all-2" type="checkbox" />
					<?php printf( '</%s>', ( $old_wp_version ? 'th': 'td' ) ); ?>
					<th scope="col" class="manage-column column-title column-primary sortable desc">
						<a href="#">
							<span><?php _e( 'IP Address', 'captcha-bws' ); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
					<th scope="col" class="manage-column column-title column-primary sortable desc">
						<a href="#">
							<span><?php _e( 'Range from/to', 'captcha-bws' ); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
					<th scope="col" class="manage-column column-title"><?php _e( 'Reason', 'captcha-bws' ); ?></th>
					<th scope="col" class="manage-column column-date sortable desc">
						<a href="#">
							<span><?php _e( 'Date Added', 'captcha-bws' ); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
			</thead>
			<tbody id="the-list" data-wp-lists="list:whitelist">
				<tr>
					<th scope="row" class="check-column">
						<input disabled="disabled" id="cb_19" type="checkbox" />
					</th>
					<td class="title column-title has-row-actions column-primary" data-colname="Title">
						<span>127.0.0.1</span>
						<div class="row-actions">
							<span class="edit"><a href="#"><?php _e( 'Edit', 'captcha-bws' ); ?></a> | </span>
							<span class="trash"><a class="submitdelete" href="#"><?php _e( 'Trash', 'captcha-bws' ); ?></a></span>
						</div>
						<?php if ( ! $old_wp_version ) { ?>
							<button type="button" class="toggle-row"></button>
							<button type="button" class="toggle-row"></button>
						<?php } ?>
					</td>
					<td class="column-range" data-colname="range">
						<p> - </p>
					</td>
					<td class=" column-reason" data-colname="reason">
						<p> Lorem Ipsum dolor sit amet</p>
					</td>
					<td class="date column-date" data-colname="<?php _e( 'Date Added', 'captcha-bws' ); ?>"><?php echo date_i18n( $date_format, strtotime( 'May 3, 2017' ) ); ?></td>
				</tr>
			</tbody>
			<tfoot>
				<tr>
					<?php printf( '<%s class="manage-column column-cb check-column">', ( $old_wp_version ? 'th' : 'td' ) ); ?>
						<input disabled="disabled" id="cb-select-all-2" type="checkbox" />
					<?php printf( '</%s>', ( $old_wp_version ? 'th': 'td' ) ); ?>
					<th scope="col" class="manage-column column-title column-primary sortable desc">
						<a href="#">
							<span><?php _e( 'IP Address', 'captcha-bws' ); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
					<th scope="col" class="manage-column column-title column-primary sortable desc">
						<a href="#">
							<span><?php _e( 'Range from/to', 'captcha-bws' ); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
					<th scope="col" class="manage-column column-print"><?php _e( 'Reason', 'captcha-bws' ); ?></th>
					<th scope="col" class="manage-column column-date sortable desc">
						<a href="#">
							<span><?php _e( 'Date Added', 'captcha-bws' ); ?></span>
							<span class="sorting-indicator"></span>
						</a>
					</th>
				</tr>
			</tfoot>
		</table>
		<div class="tablenav bottom">
			<div class="alignleft actions bulkactions">
				<select disabled="disabled" name="action2" id="bulk-action-selector-bottom">
					<option value="-1"><?php _e( 'Bulk Actions', 'captcha-bws' ); ?></option>
					<option value="trash"><?php _e( 'Trash', 'captcha-bws' ); ?></option>
				</select>
				<input disabled="disabled" type="submit" id="doaction3" class="button action" value="Apply" />
			</div>
			<div class="alignleft actions bulkactions">
				<select disabled="disabled" name="action" id="filter-selector-bottom">
					<option value="-1"><?php _e( 'All dates', 'captcha-bws' ); ?></option>
					<option value="filter"><?php _e( 'Filter', 'captcha-bws' ); ?></option>
				</select>
				<input disabled="disabled" type="submit" id="doaction4" class="button action" value="Filter" />
			</div>
			<div class="tablenav-pages one-page">
				<span class="displaying-num">1 <?php _e( 'items', 'captcha-bws' ); ?></span>
			</div>
			<br class="clear">
		</div>
	<?php }
}