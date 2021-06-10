<?php
/*
Plugin Name: Tutor LMS Divi Modules
Plugin URI:  https://www.themeum.com/product/tutor-lms/
Description: Divi Modules Integration - Tutor LMS plugin let's you to design your courses, lesson page by Divi Builder.
Version: 1.0.0
Author: Themeum
Author URI: https://themeum.com
Requires at least: 4.5
Tested up to: 5.7
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html
Text Domain: tutor-lms-divi-modules
Domain Path: /languages
*/

defined('ABSPATH') || die();

define('DTLMS_VERSION', '1.0.0');
define('DTLMS_FILE__', __FILE__);
define('DTLMS_DIR_PATH', plugin_dir_path(DTLMS_FILE__));
define('DTLMS_DIR_URL', plugin_dir_url(DTLMS_FILE__));
define('DTLMS_ASSETS', trailingslashit(DTLMS_DIR_URL . 'assets'));
/**
 * Environment
 * PROD 
 * DEV
 */
define('DTLMS_ENV', 'PROD');

if ( ! function_exists( 'tudm_initialize_extension' ) ):
/**
 * Creates the extension's main class instance.
 *
 * @since 1.0.0
 */
function tudm_initialize_extension() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/TutorDiviModules.php';
}

/**
 * Load plugin text domain
 * @since 1.0.0
 */
add_action( 'init', 'tutor_divi_textdomain' );
if( !function_exists( 'tutor_divi_textdomain' )) {
	function tutor_divi_textdomain() {
		load_plugin_textdomain( 'tutor-lms-divi-modules' , false, dirname( plugin_basename( __FILE__) .'/languages' ) );		
	}
}

add_action( 'divi_extensions_init', 'tudm_initialize_extension' );
endif;

