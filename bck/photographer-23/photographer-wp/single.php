<?php
	if ( isset( $_GET['post_sidebar'] ) )
	{
		if ( $_GET['post_sidebar'] == 'no' )
		{
			get_template_part( 'post', 'no_sidebar' );
		}
		else
		{
			get_template_part( 'post', 'sidebar' );
		}
	}
	else
	{
		$post_sidebar = get_option( 'post_sidebar', 'Yes' );
		
		
		if ( $post_sidebar == 'No' )
		{
			get_template_part( 'post', 'no_sidebar' );
		}
		else
		{
			get_template_part( 'post', 'sidebar' );
		}
	}
?>