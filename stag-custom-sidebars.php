<?php
/**
 * Plugin Name: Stag Custom Sidebars
 * Plugin URI: http://wordpress.org/plugins/stag-custom-sidebars
 * Description: Create custom dynamic sidebars and use anywhere with shortcodes.
 * Version: 0.1.0
 * Author: Ram Ratan Maurya
 * Author URI: http://mauryaratan.me
 * Requires at least: 3.1
 * Tested up to: 3.6.1
 * License: GPLv2 or later
 *
 * Text Domain: stag
 * Domain Path: /languages/
 *
 * @package Stag_Custom_Sidebars
 * @category Core
 * @author Ram Ratan Maurya
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Plugin class for Stag Custom Sidebars.
 *
 * @package Stag_Custom_Sidebars
 * @author Ram Ratan Maurya
 * @version 0.1.0
 * @copyright 2013 Ram Ratan Maurya
 */
class Stag_Custom_Sidebars {

	/**
	 * @var string
	 */
	public $version = '0.1.0';

	/**
	 * Plugin Constructor.
	 *
	 * @access public
	 * @return void
	 */
	function __construct() {

		// Load plugin text domain
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );
	}

	// Internationalization
	function load_plugin_textdomain () {
		load_plugin_textdomain ( 'stag', FALSE, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}
}

new Stag_Custom_Sidebars();
