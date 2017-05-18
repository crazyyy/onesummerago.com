<?php
	if ( isset( $_GET['blog_sidebar'] ) )
	{
		if ( $_GET['blog_sidebar'] == 'no' )
		{
			get_template_part( 'blog', 'regular_no_sidebar' );
		}
		else
		{
			get_template_part( 'blog', 'regular_sidebar' );
		}
	}
	else
	{
		$blog_sidebar = get_option( 'blog_sidebar', 'Yes' );
		
		
		if ( $blog_sidebar == 'No' )
		{
			get_template_part( 'blog', 'regular_no_sidebar' );
		}
		else
		{
			get_template_part( 'blog', 'regular_sidebar' );
		}
	}
?>