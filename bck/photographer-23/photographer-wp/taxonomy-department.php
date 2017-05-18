<?php
	get_header();
?>


<div id="main" class="site-main">
	<div id="primary" class="content-area">
		<div id="content" class="site-content" role="main">
			<div class="layout-full">
				<header class="entry-header">
					<h1 class="entry-title"><?php single_cat_title(); ?></h1>
				</header>
			</div>
			
			
			<?php
				$pixelwars__portfolio_page_content = get_option( 'pixelwars_portfolio_page_content', 'Top' );
				
				if ( $pixelwars__portfolio_page_content != 'Bottom' )
				{
					$category_description = category_description();
					
					if ( $category_description != "" )
					{
						?>
							<div class="layout-fixed">
								<div class="entry-content">
									<?php
										echo $category_description;
									?>
								</div>
							</div>
						<?php
					}
				}
			?>
			
			
			<div class="layout-full">
				<div class="media-grid-wrap">
					<div class="gallery-grid">
						<?php
							$queried_object = get_queried_object();
							
							$children = get_terms(  $queried_object->taxonomy,
													array( 'parent' => $queried_object->term_id ) );
							
							if ( ! empty( $children ) )
							{
								foreach ( $children as $department )
								{
									?>
										<div id="item-<?php echo esc_attr( $department->term_id ); ?>" class="masonry-item">
											<figure>
												<?php
													$args2 = array( 'post_type'      => 'portfolio',
																	'department'     => $department->slug,
																	'posts_per_page' => 1 );
													
													$postslist = get_posts( $args2 );
													
													if ( $postslist )
													{
														echo get_the_post_thumbnail( $postslist[0]->ID, 'pixelwars_theme_image_size_760x507' );
													}
												?>
												
												<figcaption>
													<h2><?php echo $department->name; ?></h2>
													
													<p><?php echo $department->description; ?></p>
													
													<a href="<?php echo esc_url( get_category_link( $department ) ); ?>"></a>
												</figcaption>
											</figure>
										</div>
									<?php
								}
							}
						?>
						
						
						<?php
							$parent_department_slug = get_query_var( 'term' );
							
							$args_portfolio = array('post_type' => 'portfolio',
													'tax_query' => array( array('taxonomy'         => 'department',
																				'field'            => 'slug',
																				'terms'            => $parent_department_slug,
																				'include_children' => false  ) ),
													'posts_per_page' => -1 );
							
							$loop_portfolio = new WP_Query( $args_portfolio );
							
							if ( $loop_portfolio->have_posts() ) :
								while ( $loop_portfolio->have_posts() ) : $loop_portfolio->the_post();
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
								endwhile;
							endif;
							wp_reset_postdata();
						?>
					</div>
				</div>
			</div>
			
			
			<?php
				if ( $pixelwars__portfolio_page_content == 'Bottom' )
				{
					$category_description = category_description();
					
					if ( $category_description != "" )
					{
						?>
							<div class="layout-fixed">
								<div class="entry-content">
									<?php
										echo $category_description;
									?>
								</div>
							</div>
						<?php
					}
				}
			?>
		</div>
	</div>
</div>


<?php
	get_footer();
?>