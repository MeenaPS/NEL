<?php
/**
 * Plugin Name: WP Logo Showcase Responsive Slider
 * Plugin URI: https://www.wponlinesupport.com/plugins/
 * Description: Easy to add and display Logo Showcase Responsive Slider on your website. Also work with Gutenberg shortcode block.
 * Author: WP OnlineSupport
 * Text Domain: wp-logo-showcase-responsive-slider-slider
 * Domain Path: /languages/
 * Version: 2.2.3
 * Author URI: https://www.wponlinesupport.com/
 *
 * @package WordPress
 * @author WP OnlineSupport
 */

if( !defined( 'WPLS_VERSION' ) ) {
	define( 'WPLS_VERSION', '2.2.3' ); // Version of plugin
}
if( !defined( 'WPLS_DIR' ) ) {
	define( 'WPLS_DIR', dirname( __FILE__ ) ); // Plugin dir
}
if( !defined( 'WPLS_URL' ) ) {
	define( 'WPLS_URL', plugin_dir_url( __FILE__ ) ); // Plugin url
}
if( !defined( 'WPLS_POST_TYPE' ) ) {
	define( 'WPLS_POST_TYPE', 'logoshowcase' ); // Plugin Post Type
}
if( !defined( 'WPLS_CAT_TYPE' ) ) {
	define( 'WPLS_CAT_TYPE', 'wplss_logo_showcase_cat' ); // Plugin Post Type
}
if( !defined( 'WPLS_META_PREFIX' ) ) {
	define( 'WPLS_META_PREFIX', '_wpls_' ); // Plugin metabox prefix
}

/**
 * Activation Hook
 * 
 * Register plugin activation hook.
 * 
 * @package WP Logo Showcase Responsive Slider
 * @since 1.0.0
 */
register_activation_hook( __FILE__, 'wpls_install' );

/**
 * Deactivation Hook
 * 
 * Register plugin deactivation hook.
 * 
 * @package WP Logo Showcase Responsive Slider
 * @since 1.0.0
 */
register_deactivation_hook( __FILE__, 'wpls_uninstall');

/**
 * Plugin Activation Function
 * Does the initial setup, sets the default values for the plugin options
 * 
 * @package WP Logo Showcase Responsive Slider
 * @since 1.0.0
 */
function wpls_install() {

	// Register post type function
	wplss_logo_showcase_post_types();
	wplss_logo_showcase_taxonomies();

	// IMP need to flush rules for custom registered post type
	flush_rewrite_rules();

	// Deactivate free version
	if( is_plugin_active('wp-logo-showcase-responsive-slider-pro/logo-showcase.php') ){
		add_action('update_option_active_plugins', 'wpls_deactivate_free_version');
	}
}

/**
 * Plugin Deactivation Function
 * Delete  plugin options
 * 
 * @package WP Logo Showcase Responsive Slider
 * @since 1.0.0
 */
function wpls_uninstall() {
	
	// IMP need to flush rules for custom registered post type
	flush_rewrite_rules();
}

/**
 * Deactivate free plugin
 * 
 * @package WP Logo Showcase Responsive Slider
 * @since 1.2.4
 */
function wpls_deactivate_free_version() {
	deactivate_plugins('wp-logo-showcase-responsive-slider-pro/logo-showcase.php', true);
}

/**
 * Function to display admin notice of activated plugin.
 * 
 * @package WP Logo Showcase Responsive Slider
 * @since 1.2.4
 */
function wpls_admin_notice() {
	
	global $pagenow;

	$dir = ABSPATH . 'wp-content/plugins/wp-logo-showcase-responsive-slider-pro/logo-showcase.php';

	$notice_link        = add_query_arg( array('message' => 'wpls-plugin-notice'), admin_url('plugins.php') );
	$notice_transient   = get_transient( 'wpls_install_notice' );
	
	// If Free plugin is active and PRO plugin exist
	if( $notice_transient == false && $pagenow == 'plugins.php' && file_exists( $dir ) && current_user_can( 'install_plugins' ) ) {        
				echo '<div class="updated notice" style="position:relative;">
					<p>
						<strong>'.sprintf( __('Thank you for activating %s', 'wp-logo-showcase-responsive-slider-slider'), 'WP Logo Showcase Responsive Slider').'</strong>.<br/>
						'.sprintf( __('It looks like you had PRO version %s of this plugin activated. To avoid conflicts the extra version has been deactivated and we recommend you delete it.', 'wp-logo-showcase-responsive-slider-slider'), '<strong>(<em>WP Logo Showcase Responsive Slider Pro</em>)</strong>' ).'
					</p>
					<a href="'.esc_url( $notice_link ).'" class="notice-dismiss" style="text-decoration:none;"></a>
				</div>';
	}

}

// Action to display notice
add_action( 'admin_notices', 'wpls_admin_notice');

/**
 * Load Text Domain
 * This gets the plugin ready for translation
 * 
 * @package WP Logo Showcase Responsive Slider
 * @since 1.0.0
 */
function wpls_load_textdomain() {
	load_plugin_textdomain( 'wp-logo-showcase-responsive-slider-slider', false, dirname( plugin_basename(__FILE__) ) . '/languages/' );
}
add_action('plugins_loaded', 'wpls_load_textdomain');

// Script file
require_once( WPLS_DIR . '/includes/class-wpls-script.php' );

//functions file
require_once( WPLS_DIR . '/includes/wpls-functions.php' );

//post types file
require_once( WPLS_DIR . '/includes/wpls-post-types.php' );

//post metabox file
require_once( WPLS_DIR . '/includes/admin/metabox/wpls-post-setting-metabox.php' );

//post metabox file
require_once( WPLS_DIR . '/includes/shortcode/wpls-logo-slider.php' );

//Admin file for pro vs free
require_once( WPLS_DIR . '/includes/admin/class-wpls-admin.php' );

// Load admin files
if ( is_admin() || ( defined( 'WP_CLI' ) && WP_CLI ) ) {
	require_once( WPLS_DIR . '/includes/admin/wpls-how-it-work.php' );	
}

/* Plugin Wpos Analytics Data Starts */
function wpos_analytics_anl23_load() {

	require_once dirname( __FILE__ ) . '/wpos-analytics/wpos-analytics.php';

	$wpos_analytics =  wpos_anylc_init_module( array(
							'id'			=> 23,
							'file'			=> plugin_basename( __FILE__ ),
							'name'			=> 'WP Logo Showcase Responsive Slider',
							'slug'			=> 'wp-logo-showcase-responsive-slider',
							'type'			=> 'plugin',
							'menu'			=> 'edit.php?post_type=logoshowcase',
							'text_domain'	=> 'wp-logo-showcase-responsive-slider-slider',
							'promotion'     => array( 
													'bundle' => array(
															'name'	=> 'Download FREE 50+ Plugins, 10+ Themes and Dashboard Plugin',
															'desc'	=> 'Download FREE 50+ Plugins, 10+ Themes and Dashboard Plugin',
															'file'	=> 'https://www.wponlinesupport.com/latest/wpos-free-50-plugins-plus-12-themes.zip'
														)
													),
							'offers'        => array(
													'trial_premium' => array(
														'image'	=> 'http://analytics.wponlinesupport.com/?anylc_img=23',
														'link'	=> 'http://analytics.wponlinesupport.com/?anylc_redirect=23',
														'desc'	=> 'Or start using the plugin from admin menu',
													)
												),
						));

	return $wpos_analytics;
}

// Init Analytics
wpos_analytics_anl23_load();
/* Plugin Wpos Analytics Data Ends */