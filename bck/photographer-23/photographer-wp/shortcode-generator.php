<?php


/* ============================================================================================================================================= */


	function tinyplugin_register( $plugin_array )
	{
		$url = get_template_directory_uri() . '/admin/shortcode-generator.js';
	 
		$plugin_array['tinyplugin'] = $url;
		
		return $plugin_array;
	}
	
	
	function tinyplugin_add_button( $buttons )
	{
		array_push( $buttons, 'separator', 'tinyplugin' );
		
		return $buttons;
	}


/* ============================================================================================================================================= */


	add_filter( 'mce_external_plugins', 'tinyplugin_register' );
	add_filter( 'mce_buttons', 'tinyplugin_add_button', 0 );


/* ============================================================================================================================================= */


?>