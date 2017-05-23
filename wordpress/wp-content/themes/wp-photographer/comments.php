<?php

/*
	If the current post is protected by a password and
	the visitor has not yet entered the password we will
	return early without loading the comments.
*/

	if ( post_password_required() )
	{
		return;
	}
?>


<div id="comments" class="comments-area">
	<?php
		if ( have_comments() ) :
			?>
				<h3 class="comments-title">
					<?php
						printf( _n( '1 Comment %2$s', '%1$s Comments %2$s', get_comments_number(), 'read' ),
								number_format_i18n( get_comments_number() ),
								'<span class="on" style="display: none;">&#8594;</span> <span style="display: none;">' . get_the_title() . '</span>' );
					?>
				</h3>


				<ol class="commentlist">
					<?php
						wp_list_comments( array('callback' => 'pixelwars_theme_comments',
												'style'    => 'ol' ) );
					?>
				</ol>


				<?php
					// are there comments to navigate through
					if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) :
						?>
							<nav id="comment-nav-below" class="navigation" role="navigation">
								<h1 class="assistive-text section-heading"><?php _e( 'Comment navigation', 'read' ); ?></h1>

								<div class="nav-previous">
									<?php
										previous_comments_link( __( '&larr; Older Comments', 'read' ) );
									?>
								</div>

								<div class="nav-next">
									<?php
										next_comments_link( __( 'Newer Comments &rarr;', 'read' ) );
									?>
								</div>
							</nav>
						<?php
					endif;
					// end Check for comment navigation
				?>

				<?php
					if ( ! comments_open() && get_comments_number() ) :
						?>
							<p class="nocomments"><?php _e( 'Comments are closed.' , 'read' ); ?></p>
						<?php
					endif;
				?>
			<?php
		endif;
	?>


	<?php
		$comments_args = array( 'title_reply' => __( 'Оставить комментарий', 'read' ) );

		comment_form( $comments_args );
	?>
</div>
