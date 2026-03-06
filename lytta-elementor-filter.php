<?php
/**
 * Plugin Name: Lytta Elementor Advanced Filter
 * Description: Un plugin integrato con Elementor per filtrare Custom Post Types (CPT), Taxonomies e campi ACF in tempo reale con AJAX.
 * Plugin URI: https://lytta.it
 * Author: Lytta
 * Version: 1.0.0
 * Author URI: https://lytta.it
 * Text Domain: lytta-filter
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'LYTTA_FILTER_VERSION', '1.0.0' );
define( 'LYTTA_FILTER_DIR', plugin_dir_path( __FILE__ ) );
define( 'LYTTA_FILTER_URL', plugin_dir_url( __FILE__ ) );

/**
 * Notice if Elementor is not active
 */
function lytta_filter_missing_elementor_notice() {
	if ( isset( $_GET['activate'] ) ) {
		unset( $_GET['activate'] );
	}

	$message = sprintf(
		/* translators: 1: Plugin name 2: Elementor */
		esc_html__( '"%1$s" requires "%2$s" to be installed and activated.', 'lytta-filter' ),
		'<strong>' . esc_html__( 'Lytta Elementor Advanced Filter', 'lytta-filter' ) . '</strong>',
		'<strong>' . esc_html__( 'Elementor', 'lytta-filter' ) . '</strong>'
	);

	printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );
}

/**
 * Initialize the plugin
 */
function lytta_filter_init() {
	// Check if Elementor is active
	if ( ! did_action( 'elementor/loaded' ) ) {
		add_action( 'admin_notices', 'lytta_filter_missing_elementor_notice' );
		return;
	}

	// Require main class
	require_once LYTTA_FILTER_DIR . 'includes/class-lytta-elementor-filter.php';
	
	// Instantiate main class
	\Lytta_Filter\Lytta_Elementor_Filter::instance();
}
add_action( 'plugins_loaded', 'lytta_filter_init' );
