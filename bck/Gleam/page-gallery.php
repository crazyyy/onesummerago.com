<?php
/*
Template Name: Gallery Page
*/
?>
<?php
$et_ptemplate_settings = array();
$et_ptemplate_settings = maybe_unserialize( get_post_meta(get_the_ID(),'et_ptemplate_settings',true) );

$fullwidth = isset( $et_ptemplate_settings['et_fullwidthpage'] ) ? (bool) $et_ptemplate_settings['et_fullwidthpage'] : (bool) $et_ptemplate_settings['et_fullwidthpage'];

$gallery_cats = isset( $et_ptemplate_settings['et_ptemplate_gallerycats'] ) ? $et_ptemplate_settings['et_ptemplate_gallerycats'] : array();
$et_ptemplate_gallery_perpage = isset( $et_ptemplate_settings['et_ptemplate_gallery_perpage'] ) ? (int) $et_ptemplate_settings['et_ptemplate_gallery_perpage'] : 12;
?>

<?php get_header(); ?>

<?php get_template_part('includes/breadcrumbs','index'); ?>
<?php get_template_part('loop','page'); ?>

<div id="gleam_gallery" class="clearfix">
	<?php $gallery_query = '';
	if ( !empty($gallery_cats) ) $gallery_query = '&cat=' . implode(",", $gallery_cats);
	else echo '<!-- gallery category is not selected -->'; ?>
	<?php
		$et_paged = is_front_page() ? get_query_var( 'page' ) : get_query_var( 'paged' );
		$i = 0;
	?>
	<?php query_posts("posts_per_page=$et_ptemplate_gallery_perpage&paged=" . $et_paged . $gallery_query); ?>
	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

		<?php $i++;
		$width = 195;
		$height = 195;
		$titletext = get_the_title();

		$thumbnail = get_thumbnail($width,$height,'portfolio',$titletext,$titletext,true,'Portfolio');
		$thumb = $thumbnail["thumb"]; ?>

		<div class="gleam_gallery_entry<?php if ( $i % 3 == 0 ) echo ' last'; ?>">
			<div class="gleam_gallery_image">
				<?php print_thumbnail($thumb, $thumbnail["use_timthumb"], $titletext, $width, $height, 'portfolio'); ?>
				<span class="overlay"></span>

				<span class="gallery_overlay"></span>
				<div class="gleam_info">
					<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
					<div class="gallery_icons">
						<a class="gleam_zoom-icon fancybox" title="<?php the_title_attribute(); ?>" rel="gallery" href="<?php echo($thumbnail['fullpath']); ?>"><?php esc_html_e('Zoom in','Gleam'); ?></a>
						<a class="gleam_more-icon" href="<?php the_permalink(); ?>"><?php esc_html_e('Read more','Gleam'); ?></a>
					</div>
				</div>
			</div> <!-- end .et_pt_item_image -->
		</div> <!-- end .et_pt_gallery_entry -->

	<?php endwhile; ?>
		<div class="page-nav clearfix">
			<?php if(function_exists('wp_pagenavi')) { wp_pagenavi(); }
			else { ?>
				 <?php get_template_part('includes/navigation'); ?>
			<?php } ?>
		</div> <!-- end .entry -->
	<?php else : ?>
		<?php get_template_part('includes/no-results'); ?>
	<?php endif; wp_reset_query(); ?>
</div> <!-- end #et_pt_gallery -->

<?php get_footer(); ?>