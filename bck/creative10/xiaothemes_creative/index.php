<?php
	get_header(); // get the header.php file	
?>

	<!-- MENU --> 
	<div id="menu">
		<ul>
			
			<?php
	
			$tpageids = wpts_get_option("pages", "ids");
			
			$tpages = explode ( ";" , $tpageids );

			foreach($tpages as $tID) {

				if($tID != '') {

				$ttquery = new WP_Query( 'page_id='.$tID.'&post_type=page' );
				
				$template = get_post_meta( $tID, '_wp_page_template', true );
				$type = 'page';
				if($template == 'page_blog.php') { $type = 'blog'; }
				
				if($ttquery->have_posts()) :
				
				$ttquery->the_post();
				
				$ttitle = get_the_title();
				
				$tblockId = strtolower($ttitle);
				$tblockId = str_replace(' ', '_', $tblockId);
				$tblockId = str_replace('!', '', $tblockId);
				$tblockId = str_replace('?', '', $tblockId);
				$tblockId = str_replace('.', '', $tblockId);
				$tblockId = str_replace(',', '', $tblockId);
				$tblockId = str_replace(';', '', $tblockId);


			
			?>
				<li><a href="#<?php echo $tblockId; ?>" id="menu_<?php echo $tblockId; ?>"><?php echo $ttitle; ?></a></li>
			<?php
				endif;
				wp_reset_query();
				}
			}
			?>
		</ul>
	</div>
	
	
	
	<!-- MENU MARKER -->
	<div id="marker">
		<img src="<?php echo THEME_DIR; ?>/images/menu_marker.png" alt="marker"/>
	</div>


	
	<!-- BASE --> 
	<div id="base">
		<span class="close"><a href="#" id="close_base_bt" title="<?php _e("Close", "creative"); ?>"><img src="<?php echo THEME_DIR; ?>/images/close_big.png" alt="close"/></a></span>
	</div>
	


	<?php

	$tpageids = wpts_get_option("pages", "ids");
			
	$tpages = explode ( ";" , $tpageids );

	foreach($tpages as $tID) {

		$ttquery = new WP_Query( 'page_id='.$tID.'&post_type=page' );
				
		$template = get_post_meta( $tID, '_wp_page_template', true );
		$type = 'page';
		if($template == 'page_blog.php') { $type = 'blog'; }
				
			if($ttquery->have_posts()) :
				
				$ttquery->the_post();
				
				$ttitle = get_the_title();

				
				$tblockId = strtolower($ttitle);
				$tblockId = str_replace(' ', '_', $tblockId);
				$tblockId = str_replace('!', '', $tblockId);
				$tblockId = str_replace('?', '', $tblockId);
				$tblockId = str_replace('.', '', $tblockId);
				$tblockId = str_replace(',', '', $tblockId);
				$tblockId = str_replace(';', '', $tblockId);

			
			?>
				<div id="<?php echo $tblockId; ?>" class="creative-block">

					<div class="creative-content" id="<?php echo $tblockId; ?>-content">

						<div class="content-inner">
						<?php

						$usebuilder = get_post_meta($post->ID, "usebuilder", true);
											
						if($usebuilder) :
							
							$elements = get_post_meta($post->ID, "elements", true);

							if($elements != "") :

								if (@base64_decode( $elements, true )) {
							
									$elements = base64_decode( $elements );
									$elements = maybe_unserialize( $elements );
									//- var_dump($elements);
								}
								
								foreach($elements as $element) {
									if($element[0] == "widget") {
										echo readWidget($element);
										continue;
									}
									
									if($element[0] == "layout") {
										readLayout($element);
										continue;
									}
									
								}
							endif;
						
						else :
							$c = str_replace("[blog]", '[blog blogid="'.$tblockId.'"]', get_the_content() );
							$c = do_shortcode($c);
							$c = str_replace("[raw]", '', $c);
							$c = str_replace("[/raw]", '', $c);
							echo $c;
						endif;
						
						?>
						</div>

					</div>

				</div>
			<?php
				//var_dump($ttquery);
				endif;
			wp_reset_query();
		}
	?>
	
	<?php if(wpts_get_option("general", "enable_msg") == "true") : ?>
	<!-- ALERT BOX --> 
	<div id="alert">
		<span class="title1"><?php echo wpts_get_option("general", "title_msg"); ?></span><br/>
		<span class="title2"><?php echo wpts_get_option("general", "content_msg"); ?></span>
		<span class="close"><a href="#" id="close_bt" title="<?php _e("Close", "creative"); ?>"><img src="<?php echo THEME_DIR; ?>/images/close.png" alt="close"/></a></span>
	</div>
	<?php endif; ?>
	
	<?php
		$options = get_option("slider");

		if($options != null && empty($options) != true) :

		require_once("wpts/sliders/slider_functions.php");
		
		$continue = false;
		
		$key = 0;
		
		$type = $options[$key][1];

		$fn = 'slider_'.$type;
		$fn($options[$key]);
		?>

	<?php
		endif;
	?>

	<?php get_template_part("script", "photo"); ?>

<?php
	get_footer(); // get the footer.php file	
?>