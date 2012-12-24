<?php

/**
 * Posttype class for Radium_PriceTables.
 *
 * @since 1.0.0
 *
 * @package	Radium_PriceTables
 * @author	Franklin M Gitonga
 */

class Radium_PriceTables_Posttype {

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
	public function __construct(){
		
		self::$instance = $this;
		
		$labels = apply_filters ('radium_pricetables_post_type_labels', array(
			'name' 			=> __('Price Tables', 'radium'),
			'singular_name' => __('Price Table', 'radium'),
			'add_new' 		=> __('Add New', 'book', 'radium'),
			'add_new_item' 	=> __('Add New Price Table', 'radium'),
			'edit_item' 	=> __('Edit Price Table', 'radium'),
			'new_item' 		=> __('New Price Table', 'radium'),
			'all_items' 	=> __('All Price Tables', 'radium'),
			'view_item' 	=> __('View Price Table', 'radium'),
			'search_items' 	=> __('Search Price Tables', 'radium'),
			'not_found' 	=> __('No Price Tables found', 'radium'),
		));
		
		$args = apply_filters( 'radium_pricetables_post_type_args', array(
			'labels' 		=> $labels,
			'public' 		=> true,
			'has_archive' 	=> false,
			'supports' 		=> array( 'title',  'revisions' ),
			'menu_icon' 	=> plugins_url('assets/admin/images/icon.png', dirname(dirname(__FILE__)) ),
		));
		
		/** Register post type with args */
		register_post_type('pricetable', $args);
		
		/** Register Hooks */
		add_filter( 'manage_pricetable_posts_columns', array( $this, 'register_custom_columns') );
		add_action( 'manage_pricetable_posts_custom_column', array( $this, 'custom_column') );
	
	}
	
	/**
	 * Add custom columns to pricetable post list in the admin
	 * @param $cols
	 * @return array
	 */
	public function register_custom_columns($cols){
	
		unset($cols['title']);
		unset($cols['date']);
		
		$cols['title'] 		= __('Title', 'radium');
		$cols['options'] 	= __('Options', 'radium');
		$cols['features'] 	= __('Features', 'radium');
		$cols['featured'] 	= __('Featured Option', 'radium');
		$cols['date'] 		= __('Date', 'radium');
		
		return $cols;
		
	}
	
	/**
	 * Render the contents of the admin columns
	 * @param $column_name
	 * @global $post
	 */
	public function custom_column($column_name){
	
		global $post;
		
		switch($column_name) {
			
			case 'options' :
			
				$table = get_post_meta($post->ID, 'price_table', true);
				print count($table);
				break;
				
			case 'features' :
			
				case 'featured' :
				
					$table = get_post_meta($post->ID, 'price_table', true);
					
					foreach($table as $col){
					
						if(!empty($col['featured']) && $col['featured'] == 'true') {
						
							if($column_name == 'featured') { 
								
								print $col['title'];
								
							} else {
							
								print count($col['features']);
								
							}
							
							break;
							
						}
					}
					
				break;
		} //switch
		
	}

}
