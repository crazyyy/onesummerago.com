<?php
/*
Template Name: Homepage Landing with Slideshow
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
								<div class="home-background">
                 <div class="bgi">
                  <div class="intro">
                  <h2>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Aut soluta repellat hic deleniti dignissimos provident cupiditate quibusdam adipisci facere perferendis.</h2>
                    <a href="<?php echo get_page_link( 491 ); ?>" class = "button">Перейти!</a>
                  </div>
                 </div>
                </div>
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
