<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
	<?php if (get_option('gleam_integration_single_top') <> '' && get_option('gleam_integrate_singletop_enable') == 'on') echo (get_option('gleam_integration_single_top')); ?>

	<article class="entry post">
		<div class="title_area">
			<h1 class="main_title"><?php the_title(); ?></h1>
			<?php get_template_part('includes/postinfo'); ?>
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
			<?php if ( '' <> $thumb && 'on' == get_option( 'gleam_thumbnails' ) ) { ?>
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

	<?php if (get_option('gleam_integration_single_bottom') <> '' && get_option('gleam_integrate_singlebottom_enable') == 'on') echo(get_option('gleam_integration_single_bottom')); ?>

	<?php
		if ( get_option('gleam_468_enable') == 'on' ){
			if ( get_option('gleam_468_adsense') <> '' ) echo( get_option('gleam_468_adsense') );
			else { ?>
			   <a href="<?php echo esc_url(get_option('gleam_468_url')); ?>"><img src="<?php echo esc_attr(get_option('gleam_468_image')); ?>" alt="468 ad" class="foursixeight" /></a>
	<?php 	}
		}
	?>

	<?php
		if ( 'on' == get_option('gleam_show_postcomments') ) comments_template('', true);
	?>
<?php endwhile; // end of the loop. ?>