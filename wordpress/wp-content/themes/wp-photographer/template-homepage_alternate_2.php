<?php
/*
Template Name: Homepage Alternate with Slider
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
								<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
									<div class="entry-content">
										<?php
											the_content();
										?>
									</div>
								</article>
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