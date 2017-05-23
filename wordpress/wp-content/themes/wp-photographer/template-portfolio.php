<?php
/*
Template Name: Portfolio
*/

get_header();
?>


<div id="main" class="site-main">
	<div id="primary" class="content-area">
		<div id="content" class="site-content" role="main">
			<?php
				$pixelwars__hide_title = get_option( $post->ID . 'hide_post_title', false );

				if ( $pixelwars__hide_title != true )
				{
					?>
						<div class="layout-full">
							<header class="entry-header">
								<h1 class="entry-title"><?php single_post_title(); ?></h1>
							</header>
						</div>
					<?php
				}
			?>


			<?php
				$pixelwars_portfolio_page_content = get_option( 'pixelwars_portfolio_page_content', 'Top' );

				if ( $pixelwars_portfolio_page_content != 'Bottom' )
				{
					if ( have_posts() ) :
						while ( have_posts() ) : the_post();

							$page_content = get_the_content();

							if ( $page_content != "" )
							{
								?>
									<div class="layout-fixed">
										<div class="entry-content">
											<?php
												the_content();
											?>
										</div>
									</div>
								<?php
							}

						endwhile;
					endif;
				}
			?>


			<div class="layout-full">
				<div class="media-grid-wrap">
					<div class="gallery-grid">
						<?php
							$args = array(  'taxonomy' => 'department',
											'parent'   => 0 );

							$categories = get_categories( $args );

							foreach ( $categories as $category )
							{
								?>
									<div id="item-<?php echo esc_attr( $category->term_id ); ?>" class="masonry-item">
										<figure>
											<?php
												$args2 = array( 'post_type'      => 'portfolio',
																'department'     => $category->slug,
																'posts_per_page' => 1 );

												$postslist = get_posts( $args2 );

												if ( $postslist )
												{
													echo get_the_post_thumbnail( $postslist[0]->ID, 'pixelwars_theme_image_size_300x507' );
												}
											?>

											<figcaption>
												<h2><?php echo $category->name; ?></h2>

												<p><?php echo $category->description; ?></p>

												<a href="<?php echo esc_url( get_category_link( $category ) ); ?>"></a>
											</figcaption>
										</figure>
									</div>
								<?php
							}
						?>


						<?php
							$args_portfolio = array('post_type'      => 'portfolio',
													'posts_per_page' => -1 );

							$loop_portfolio = new WP_Query( $args_portfolio );

							if ( $loop_portfolio->have_posts() ) :
								while ( $loop_portfolio->have_posts() ) : $loop_portfolio->the_post();

									$the_terms = get_the_terms( get_the_ID(), 'department' );

									if ( $the_terms == false )
									{
										?>
											<div id="post-<?php the_ID(); ?>" <?php post_class( 'masonry-item' ); ?>>
												<figure>
													<?php
														the_post_thumbnail( 'pixelwars_theme_image_size_760x507' );
													?>

													<figcaption>
														<h2><?php the_title(); ?></h2>

														<?php
															$pf_short_description = stripcslashes( get_option( get_the_ID() . 'pf_short_description', "" ) );
														?>

														<p><?php echo $pf_short_description; ?></p>

														<a href="<?php the_permalink(); ?>"></a>
													</figcaption>
												</figure>
											</div>
										<?php
									}

								endwhile;
							endif;
							wp_reset_postdata();
						?>
					</div>
				</div>
			</div>


			<?php
				if ( $pixelwars_portfolio_page_content == 'Bottom' )
				{
					if ( have_posts() ) :
						while ( have_posts() ) : the_post();

							$page_content = get_the_content();

							if ( $page_content != "" )
							{
								?>
									<div class="layout-fixed">
										<div class="entry-content">
											<?php
												the_content();
											?>
										</div>
									</div>
								<?php
							}

						endwhile;
					endif;
				}
			?>
		</div>
	</div>
</div>


<?php
	get_footer();
?>
