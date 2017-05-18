/*
	Name: Photographer
	Description: Responsive HTML5 Template
	Version: 1.0
	Author: pixelwars
*/

(function($) { "use strict"; 
	
	
	/* DOCUMENT READY */
	$(function() {
		
		// ------------------------------
        // MASONRY GALLERY
		var gallery = $('.gallery');
		if(gallery.length) {
			gallery.each(function(index, element) {
				//wait for images
				$(element).imagesLoaded( function() {
					
					$(element).masonry();
					
				});
			});	
		}
		// ------------------------------
		
		
		
		// ------------------------------
		// KEN SLIDER
		var ken = $('.ken-slider');
		if(ken.length) {
			ken.slippry({
				adaptiveHeight: false,
				captions: false,
				pager: false,
				controls: false,
				autoHover: false,
				transition: ken.data('animation'), // fade, horizontal, kenburns, false
				kenZoom: 120,
				speed: ken.data('speed') // time the transition takes (ms)
			});
		}
		// ------------------------------
		
		
		// ------------------------------
		// PHOTOWALL
		photowall();
		// ------------------------------
		
		
		// ------------------------------
		// GALLERY COLLAGE LAYOUT
		var resizeTimer = null;
		$(window).bind('resize', function() {
			// hide all the images until we resize them
			// set the element you are scaling i.e. the first child nodes of ```.Collage``` to opacity 0
			$('.pw-collage > a').css("opacity", 0);
			// set a timer to re-apply the plugin
			if (resizeTimer) { 
				clearTimeout(resizeTimer);
				}
			resizeTimer = setTimeout(collage(), 200);
		});
		// ------------------------------
		
		
		// ------------------------------
		// FADE SLIDER
		var fadeSlider = $('.fade-slider');
		if(fadeSlider.length) {
			fadeSlider.find('.fs-slide').each(function() {
                $(this).css('background-image', 'url(' + $(this).find('img').attr('src') + ')');
            });
			fadeSlider.imagesLoaded( { background: true }, function() {
				
				fadeSlider.addClass('loaded');
				var firstSlide = fadeSlider.find('.fs-slide').first();
				fadeImage(firstSlide);	  
				//console.log('.fadeSlider background image loaded');
				
			});
		}
		
		// fn : FADE IMAGE
		function fadeImage(slide) {
			
			var animationTime = (0.85 / (slide.index()+2)) + 0.1 + 's';
			
			slide.css({ 
				'animation-duration': animationTime, 
				'-webkit-animation-duration':  animationTime
				});
			
			slide.addClass('fade-in');
			slide.on('animationend webkitAnimationEnd oAnimationEnd MSAnimationEnd',   
				function(e) {
					if(slide.is(':last-child')) {
					  fadeSlider.addClass('finito');
					} else {
						// recursive call : happy developer :P
						fadeImage(slide.next());
						slide.removeClass('fade-in');
						slide.hide();
					}
			});  
		}
		// ------------------------------
		
		
		// ------------------------------
        // OWL-CAROUSEL
		var owl = $('.owl-carousel');
		if(owl.length) {
			owl.each(function(index, element) {
				//wait for images
				$(element).imagesLoaded( function() {
					
					//remove loading
					$(element).find('.loading').remove();
					
					var items = $(element).data('items');
					$(element).owlCarousel({
						loop: 				$(element).data('loop'),
						center : 			$(element).data('center'),
						mouseDrag : 		$(element).data('mouse-drag'),
						dots : 				$(element).data('dots'),
						nav : 				$(element).data('nav'),
						autoplay : 			$(element).data('autoplay'),
						autoplaySpeed : 	$(element).data('autoplay-speed'),
						autoplayTimeout : 	$(element).data('autoplay-timeout'),
    					autoWidth : 		$(element).data('auto-width'),
						autoplayHoverPause :$(element).hasClass('fs-slider') ? false : true,
						navText :           [$(element).data('nav-text-prev'),$(element).data('nav-text-next')],
						animateOut: $(element).hasClass('fs-slider') ? 'fadeOut' : '',
    					//autoHeight: true,
						responsive:{
							0:		{ items: 1 },
							768:	{ items: items <= 2 ? items : 2 },
							1200:	{ items: items <= 3 ? items : 3 },
							1600:	{ items: items }
						}
					});
					
					
				});
			});	
		}
		// ------------------------------
		
		
		// ------------------------------
        // MAGNIFIC POPUP
		var mfp = $('.mfp-gallery');
		if(mfp.length) {
			mfp.each(function(index, element) {
				$(element).magnificPopup({
					  delegate: 'a',
					  type: 'image',
					  image: {
						  markup: '<div class="mfp-figure">'+
									'<div class="mfp-close"></div>'+
									'<div class="mfp-img"></div>'+
								  '</div>' +
								  '<div class="mfp-bottom-bar">'+
								    '<div class="mfp-title"></div>'+
								    '<div class="mfp-counter"></div>'+
								  '</div>', // Popup HTML markup. `.mfp-img` div will be replaced with img tag, `.mfp-close` by close button
						
						  cursor: 'mfp-zoom-out-cur', // Class that adds zoom cursor, will be added to body. Set to null to disable zoom out cursor. 
						  
						  titleSrc: 'title', // Attribute of the target element that contains caption for the slide.
						  // Or the function that should return the title. For example:
						  // titleSrc: function(item) {
						  //   return item.el.attr('title') + '<small>by Marsel Van Oosten</small>';
						  // }
						
						  verticalFit: true, // Fits image in area vertically
						
						  tError: '<a href="%url%">The image</a> could not be loaded.' // Error message
						},
						gallery: {
						  enabled:true,
						  tCounter: '<span class="mfp-counter">%curr% / %total%</span>' // markup of counter
						},
					  mainClass: 'mfp-zoom-in',
					  tLoading: '',
					  removalDelay: 300, //delay removal by X to allow out-animation
					  callbacks: {
						imageLoadComplete: function() {
						  var self = this;
						  setTimeout(function() {
							self.wrap.addClass('mfp-image-loaded');
						  }, 16);
						},
						close: function() {
						  this.wrap.removeClass('mfp-image-loaded');
						}
					  },
					  closeBtnInside: false,
					  closeOnContentClick: true,
					  midClick: true
					});
			});	
		}
		// ------------------------------
		
		
		// ------------------------------
        // HEADER MENU TOGGLE
        $('.menu-toggle').on( "click", function() {
            $('html').toggleClass('is-menu-toggled-on');
        });
		// ------------------------------

        
		// ------------------------------
		/* SOCIAL FEED WIDGET */
		// see plugin github page for documentation : https://github.com/pixel-industry/Social-Photo-Stream-jQuery-Plugin
		var socialFeed = $('.social-feed');
		if(socialFeed.length) {
			socialFeed.each(function() {
				$(this).socialstream({
					socialnetwork: $(this).data("social-network"),
					limit: $(this).data("limit"),
					username: $(this).data("username"),
					accessToken: $(this).data("access-token") /* for instagram or dribbble */
					//picasaAlbumId: $(this).data("picasa-album-id"), /* for picassa only */
					//apikey: $(this).data("api-key") /* for youtube only */
				});
			});	
		}
		// ------------------------------
		
        
		// ------------------------------
		// FluidBox : Zoomable Images
		$('.fluidbox-gallery a').fluidbox();
		$('.entry-content > p a, .wp-caption a').each(function(index, element) {
            if($(this).attr('href').match(/\.(jpeg|jpg|gif|png)$/) != null) {
				$(this).fluidbox();
				}
        });
		if(!($('html').hasClass('no-fluidbox'))) {
			$('.gallery a').fluidbox();
			}
        // ------------------------------

		
		// ------------------------------
		// remove click delay on touch devices
		FastClick.attach(document.body);
		// ------------------------------
		

		// ------------------------------
		// TABS
		$('.tabs').each(function() {
			if(!$(this).find('.tab-titles li a.active').length) {
				$(this).find('.tab-titles li:first-child a').addClass('active');
				$(this).find('.tab-content > div:first-child').show();
			} else {
				$(this).find('.tab-content > div').eq($(this).find('.tab-titles li a.active').parent().index()).show();	
			}
		});
		
		$('.tabs .tab-titles li a').on( "click", function() {
			if($(this).hasClass('active')) { return; }
			$(this).parent().siblings().find('a').removeClass('active');
			$(this).addClass('active');
			$(this).parents('.tabs').find('.tab-content > div').hide().eq($(this).parent().index()).show();
			return false;
		});
		// ------------------------------
		
		
		// ------------------------------
		// TOGGLES
		var toggleSpeed = 300;
		$('.toggle h4.active + .toggle-content').show();
	
		$('.toggle h4').on("click", function() {
			if($(this).hasClass('active')) { 
				$(this).removeClass('active');
				$(this).next('.toggle-content').stop(true,true).slideUp(toggleSpeed);
			} else {
				
				$(this).addClass('active');
				$(this).next('.toggle-content').stop(true,true).slideDown(toggleSpeed);
				
				//accordion
				if($(this).parents('.toggle-group').hasClass('accordion')) {
					$(this).parent().siblings().find('h4').removeClass('active');
					$(this).parent().siblings().find('.toggle-content').stop(true,true).slideUp(toggleSpeed);
				}
				
			}
			return false;
		});
		// ------------------------------
		
		
		
		// ------------------------------
		// Fitvids.js : fluid width video embeds
		$("body").fitVids();
		// ------------------------------
		
		
		
		// ------------------------------
		// UNIFORM
		$("select:not([multiple]), input:checkbox, input:radio, input:file").uniform();
		var ua = navigator.userAgent.toLowerCase();
		var isAndroid = ua.indexOf("android") > -1;
		if(isAndroid) {
			$('html').addClass('android');
		}
		// ------------------------------
		
		
		
		// ------------------------------
		// FORM VALIDATION
		// comment form validation fix
		$('#commentform').addClass('validate-form');
		$('#commentform').find('input,textarea').each(function(index, element) {
            if($(this).attr('aria-required') == "true") {
				$(this).addClass('required');
			}
			if($(this).attr('name') == "email") {
				$(this).addClass('email');
			}
		});
		
		// validate form
		if($('.validate-form').length) {
			$('.validate-form').each(function() {
					$(this).validate();
				});
		}
		// ------------------------------
		


		// ------------------------------
		// GOOGLE MAP
		/*
			custom map with google api
			check out the link below for more information about api usage
			https://developers.google.com/maps/documentation/javascript/examples/marker-simple
		*/
		function initializeMap() {
			if($('.map').length) {
				var styles = [{"featureType":"landscape","stylers":[{"saturation":-100},{"lightness":65},{"visibility":"on"}]},{"featureType":"poi","stylers":[{"saturation":-100},{"lightness":51},{"visibility":"simplified"}]},{"featureType":"road.highway","stylers":[{"saturation":-100},{"visibility":"simplified"}]},{"featureType":"road.arterial","stylers":[{"saturation":-100},{"lightness":30},{"visibility":"on"}]},{"featureType":"road.local","stylers":[{"saturation":-100},{"lightness":40},{"visibility":"on"}]},{"featureType":"transit","stylers":[{"saturation":-100},{"visibility":"simplified"}]},{"featureType":"administrative.province","stylers":[{"visibility":"off"}]},{"featureType":"water","elementType":"labels","stylers":[{"visibility":"on"},{"lightness":-25},{"saturation":-100}]},{"featureType":"water","elementType":"geometry","stylers":[{"hue":"#ffff00"},{"lightness":-25},{"saturation":-97}]}];
				var mapCanvas = $('#map-canvas');
				var myLatlng = new google.maps.LatLng(mapCanvas.data("latitude"),mapCanvas.data("longitude"));
				var mapOptions = {
					zoom: mapCanvas.data("zoom"),
					center: myLatlng,
					disableDefaultUI: true,
					styles: styles
				}
				var map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);
				
				var marker = new google.maps.Marker({
				  position: myLatlng,
				  map: map
		  	});
			}
		  
		}
		if($('.map').length) {
			google.maps.event.addDomListener(window, 'load', initializeMap);
		}
		// ------------------------------
				
		
		
		// ------------------------------
		/* jQuery Ajax Mail Send Script */	
		var contactForm = $( '#contact-form' );
		var $submit = contactForm.find('.submit');
		
		contactForm.submit(function()
		{
			if (contactForm.valid())
			{
				$submit.addClass("active loading");
				var formValues = contactForm.serialize();
				
				$.post(contactForm.attr('action'), formValues, function(data)
				{
					if ( data == 'success' )
					{
						setTimeout(function() { 
							$submit.removeClass("loading").addClass("success"); 
							contactForm.clearForm();
						},2000);
					}
					else
					{
						$submit.removeClass("loading").addClass("error");
					}
				});
			}
			
			return false
		});

		$.fn.clearForm = function() {
		  return this.each(function() {
		    var type = this.type, tag = this.tagName.toLowerCase();
		    if (tag == 'form')
		      return $(':input',this).clearForm();
		    if (type == 'text' || type == 'password' || tag == 'textarea')
		      this.value = '';
		    else if (type == 'checkbox' || type == 'radio')
		      this.checked = false;
		    else if (tag == 'select')
		      this.selectedIndex = -1;
		  });
		};
		// ------------------------------
		
		
		
		// ------------------------------
		// GALLERY LOAD ONE BY ONE
		var lazy_gallery = $('.pw-collage.load-one-by-one');
		if(lazy_gallery.length) {
			//lazy_gallery.find('img').addClass('no-auto-height');
			collage();	
			}
		//
		// ------------------------------
		
		
		
		// ------------------------------
		// HOME LANDING FULLSCREEN VIDEO
		var fs_video = $('.home-wrap .fluid-width-video-wrapper');
		if(fs_video.length) {
			fs_video.wrap( "<div class='fs-video'></div>" );
			fs_video = $('.fs-video');
			bgVideo(fs_video);
			$( window ).resize(function() {
				bgVideo(fs_video);
				//setTimeout(bgVideo(fs_video), 1500);
			});
		}
		
		
		
			var resizeTimer = null;
			$(window).bind('resize', function() {
				if (resizeTimer) clearTimeout(resizeTimer);
				resizeTimer = setTimeout(bgVideo(fs_video), 200);
			});

		
		
		
		
    });
    // DOCUMENT READY
	
	
	
	// ------------------------------
	// FULL SCREEN BG VIDEO
	function bgVideo(fs_video) {
		
		var videoW = fs_video.find('iframe').width(),
			videoH = fs_video.find('iframe').height(),
			screenW = $(window).width(),
			screenH = $(window).height();
			
		var video_ratio =  videoW / videoH;
		var screen_ratio = screenW / screenH;
		
		if(video_ratio > screen_ratio) {
			var diffW = screenH / videoH;
			var newWidth = videoW  * diffW;				
			fs_video.css( {'width' : newWidth, 'margin-left' : -((newWidth-screenW)/2), 'margin-top' : 0 });	
		} else {
			var diffH = screenH / videoH;
			var newHeight = screenH  * diffH;	
			fs_video.css( {'width' : "100%", 'margin-left' : 0, 'margin-top' : -((videoH-screenH)/2) });		
		}
	}
	// ------------------------------
	
	
	
	// WINDOW ONLOAD
	window.onload = function() {
		
		// html addclass : loaded
		$('html').addClass('loaded');
		
		// GALLERY COLLAGE LAYOUT
		collage();
		
	
		// ------------------------------
        // FULL WIDTH IMAGES
		fullWidthImages();
		// ------------------------------
		
		
		// ------------------------------
		// Rotating Words
		var rotate_words = $('.rotate-words'),
			interval = 3000;
		if(rotate_words.length) {
			
			var next_word_index = 0;
			interval = rotate_words.data("interval");
			
			if(Modernizr.csstransforms3d) {
			
				rotate_words.each(function(index, element) {
					$(element).find('span').eq(0).addClass('active');
					setInterval(function(){
						next_word_index = $(element).find('.active').next().length ? $(element).find('.active').next().index() : 0;
						$(element).find('.active').addClass('rotate-out').removeClass('rotate-in active');
						$(element).find('span').eq(next_word_index).addClass('rotate-in active').removeClass('rotate-out');
					},interval);
				});
	
			}
			else {
				
				rotate_words.each(function(index, element) {
					$(element).find('span').eq(0).addClass('active').show();
					setInterval(function(){
						next_word_index = $(element).find('.active').next().length ? $(element).find('.active').next().index() : 0;
						$(element).find('.active').removeClass('active').slideUp(500);
						$(element).find('span').eq(next_word_index).addClass('active').slideDown(500);
					},interval);
				});
			}
		}
		// ------------------------------
	
	};
	// WINDOW ONLOAD	
	
	
	
	
	// ------------------------------------------------------------
	// ------------------------------------------------------------
		// FUNCTIONS
	// ------------------------------------------------------------
	// ------------------------------------------------------------
	
	
	
	// ------------------------------
	// FULL WIDTH IMAGES
	function fullWidthImages() { 
		$('.full-width-image').each(function(index, element) {
            $(element).css("min-height", $(element).find('img').height());
			$( window ).resize(function() {
			  $(element).css("min-height", $(element).find('img').height());
			});
        });
	}
	// ------------------------------
	
	
	// ------------------------------
	// GALLERY COLLAGE LAYOUT
	function collage() {
		var collage = $('.pw-collage');
		var mobile = $(window).width() < 768;
		var row_height = mobile ? collage.data('mobile-row-height') : collage.data('row-height');
		if(collage.length) {
			collage.removeClass('pw-collage-loading');
			collage.collagePlus({
				
				'targetHeight' : row_height,
				'effect' : collage.data('effect'),
				'allowPartialLastRow' : true
				
			});
		}
	}
	// ------------------------------
	
	
	// ------------------------------
	// PHOTOWALL - ri-grid
	function photowall() {
		var riGrid = $('.ri-grid');
		if(riGrid.length) {
			
			if($('html').hasClass('home-landing')) {
			
				riGrid.gridrotator( {
					rows : 5,
					columns : 9,
					maxStep : riGrid.data('max-step'), // 1 to 3
					interval : riGrid.data('interval'), // in ms
					
					// animation type
					// showHide || fadeInOut || slideLeft || 
					// slideRight || slideTop || slideBottom || 
					// rotateLeft || rotateRight || rotateTop || 
					// rotateBottom || scale || rotate3d || 
					// rotateLeftScale || rotateRightScale || 
					// rotateTopScale || rotateBottomScale || random
					animType : riGrid.data('animation'),
					
					w1500 : {
						rows : 5,
						columns : 8
					},
					w1200 : {
						rows : 6,
						columns : 7
					},
					w1024 : {
						rows : 7,
						columns : 5
					},
					w768 : {
						rows : 7,
						columns : 4
					},
					w480 : {
						rows : 7,
						columns : 3
					},
					w320 : {
						rows : 6,
						columns : 3
					}
				});
				
				
			} else {
			
				riGrid.gridrotator( {
					rows : 3,
					columns : 12,
					maxStep : riGrid.data('max-step'), // 1 to 3
					interval : riGrid.data('interval'), // in ms
					
					// animation type
					// showHide || fadeInOut || slideLeft || 
					// slideRight || slideTop || slideBottom || 
					// rotateLeft || rotateRight || rotateTop || 
					// rotateBottom || scale || rotate3d || 
					// rotateLeftScale || rotateRightScale || 
					// rotateTopScale || rotateBottomScale || random
					animType : riGrid.data('animation'),
					
					w1500 : {
						rows : 3,
						columns : 12
					},
					w1200 : {
						rows : 3,
						columns : 13
					},
					w1024 : {
						rows : 3,
						columns : 12
					},
					w768 : {
						rows : 3,
						columns : 8
					},
					w480 : {
						rows : 3,
						columns : 7
					},
					w320 : {
						rows : 2,
						columns : 4
					}
				});
			}// else
		}
	}
	// ------------------------------
	

})(jQuery);
