<?php
/*
Template Name: Homepage Landing with Slideshow
*/

get_header();
?>


<div id="main" class="site-main">
	<div id="primary" class="content-area">
		<div id="content" class="site-content" role="main">
			<div class="layout-full">
				<?php
					while ( have_posts() ) : the_post();
						?>
							<div id="post-<?php the_ID(); ?>" <?php post_class( 'home-wrap' ); ?>>
								<?php
									the_content();
								?>
							</div>
						<?php
					endwhile;
				?>
			</div>
		</div>
	</div>
</div>


<?php
	get_footer();
?>