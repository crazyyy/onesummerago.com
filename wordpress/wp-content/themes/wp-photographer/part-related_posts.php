<?php

	function pixelwars__related_posts()
	{
		$categories = get_the_category();

		$category_ids = "";

		if ( $categories )
		{
			foreach ( $categories as $category )
			{
				$category_ids .= $category->cat_ID . ',';
			}

			$category_ids = trim( $category_ids, ',' );
		}

		$exclude_ids = array( get_the_ID() );

		$args = array( 'post_type' => 'post', 'cat' => $category_ids, 'post__not_in' => $exclude_ids, 'posts_per_page' => 3 );
		$query = new WP_Query( $args );

		if ( $query->have_posts() )
		{
			?>
				<div class="yarpp-related">
					<h3><?php echo __( 'Смотреть также', 'read' ); ?></h3>

					<div class="yarpp-thumbnails-horizontal">
						<?php
							while ( $query->have_posts() )
							{
								$query->the_post();

								if ( has_post_thumbnail() )
								{
									?>
										<a class="yarpp-thumbnail" href="<?php the_permalink(); ?>">
											<span class="yarpp-thumbnail-default">
												<?php
													the_post_thumbnail( 'pixelwars_theme_image_size_1', array( 'alt' => the_title_attribute( 'echo=0' ), 'title' => "" ) );
												?>
											</span>

											<span class="yarpp-thumbnail-title"><?php the_title(); ?></span>
										</a>
									<?php
								}
								else
								{
									?>
										<a class="yarpp-thumbnail" href="<?php the_permalink(); ?>">
											<span class="yarpp-thumbnail-title"><?php the_title(); ?></span>

											<span class="related-posts-excerpt"><?php pixelwars_theme_excerpt_max_charlength( 80 ); ?></span>
										</a>
									<?php
								}
							}
						?>
					</div>
				</div>
			<?php
		}

		wp_reset_postdata();
	}


	// ===============================================================


	pixelwars__related_posts();

?>
