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

                      <?php
            if ( ! function_exists( 'dynamic_sidebar' ) || ! dynamic_sidebar( 'pixelwars_footer_sidebar' ) ) :
            endif;
          ?>

                    <h2 class="section-title">Контакты</h2>
                    <div class="row">
                      <div class="col-sm-3 col-xs-6">
                      <div class="fun-fact">
                        <i class="pw-icon-location-outline"></i>
                        <p></p>
                        <h4>г.Винница</h4>
                      </div>
                      </div>
                      <div class="col-sm-3 col-xs-6">
                      <div class="fun-fact">
                        <i class="pw-icon-phone-outline"></i>
                        <p></p>
                        <h4>+380 63 8558308</h4>
                      </div>
                      </div>
                      <div class="col-sm-3 col-xs-6">
                      <div class="fun-fact">
                        <i class="pw-icon-mail-1"></i>
                        <p></p>
                        <h4><a href="mailto:onesummerago@gmail.com">onesummerago</a></h4>
                      </div>
                      </div>
                      <div class="col-sm-3 col-xs-6">
                      <div class="fun-fact">
                        <i class="pw-icon-skype"></i>
                        <p></p>
                        <h4><a href="skype:onesummerago?call">onesummerago</a></h4>
                      </div>
                      </div>
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
