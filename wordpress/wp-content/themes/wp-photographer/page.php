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

										<h2 class="section-title">Я в соц.сетях</h2>
                    <ul class="social">
                      <li></li>
                      <li></li>
                      <li></li>
                      <li></li>
                      <li></li>
                    </ul>
                    <h2 class="section-title">Контакты</h2>
                    <div class="row">
                      <div class="col-sm-3 col-xs-6"></div>
                      <div class="col-sm-3 col-xs-6"></div>
                      <div class="col-sm-3 col-xs-6"></div>
                      <div class="col-sm-3 col-xs-6"></div>
                    </div>
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
