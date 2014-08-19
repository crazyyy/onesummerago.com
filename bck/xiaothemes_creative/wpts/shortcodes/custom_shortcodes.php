<?php
/**
 * WordPress Toolset 2.0 Shortcodes - Add custom shortcodes here
 *
 */
 
/*-----------------------------------------------------------------------------------*/
/* Enqueue Scripts
/*-----------------------------------------------------------------------------------*/

function wpts_custom_enqueue_scripts() {

	//wp_enqueue_script( "script-id", get_template_directory_uri() . "/url-script", array(''), NULL, false );
}
add_action( 'init', 'wpts_custom_enqueue_scripts' );

/*-----------------------------------------------------------------------------------*/
/* Enqueue the styles used by ShortCodes
/*-----------------------------------------------------------------------------------*/

function wpts_custom_enqueue_styles() {

	/*wp_register_style( 'style-id', get_template_directory_uri() . '/style-address', array(), '1', 'all' );
	wp_enqueue_style( 'style-id' );*/
}
add_action( 'init', 'wpts_custom_enqueue_styles' );

/*-----------------------------------------------------------------------------------*/
/* Shortcode Template
/*-----------------------------------------------------------------------------------*/

/*function wpts_attr($atts, $content = null, $code) {
	extract(shortcode_atts(array(
		'attr' => '',
	), $atts));

	return '';
}
add_shortcode('attr', 'wpts_attr');*/

function wpts_divider_empty($atts, $content = null, $code) {
	extract(shortcode_atts(array(
		'attr' => '',
	), $atts));

	return '<div class="divider_empty"></div>';
}
add_shortcode('divider_empty', 'wpts_divider_empty');

/*-----------------------------------------------------------------------------------*/
/* [image]
/*-----------------------------------------------------------------------------------*/

function wpts_image($atts, $content = null, $code) {
	extract(shortcode_atts(array(
		'src' => '',
		'width' => '',
		'height' => '',
		'padding' => '0',
		'align' => '',
	), $atts));
				
	return '';
}

add_shortcode('image', 'wpts_image');

/*-----------------------------------------------------------------------------------*/
/* [slider]
/*-----------------------------------------------------------------------------------*/

function wpts_slider($atts, $content = null, $code) {
	extract(shortcode_atts(array(
		'attr' => '',
	), $atts));

	return '[raw]
		<div class="gallery">
           '.do_shortcode($content).'
        </div>
	[/raw]';
}
add_shortcode('slider', 'wpts_slider');

/*-----------------------------------------------------------------------------------*/
/* [slide]
/*-----------------------------------------------------------------------------------*/

function wpts_slide($atts, $content = null, $code) {
	extract(shortcode_atts(array(
		'src' => '',
		'description' => '',
		'title' => ''
	), $atts));

	return '<img src="'.$src.'" alt="'.$description.'" title="'.$title.'" />';
}
add_shortcode('slide', 'wpts_slide');

/*-----------------------------------------------------------------------------------*/
/* [tooltip]
/*-----------------------------------------------------------------------------------*/

function wpts_tooltip($atts, $content = null, $code) {
	extract(shortcode_atts(array(
		'href' => '',
		'title' => '',
		'target' => ''
	), $atts));

	if($target != '')
		$target = 'target="_blank"';

	return '<span class="tooltip" title="'.$title.'"><a href="'.$href.'" '.$target.' class="tooltip">'.trim($content).'</a></span>';
}
add_shortcode('tooltip', 'wpts_tooltip');

/*-----------------------------------------------------------------------------------*/
/* [contact_form]
/*-----------------------------------------------------------------------------------*/

function wpts_contact_form($atts, $content = null, $code) {
	extract(shortcode_atts(array(
		'attr' => '',
	), $atts));

	return '[raw]
		<div class="contact">
			<div class="form">
			<hr/>
			<form id="myForm" method="post" action="'.THEME_DIR.'/contact.php">
				<input type="text" value="Name" id="name" name="name" class="fields" onfocus="if(this.value == \''.__("Name", "creative").'\') {this.value = \'\';}" onblur="if (this.value == \'\') {this.value = \''.__("Name", "creative").'\';}"><br/>
				<input type="text" value="Email" id="email" name="email" class="fields" onfocus="if(this.value == \''.__("Email", "creative").'\') {this.value = \'\';}" onblur="if (this.value == \'\') {this.value = \''.__("Email", "creative").'\';}"><br/>
				<textarea cols="20" rows="5" id="message" name="message" class="fields" onfocus="if(this.value == \''.__("Your comments...", "creative").'\') {this.value = \'\';}" onblur="if (this.value == \'\') {this.value = \''.__("Your comments...", "creative").'\';}">'.__("Your comments...", "creative").'</textarea><br/>
				<button type="submit" id="submit" name="submit" class="button"><span>'.__("SEND", "creative").'</span></button>
				<div id="ajax_loader" class="loader_message"></div><div id="loader_icon" class="loader_icon"></div>
			</form>
			</div>
		</div>
	[/raw]';
}
add_shortcode('contact_form', 'wpts_contact_form');

/*-----------------------------------------------------------------------------------*/
/* [small_text]
/*-----------------------------------------------------------------------------------*/

function wpts_small_text($atts, $content = null, $code) {
	extract(shortcode_atts(array(
		'attr' => '',
	), $atts));

	return '<div class="small-text">'.do_shortcode($content).'</div>';
}
add_shortcode('small_text', 'wpts_small_text');

/*-----------------------------------------------------------------------------------*/
/* [header]
/*-----------------------------------------------------------------------------------*/

function wpts_header($atts, $content = null, $code) {
	extract(shortcode_atts(array(
		'title' => '',
		'subtitle' => ''
	), $atts));

	return '<div class="header-title"><span class="title">'.$title.'</span><span class="subtitle">'.$subtitle.'</span></div>';
}
add_shortcode('header', 'wpts_header');

/*-----------------------------------------------------------------------------------*/
/* [recent_projects]
/*-----------------------------------------------------------------------------------*/

function wpts_recent_projects($atts, $content = null, $code) {
	extract(shortcode_atts(array(
		'number' => '3'
	), $atts));


	global $post;
	
	$html = '';
		
	$query = new WP_Query(array('post_type' => 'project', 'posts_per_page' => $number));
	
	ob_start();
	?>

	<div class="recent-projects">
		<ul>
			<?php	
						
				$count = 0;

				$largephotos = '';
						
				while ( $query->have_posts() ) : $query->the_post();
			?>
				<?php 

					$href = get_post_meta(get_the_ID(), 'project-url', true);
					$label = get_post_meta(get_the_ID(), 'project-label', true);

					$thumb = get_post_meta(get_the_ID(), 'recent-thumb', true);

					if($thumb == "") {
						$thumb = THEME_DIR . '/images/project_dummy.png';
					}

					$video = get_post_meta(get_the_ID(), 'video-url', true);

					?>

					<?php 
					$name = trim(get_the_title());
					$name = strtolower($name);
					$name = str_replace(' ', '_', $name);
					$name = str_replace('!', '', $name);
					$name = str_replace('?', '', $name);
					$name = str_replace('.', '', $name);
					$name = str_replace(',', '', $name);
					$name = str_replace(';', '', $name);
					$name .= '-recent';

					$images = 0;

					$firstImg = '';
					$firstTitle = '';

					$message = '';

					if($video == "") {

						$args = array(
							'post_type' => 'attachment',
							'numberposts' => -1,
							'post_status' => null,
							'post_parent' => $post->ID
						);
																  
						$attachments = get_posts( $args );

						if(count($attachments) > 0) {
							$images = count($attachments);
							$message = $images . ' ' . __("images", "creative"); 
						}

						if ( $attachments ) {		
							foreach ( $attachments as $attachment ) {

								if($firstImg != '') :

									$largephotos .= '<li class="hidden"><a href="' . wp_get_attachment_url($attachment->ID) .'" data-rel="prettyPhoto['.$name.']" title="'. $attachment->post_content.'"></a></li>';
									
								else :

									$firstImg = wp_get_attachment_url($attachment->ID); 
									$firstTitle = $attachment->post_title;

								endif;
									
							}
						}

					}
					else {

						$firstImg = $video;
						$firstTitle = '';
						$message = __("1 Video", "creative");

					}

				?>
				<li>
					<a href="<?php echo $firstImg; ?>" data-rel="prettyPhoto[<?php echo $name; ?>]" title="<?php echo $firstTitle; ?>" class="rollover">
						<img src="<?php echo $thumb; ?>" alt=""/></a>
						<div class="clear clearboth"></div>
						<a href="<?php echo $href; ?>" target="_blank"><?php echo $label; ?></a>
				</li>	
				<?php $count++; ?>
						
			<?php
				endwhile;
			?>

			<?php echo $largephotos; ?>

		</ul>
	</div>

	<?php
	$html = ob_get_clean();
	ob_flush();
	return '[raw]'. $html . '[/raw]';	

}
add_shortcode('recent_projects', 'wpts_recent_projects');

/*-----------------------------------------------------------------------------------*/
/* [portfolio]
/*-----------------------------------------------------------------------------------*/

function wpts_portfolio($atts, $content = null, $code) {
	extract(shortcode_atts(array(
		'attr' => '',
	), $atts));

	global $post;
	
	$html = '';
		
	$query = new WP_Query(array('post_type' => 'project', 'posts_per_page' => -1));
	
	ob_start();
	?>
				<div class="portfolio">
					<ul>
					<?php	
						
						$count = 0;

						$largephotos = '';
						
						while ( $query->have_posts() ) : $query->the_post();
					?>
						
						<li>
							<?php 
								$thumb = get_post_meta(get_the_ID(), 'grid-thumb', true);

								if($thumb == "") {
									$thumb = THEME_DIR . '/images/project_dummy.png';
								}

								$video = get_post_meta(get_the_ID(), 'video-url', true);
							?>

							<?php 
								$name = trim(get_the_title());
								$name = strtolower($name);
								$name = str_replace(' ', '_', $name);
								$name = str_replace('!', '', $name);
								$name = str_replace('?', '', $name);
								$name = str_replace('.', '', $name);
								$name = str_replace(',', '', $name);
								$name = str_replace(';', '', $name);

								$images = 0;

								$firstImg = '';
								$firstTitle = '';

								$message = '';

							if($video == "") {

								$args = array(
									'post_type' => 'attachment',
									'numberposts' => -1,
									'post_status' => null,
									'post_parent' => $post->ID
								);
																  
								$attachments = get_posts( $args );

								if(count($attachments) > 0) {
									$images = count($attachments);
									$message = $images . ' ' . __("images", "creative"); 
								}

								if ( $attachments ) {		
									foreach ( $attachments as $attachment ) {

										if($firstImg != '') :

										$largephotos .= '<li class="hidden"><a href="' . wp_get_attachment_url($attachment->ID) .'" data-rel="prettyPhoto['.$name.']" title="'. $attachment->post_content.'"></a></li>';
									
										else :

										$firstImg = wp_get_attachment_url($attachment->ID); 
										$firstTitle = $attachment->post_title;

										endif;
									
									}
								}

							}
							else {

								$firstImg = $video;
								$firstTitle = '';
								$message = __("1 Video", "creative");

							}

							?>

							<a href="<?php echo $firstImg; ?>" data-rel="prettyPhoto[<?php echo $name; ?>]" title="<?php echo $firstTitle; ?>" class="rollover">
								<div class="icon"></div>
								<div class="categories">
									<img src="<?php echo $thumb; ?>" />
									<div class="title"><?php the_title(); ?></div>
									<div class="description"><?php echo get_the_excerpt(); ?></div>
									<div class="number"><?php echo $message; ?></div>
								</div>
							</a>
						</li>

						<?php $count++; ?>
						
					<?php
						endwhile;
					?>

					<?php echo $largephotos; ?>

					</ul>
				</div>
	<?php

	$html = ob_get_clean();
	ob_flush();
	return '[raw]'. $html . '[/raw]';	

}
add_shortcode('portfolio', 'wpts_portfolio');

/*-----------------------------------------------------------------------------------*/
/* [blog]
/*-----------------------------------------------------------------------------------*/

function wpts_blog($atts, $content = null, $code) {
	extract(shortcode_atts(array(
		'blogid' => '',
	), $atts));

	$enableAJAX = true;

	if($enableAJAX) :
		wp_enqueue_script("load-posts");
	endif;

	$max = $wp_query->max_num_pages;

	$html = '';

	global $bid;

	$bid = $blogid;
		
	ob_start();
	?>

		<script type="text/javascript">
			var BLOG_SLUG = "#<?php echo $bid; ?>";
		</script>

		<div class="blog-title">
			<div class="header-title"><span class="title"><?php _e("Blog", "creative"); ?></span></div>
			<a href="<?php echo home_url("/").'#'.$blogid; ?>" id="back-blog"><?php _e("Back to Blog", "creative"); ?></a>
		</div>

		<div class="loader"><img src="<?php echo THEME_DIR; ?>/images/preload.gif" alt="Loading..." /></div>

		<div class="wpts-blog-list">
		
		<?php
			if(!isset($_GET["p"])) : 
				get_template_part("ajax", "blog");
			endif;
		?>

		</div> <!-- .wpts-blog-list -->

		<div class="wpts-single-post" <?php if(isset($_GET["p"])) echo 'style="display: block;"'; ?>>
			<?php
				get_template_part("ajax", "single");
			?>
		</div> <!-- .single-post -->

		<?php if($enableAJAX && isset($_GET["p"])) : ?>
			<script type="text/javascript">
				jQuery(document).ready(function($) {
					$(".wpts-blog-list").css("display", "none");
					$(".wpts-single-post").css("display", "block");
				});
			</script>
		<?php endif; ?>
	<?php

	$html = ob_get_clean();
	ob_flush();
	return $html;	

}
add_shortcode('blog', 'wpts_blog');

?>