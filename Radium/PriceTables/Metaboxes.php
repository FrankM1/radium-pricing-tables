<?php
/**
 * Admin class for Radium Pricetables.
 *
 * @since 1.0.0
 *
 * @package	Radium_Sliders
 * @author	Franklin M Gitonga
 */
 
class Radium_PriceTables_Metaboxes {
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
		
		//add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
		add_action( 'add_meta_boxes',  array( $this, 'radium_remove_price_table_seo_support'), 99 );
		add_action( 'save_post', array( $this,  'radium_pricetable_save') );
		
	}
		
	/**
	 * Metaboxes because we're boss
	 * 
	 * @action add_meta_boxes
	 */
	function add_meta_boxes(){
		add_meta_box('pricetable', 				__('Price Table', 'radium'), 	array( $this, 'radium_pricetable_render_metabox'), 			 'pricetable', 'normal', 'high');
		add_meta_box('pricetable-shortcode',	__('Shortcode', 'radium'),		array( $this, 'radium_pricetable_render_metabox_shortcode'), 'pricetable', 'side',   'low');
	}
 	
 	
	/**
	 * Render the price table building interface
	 * 
	 * @param $post
	 * @param $metabox
	 */
	function radium_pricetable_render_metabox($post, $metabox){
		wp_nonce_field( plugin_basename( __FILE__ ), 'radium_pricetable_nonce' );
		
		$table = get_post_meta($post->ID, 'price_table', true);
		if(empty($table)) $table = array();
				
		echo $this->html( $table );	
	}
	
	
	/**
	 * Render the shortcode metabox
	 * @param $post
	 * @param $metabox
	 */
	function radium_pricetable_render_metabox_shortcode($post, $metabox){
		?>
			<code>[price_table id=<?php print $post->ID ?>]</code>
			<small class="description"><?php _e('Displays price table on another page.', 'radium') ?></small>
		<?php
	}
	
	
	/**
	 * Create Price Table Builder HTML
	 *
	 * @param string $html
	 * @param array $table
	 *
	 * @return string
	 */
	public function html( $table ) { 
		
		$html = null;
		
		$html .= '<div class="price-columns">';
		$html .= '<div id="column-skeleton" style="display:none">';
				
		$html .= '<span class="ui-icon ui-icon-carat-2-e-w column-handle"></span>';
		$html .= '<a href="#" class="ui-icon ui-icon-trash submitdelete deletion"></a>';
				
		$html .= '<div class="type">';
		$html .= '<input type="radio" value="recommend" name="price_recommend" />';
		$html .= '<label>'. __('Recommend', 'radium') .'</label>';
		$html .= '</div>';
				
		$html .= '<input type="text" class="column-title" name="" placeholder="'. __('Title', 'radium') .'" />';
		$html .= '<input type="text" class="column-price" name="" placeholder="'. __('Price', 'radium') .'" />';
		$html .= '<input type="text" class="column-detail" name="" placeholder="'. __('Detail', 'radium') .'" />';
		$html .= '<input type="text" class="column-url" name="" placeholder="'. __('Button URL', 'radium') .'" />';
		$html .= '<input type="text" class="column-button" name="" placeholder="'. __('Button Text', 'radium') .'" />';
				
		$html .= '<h4><a href="#" class="addfeature">'. __('Add', 'radium') .'</a>'. __('Features', 'radium') .'</h4>';
		$html .= '<div class="feautres">';
		$html .= '<div class="feature">';
		$html .= '<span class="ui-icon ui-icon-carat-2-n-s feature-handle"></span>';
		$html .= '<a href="#" class="ui-icon ui-icon-trash submitdelete deletion"></a>';
						
		$html .= '<div><input type="text" class="feature-title" placeholder="'. __('Title', 'radium') .'"/></div>';
		$html .= '<div><input type="text" class="feature-sub" placeholder="'. __('Sub title', 'radium') .'" /></div>';
		$html .= '</div>';
		$html .= '</div>';
		$html .= '</div>';
			
		// Existing columns of the price table 
		foreach($table as $i => $column) :
		
			$html .= '<div class="column">';
			$html .= '<a href="#" class="ui-icon ui-icon-squaresmall-close submitdelete deletion"></a>';
			$html .= '<span class="ui-icon ui-icon-transfer-e-w column-handle"></span>';
			$html .= '<div class="type price_recommend">';
			$html .= '<input type="radio" name="price_recommend" id="price_recommend_' . $i . '" value="' . $i . '"';
			if(isset($column["featured"]) && $column["featured"] === "true") 
			$html .= 'checked="true"';
			$html .= '/>';
			$html .= '<label for="price_recommend_' . $i . '">'. __('Recommend', 'radium') .'</label>';
			$html .= '</div>';
			$html .= '<input type="text" class="column-title" name="price_' . $i . '_title" value="'. esc_attr_e($column['title']) .'" placeholder="'. __('Title', 'radium') .'" />';
			$html .= '<input type="text" class="column-price" name="price_' . $i . '_price" value="'. esc_attr_e($column['price']) .'" placeholder="'. __('Price', 'radium') .'" />';
			$html .= '<input type="text" class="column-detail" name="price_' . $i . '_detail" value="'. esc_attr_e($column['detail']) .'" placeholder="'. __('Detail', 'radium') .'" />';
			$html .= '<input type="text" class="column-url" name="price_' . $i . '_url" value="'. esc_attr_e($column['url']) .'" placeholder="'. __('Button URL', 'radium') .'" />';
			$html .= '<input type="text" class="column-button" name="price_' . $i . '_button" value="'. esc_attr_e($column['button']) .'" placeholder="'. __('Button Text', 'radium') .'" />';
			$html .= '<h4><a href="#" class="addfeature">'. __('Add', 'radium') .'</a>'. __('Features', 'radium') .'</h4>';
			$html .= '<div class="feautres">';
			
			if(isset($column['features'])) : 
				foreach($column['features'] as $j => $feature) :
				
					$html .= '<div class="feature">';
					$html .= '<a href="#" class="ui-icon ui-icon-squaresmall-close submitdelete deletion"></a>';
					$html .= '<span class="ui-icon ui-icon-carat-2-n-s feature-handle"></span>';
					$html .= '<div><input type="text" class="feature-title" name="price_' . $i . '_feature_' . $j . '_title" value="'. esc_attr_e($feature['title']) .'" placeholder="'. __('Title', 'radium') .'"/></div>';
					$html .= '<div><input type="text" class="feature-sub" name="price_' . $i . '_feature_' . $j . '_sub" value="'. esc_attr_e($feature['sub']) .'" placeholder="'. __('Sub title', 'radium') .'" /></div>';
					$html .= '</div>';
				
				endforeach; 
			endif;
				
			$html .= '</div>';
			$html .= '</div>';
			
		endforeach;
			
		$html .= '<div class="column addnew">'. __("Add Column", "radium") .'</div>';
		$html .= '<div class="clear"></div>';
		$html .= '</div>';
		
		return $html;
	}

	
	/**
	 * Save the price table
	 * @param $post_id
	 * @return
	 * 
	 * @action save_post
	 */
	function radium_pricetable_save($post_id){
	
		// Authorization, verification this is my vocation 
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
		if ( !wp_verify_nonce( @$_POST['radium_pricetable_nonce'], plugin_basename( __FILE__ ) ) ) return;
		if ( !current_user_can( 'edit_post', $post_id ) ) return;
		
		// Create the price table from the post variables
		$table = array();
		
		foreach($_POST as $name => $val){
		
			if(substr($name,0,6) == 'price_'){
				$parts = explode('_', $name);
				
				$i = intval($parts[1]);
				
				if(@$parts[2] == 'feature'){
				
					// Adding a feature
					$fi = intval($parts[3]);
					$fn = $parts[4];
					
					if(empty($table[$i]['features'])) $table[$i]['features'] = array();
					$table[$i]['features'][$fi][$fn] = $val;
				
				} elseif(isset($parts[2])) {
				
					// Adding a field
					$table[$i][$parts[2]] = $val;
					
				}
			}
			
		}
		
		// Clean up the features
		foreach($table as $i => $col){
			if(empty($col['features'])) continue;
			
			foreach($col['features'] as $fi => $feature){
			
				if(empty($feature['title']) && empty($feature['sub']) && empty($feature['description'])){
					unset($table[$i]['features'][$fi]);
				}
			
			}
			$table[$i]['features'] = array_values($table[$i]['features']);
		}
		
		if(isset($_POST['price_recommend'])){
			$table[intval($_POST['price_recommend'])]['featured'] = 'true';
		}
		
		$table = array_values($table);
		
		update_post_meta($post_id,'price_table', $table);
	}
	
	
	
	/**
	 * There is no need to apply SEO to the price_table post type, so we check to 
	 * see if some popular SEO plugins are installed, and if so, remove the inpost
	 * meta boxes from view.
	 *
	 * This method also has a filter that can be used to remove any unwanted metaboxes
	 * from the Radium price_table screen - radium_remove_price_table_metaboxes.
	 *
	 * @since 2.0.0
	 */
	 
	function radium_remove_price_table_seo_support() {
	
		$plugins = array(
			array( 'WPSEO_Metabox', 'wpseo_meta', 'normal' ),
			array( 'All_in_One_SEO_Pack', 'aiosp', 'advanced' ),
			array( 'Platinum_SEO_Pack', 'postpsp', 'normal' ),
	 		array( 'SEO_Ultimate', 'su_postmeta', 'normal' )
		);
		$plugins = apply_filters( 'radium_remove_price_table_metaboxes', $plugins );
	
		/** Loop through the arrays and remove the metaboxes */
		foreach ( $plugins as $plugin )
			if ( class_exists( $plugin[0] ) )
				remove_meta_box( $plugin[1], convert_to_screen( 'pricetable' ), $plugin[2] );
	
	}
	
}