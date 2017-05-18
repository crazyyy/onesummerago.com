<div id="secondary" class="widget-area sidebar" role="complementary">
	<?php
		if ( get_post_type() == 'page' )
		{
			$my_sidebar = get_option( $post->ID . 'my_sidebar', 'pixelwars_page_sidebar' );
			
			if ( ! function_exists( 'dynamic_sidebar' ) || ! dynamic_sidebar( $my_sidebar ) ) :
			endif;
		}
		else
		{
			if ( ! function_exists( 'dynamic_sidebar' ) || ! dynamic_sidebar( 'pixelwars_blog_sidebar' ) ) :
			endif;
		}
	?>
</div>