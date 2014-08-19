// DOM Ready
$(document).ready(function() {
	// jQuery Code
	
	
	
	
	// Responsive Projects, iPhone/iPad URL bar hides itself on pageload
	if (navigator.userAgent.indexOf('iPhone') != -1) {
	    addEventListener("load", function () {
	        setTimeout(hideURLbar, 0);
	    }, false);
	}
	
	function hideURLbar() {
	    window.scrollTo(0, 0);
	}
});

// theme script 
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

									var slider = jQuery('#slider');
											var about = jQuery('#about');
											var portfolio = jQuery('#portfolio');
											var blog = jQuery('#blog');
											var reel = jQuery('#reel');
											var contact = jQuery('#contact');
											var awards = jQuery('#awards');
								
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
						th.attr('title', 'Stop slideshow');
						
						jQuery.vegas('slideshow', { 
							delay: 5000,
							backgrounds: backgroundList
						})

					} else { 
		                slideshowRunning = false;
						
						th.attr('src', THEME_DIR + '/images/play.png');
						th.attr('title', 'Play slideshow');
						
		                jQuery.vegas('pause');  
					}
				 
		         return false;
			});

		    ta.click(function() { 
			
					slideshowRunning = false;

					th.attr('src', THEME_DIR + '/images/play.png');
					th.attr('title', 'Play slideshow');

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
				th.attr('title', 'Stop slideshow');

				jQuery.vegas('slideshow', { 
					delay: 5000,
					backgrounds: backgroundList
				})
			};
			 
			 
			//BORDER ON ACTIVE THUMB - MOUSEOVER / MOUSEOUT
			jQuery('body').bind('vegasload', function(e, bg) { 
			    var src = jQuery(bg).attr('src').replace('background', 'thumbnail');

				ti.css('border', '1px solid #3b3b3b');
				jQuery('img[src="' + src + '"]').css('border', '1px solid #f30b27');
			});
			
			var currentThumb = jQuery('#thumbs a:first img')[0];
			
			ti.click(function() {
				currentThumb = this;
			});

			ti.mouseover(function() {
				jQuery(this).css('border', '1px solid #f30b27');
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

									slider.animate({ opacity: 0 }, 0);
											about.animate({ opacity: 0 }, 0);
											portfolio.animate({ opacity: 0 }, 0);
											blog.animate({ opacity: 0 }, 0);
											reel.animate({ opacity: 0 }, 0);
											contact.animate({ opacity: 0 }, 0);
											awards.animate({ opacity: 0 }, 0);
								

			//THUMBS LOADING
			thumbs.delay(1500).animate({ opacity: 1 }, 1000, 'easeOutQuart');
			
	
			
			//ALERT BOX LOADING & CLOSE
			alert.show().delay(2800).animate({ opacity: 1, bottom: '158px' }, 1000, 'easeOutQuart');
		    jQuery('#close_bt').click(function() {
				alert.animate({ opacity: 0, bottom: '200px', height: 'toggle' }, 1000, 'easeOutQuart');
			});
			
			
			//MANAGE OPEN/CLOSE CONTENTS	
			jQuery('#menu a').click(openContent);
			jQuery('#close_base_bt').click(closeContent);

			
			// TIPSY - TOOLTIPS
			th.tipsy({ gravity: 'w', fade: true, offset: 17 });
			jQuery("#close_bt").tipsy({ gravity: 'w', fade: true, offset: 15 });
			jQuery("#close_base_bt").tipsy({ gravity: 'w', fade: true, offset: 44 });
			jQuery(".tooltip").tipsy({ gravity: 'w', fade: true, offset: 5 });
			jQuery("#social a").each(function() { 
				 jQuery(this).tipsy({ gravity: 'se', fade: true, offset: 7 });
			});
			
			
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
				

				
					//SET CURRENT SECTION
					alert([element, this]); // [object HTMLInputElement],[object Window]

					section = jQuery(clickedItem).attr("href");
				
					//BASE LAYER
					base.animate({ opacity: 1, left: '0px' }, t/2, 'easeInOutQuint');
					
					//PLACE AND ADJUST WIDTH OF MARKER
					marker.animate({ left: jQuery(clickedItem).position().left + 25 }, t/2, 'easeOutQuint' );
					markerImg.animate({ width: jQuery(clickedItem).outerWidth() + 10 }, t/2, 'easeOutQuint' );
					
													slider.stop(true,true).animate({ opacity: 0, top: '210px' }, t/2, 'easeOutQuint');
								slider.delay(t/2).hide(0);
															about.stop(true,true).animate({ opacity: 0, top: '210px' }, t/2, 'easeOutQuint');
								about.delay(t/2).hide(0);
															portfolio.stop(true,true).animate({ opacity: 0, top: '210px' }, t/2, 'easeOutQuint');
								portfolio.delay(t/2).hide(0);
															blog.stop(true,true).animate({ opacity: 0, top: '210px' }, t/2, 'easeOutQuint');
								blog.delay(t/2).hide(0);
															reel.stop(true,true).animate({ opacity: 0, top: '210px' }, t/2, 'easeOutQuint');
								reel.delay(t/2).hide(0);
															contact.stop(true,true).animate({ opacity: 0, top: '210px' }, t/2, 'easeOutQuint');
								contact.delay(t/2).hide(0);
															awards.stop(true,true).animate({ opacity: 0, top: '210px' }, t/2, 'easeOutQuint');
								awards.delay(t/2).hide(0);
												
					//LOAD SECTION
					jQuery(section).stop(true,true).show().delay(t/2).animate({ opacity: 1, top: '190px' }, t, 'easeOutQuint');
					
					//DISABLE-ENABLED BUTTON
					jQuery(clickedItem).addClass("disabled");
					jQuery(activeItem).removeClass("disabled");
					activeItem = clickedItem;
					jQuery('.creative-content').jScrollPane();
		
				
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
// end theme script 