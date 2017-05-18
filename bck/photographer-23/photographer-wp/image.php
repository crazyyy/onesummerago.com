<?php
	get_header();
?>


<div id="main" class="site-main">
	<div id="primary" class="content-area">
		<div id="content" class="site-content" role="main">
			<div class="layout-fixed">
				<?php
					if ( have_posts() ) :
						while ( have_posts() ) : the_post();
							?>
								<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
									<header class="entry-header">
										<h1 class="entry-title"><?php the_title(); ?></h1>
										
										<div class="entry-meta">
											<span class="entry-date">
												<time class="entry-date" datetime="2012-02-13T04:34:10+00:00"><?php echo get_the_date(); ?></time>
											</span>
											
											<span class="comment-link">
												<?php
													comments_popup_link( __( '0 Comment', 'read' ), __( '1 Comment', 'read' ), __( '% Comments', 'read' ) );
												?>
											</span>
											
											<?php
												edit_post_link( __( 'Edit', 'read' ), '<span class="edit-link">', '</span>' );
											?>
										</div>
									</header>
									
									
									<div class="entry-content">
										<img alt="<?php the_title_attribute(); ?>" src="<?php echo esc_url( wp_get_attachment_url() ); ?>">
										
										
										<?php
											the_content();
										?>
										
										
										<?php
											wp_link_pages( array( 'before' => '<div class="page-links">' . __( 'Pages:', 'read' ), 'after' => '</div>' ) );
										?>
										
										
										<nav class="nav-single row">
											<div class="nav-previous col-sm-6">
												<?php
													previous_image_link( false, '<span class="meta-nav">&#8592;</span>' . __( 'PREVIOUS IMAGE', 'read' ) );
												?>
											</div>
											
											<div class="nav-next col-sm-6">
												<?php
													next_image_link( false, __( 'NEXT IMAGE', 'read' ) . '<span class="meta-nav">&#8594;</span>' );
												?>
											</div>
										</nav>
									</div>
								</article>
								
								
								<?php
									comments_template( "", true );
								?>
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