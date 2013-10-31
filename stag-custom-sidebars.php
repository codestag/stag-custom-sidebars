<?php
/**
 * Plugin Name: Stag Custom Sidebars
 * Plugin URI: http://wordpress.org/plugins/stag-custom-sidebars
 * Description: Create custom dynamic sidebars and use anywhere with shortcodes.
 * Version: 1.0.1
 * Author: Ram Ratan Maurya
 * Author URI: http://mauryaratan.me
 * Requires at least: 3.3
 * Tested up to: 3.6.1
 * License: GPLv2 or later
 *
 * Text Domain: stag
 * Domain Path: /languages/
 *
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Plugin class for Stag Custom Sidebars.
 *
 * @package Stag_Custom_Sidebars
 * @author Ram Ratan Maurya
 * @version 1.0.1
 * @copyright 2013 Ram Ratan Maurya
 */
class Stag_Custom_Sidebars {

	/**
	 * @var string
	 */
	public $version = '1.0.1';

	/**
	 * @var string
	 */
	public $plugin_url;

	/**
	 * @var string
	 */
	public $stored;

	/**
	 * @var array
	 */
	public $sidebars = array();

	/**
	 * @access protected
	 * @var string
	 */
	protected $title;

	/**
	 * Plugin Constructor.
	 *
	 * @access public
	 * @return void
	 */
	function __construct() {

		$this->title  = __( 'Custom Widget Area', 'stag' );
		$this->stored = 'stag_custom_sidebars';

		// Load plugin text domain
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );
		
		add_action( 'admin_footer', array( $this, 'template_custom_widget_area' ), 200 );
		add_action( 'load-widgets.php', array( $this, 'load_scripts_styles' ) , 5 );

		add_action( 'widgets_init', array( $this, 'register_custom_sidebars') );
		add_action( 'wp_ajax_stag_ajax_delete_custom_sidebar', array( $this, 'delete_sidebar_area' ) , 1000 );

		add_shortcode( 'stag_sidebar', array( $this, 'stag_sidebar_shortcode' ) );
	}

	/**
	 * Internationalization.
	 * 
	 * @return void
	 */
	function load_plugin_textdomain () {
		load_plugin_textdomain ( 'stag', FALSE, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * Get the plugin url.
	 *
	 * @access public
	 * @return string
	 */
	public function plugin_url() {
		if ( $this->plugin_url ) return $this->plugin_url;
		return $this->plugin_url = untrailingslashit( plugins_url( '/', __FILE__ ) );
	}

	/**
	 * Register/queue scripts.
	 *
	 * @access public
	 * @return void
	 */
	public function load_scripts_styles() {

		add_action( 'load-widgets.php', array( $this, 'add_sidebar_area'), 100 );

		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'stag-custom-sidebars', $this->plugin_url() . '/assets/js/stag-custom-sidebars.js', array('jquery'), $this->version, true );

		wp_localize_script( 'stag-custom-sidebars', 'objectL10n', array(
			'shortcode' => __( 'Shortcode', 'stag' )
		) );

		wp_enqueue_style( 'stag-custom-sidebars', $this->plugin_url() .  '/assets/css/stag-custom-sidebars.css', '', $this->version, 'screen' );
	}

	/**
	 * Template for displaying the custom widget area add interface.
	 *
	 * @return Output custom widget area field
	 */
	public function template_custom_widget_area() {
		?>
		<script type="text/html" id="tmpl-stag-add-widget">
			<div class="stag-widgets-holder-wrap">
				<div class="sidebar-name">
					<h3><?php echo $this->title ?></h3>
				</div>

				<form class="stag-add-widget" method="post">
					<input type="text" name="stag-add-widget" value="" placeholder="<?php _e( 'Enter name of the new widget area here', 'stag' ); ?>" required />
					<?php submit_button( __( 'Add Widget Area', 'stag' ), 'secondary large', $name = 'stag-custom-sidebar-submit' ); ?>
					<input type='hidden' name='scs-delete-nonce' value="<?php echo wp_create_nonce( 'scs-delete-nonce' ) ?>">
				</form>
			</div>
		</script>
		<?php
	}

	/**
	 * Add Sidebar area.
	 *
	 * @return void
	 */
	public function add_sidebar_area() {
		if ( !empty( $_POST['stag-add-widget'] ) ) {
			$this->sidebars = get_option($this->stored);
			$name           = $this->get_name( $_POST['stag-add-widget'] );
			$this->sidebars = array_merge( $this->sidebars, array($name) );

			update_option( $this->stored, $this->sidebars );
			wp_redirect( admin_url('widgets.php') );
			die();
		}
	}

	/**
	 * Delete Sidebar area.
	 *
	 * @return void
	 */
	public function delete_sidebar_area() {
		check_ajax_referer('scs-delete-nonce');

		if ( ! empty( $_POST['name'] ) ) {
			$name           = stripslashes($_POST['name']);
			$this->sidebars = get_option($this->stored);

			if ( ($key = array_search( $name, $this->sidebars ) ) !== false) {
				unset( $this->sidebars[$key] );
				update_option( $this->stored, $this->sidebars );
				echo "sidebar-deleted";
			}
		}
		die();
	}

	/**
	 * Check user entered widget area name and manage conflicts.
	 * 
	 * @param string $name User entered name
	 * @return string Processed name
	 */
	public function get_name( $name ) {
        if( empty( $GLOBALS['wp_registered_sidebars'] ) )
        	return $name;

        $taken = array();

        foreach ( $GLOBALS['wp_registered_sidebars'] as $sidebar ) {
        	$taken[] = $sidebar['name'];
        }

        if ( empty($this->sidebars) ) $this->sidebars = array();

        $taken = array_merge( $taken, $this->sidebars );

        if ( in_array($name, $taken) ) {
        	$counter  = substr($name, -1);  
			$new_name = "";

			if ( ! is_numeric($counter) ) {
				$new_name = $name . " 1";
			} else {
				$new_name = substr($name, 0, -1) . ((int) $counter + 1);
			}

			$name = $this->get_name($new_name);
        }

        return $name;
	}

	/**
	 * Register sidebars.
	 *
	 * @access public
	 * @return void
	 */
	public function register_custom_sidebars() {

		if( empty( $this->sidebars ) ) $this->sidebars = get_option($this->stored);

		$args = apply_filters( 'stag_custom_sidebars_widget_args', array(
				'before_widget' => '<aside id="%1$s" class="widget %2$s">',
				'after_widget'  => '</aside>',
				'before_title'  => '<h3 class="widgettitle">', 
				'after_title'   => '</h3>'
			)
		);

		if( is_array( $this->sidebars ) ) {
			foreach( $this->sidebars as $sidebar ) {
				$args['name']  = $sidebar;
				$args['class'] = 'stag-custom';
				$args['id']    = sanitize_html_class( sanitize_title_with_dashes( $sidebar ) );
				
				register_sidebar($args);
			}
		}
	}

	/**
	 * Shortcode handler.
	 * 
	 * @param  array $atts Array of attributes
	 * @return string $output returns the modified html string
	 */
	public function stag_sidebar_shortcode( $atts ) {
		extract( shortcode_atts( array(
			'id' => '1',
			'class' => ''
		), $atts ) );

		$output = '';

		if( is_active_sidebar( $id ) ) {
			ob_start();

			do_action( 'stag_custom_sidebars_before', $id );

			echo "<section id='{$id}' class='stag-custom-widget-area {$class}'>";
			dynamic_sidebar( $id );
			echo "</section>";
			
			do_action( 'stag_custom_sidebars_after' );
			
			$output = ob_get_clean();
		}

		return $output;
	}
}

new Stag_Custom_Sidebars();
