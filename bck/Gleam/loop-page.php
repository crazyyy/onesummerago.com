<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
	<article class="entry post page">
		<div class="title_area">
			<h1 class="main_title"><?php the_title(); ?></h1>
			<?php if ( ( $page_description = get_post_meta( get_the_ID(), 'Description', true ) ) && '' != $page_description ) { ?>
				<p id="page_description"><?php echo esc_html( $page_description ); ?></p>
			<?php } ?>
		</div> <!-- .title_area -->

		<div class="post-content clearfix">
			<?php
				$thumb = '';
				$width = (int) apply_filters('et_image_width',678);
				$height = (int) apply_filters('et_image_height',200);
				$classtext = '';
				$titletext = get_the_title();
				$thumbnail = get_thumbnail($width,$height,$classtext,$titletext,$titletext,false,'Single');
				$thumb = $thumbnail["thumb"];
			?>
			<?php if ( '' <> $thumb && 'on' == get_option( 'gleam_page_thumbnails' ) ) { ?>
				<div class="post-image">
					<?php print_thumbnail($thumb, $thumbnail["use_timthumb"], $titletext, $width, $height, $classtext); ?>
					<span class="overlay"></span>
				</div> 	<!-- end .post-image -->
			<?php } ?>
			<?php the_content(); ?>
			<?php wp_link_pages(array('before' => '<p><strong>'.esc_attr__('Pages','Gleam').':</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>
			<?php edit_post_link(esc_attr__('Edit this page','Gleam')); ?>
		</div> <!-- end .post-content -->
	</article> 	<!-- end .post-->
<?php endwhile; // end of the loop. ?>