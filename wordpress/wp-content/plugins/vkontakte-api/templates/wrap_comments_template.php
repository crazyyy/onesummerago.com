<?php global $post; ?>

<div id="comments">
	<input type="hidden" id="comments_post_id" value="<?php echo $post->ID; ?>"/>

<?php

do_action( 'vkapi_comments_template' );

if ( ! get_option( 'vkapi_close_wp' ) ) {
	echo '<div id="wp-comments">';
	global $comments_template_file;
	if ( file_exists( $comments_template_file ) ) {
		require( $comments_template_file );
	} else {
		require( ABSPATH . WPINC . '/theme-compat/comments.php');
	}
	echo '</div>';
}

?>

</div>
	