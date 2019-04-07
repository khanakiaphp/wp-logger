<?php
/**
 * Plugin Name: WP Logger - Tenbulls
 * Description: Logs every single request made to your Wordpress website and helps you to tighten security by analysing the requests made to your website.
 * Version: 1.0.0
 * Author: Aman Khanakia
 * Author URI: https://wordpress.org/support/users/mrkhanakia/
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: wp-logger
 * Domain Path: /lang/
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Define WP_LOGGER_PLUGIN_FILE.
if ( ! defined( 'WP_LOGGER_PLUGIN_FILE' ) ) {
	define( 'WP_LOGGER_PLUGIN_FILE', __FILE__ );
}
if( ! defined('WP_LOGGER_UPLOAD_DIR')) {
    define( 'WP_LOGGER_UPLOAD_DIR', wp_get_upload_dir()["basedir"].'/wp-logger' );
    
}


if ( ! function_exists( 'wp_logger_plugin_actions' ) ) {
    /**
     * Add settings to plugin links.
     * @param $actions
     * @return mixed
     */
    function wp_logger_plugin_actions( $actions )
    {
        array_unshift( $actions, "<a href=\"" . get_admin_url(). 'admin.php?page=WPLoggerOptions&tab=1' . "\">" . esc_html__( "Settings" ) . "</a>" );
        return $actions;
    }
    add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'wp_logger_plugin_actions', 10, 1 );
}

if ( ! function_exists( 'wp_logger_load_textdomain' ) ) {
    /**
     * Set languages directory.
     */
    function wp_logger_load_textdomain()
    {
        load_plugin_textdomain( 'wp-logger', false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );
    }
    add_action( 'plugins_loaded', 'wp_logger_load_textdomain' );
}

if ( ! function_exists( 'wp_logger_plugin_settings' ) ) {
	/**
     * Init Main Pluign
     */
	function wp_logger_plugin_settings() {
		include 'vendor/autoload.php';
	
		if ( !class_exists( 'ReduxFramework' ) && file_exists( dirname( __FILE__ ) . '/vendor/redux-framework/ReduxCore/framework.php' ) ) {
			require_once( dirname( __FILE__ ) . '/vendor/redux-framework/ReduxCore/framework.php' );
		}
		
		if ( ! class_exists( 'WP_LOGGER_FILE' ) ) {
			include_once 'includes/class-wp-logger.php';
        }
        
        $WP_LOGGER = WP_LOGGER::instance();
        $WP_LOGGER->init();
	}
	add_action( 'wp_loaded', 'wp_logger_plugin_settings' );
}


if ( ! function_exists( 'wp_logger_redux_init' ) ) {
	add_action( 'redux/init', 'wp_logger_redux_init' );
	function wp_logger_redux_init() {
        require_once( dirname( __FILE__ ) . '/redux-config.php' );
        Redux::init($opt_name);
	}
}

if ( class_exists("Redux") ) {
    require_once( dirname( __FILE__ ) . '/redux-config.php' );
    Redux::init($opt_name);
}


