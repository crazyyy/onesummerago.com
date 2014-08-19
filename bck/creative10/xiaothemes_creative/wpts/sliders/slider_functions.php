<?php 

	/*** BLAST EFFECT */

	function slider_0($options) {
		$ops = $options[3];
		$counter = 0;
		?>

		<!-- THUMBNAILS - FULLSCREEN GALLERY --> 
		<div id="thumbs">
			<ul>
				<?php foreach($ops as $slide) { ?>
				<li><a href="<?php echo $slide[0]; ?>" class="rollover" data-valign="top" data-align="right"><img src="<?php echo $slide[1]; ?>" alt="thumbnail"/></a></li>
				<?php } ?>
			</ul>
			
			<img id="slideshow" class="playpause" title="<?php _e("Play slideshow", "creative"); ?>" src="<?php echo THEME_DIR; ?>/images/play.png" alt="slideshow"/>
		</div>
		<?php
	}
	
?>