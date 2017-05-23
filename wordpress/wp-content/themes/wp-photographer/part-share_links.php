<div class="share-links">
	<h3><?php echo __( 'Поделиться', 'read' ); ?></h3>

	<div class="share-links-wrap">
		<a rel="nofollow" target="_blank" href="mailto:?subject=<?php echo __( 'I wanted you to see this post', 'read' ); ?>&amp;body=<?php echo __( 'Check out this post', 'read' ); ?> : <?php the_title_attribute(); ?> - <?php the_permalink(); ?>" title="<?php echo __( 'Отправить пост другу1', 'read' ); ?>"><i class="pw-icon-mail"></i></a>

		<a rel="nofollow" target="_blank" href="https://plus.google.com/share?url=<?php the_permalink(); ?>" title="<?php echo __( 'Поделиться на Google+', 'read' ); ?>"><i class="pw-icon-gplus"></i></a>

		<a rel="nofollow" target="_blank" href="http://twitter.com/home?status=<?php echo __( 'Currently reading', 'read' ); ?>: '<?php the_title_attribute(); ?>' <?php the_permalink(); ?>" title="<?php echo __( 'Поделиться со своими подпищиками', 'read' ); ?>"><i class="pw-icon-twitter"></i></a>

		<a rel="nofollow" target="_blank" href="http://www.facebook.com/sharer.php?u=<?php the_permalink(); ?>&amp;t=<?php the_title_attribute(); ?>" title="<?php echo __( 'Распостранить пост на Facebook', 'read' ); ?>"><i class="pw-icon-facebook"></i></a>
	</div>
</div>
