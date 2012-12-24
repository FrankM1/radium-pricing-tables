<?php
/**
 * Admin class for Radium Pricetables.
 *
 * @since 1.0.0
 *
 * @package	Radium_Sliders
 * @author	Franklin M Gitonga
 */
 
class Radium_PriceTables_Admin {
	
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
		
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'add_meta_boxes', array( $this, 'remove_seo_support' ), 99 );
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
		add_action( 'save_post', array( $this, 'save_slider_settings' ), 10, 2 );
		add_action( 'admin_notices', array( $this, 'admin_notices' ) );
		add_action( 'tgmsp_soliloquy_settings', array( $this, 'output_soliloquy_plugin_settings' ) );
		add_filter( 'plugin_action_links_' . plugin_basename( Tgmsp::get_file() ), array( $this, 'settings_link' ) );
		
	}
	
  	
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
	 * Outputs the form callback and hooks everything into tgmsp_soliloquy_settings.
	 *
	 * @since 1.0.0
	 */
	public function soliloquy_plugin_settings() {
  		
		echo '<div class="wrap soliloquy-settings">';
			screen_icon( 'radium_pricetables' );
			echo '<h2 class="soliloquy-settings-title">' . esc_html( get_admin_page_title() ) . '</h2>';
 		echo '</div>';

	}
	
	/**
	 * Add the metaboxes to the Soliloquy edit screen.
	 *
	 * @since 1.0.0
	 */
	public function add_meta_boxes() {

		add_meta_box( 'soliloquy_uploads', Tgmsp_Strings::get_instance()->strings['meta_uploads'], array( $this, 'soliloquy_uploads' ), 'soliloquy', 'normal', 'high' );
		add_meta_box( 'soliloquy_settings', Tgmsp_Strings::get_instance()->strings['meta_settings'], array( $this, 'soliloquy_settings' ), 'soliloquy', 'normal', 'high' );
		add_meta_box( 'soliloquy_instructions', Tgmsp_Strings::get_instance()->strings['meta_instructions'], array( $this, 'soliloquy_instructions' ), 'soliloquy', 'side', 'core' );

	}
	
	/**
	 * Outputs any error messages when verifying license keys.
	 *
	 * @since 1.0.0
	 *
	 * @global array $soliloquy_license Soliloquy license information
	 */
	public function admin_notices() {

		global $soliloquy_license;
		$current_screen = get_current_screen();

		if ( Tgmsp::is_soliloquy_screen() && current_user_can( 'manage_options' ) ) {
			/** No license has been entered, so encourage users to enter the license */
			if ( ! isset( $soliloquy_license['license'] ) && 'soliloquy_page_soliloquy-settings' !== $current_screen->id )
				add_settings_error( 'tgmsp', 'tgmsp-no-key', sprintf( Tgmsp_Strings::get_instance()->strings['no_license'], add_query_arg( array( 'post_type' => 'soliloquy', 'page' => 'soliloquy-settings' ), admin_url( 'edit.php' ) ) ), 'updated' );

			/** The license has been deactivated, so advise users */
			if ( isset( $soliloquy_license['upgrade'] ) && isset( $soliloquy_license['upgrade_status'] ) && 'hold' == $soliloquy_license['upgrade_status'] )
				add_settings_error( 'tgmsp', 'tgmsp-hold-upgrades', Tgmsp_Strings::get_instance()->strings['license_deactivated'], 'updated' );

			/** The license has expired, so output a message and renewal link */
			if ( isset( $soliloquy_license['upgrade_status'] ) && 'expired' == $soliloquy_license['upgrade_status'] )
				add_settings_error( 'tgmsp', 'tgmsp-key-expired', sprintf( Tgmsp_Strings::get_instance()->strings['license_expired'], '<a href="http://soliloquywp.com" target="_blank">http://soliloquywp.com</a>' ), 'error' );
				
			/** Allow settings notices to be filtered */
			apply_filters( 'tgmsp_output_notices', settings_errors( 'tgmsp' ) );
		}

	}
	
	/**
	 * Helper function to get custom field values for the Soliloquy post type.
	 *
	 * @since 1.0.0
	 *
	 * @global int $id The current Soliloquy ID
	 * @global object $post The current Soliloquy post type object
	 * @param string $field The custom field name to retrieve
	 * @param string|int $setting The setting or array index to retrieve within the custom field
	 * @param int $index The array index number to retrieve
	 * @param int $postid The current post ID
	 * @return string|boolean The custom field value on success, false on failure
	 */
	public function get_custom_field( $field, $setting = null, $index = null, $postid = null ) {

		global $id, $post;

		/** Do nothing if the field is not set */
		if ( ! $field )
			return false;

		/** Get the current Soliloquy ID */
		if ( is_null( $postid ) )
			$post_id = ( null === $id ) ? $post->ID : $id;
		else
			$post_id = absint( $postid );

		$custom_field = get_post_meta( $post_id, $field, true );

		/** Return the sanitized field and setting if an array, otherwise return the sanitized field */
		if ( $custom_field && isset( $custom_field[$setting] ) ) {
			if ( is_int( $index ) && is_array( $custom_field[$setting] ) )
				return stripslashes_deep( $custom_field[$setting][$index] );
			else
				return stripslashes_deep( $custom_field[$setting] );
		} elseif ( is_array( $custom_field ) ) {
			return stripslashes_deep( $custom_field );
		} else {
			return stripslashes( $custom_field );
		}

		return false;

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