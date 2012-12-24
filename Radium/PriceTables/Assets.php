<?php
/**
 * Assets class for Radium_PriceTables.
 *
 *
 * @since 1.0.0
 *
 * @package	Radium_PriceTables
 * @author	Franklin M Gitonga
 */
class Radium_PriceTables_Assets {

	/**
	 * Holds a copy of the object for easy reference.
	 *
	 * @since 1.0.0
	 *
	 * @var object
	 */
	private static $instance;

	/**
	 * Constructor. Hooks all interactions to initialize the class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
	
		self::$instance = $this;
		
 		/** Register scripts and styles */
 		add_action('admin_enqueue_scripts', array( $this, 'admin_scripts'));
 		
		add_action('wp_enqueue_scripts', 	array( $this, 'frontend_scripts'));
		add_action('wp_footer', 			array( $this, 'frontend_footer_scripts'));
	
	}
	
	/**
	 * @return string The URL of the CSS file to use
	 */
	public function radium_pricetable_css_url(){
	
		// Find the best price table file to use
		if(file_exists(get_stylesheet_directory().'/pricetable/pricetable.css')) {
		
			return get_stylesheet_directory_uri().'/pricetable/pricetable.css';
			
		} elseif(file_exists(get_template_directory().'/pricetable/pricetable.css')) {
		
			return get_template_directory_uri().'/pricetable/pricetable.css';
		
		} else {
		
			return plugins_url( 'assets/frontend/css/pricetable.css', dirname(dirname(__FILE__)) );
		}	
	}
	
	/**
	 * Enqueue the pricetable scripts
	 */
	public function frontend_scripts(){
		global $post, $pricetable_queued, $pricetable_displayed;
		
		if(is_singular() && (($post->post_type == 'pricetable') || ($post->post_type != 'pricetable' && preg_match( '#\[ *price_table([^\]])*\]#i', $post->post_content ))) || !empty($pricetable_displayed)){
			
			wp_enqueue_style('pricetable',  $this->radium_pricetable_css_url(), null, '1.0.0');
			$pricetable_queued = true;
		
		}
	}
	
	/**
	 * Add administration scripts
	 * @param $page
	 */
	public function admin_scripts($page){
		if($page == 'post-new.php' || $page == 'post.php'){
			global $post;
			
			if(!empty($post) && $post->post_type == 'pricetable'){
			
				// Scripts for building the pricetable
				wp_enqueue_script('jquery-ui');
				wp_enqueue_script('placeholder', plugins_url( 'assets/admin/js/placeholder.jquery.js', dirname(dirname(__FILE__))), array('jquery'), '1.1.1', true);
				wp_enqueue_script('pricetable-admin', plugins_url( 'assets/admin/js/pricetable.build.js', dirname(dirname(__FILE__))), array('jquery'), '1.0.0', true);
				
				wp_localize_script('pricetable-admin', 'pt_messages', array(
					'delete_column' => __('Are you sure you want to delete this column?', 'radium'),
					'delete_feature' => __('Are you sure you want to delete this feature?', 'radium'),
				));
				
				wp_enqueue_style('pricetable-admin',  plugins_url( 'assets/admin/css/pricetable.admin.css', dirname(dirname(__FILE__))), array(), '1.0.0');
				wp_enqueue_style('pricetable-icon',  plugins_url( 'assets/admin/css/pricetable.icon.css', dirname(dirname(__FILE__))), array(), '1.0.0');
				wp_enqueue_style('jquery-ui', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.0/themes/base/jquery-ui.css', array(), '1.7.0');
			
			}
		}
		
		// The light weight CSS for changing the icon
		if(@$_GET['post_type'] == 'radium'){
		
			wp_enqueue_style('pricetable-icon',  plugins_url( 'assets/admin/css/pricetable.icon.css', dirname(dirname(__FILE__))), array(), '1.0.0');
		
		}
		
	}
	
	/**
	 * @action wp_footer
	 */
	public function frontend_footer_scripts(){
		global $pricetable_queued, $pricetable_displayed;
		
		if(!empty($pricetable_displayed) && empty($pricetable_queued)){
		
			$pricetable_queued = true;
			// The pricetable has been rendered, but its CSS not enqueued (happened with some themes)
			?><link rel="stylesheet" type="text/css" href="<?php print $this->radium_pricetable_css_url() ?>" /><?php
		
		}
	}
	
	/**
	 * Getter method for retrieving the object instance.
	 *
	 * @since 1.0.0
	 */
	public static function get_instance() {
	
		return self::$instance;
	
	}
}