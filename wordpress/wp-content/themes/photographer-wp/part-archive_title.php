<?php
	if ( is_category() )
	{
		?>
			<header class="entry-header">
				<h1 class="entry-title"><i><?php echo __( 'Category', 'read' ); ?></i> <span class="cat-title"><?php echo single_cat_title(); ?></span></h1>
			</header>
		<?php
	}
	elseif ( is_tag() )
	{
		?>
			<header class="entry-header">
				<h1 class="entry-title"><i><?php echo __( 'Posts Tagged', 'read' ); ?></i> <span class="cat-title"><?php echo single_tag_title(); ?></span></h1>
			</header>
		<?php
	}
	elseif ( is_author() )
	{
		?>
			<header class="entry-header">
				<h1 class="entry-title"><i><?php echo __( 'Posts By', 'read' ); ?></i> <span class="cat-title"><?php the_author(); ?></span></h1>
			</header>
		<?php
	}
	elseif ( is_date() )
	{
		?>
			<header class="entry-header">
				<h1 class="entry-title"><i><?php echo __( 'Date Archives', 'read' ); ?></i>
					<span class="cat-title"><?php
												if ( is_day() )
												{
													printf( get_the_date() );
												}
												elseif ( is_month() )
												{
													printf( get_the_date( _x( 'F Y', 'monthly archives date format', 'read' ) ) );
												}
												elseif ( is_year() )
												{
													printf( get_the_date( _x( 'Y', 'yearly archives date format', 'read' ) ) );
												}
												else
												{
													_e( 'Archives', 'read' );
												}
											?></span>
				</h1>
			</header>
		<?php
	}
	elseif ( is_search() )
	{
		?>
			<header class="entry-header">
				<h1 class="entry-title"><i><?php echo __( 'searched for :', 'read' ); ?></i> <span class="cat-title"><?php echo the_search_query(); ?></span></h1>
			</header>
		<?php
	}
	elseif ( is_post_type_archive() )
	{
		?>
			<header class="entry-header">
				<h1 class="entry-title"><i><?php echo __( 'Archives', 'read' ); ?></i></h1>
			</header>
		<?php
	}
?>