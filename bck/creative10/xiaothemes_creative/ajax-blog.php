<?php	
	global $bid; 

?>

<?php

	$paged = ( get_query_var('paged') > 1 ) ? get_query_var('paged') : 1;

	$query = new WP_Query(array('post_type' => 'post', 'posts_per_page' => $posts->max_num_pages, 'paged' => $paged, 'post_status' => 'publish'));

	?>

<article>

<?php
	while ( $query->have_posts() ) : $query->the_post();
?>

	<div class="post<?php if( has_post_thumbnail() ) echo ' withthumb'; ?>">
		<?php if( has_post_thumbnail() ) : ?>
			<div class="thumb">
				<a href="<?php the_permalink(); ?>#<?php echo $bid; ?>" class=""><?php the_post_thumbnail('blog-size', array('title' => get_the_title()) ); ?></a>
			</div> <!-- .thumb -->
		<?php endif; ?>
		<div class="post-meta">
			<h1 class="title"><a href="<?php the_permalink(); ?>#<?php echo $bid; ?>"><?php the_title(); ?></a></h1>
			<p class="meta"><span class="date"><?php the_time("M d, Y"); ?></span><span class="author"><?php _e("Author", "creative"); ?>: <?php the_author(); ?></span>
			<div class="excerpt">
				<?php the_excerpt(); ?>
			</div> <!-- .excerpt -->
			<div class="read-more">
				<a href="<?php the_permalink(); ?>#<?php echo $bid; ?>"><?php _e("Read More", "creative"); ?></a>
			</div> <!-- .read-more -->
		</div> <!-- .post-meta -->
		<div class="clear clearboth"></div>
	</div>
						
<?php
	endwhile;
?>

<?php nav_pagination(2, $posts->max_num_pages ); ?>

</article>