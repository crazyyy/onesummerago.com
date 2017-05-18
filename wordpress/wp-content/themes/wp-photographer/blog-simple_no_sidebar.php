<?php
	get_header();
?>

<div id="main" class="site-main">
	<div id="primary" class="content-area">
		<div id="content" class="site-content" role="main">
			<div class="layout-fixed">
				<?php
					get_template_part( 'part', 'archive_title' );
				?>
				
				<div class="blog-simple">
					<?php
						if ( have_posts() ) :
							while ( have_posts() ) : the_post();
								?>
									<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
										<header class="entry-header">
											<h1 class="entry-title">
												<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
											</h1>
											
											<div class="entry-meta">
												<span class="vcard author post-author">
													<span class="fn"><?php the_author(); ?></span>
												</span>
												<span class="entry-date post-date updated">
													<time class="entry-date" datetime="<?php echo get_the_date( 'c' ); ?>">
														<?php
															echo get_the_date();
														?>
													</time>
												</span>
												<span class="comment-link">
													<?php
														comments_popup_link(__( '0 Comments', 'read' ),
																			__( '1 Comment', 'read' ),
																			__( '% Comments', 'read' ) );
													?>
												</span>
												<span class="cat-links">
													<?php
														the_category( ', ' );
													?>
												</span>
											</div>
											
											<p>
												<?php
													pixelwars_theme_excerpt_max_charlength( 55 );
												?>
											</p>
										</header>
									</article>
								<?php
							endwhile;
						endif;
					?>
					
					<?php
						get_template_part( 'part', 'pagination' );
					?>
				</div>
			</div>
		</div>
	</div>
</div>

<?php
	get_footer();
?>