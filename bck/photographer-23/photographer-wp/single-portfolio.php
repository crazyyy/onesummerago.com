<?php
	$pf_type = get_option( get_the_ID() . 'pf_type', 'Standard' );
	
	if ( $pf_type == 'Photo Gallery' )
	{
		get_template_part( 'portfolio_item', 'photo_gallery' );
	}
	elseif ( $pf_type == 'Photo Gallery 2' )
	{
		get_template_part( 'portfolio_item', 'photo_gallery_2' );
	}
	elseif ( $pf_type == 'Photo Gallery 3' )
	{
		get_template_part( 'portfolio_item', 'photo_gallery_3' );
	}
	else
	{
		get_template_part( 'portfolio_item', 'standard' );
	}
?>