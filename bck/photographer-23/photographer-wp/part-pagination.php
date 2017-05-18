<?php
	$pagination = get_option( 'pagination', 'No' );
	
	if ( $pagination == 'Yes' )
	{
		?>
			<nav class="post-pagination">
				<?php
					oxo_pagination( array() );
				?>
			</nav>
		<?php
	}
	else
	{
		?>
			<nav class="navigation" role="navigation">
				<div class="nav-previous">
					<?php
						next_posts_link( __( '<span class="meta-nav">&#8592;</span> Older posts', 'read' ) );
					?>
				</div>
				
				<div class="nav-next">
					<?php
						previous_posts_link( __( 'Newer posts <span class="meta-nav">&#8594;</span>', 'read' ) );
					?>
				</div>
			</nav>
		<?php
	}
?>