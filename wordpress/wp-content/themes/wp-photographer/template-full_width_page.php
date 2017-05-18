<?php
/*
Template Name: Full-width Page
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
									<?php
										$hide_post_title = get_option( $post->ID . 'hide_post_title', false );
										
										if ( $hide_post_title )
										{
											$hide_post_title = 'style="display: none;"';
										}
										else
										{
											$hide_post_title = "";
										}
									?>
									<header class="entry-header" <?php echo $hide_post_title; ?>>
										<h1 class="entry-title"><?php the_title(); ?></h1>
									</header>
									
									<div class="entry-content">
										<?php
											the_content();
										?>
										
										<?php
											wp_link_pages( array( 'before' => '<div class="page-links clearfix">' . __( 'Pages:', 'read' ), 'after' => '</div>' ) );
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