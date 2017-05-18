<?php
	if ( isset( $_GET['blog_type'] ) )
	{
		if ( $_GET['blog_type'] == 'simple' )
		{
			get_template_part( 'blog', 'simple' );
		}
		else
		{
			get_template_part( 'blog', 'regular' );
		}
	}
	else
	{
		$blog_type = get_option( 'blog_type', 'Regular' );
		
		
		if ( $blog_type == 'Simple' )
		{
			get_template_part( 'blog', 'simple' );
		}
		else
		{
			get_template_part( 'blog', 'regular' );
		}
	}
?>