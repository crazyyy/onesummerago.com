<?php
/*
Template Name: Homepage Landing with Single Image
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
									if ( has_post_thumbnail() )
									{
										?>
											<div class="fs-slider fs-single-image">
												<?php
													$featured_image = wp_get_attachment_image_src( get_post_thumbnail_id(), 'pixelwars_theme_image_size_1920' );
													$featured_image_url = $featured_image[0];
												?>
												<div class="fs-slide" style="background-image: url( <?php echo esc_url( $featured_image_url ); ?> );"></div>
											</div>
										<?php
									}
								?>
								
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