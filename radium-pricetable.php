<?php

/*
Plugin Name: Price Table
Plugin URI: http://radiumthemes.com/plugins/pricetable-wordpress-plugin
Description: Creates a price table using a drag and drop builder.  
Author: Franklin M Gitonga
Version: 0.1
Author URI: http://radiumthemes.com/
License: GPL
*/

define('PRICETABLE_FEATURED_WEIGHT', 1.175);
define('PRICETABLE_VERSION', '0.1');

/**
 * Activate the pricetable plugin
 */
function radium_pricetable_activate(){
	// Flush rules so we can view price table pages
	flush_rewrite_rules();
	
}
register_activation_hook(__FILE__, 'radium_pricetable_activate');


/**
 * Register the price table post type
 */
function radium_pricetable_register(){
	register_post_type('pricetable',array(
		'labels' => array(
			'name' => __('Price Tables', 'radium'),
			'singular_name' => __('Price Table', 'radium'),
			'add_new' => __('Add New', 'book', 'radium'),
			'add_new_item' => __('Add New Price Table', 'radium'),
			'edit_item' => __('Edit Price Table', 'radium'),
			'new_item' => __('New Price Table', 'radium'),
			'all_items' => __('All Price Tables', 'radium'),
			'view_item' => __('View Price Table', 'radium'),
			'search_items' => __('Search Price Tables', 'radium'),
			'not_found' =>  __('No Price Tables found', 'radium'),
		),
		'public' => true,
		'has_archive' => false,
		'supports' => array( 'title',  'revisions' ),
		'menu_icon' => plugins_url('assets/images/icon.png', __FILE__),
	));
}
add_action( 'init', 'radium_pricetable_register');

/**
 * Add custom columns to pricetable post list in the admin
 * @param $cols
 * @return array
 */
function radium_pricetable_register_custom_columns($cols){
	unset($cols['title']);
	unset($cols['date']);
	
	$cols['title'] = __('Title', 'radium');
	$cols['options'] = __('Options', 'radium');
	$cols['features'] = __('Features', 'radium');
	$cols['featured'] = __('Featured Option', 'radium');
	$cols['date'] = __('Date', 'radium');
	
	return $cols;
	
}
add_filter( 'manage_pricetable_posts_columns', 'radium_pricetable_register_custom_columns');

/**
 * Render the contents of the admin columns
 * @param $column_name
 */
function radium_pricetable_custom_column($column_name){
	global $post;
	
	switch($column_name){
		
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
	}
}
add_action( 'manage_pricetable_posts_custom_column', 'radium_pricetable_custom_column');

/**
 * @return string The URL of the CSS file to use
 */
function radium_pricetable_css_url(){

	// Find the best price table file to use
	if(file_exists(get_stylesheet_directory().'/pricetable/pricetable.css')) {
	
		return get_stylesheet_directory_uri().'/pricetable/pricetable.css';
		
	} elseif(file_exists(get_template_directory().'/pricetable/pricetable.css')) {
	
		return get_template_directory_uri().'/pricetable/pricetable.css';
	
	} else {
	
		return plugins_url( 'assets/css/pricetable.css', __FILE__);
	}	
}

/**
 * Enqueue the pricetable scripts
 */
function radium_pricetable_scripts(){
	global $post, $pricetable_queued, $pricetable_displayed;
	
	if(is_singular() && (($post->post_type == 'pricetable') || ($post->post_type != 'pricetable' && preg_match( '#\[ *price_table([^\]])*\]#i', $post->post_content ))) || !empty($pricetable_displayed)){
		
		wp_enqueue_style('pricetable',  radium_pricetable_css_url(), null, PRICETABLE_VERSION);
		$pricetable_queued = true;
	
	}
}
add_action('wp_enqueue_scripts', 'radium_pricetable_scripts');

/**
 * Add administration scripts
 * @param $page
 */
function radium_pricetable_admin_scripts($page){
	if($page == 'post-new.php' || $page == 'post.php'){
		global $post;
		
		if(!empty($post) && $post->post_type == 'pricetable'){
		
			// Scripts for building the pricetable
			wp_enqueue_script('placeholder', plugins_url( 'assets/js/placeholder.jquery.js', __FILE__), array('jquery'), '1.1.1', true);
			wp_enqueue_script('jquery-ui');
			wp_enqueue_script('pricetable-admin', plugins_url( 'assets/js/pricetable.build.js', __FILE__), array('jquery'), PRICETABLE_VERSION, true);
			
			wp_localize_script('pricetable-admin', 'pt_messages', array(
				'delete_column' => __('Are you sure you want to delete this column?', 'radium'),
				'delete_feature' => __('Are you sure you want to delete this feature?', 'radium'),
			));
			
			wp_enqueue_style('pricetable-admin',  plugins_url( 'assets/css/pricetable.admin.css', __FILE__), array(), PRICETABLE_VERSION);
			wp_enqueue_style('pricetable-icon',  plugins_url( 'assets/css/pricetable.icon.css', __FILE__), array(), PRICETABLE_VERSION);
			wp_enqueue_style('jquery-ui', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.7.0/themes/base/jquery-ui.css', array(), '1.7.0');
		
		}
	}
	
	// The light weight CSS for changing the icon
	if(@$_GET['post_type'] == 'radium'){
	
		wp_enqueue_style('pricetable-icon',  plugins_url( 'assets/css/pricetable.icon.css', __FILE__), array(), PRICETABLE_VERSION);
	
	}
	
}
add_action('admin_enqueue_scripts', 'radium_pricetable_admin_scripts');

/**
 * Metaboxes because we're boss
 * 
 * @action add_meta_boxes
 */
function radium_pricetable_meta_boxes(){
	add_meta_box('pricetable', __('Price Table', 'radium'), 'radium_pricetable_render_metabox', 'pricetable', 'normal', 'high');
	add_meta_box('pricetable-shortcode', __('Shortcode', 'radium'), 'radium_pricetable_render_metabox_shortcode', 'pricetable', 'side', 'low');
}
add_action( 'add_meta_boxes', 'radium_pricetable_meta_boxes' );

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
	
	include(dirname(__FILE__).'/tpl/pricetable.build.php');
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
add_action( 'save_post', 'radium_pricetable_save' );

/**
 * The price table shortcode.
 * @param array $atts
 * @return string
 * 
 * 
 */
function radium_pricetable_shortcode($atts = array()) {
	global $post, $pricetable_displayed;
	
	$pricetable_displayed = true;
	
	extract( shortcode_atts( array(
		'id' => null,
		'width' => 100,
	), $atts ) );
	
	if($id == null) $id = $post->ID;
	
	$table = get_post_meta($id , 'price_table', true);
	if(empty($table)) $table = array();
	
	// Set all the classes
	$featured_index = null;
	
	foreach($table as $i => $column) {
	
		$table[$i]['classes'] = array('pricetable-column');
		$table[$i]['classes'][] = (@$table[$i]['featured'] === 'true') ? 'pricetable-featured' : 'pricetable-standard';
		
		if(@$table[$i]['featured'] == 'true') $featured_index = $i;
		if(@$table[$i+1]['featured'] == 'true') $table[$i]['classes'][] = 'pricetable-before-featured';
		if(@$table[$i-1]['featured'] == 'true') $table[$i]['classes'][] = 'pricetable-after-featured';
	
	}
	
	$table[0]['classes'][] = 'first-column';
	$table[count($table)-1]['classes'][] = 'last-column';
	
	// Calculate the widths
	$width_total = 0;
	
	foreach($table as $i => $column){
	
		if(@$column['featured'] === 'true') {
			
			$width_total += PRICETABLE_FEATURED_WEIGHT;
	
		} else {
	
			$width_total++;
	
		}
		
	}
	
	$width_sum = 0;
	
	foreach($table as $i => $column){
	
		if(@$column['featured'] === 'true'){
			// The featured column takes any width left over after assigning to the normal columns
	
			$table[$i]['width'] = 100 - (floor(100/$width_total) * ($width_total-PRICETABLE_FEATURED_WEIGHT));
	
		} else {
			
			$table[$i]['width'] = floor(100/$width_total);
		
		}
		
		$width_sum += $table[$i]['width'];
	
	}
	
	// Create fillers
	if(!empty($table[0]['features'])){
	
		for($i = 0; $i < count($table[0]['features']); $i++){
	
			$has_title = false;
			$has_sub = false;
			
			foreach($table as $column){
	
				$has_title = ($has_title || !empty($column['features'][$i]['title']));
				$has_sub = ($has_sub || !empty($column['features'][$i]['sub']));
	
			}
			
			foreach($table as $j => $column){
	
				if($has_title && empty($table[$j]['features'][$i]['title'])) $table[$j]['features'][$i]['title'] = '&nbsp;';
				if($has_sub && empty($table[$j]['features'][$i]['sub'])) $table[$j]['features'][$i]['sub'] = '&nbsp;';
	
			}
		}
	}
	
	// Find the best pricetable file to use
	if(file_exists(get_stylesheet_directory().'/pricetable.php')) {
		
		$template = get_stylesheet_directory().'/pricetable.php';
		
	} elseif (file_exists(get_template_directory().'/pricetable.php')) {
		
		$template = get_template_directory().'/pricetable.php'; 
	
	} else {
		
		$template = dirname(__FILE__).'/tpl/pricetable.php';
	}	
	
	// Render the pricetable
	ob_start();
	include($template);
	$pricetable = ob_get_clean();
	
	if($width != 100) $pricetable = '<div style="width:'.$width.'%; margin: 0 auto;">'.$pricetable.'</div>';
	
	$post->pricetable_inserted = true;
	
	return $pricetable;

}
add_shortcode( 'price_table', 'radium_pricetable_shortcode' );

/**
 * Add the pricetable to the content.
 * 
 * @param $the_content
 * @return string
 * 
 * @filter the_content
 */
function radium_pricetable_the_content_filter($the_content){
	global $post;
	
	if(is_single() && $post->post_type == 'pricetable' && empty($post->pricetable_inserted)){
	
		$the_content = radium_pricetable_shortcode().$the_content;
	
	}
	return $the_content;
}
// Filter the content after WordPress has had a chance to do shortcodes (priority 10)
add_filter('the_content', 'radium_pricetable_the_content_filter',11);

/**
 * @action wp_footer
 */
function radium_pricetable_footer(){
	global $pricetable_queued, $pricetable_displayed;
	
	if(!empty($pricetable_displayed) && empty($pricetable_queued)){
	
		$pricetable_queued = true;
		// The pricetable has been rendered, but its CSS not enqueued (happened with some themes)
		?><link rel="stylesheet" type="text/css" href="<?php print radium_pricetable_css_url() ?>" /><?php
	
	}
}
add_action('wp_footer', 'radium_pricetable_footer');


/**
 * Add a price_table button to the post composition screen
 * uses the button added to the media buttons above TinyMCE.
 *
 * @since 2.0.0
 *
 * @global string $pagenow The current page slug
 */
add_filter( 'media_buttons_context', 'radium_price_table_media_button');
function radium_price_table_media_button($context) {

	global $pagenow;
	
	$output = '';

	/** Only run in post/page creation and edit screens */
	if ( in_array( $pagenow, array( 'post.php', 'page.php', 'post-new.php', 'post-edit.php' ) ) ) {
		
		$title = esc_attr( __( 'Add a pricetable', 'radium' ) );
 		$img 	= '<img src="' . plugins_url('assets/images/icon.png', __FILE__) .'" alt="' . $title . '" width="13" height="12" />';
 		$output = '<a href="#TB_inline?width=640&inlineId=choose-radium-pricetable" class="thickbox" title="' . $title . '">' . $img . '</a>';
	}

	return $context . $output;

}


/**
 * Outputs the jQuery and HTML necessary to insert a price_table when the user
 * uses the button added to the media buttons above TinyMCE.
 *
 * @since 2.0.0
 *
 * @global string $pagenow The current page slug
 */
add_action( 'admin_footer', 'radium_price_table_admin_footer');
 
function radium_price_table_admin_footer() {

	global $pagenow;
		
	/** Only run in post/page creation and edit screens */
	if ( in_array( $pagenow, array( 'post.php', 'page.php', 'post-new.php', 'post-edit.php' ) ) ) {
		/** Get all published price_tables */
		$price_tables = get_posts( array( 'post_type' => 'pricetable', 'posts_per_page' => -1, 'post_status' => 'publish' ) );
		
		?>
		<script type="text/javascript">
			function insertprice_table() {
				var id = jQuery('#select-radium-pricetable').val();

				/** Return early if no price_table is selected */
				if ( '' == id ) {
					alert('<?php echo esc_js( 'Please select a pricetable.' ); ?>');
					return;
				}

				/** Send the shortcode to the editor */
				window.send_to_editor('[price_table id="' + id + '"]');
			}
		</script>

		<div id="choose-radium-pricetable" style="display: none;">
		
			<div class="wrap" style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;">
			
				<div id="icon-radium" class="icon32" style="background: url(<?php echo plugins_url('assets/images/icon.png', __FILE__); ?>) no-repeat scroll 0 50%; width: 16px;"><br></div>
				<h2><?php _e('Choose Your Price Table', 'radium'); ?></h2>
				
				<?php do_action( 'radium_before_price_table_insertion', $price_tables ); ?>
				
				<p style="font-weight: bold; padding-bottom: 10px;">
					<?php _e('Select a pricetable below from the list of available pricetables and then click \'Insert\' to place the pricetable into the editor.', 'radium'); ?>
				</p>
				<select id="select-radium-pricetable" style="clear: both; display: block; margin-bottom: 1em;">
					<?php
						foreach ( $price_tables as $price_table )
							echo '<option value="' . $price_table->ID . '">' . esc_attr( $price_table->post_title ) . '</option>';
					?>
				</select>
				<input type="button" id="radium-insert-price_table" class="button-primary" value="<?php echo esc_attr( 'Insert pricetable' ); ?>" onclick="insertprice_table();" />
				<a id="radium-cancel-price_table" class="button-secondary" onclick="tb_remove();" title="<?php echo esc_attr( 'Cancel pricetable Insertion' ); ?>"><?php _e('Cancel pricetable Insertion' , 'radium'); ?></a>
				
				<?php do_action( 'radium_after_price_table_insertion', $price_tables ); ?>
			
			</div>
			
		</div>
		
		<?php
	}

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
add_action( 'add_meta_boxes', 'radium_remove_price_table_seo_support', 99 );
 
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

