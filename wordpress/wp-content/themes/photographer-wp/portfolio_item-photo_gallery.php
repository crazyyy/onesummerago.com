<?php
	get_header();
?>

<div id="main" class="site-main">
	<div id="primary" class="content-area">
		<div id="content" class="site-content" role="main">
			<div class="layout-full">
				<?php
					while ( have_posts() ) : the_post();
						?>
							<div id="post-<?php the_ID(); ?>" <?php post_class( 'gallery-single' ); ?>>
								<header class="entry-header">
									<h1 class="entry-title"><?php the_title(); ?></h1>
								</header>
								
								<div class="entry-content">
									<?php
										the_content();
									?>
									
									<?php
										wp_link_pages( array( 'before' => '<div class="page-links">' . __( 'Pages:', 'read' ), 'after' => '</div>' ) );
									?>
								</div>
								
								<nav class="row nav-single">
									<div class="col-xs-6 nav-previous">
										<?php
											next_post_link( '<h4>' . __( 'PREVIOUS', 'read' ) . '</h4>' . '%link', '<span class="meta-nav">&#8592;</span> %title' );
										?>
									</div>
									<div class="col-xs-6 nav-next">
										<?php
											previous_post_link( '<h4>' . __( 'NEXT', 'read' ) . '</h4>' . '%link', '%title <span class="meta-nav">&#8594;</span>' );
										?>
									</div>
								</nav>
							</div>
						<?php
					endwhile;
				?>
			</div>
			<div class="layout-fixed">
				<?php
					comments_template( "", true );
				?>
			</div>
		</div>
	</div>
</div>

<?php
	get_footer();
?>