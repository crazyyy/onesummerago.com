<?php
/*
Template Name: Homepage Alternate with Slideshow
*/

get_header();
?>


<div id="main" class="site-main">
	<div id="primary" class="content-area">
		<div id="content" class="site-content" role="main">
			<div class="layout-full">
				<?php
					if ( have_posts() ) :
						while ( have_posts() ) : the_post();
							?>
								<div id="post-<?php the_ID(); ?>" <?php post_class( 'home-wrap' ); ?>>
									<?php
										the_content();
									?>
								</div>
							<?php
						endwhile;
					endif;
					wp_reset_query();
				?>
			</div>
		</div>
	</div>
</div>


<?php
	get_footer();
?>