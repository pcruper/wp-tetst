<?php
/**
 * Plugin Name: WP Test Plugin
 * Version: 1.0.0
 * Plugin URI: https://github.com/pcruper/wp-test/
 * Description: This is your starter template for your next WordPress plugin.
 * Author: Pedro Cruz
 * Author URI: https://github.com/pcruper/
 * Requires at least: 4.0
 * Tested up to: 5.6
 * Network: true
 *
 * Text Domain: wp-test-plugin
 * Domain Path: /lang/
 *
 * @package WordPress
 * @author Pedro Cruz
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Load plugin class files.
require_once 'includes/class-wp-test-plugin.php';
require_once 'includes/class-wp-test-plugin-settings.php';

// Load plugin libraries.
require_once 'includes/lib/class-wp-test-plugin-admin-api.php';
require_once 'includes/lib/class-wp-test-plugin-post-type.php';
require_once 'includes/lib/class-wp-test-plugin-taxonomy.php';

/**
 * Returns the main instance of WP_Test_Plugin to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return object WP_Test_Plugin
 */
function wp_test_plugin() {
	$instance = WP_Test_Plugin::instance( __FILE__, '1.0.0' );

	if ( is_null( $instance->settings ) ) {
		$instance->settings = WP_Test_Plugin_Settings::instance( $instance );
	}

	return $instance;
}

wp_test_plugin();
