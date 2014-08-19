<?php	
	the_post();
?>

	<article>
	<div id="post-<?php the_ID(); ?>" <?php post_class('single-post'); ?>>

		<div class="post-meta">
			<h1 class="title"><?php the_title(); ?></h1>
			<p class="meta"><span class="date"><?php the_time("M d, Y"); ?></span><span class="author"><?php _e("Author", "creative"); ?>: <?php the_author(); ?></span>
		</div> <!-- .post-meta -->

		<div class="post-content">
						<?php

						$usebuilder = get_post_meta($post->ID, "usebuilder", true);
											
						if($usebuilder) :
							
							$elements = get_post_meta($post->ID, "elements", true);

							if($elements != "") :

								if (@base64_decode( $elements, true )) {
							
									$elements = base64_decode( $elements );
									$elements = maybe_unserialize( $elements );
									//- var_dump($elements);
								}
								
								foreach($elements as $element) {
									if($element[0] == "widget") {
										echo readWidget($element);
										continue;
									}
									
									if($element[0] == "layout") {
										readLayout($element);
										continue;
									}
									
								}
							endif;
						
						else :
							echo do_shortcode(get_the_content());
						endif;
						
						?>
			<div class="seo-meta">
				<div style="display: none !important;">
						<?php the_tags(); ?>
				</div>
			</div>
		</div> <!-- .post-content -->

		<?php if(isset($_GET["p"])) : ?>
		<script type="text/javascript">
			jQuery(document).ready(function($) {
				$(".blog-title #back-blog").css("display", "block");
			});
		</script>
		<?php endif; ?>
						
	</div> <!-- .single-post -->
	</article>
<?php
?>