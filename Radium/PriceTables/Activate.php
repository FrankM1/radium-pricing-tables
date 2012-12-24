<?php
/**
 * Activate the pricetable plugin
 */
function radium_pricetable_activate(){
	// Flush rules so we can view price table pages
	flush_rewrite_rules();
	
}
register_activation_hook(__FILE__, 'radium_pricetable_activate');

