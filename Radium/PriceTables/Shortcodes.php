<?php 

/**
 * Shortcodes class for Radium_PriceTables.
 *
 * @since 1.0.0
 *
 * @package	Radium_PriceTables
 * @author	Franklin M Gitonga
 */

class Radium_PriceTables_Shortcodes {
	
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
				
		/** Register Hooks */
		add_shortcode( 'price_table', array( $this, 'shortcode'));
		
		// Filter the content after WordPress has had a chance to do shortcodes (priority 10)
		add_filter('the_content', array( $this, 'the_content_filter',11));
	}	
		
	/**
	 * The price table shortcode.
	 *
	 * @since 1.0.0
	 *
	 * @param array $atts
	 * @return string
	 */
	public function shortcode($atts = array()) {
	
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
			
			$template = $this->html( $table );
		}	
		
		if($width != 100) $pricetable = '<div style="width:'.$width.'%; margin: 0 auto;">'.$pricetable.'</div>';
		
		$post->pricetable_inserted = true;
		
		return $pricetable;
	
	}
	
	/**
	 * Create the price stored in the database.
	 *
	 * @since 1.0.0
	 */
	public function html( $table ){
		
		//echo 'style="width:'.print $column['width'].'%'"
		
		$i = 0;
		
		foreach((array) $table as $i => $column) : 
		
			$colClass = 'radium-price-column'; $n = $i + 1;
			// column classes
			$colClass .= ( $n % 2 ) ?  '' : ' even-column';
		
			?>
			
		<div class="radium-price-column <?php print implode(' ', $column['classes']) ?> <?php print $colClass ?>" >
		
			<div class="radium-price-column-inner">
					
				<div class="pricetable-column-inner">
				
					<div class="pricetable-header">
						<h3 class="column-title"><?php print $column['title'] ?></h3>
						<div class="price-info">
							<div class="cost"><?php print $column['price'] ?></div>
							<div class="details"><?php print $column['detail'] ?></div>
						</div>
					</div>
					
					<div class="features">
						<?php if(!empty($column['features'])) : ?>
							<?php foreach($column['features'] as $j => $feature) : ?>
								<div class="pricetable-feature <?php print $j == 0 ? 'pricetable-first' : '' ?>">
									<?php print $feature['title'] ?>
									<?php if(!empty($feature['sub'])) : ?>
										<small><?php print $feature['sub'] ?></small>
									<?php endif; ?>
								</div>
							<?php endforeach; ?>
						<?php endif; ?>
					</div>
					
					<div class="pricetable-button-container">
						<a href="<?php print empty($column['url']) ? '#' : $column['url'] ?>" class="btn signup"><?php print empty($column['button']) ? __('Select', 'radium') : $column['button'] ?></a>
					</div>	
					<div class="last-divider"></div>			
				</div>
				
			</div>
			
		</div>
		
		<?php $i++; endforeach; 
			
	}
	
	
	/**
	 * Add the price table to the content.
	 * 
	 * @since 1.0.0
	 *
	 * @param $the_content
	 * @return string
	 * 
	 * @filter the_content
	 */
	public function the_content_filter($the_content){
	
		global $post;
		
		if(is_single() && $post->post_type == 'pricetable' && empty($post->pricetable_inserted)){
		
			$the_content = radium_pricetable_shortcode().$the_content;
		
		}
		
		return $the_content;
		
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
