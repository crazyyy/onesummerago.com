<?php
	// CONFIGS FROM ADMIN PANE

	$bottomAlert = "158px";

	if(wpts_get_option("colors", "base_skin") == "photography")
		$bottomAlert = "158px";
	else
		$bottomAlert = "30px";

	$baseColor = "f30b27";

	if(wpts_get_option("colors", "base_skin") == "photography") {
		$baseColor = "f30b27";
	}
	else if(wpts_get_option("colors", "base_skin") == "architect") {
		$baseColor = "4BC9F2";
	}

	if(wpts_get_option("colors", "custom_base") != '')
		$baseColor = wpts_get_option("colors", "custom_base");
?>

<script type="text/javascript">
		jQuery(document).ready(function() {


			//STORING ELEMENTS - VARS
			var th = jQuery('#slideshow');
			var ta = jQuery('#thumbs ul li a');
			var ti = jQuery('#thumbs ul li img');
			var thumbs = jQuery('#thumbs');
			var alert = jQuery('#alert');
			var base = jQuery('#base');
			var slider = jQuery('#slider');
			var marker = jQuery('#marker');
			var markerImg = jQuery('#marker img');
			var section;
			var clickedItem;
			var activeItem = "none";

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
						var <?php echo $tblockId; ?> = jQuery('#<?php echo $tblockId; ?>');
					<?php
						endif;
				}
			?>
			
			//////////////////////////////////////////////////////
			//VEGAS PLUGIN (FULLSCREEN GALLERY) CONFIGURATION
			var slideshowRunning = false,
			backgroundList = [];
			
			//CUSTOM CONFIGURATION (SETUP: true/false)
			var autoSlideOnInit = false;
			var preloadBackgrounds = false;
			
		    ta.each(function() { 
		        backgroundList.push({ 
		            src: jQuery(this).attr('href'),
		            valign: jQuery(this).data('valign'),
					align: jQuery(this).data('align'),
					fade: 2000
				});
			});

			jQuery.vegas(backgroundList[0])
			('overlay', {
				opacity: 0.5
			})
			('pause');

		    th.click(function() { 

		            //START SLIDESHOW
		            if (slideshowRunning == false) { 
		                slideshowRunning = true;

						th.attr('src', THEME_DIR + '/images/pause.png');
						th.attr('title', '<?php _e("Stop slideshow", "creative"); ?>');
						
						jQuery.vegas('slideshow', { 
							delay: 5000,
							backgrounds: backgroundList
						})

					} else { 
		                slideshowRunning = false;
						
						th.attr('src', THEME_DIR + '/images/play.png');
						th.attr('title', '<?php _e("Play slideshow", "creative"); ?>');
						
		                jQuery.vegas('pause');  
					}
				 
		         return false;
			});

		    ta.click(function() { 
			
					slideshowRunning = false;

					th.attr('src', THEME_DIR + '/images/play.png');
					th.attr('title', '<?php _e("Play slideshow", "creative"); ?>');

					var idx = jQuery(this).parent('li').index();
					jQuery.vegas('stop')(backgroundList[idx]);
				 
		         return false;
			});
			 
			//PRELOAD
			if (preloadBackgrounds == true) jQuery.vegas('preload', backgroundList);
				
			//AUTOSLIDESHOW ON INIT
			if (autoSlideOnInit == true) {
				slideshowRunning = true;
				th.attr('src', THEME_DIR + '/images/pause.png');
				th.attr('title', '<?php _e("Stop slideshow", "creative"); ?>');

				jQuery.vegas('slideshow', { 
					delay: 5000,
					backgrounds: backgroundList
				})
			};
			 
			 
			//BORDER ON ACTIVE THUMB - MOUSEOVER / MOUSEOUT
			jQuery('body').bind('vegasload', function(e, bg) { 
			    var src = jQuery(bg).attr('src').replace('background', 'thumbnail');

				ti.css('border', '1px solid #3b3b3b');
				jQuery('img[src="' + src + '"]').css('border', '1px solid #<?php echo $baseColor; ?>');
			});
			
			var currentThumb = jQuery('#thumbs a:first img')[0];
			
			ti.click(function() {
				currentThumb = this;
			});

			ti.mouseover(function() {
				jQuery(this).css('border', '1px solid #<?php echo $baseColor; ?>');
			});

			ti.mouseout(function() {
				if (this != currentThumb) {
					jQuery(this).css('border', '1px solid #3b3b3b');
				}
			});
			
			
			
			
			
			//////////////////////////////////////////////////////
			//GALLERIA PLUGIN CONFIGURATION
			jQuery(".gallery").galleria({
				width: 700,
				height: 390,
				_toggleInfo: true,
				transition: 'fade',
				transitionSpeed: 700,
				popupLinks: true,
				imageCrop: true
		    });
			
			

			
			
			//////////////////////////////////////////////////////
			//MISCELLANEOUS
			
			//HIDE DIVS (FOR IE8 NOT READING CSS OPACITY)
			thumbs.animate({ opacity: 0 }, 0);
			alert.animate({ opacity: 0 }, 0);

			slider.animate({ opacity: 0 }, 0);

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
						<?php echo $tblockId; ?>.animate({ opacity: 0 }, 0);
					<?php
						endif;
				}
			?>
			

			//THUMBS LOADING
			thumbs.delay(1500).animate({ opacity: 1 }, 1000, 'easeOutQuart');
			
			//THUMBS HOVER ICON	 	//DEACTIVATED BY DEFAULT. REMOVE COMMENTS MARK TO ACTIVATE
			/*
			jQuery("#thumbs .rollover").append("<span></span>");
			jQuery("#thumbs .rollover").hover(function(){
				jQuery(this).children("span").stop(true, true).fadeIn(600);
			},function(){
				jQuery(this).children("span").stop(true, true).fadeOut(200);
			});
			*/
			
			
			//ALERT BOX LOADING & CLOSE
			alert.show().delay(2800).animate({ opacity: 1, bottom: '<?php echo $bottomAlert ; ?>' }, 1000, 'easeOutQuart');
		    jQuery('#close_bt').click(function() {
				alert.animate({ opacity: 0, bottom: '200px', height: 'toggle' }, 1000, 'easeOutQuart');
			});
			
			
			//MANAGE OPEN/CLOSE CONTENTS	
			jQuery('#menu a').click(openContent);
			jQuery('#close_base_bt').click(closeContent);

			
			//ABOUT - LATEST PROJECTS ZOOM ICON
			jQuery(".creative-block .rollover").append("<span></span>");
			jQuery(".creative-block .rollover").hover(function(){
				jQuery(this).children("span").stop(true, true).fadeIn(600);
			},function(){
				jQuery(this).children("span").stop(true, true).fadeOut(200);
			});
			
			// TIPSY - TOOLTIPS
			th.tipsy({ gravity: 'w', fade: true, offset: 17 });
			jQuery("#close_bt").tipsy({ gravity: 'w', fade: true, offset: 15 });
			jQuery("#close_base_bt").tipsy({ gravity: 'w', fade: true, offset: 44 });
			jQuery(".tooltip").tipsy({ gravity: 'w', fade: true, offset: 5 });
			jQuery("#social a").each(function() { 
				 jQuery(this).tipsy({ gravity: 'se', fade: true, offset: 7 });
			});
			
			
			//PRETTYPHOTO LIGHTBOX GALLERY
			jQuery('a[data-rel]').each(function() {
				jQuery(this).attr('rel', jQuery(this).data('rel'));
			});
			jQuery("a[rel^='prettyPhoto']").prettyPhoto({social_tools:false, deeplinking: false});

			
			//MENU DEEPLINKING
			var hash = location.href.match(/(#.+)/);
			if (hash && hash.length) {
				hash = hash[0];
				jQuery("#menu").find('a[href="'+hash+'"]').triggerHandler("click");
			};

			
			
			
			
			
			//////////////////////////////////////////////////////
			//CUSTOM FUNCTIONS

			//OPEN CONTENT FUNCTION
			function openContent(e) {

				var id = e.currentTarget.id;
				var t = 1500;	//EASING TIME VALUE - milliseconds
				clickedItem = document.getElementById(id);
				
				//LINK ID EXCEPTION
				if (id !== 'link' && activeItem !== clickedItem) {
				
					//SET CURRENT SECTION
					section = jQuery(clickedItem).attr("href");
				
					//BASE LAYER
					base.animate({ opacity: 1, left: '0px' }, t/2, 'easeInOutQuint');
					
					//PLACE AND ADJUST WIDTH OF MARKER
					marker.animate({ left: jQuery(clickedItem).position().left + 25 }, t/2, 'easeOutQuint' );
					markerImg.animate({ width: jQuery(clickedItem).outerWidth() + 10 }, t/2, 'easeOutQuint' );
					
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
								<?php echo $tblockId; ?>.stop(true,true).animate({ opacity: 0, top: '210px' }, t/2, 'easeOutQuint');
								<?php echo $tblockId; ?>.delay(t/2).hide(0);
							<?php
								endif;
						}
					?>
					
					//LOAD SECTION
					jQuery(section).stop(true,true).show().delay(t/2).animate({ opacity: 1, top: '190px' }, t, 'easeOutQuint');
					
					//DISABLE-ENABLED BUTTON
					jQuery(clickedItem).addClass("disabled");
					jQuery(activeItem).removeClass("disabled");
					activeItem = clickedItem;
					jQuery('.creative-content').jScrollPane();
				}
				
				//STOP VIMEO VIDEO
				
				//Froogaloop('player1').api('pause');
				//Froogaloop('player1').api('unload');
				

			};


			//CLOSE CONTENT
			function closeContent() {

				var t2 = 1000;	//EASING TIME VALUE - milliseconds

				//BASE LAYER
				base.animate({ opacity: 0, left: '-784px' }, t2, 'easeInQuint' );
				
				//MARKER
				marker.animate({ left: '-10px' }, t2, 'easeOutQuint' );
				markerImg.animate({ width: '10px' }, t2, 'easeOutQuint' );
				
				//DISABLE-ENABLED BUTTON
				jQuery(activeItem).removeClass("disabled");
				activeItem = "none";
				
				//CLOSE SECTION
				jQuery(section).stop(true,true).animate({ opacity: 0, top: '210px' }, t2, 'easeOutQuint');
				jQuery(section).delay(t2).hide(0);
				
				//STOP VIMEO VIDEO
				
				//Froogaloop('player1').api('pause');
				//Froogaloop('player1').api('unload');
				
			};

			
			
			
			
			//////////////////////////////////////////////////////
			//CONTACT FORM STUFF

			//AJAX SCRIPT FOR CONTACT FORM VALIDATION
			var formOpt = { beforeSubmit:showLoader, success:checkStatus };
			jQuery('#myForm').ajaxForm(formOpt);

			function showLoader(){
				jQuery("#loader_icon").fadeIn("slow");
			};
					 
			function checkStatus(status){
				jQuery("#loader_icon").fadeOut("slow");
				document.getElementById('ajax_loader').innerHTML = status;
			};

			
		});
	</script>