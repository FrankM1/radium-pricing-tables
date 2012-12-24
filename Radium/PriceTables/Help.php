<?php
/**
 * Contextual help class for Radium_PriceTables.
 *
 * @since 1.0.0
 *
 * @package	Radium_PriceTables
 * @author	Franklin M Gitonga
 */
 
class Radium_PriceTables_Help {

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
	
		add_action( 'admin_head', array( $this, 'contextual_help' ) );
	
	}
	
	/**
	 * Adds contextual help to Soliloquy pages.
	 *
	 * @since 1.0.0
	 *
	 * @global object $post The current post object
	 */
	public function contextual_help() {
	
		global $post;
		$current_screen = get_current_screen();
		
		/** Set a 'global' help sidebar for all Soliloquy related pages */
		if ( Radium_PriceTables::is_pricetable_screen() )
			$current_screen->set_help_sidebar( sprintf( '<p><strong>%1$s</strong></p><p><a href="http://radiumthemes.com/members-area/" title="%2$s" target="_blank">%2$s</a></p><p><a href="http://radiumthemes.com/contact/" title="%3$s" target="_blank">%3$s</a></p>', Radium_PriceTables_Strings::get_instance()->strings['sidebar_help_title'], Radium_PriceTables_Strings::get_instance()->strings['sidebar_help_support'], Radium_PriceTables_Strings::get_instance()->strings['sidebar_help_contact'] ) );
		
		/** Set help for the main edit screen */
		if ( 'edit-soliloquy' == $current_screen->id && Radium_PriceTables::is_soliloquy_screen() ) {
			$current_screen->add_help_tab( array(
				'id'		=> 'radium-pricetables-main-help',
				'title'		=> Radium_PriceTables_Strings::get_instance()->strings['overview'],
				'content'	=> sprintf( '<p>%s</p><p>%s</p>', Radium_PriceTables_Strings::get_instance()->strings['main_help'], Radium_PriceTables_Strings::get_instance()->strings['main_help_two'] )
			) );
		}
		
		/** Set help for the Add New and Edit screens */
		if ( Radium_PriceTables::is_pricetable_add_edit_screen() ) {
			$current_screen->add_help_tab( array(
				'id'		=> 'radium-pricetables-add-help',
				'title'		=> Radium_PriceTables_Strings::get_instance()->strings['overview'],
				'content'	=> sprintf( '<p>%s</p>', Radium_PriceTables_Strings::get_instance()->strings['add_edit_help'] )
			) );
			$current_screen->add_help_tab( array(
				'id'		=> 'radium-pricetables-advanced-help',
				'title'		=> Radium_PriceTables_Strings::get_instance()->strings['advanced_help'],
				'content'	=> sprintf( '<p><strong>%s</strong></p><p><code>%s</code><span>%s</span><br /><code>%s</code><span>%s</span><br /><code>%s</code><span>%s</span><br /><code>%s</code><span>%s</span><br /><code>%s</code><span>%s</span><br /><code>%s</code><span>%s</span></p>', Radium_PriceTables_Strings::get_instance()->strings['advanced_help_desc'], Radium_PriceTables_Strings::get_instance()->strings['slider_cb_start'],  sprintf( Radium_PriceTables_Strings::get_instance()->strings['slider_cb_start_desc'], $post->ID ), Radium_PriceTables_Strings::get_instance()->strings['slider_cb_before'], sprintf( Radium_PriceTables_Strings::get_instance()->strings['slider_cb_before_desc'], $post->ID ), Radium_PriceTables_Strings::get_instance()->strings['slider_cb_after'], sprintf( Radium_PriceTables_Strings::get_instance()->strings['slider_cb_after_desc'], $post->ID ), Radium_PriceTables_Strings::get_instance()->strings['slider_cb_end'], sprintf( Radium_PriceTables_Strings::get_instance()->strings['slider_cb_end_desc'], $post->ID ), Radium_PriceTables_Strings::get_instance()->strings['slider_cb_added'], sprintf( Radium_PriceTables_Strings::get_instance()->strings['slider_cb_added_desc'], $post->ID ), Radium_PriceTables_Strings::get_instance()->strings['slider_cb_removed'], sprintf( Radium_PriceTables_Strings::get_instance()->strings['slider_cb_removed_desc'], $post->ID ) )
			) );
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