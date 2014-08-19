<?php
	get_template_part('includes/top_info');
	$i = 1;
?>
<div id="posts_grid" class="clearfix">
	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
		<div class="article<?php if ( $i % 2 == 0 ) echo ' last'; ?>">
			<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
			<?php get_template_part('includes/postinfo'); ?>

			<?php
				$thumb = '';
				$width = (int) apply_filters('et_image_width',316);
				$height = (int) apply_filters('et_image_height',175);
				$classtext = '';
				$titletext = get_the_title();
				$thumbnail = get_thumbnail($width,$height,$classtext,$titletext,$titletext,false,'Entry');
				$thumb = $thumbnail["thumb"];
			?>
			<?php if ( '' != $thumb && 'on' == get_option('gleam_thumbnails_index') ) { ?>
				<div class="blog_image">
					<a href="<?php the_permalink(); ?>">
						<?php print_thumbnail($thumb, $thumbnail["use_timthumb"], $titletext, $width, $height, $classtext); ?>
						<span class="overlay"></span>
					</a>
					<div class="description"><p><?php truncate_post(85); ?></p></div>
				</div> 	<!-- end .post-image -->
			<?php } ?>
		</div> <!-- .article -->
		<?php $i++; ?>
	<?php
	endwhile;
		if (function_exists('wp_pagenavi')) { wp_pagenavi(); }
		else { get_template_part('includes/navigation','entry'); }
	else:
		get_template_part('includes/no-results','entry');
	endif; ?>
</div> <!-- #posts_grid -->