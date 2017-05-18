<?php
/**
 * The main template file for display single post page.
 *
 * @package WordPress
*/

get_header(); 

if($post_type == 'gallery')
{
	$pp_portfolio_style = get_option('pp_portfolio_style');
	if(empty($pp_portfolio_style))
	{
		$pp_portfolio_style = 3;
	}

/* 	include (TEMPLATEPATH . "/templates/template-portfolio-".$pp_portfolio_style.".php"); */
	exit;
}

?>
		<!-- Begin content -->
		<div id="page_content_wrapper">
		
			<div class="inner">
			
				<div class="sidebar_content">
				
				<?php

if (have_posts()) : while (have_posts()) : the_post();

	$image_thumb = '';
								
	if(has_post_thumbnail(get_the_ID(), 'large'))
	{
	    $image_id = get_post_thumbnail_id(get_the_ID());
	    $image_thumb = wp_get_attachment_image_src($image_id, 'large', true);
	    
	    
	  	$pp_blog_image_width = 820;
		$pp_blog_image_height = 260;
	}
?>

		<?php
			$pp_blog_bg = get_option('pp_blog_bg'); 
			
			if(empty($pp_blog_bg))
			{
				$pp_blog_bg = '/example/bg.jpg';
			}
			else
			{
				$pp_blog_bg = '/data/'.$pp_blog_bg;
			}
		?>
		<script type="text/javascript"> 
			jQuery.backstretch( "<?php echo get_stylesheet_directory_uri().$pp_blog_bg; ?>", {speed: 'slow'} );
		</script>

						<!-- Begin each blog post -->
						<div class="post_wrapper">
						
							<?php
								if(!empty($image_thumb))
								{
							?>
							
							<br class="clear"/>
							<div class="post_img">
									<img src="<?php echo get_stylesheet_directory_uri(); ?>/timthumb.php?src=<?php echo $image_thumb[0]; ?>&amp;h=<?php echo $pp_blog_image_height; ?>&amp;w=<?php echo $pp_blog_image_width; ?>&amp;zc=1" alt="<?php the_title(); ?>" class=""/>
							</div>
							
							<?php
								}
							?>
							
							<br/>

							<div class="post_header">
								<h2 class="cufon"><a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a></h2>
							</div>
							
							<br class="clear"/>
							
							<div class="post_excerpt">
								<?php the_content(); ?>
<script type="text/javascript">(function() {
  if (window.pluso)if (typeof window.pluso.start == "function") return;
  if (window.ifpluso==undefined) { window.ifpluso = 1;
    var d = document, s = d.createElement('script'), g = 'getElementsByTagName';
    s.type = 'text/javascript'; s.charset='UTF-8'; s.async = true;
    s.src = ('https:' == window.location.protocol ? 'https' : 'http')  + '://share.pluso.ru/pluso-like.js';
    var h=d[g]('body')[0];
    h.appendChild(s);
  }})();</script>
<div class="pluso" data-background="transparent" data-options="medium,square,line,horizontal,counter,theme=06" data-services="facebook,vkontakte,twitter,livejournal,google,linkedin"></div>
							</div>
							
						</div>
						<!-- End each blog post -->


						<?php comments_template( '' ); ?>

				<?php endwhile; endif; ?>
				</div>
					
				<br class="clear"/>
			</div>
			<br class="clear"/>
			
		</div>
		<!-- End content -->
		
		<br class="clear"/>
	</div>
	<br class="clear"/>	

<?php get_footer(); ?>