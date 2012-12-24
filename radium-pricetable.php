<?php

/*
Plugin Name: Price Table
Plugin URI: http://radiumthemes.com/plugins/pricetable-wordpress-plugin
Description: Creates a price table using a drag and drop builder.  
Author: Franklin M Gitonga
Version: 1.0.0
Author URI: http://radiumthemes.com/
License: GPL v2+
*/
 
/** Load all of the necessary class files for the plugin */
spl_autoload_register( 'Radium_PriceTables::autoload' );

/**
 * Init class for Radium_PriceTables.
 *
 * Loads all of the necessary components for the radium sliders plugin.
 *
 * @since 1.0.0
 *
 * @package	Radium_PriceTables
 * @author	Franklin Gitonga
 */
class Radium_PriceTables {

	/**
	 * Holds a copy of the object for easy reference.
	 *
	 * @since 1.0.0
	 *
	 * @var object
	 */
	private static $instance;

	/**
	 * Current version of the plugin.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $version = '1.0.0';
	
	/**
	 * Holds a copy of the main plugin filepath.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	private static $file = __FILE__;

	/**
	 * Constructor. Hooks all interactions into correct areas to start
	 * the class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		self::$instance = $this;

		/** Run a hook before the slider is loaded and pass the object */
		do_action_ref_array( 'radium_sliders_init', array( $this ) );

		/** Run activation hook and make sure the WordPress version supports the plugin */
		register_activation_hook( __FILE__, array( $this, 'activation' ) );
		
		/** Load the plugin */
		add_action( 'widgets_init', array( $this, 'widget' ) );
		add_action( 'init', array( $this, 'init' ) );
				
	}
	
	/**
 	 * Registers a plugin activation hook to make sure the current WordPress
 	 * version is suitable (>= 3.3.1) for use.
 	 *
 	 * @since 1.0.0
 	 *
 	 * @global int $wp_version The current version of this particular WP instance
 	 */
	public function activation() {
	
		global $wp_version;
		
		if ( version_compare( $wp_version, '3.3.1', '<' ) ) {
			deactivate_plugins( plugin_basename( __FILE__ ) );
			wp_die( printf( __( 'Sorry, but your version of WordPress, <strong>%s</strong>, does not meet the Radium Pricetable\'s required version of <strong>3.3.1</strong> to run properly. The plugin has been deactivated. <a href="%s">Click here to return to the Dashboard</a>', 'radium_pricetables' ), $wp_version, admin_url() ) );
		}

	}
	
	/**
 	 * Registers the widget with WordPress.
 	 *
 	 * @since 1.0.0
 	 */
	public function widget() {
	
		//register_widget( 'Radium_PriceTables_Widget' );
	
	}
		
	/**
	 * Loads the plugin upgrader, registers the post type and
	 * loads all the actions and filters for the class.
	 *
	 * @since 1.0.0
	 */
	public function init() {
		
		define('PRICETABLE_FEATURED_WEIGHT', 1.175);
		
		/** Load the plugin textdomain for internationalizing strings */
		load_plugin_textdomain( 'radium_pricetables', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
		
		/** Only process upgrade and addons page if a key has been entered and upgrades are on */
		$args = array(
			'remote_url' 	=> 'http://radiumthemes.com/',
			'version' 		=> $this->version,
			'plugin_name'	=> 'Radium PriceTables',
			'plugin_slug' 	=> 'radium_pricetables',
			'plugin_path' 	=> plugin_basename( __FILE__ ),
			'plugin_url' 	=> WP_PLUGIN_URL . '/radium-pricetables',
			'time' 			=> 43200,
		);
		
		/** Instantiate the automatic plugin upgrade class */
		//$radium_sliders_updater = new Radium_PriceTables_Updater( $args );
		
		/** Load the addons page */
 		//$radium_sliders_addons = new Radium_PriceTables_Addons( );
				
		/** Load the updates page */
		//$radium_sliders_updates = new Radium_PriceTables_Updates;
		
		/** Instantiate all the necessary components of the plugin */
		//$radium_sliders_admin			= new Radium_PriceTables_Admin;
		//$radium_sliders_ajax			= new Radium_PriceTables_Ajax;
		$radium_pricetable_assets		= new Radium_PriceTables_Assets;
		//$radium_sliders_help			= new Radium_PriceTables_Help;
		$radium_pricetable_posttype		= new Radium_PriceTables_Posttype;
		$radium_pricetable_metaboxes	= new Radium_PriceTables_Metaboxes;
		$radium_sliders_shortcode		= new Radium_PriceTables_Shortcodes;
		//$radium_sliders_strings		= new Radium_PriceTables_Strings;

	}
	
	/**
	 * PSR-0 compliant autoloader to load classes as needed.
	 *
	 * @since 1.0.0
	 *
	 * @param string $classname The name of the class
	 * @return null Return early if the class name does not start with the correct prefix
	 */
	public static function autoload( $classname ) {
	
		if ( 'Radium_PriceTables' !== mb_substr( $classname, 0, 18 ) )
			return;
			
		$filename = dirname( __FILE__ ) . DIRECTORY_SEPARATOR . str_replace( '_', DIRECTORY_SEPARATOR, $classname ) . '.php';
		if ( file_exists( $filename ) )
			require $filename;
	}
	
	/**
	 * Getter method for retrieving the object instance.
	 *
	 * @since 1.0.0
	 */
	public static function get_instance() {
	
		return self::$instance;
	
	}
	
	/**
	 * Getter method for retrieving the main plugin filepath.
	 *
	 * @since 1.0.0
	 */
	public static function get_file() {
	
		return self::$file;
	
	}
	
	/**
	 * Getter method for retrieving all pricetables.
	 *
	 * @since 1.3.0
	 */
	public static function get_pricetables() {
	
		$args = array(
			'post_type' 		=> 'pricetable',
			'posts_per_page' 	=> -1
		);
		
		return get_posts( $args );
	
	}
	
	/**
	 * Helper flag method for any pricetable screen.
	 *
	 * @since 1.0.0
	 *
	 * @return bool True if on a pricetable screen, false if not
	 */
	public static function is_pricetable_screen() {
	
		$current_screen = get_current_screen();
		
		if ( 'pricetable' == $current_screen->post_type )
			return true;
			
		return false;
	
	}
	
	/**
	 * Helper flag method for the Add/Edit pricetable screens.
	 *
	 * @since 1.0.0
	 *
	 * @return bool True if on a pricetable Add/Edit screen, false if not
	 */
	public static function is_pricetable_add_edit_screen() {
	
		$current_screen = get_current_screen();
		
		if ( 'pricetable' == $current_screen->post_type && 'post' == $current_screen->base )
			return true;
			
		return false;
	
	}

}

/** Instantiate the init class */
new Radium_PriceTables;

if ( ! function_exists( 'radium_pricetable' ) ) {
	/**
	 * Template tag function for outputting the slider within templates.
	 *
	 * @since 1.0.0
	 *
	 * @package radium_sliders
	 * @param int|string $id The slider ID or unique slug
	 * @param bool $return Flag for returning or echoing the slider content
	 */
	function radium_pricetable( $id, $return = false ) {

		/** Check if slider ID is an integer or string */
		if ( is_numeric( $id ) )
			$id = absint( $id );
		else
			$id = esc_attr( $id );

		/** Return if no slider ID has been entered or if it is not valid */
		if ( ! $id || empty( $id ) ) {
			printf( '<p>%s</p>', Radium_PriceTables_Strings::get_instance()->strings['no_id'] );
			return;
		}

		/** Validate based on type of ID submitted */
		if ( is_numeric( $id ) ) {
			$validate = get_post( $id, OBJECT );
			if ( ! $validate || isset( $validate->post_type ) && 'pricetable' !== $validate->post_type ) {
				printf( '<p>%s</p>', Radium_PriceTables_Strings::get_instance()->strings['invalid_id'] );
				return;
			}
		} else {
			$validate = get_page_by_path( $id, OBJECT, 'pricetable' );
			if ( ! $validate ) {
				printf( '<p>%s</p>', Radium_PriceTables_Strings::get_instance()->strings['invalid_slug'] );
				return;
			}
		}

		if ( $return )
			return do_shortcode( '[pricetable id="' . $id . '"]' );
		else
			echo do_shortcode( '[pricetable id="' . $id . '"]' );

	}
}