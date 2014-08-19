(function($){
	$(document).ready(function(){
		var $et_backgrounds = $('#et_backgrounds').addClass('main_bg'),
			$et_bg_image = $et_backgrounds.find('img'),
			$et_loader = $('#et_loader'),
			$et_main_header = $('#main_header'),
			$et_main_content = $('#main_content'),
			et_window_width = $(window).width(),
			et_window_height = $(window).height(),
			$logo_area = $('#logo_area'),
			$main_menu = $('#main_menu'),
			et_is_animated = false,
			menu_slide_speed = 400,
			$et_footer_menu = null,
			$et_page_content = null,
			$et_big_arrow = null,
			$thumb_switcher = null,
			access_without_ajax = false,
			et_preload_image = document.createElement('img'),
			footer_menu_click = false,
			go_to_standart_homepage = false,
			homepage_menu_clicked = false,
			temp_content_html = '',
			first_animation = true,
			et_fullscreen_slideshow = false,
			et_is_ie7 = $('html#ie7').length;

		et_init_scripts();
		et_js_improvements();

		$main_menu.find('li').each( function(){
			var $this_li = $(this),
				li_text = $this_li.find('a').html();

			$this_li.find('a').html( '<span class="main_text">' + li_text + '</span>' + '<span class="menu_slide">' + li_text + '</span>' )
		} );
		$main_menu.find('a').bind('click', { menu: 'home' }, et_load_link_with_ajax );

		et_create_footer_menu();

		$et_main_content.append('<div id="et_page_content"></div>');
		$('body').append('<div id="temp_content"></div>');
		$('<div id="small_thumbs"><ul id="thumb_switcher"></ul><a href="#" id="thumb_prev"></a><a href="#" id="thumb_next"></a></div>').appendTo('body');
		$et_page_content = $('#et_page_content');
		$thumb_switcher = $('#thumb_switcher');

		$('body').append('<a id="big_arrow_left" class="big_arrow" href="#"></a><a id="big_arrow_right" class="big_arrow" href="#"></a>');
		$et_big_arrow = $('a.big_arrow').hide();

		et_preload_image.src = et_site_data.theme_url + '/images/main_bg.png';

		function et_load_link_with_ajax( event ){
			if ( et_is_animated ) return false;

			footer_menu_click = ( event.data.menu === 'footer' ) ? true : false;
			homepage_menu_clicked = ( event.data.menu === 'home' ) ? true : false;

			$et_this_link = $(this);

			if ( $et_this_link.attr('href').indexOf(et_site_data.site_url) == -1 ) return;

			if ( $et_this_link.closest('li').hasClass('external') || $et_this_link.hasClass('external') ){
				event.preventDefault();
				window.location.href = $et_this_link.attr('href');
			}

			if ( $et_this_link.parent().is('.wp-pagenavi') ){
				pagenavi_clicked = true;

				pagenavi_prev = $('.wp-pagenavi span.current').text();
				pagenavi_next = $et_this_link.text();
			}

			$.address.value( $et_this_link.attr('href').replace( et_site_data.site_url, '' ) );

			return false;
		}

		$.address.crawlable(true).change( function(event) {
			if ( event.value.indexOf('wp-admin') != -1 || event.value.indexOf('wp-login') != -1 || event.value.indexOf('wp-content/') != -1 ) {
				window.location.href = et_site_data.site_url + event.value;
				return;
			}

			if ( event.value ) {
				// event.value == '/#' - IE7 fix
				if ( event.value == '/' || event.value == '/#' || ( et_site_data.qtranslate_lang && event.value == ( '/' + et_site_data.qtranslate_lang ) ) || ( et_site_data.wpml_lang && event.value == ( '/' + et_site_data.wpml_lang ) ) ) {
					if ( ! $('body').hasClass('home') ) {
						go_to_standart_homepage = true;

						if ( window.location.href !== et_site_data.site_url && first_animation ){
							go_to_standart_homepage = false;
							return;
						}
					} else {
						go_to_standart_homepage = false;
						return;
					}
				} else if ( event.value.match(/\/comment-(\d)+/) ){
					return;
				}

				et_is_animated = true;
				$et_loader.css({opacity:0,'display':'block'}).animate({opacity:1},500);
				if ( $('#small_thumbs').is(':visible') ) $('#small_thumbs').animate({opacity:0},1000);

				if ( et_is_ie7 && go_to_standart_homepage ){
					window.location = et_site_data.site_url;
				}

				$('#temp_content').html('').load( et_site_data.site_url+event.value+' #wrapper', function(){
					var $temp_menu_current_li = $('#temp_content').find('#main_menu>ul>li.current-menu-item'),
						temp_menu_current_li_id,
						is_homepage = $('body').hasClass('home'),
						body_class = $('#temp_content').find('#content').attr('class');

					temp_content_html = '';

					// detect the current menu item to use for the footer menu
					$et_footer_menu.find('li.current-menu-item').removeClass('current-menu-item');
					if ( $temp_menu_current_li.length ) {
						temp_menu_current_li_id = $temp_menu_current_li.attr('id');
						$et_footer_menu.find('li#'+temp_menu_current_li_id).addClass('current-menu-item');
					}

					if ( $('#et_slideshow_fs').length ) $('#et_slideshow_fs').remove();
					$('#temp_content').find('#main_header').remove();
					$('#content').removeClass();
					$('body').removeClass().attr('class', body_class );

					$('#thumb_switcher').html('');
					$et_page_content = $('#et_page_content');
					temp_content_html = $('#temp_content').find('#main_content').html();

					if ( is_homepage ){
						$main_menu.slideUp( menu_slide_speed, function(){
							et_ajax_load_new_page('menu');
						} );
					} else {
						et_ajax_load_new_page('no_menu');
					}

					first_animation = false;
				} );
			}
		});

		function et_ajax_load_new_page( menu ){
			var animation_speed = 400,
				big_arrow_left,
				big_arrow_right,
				main_content_html;

			et_fullscreen_slideshow = false;

			if ( go_to_standart_homepage ) {
				if ( !$('#temp_header').length ) $('#main_content').append( '<div id="temp_header">' + $('.jspPane').html() + '</div>' );
				$('#temp_header').fadeIn( animation_speed );

				$('#main_header').css( { 'display' : 'block' } );
				$et_footer_menu.animate( { opacity : 0, bottom : '-48px' }, animation_speed );
				$('#temp_header #logo_area, #temp_header #main_menu').css( { 'display' : 'block', 'opacity' : '0' } );
				$('.jspContainer, #temp_header #et_page_content').css('display','none');
				$('a#close_button').css( 'display', 'none' );
				$('#temp_header #logo_area').animate( { opacity : 1 }, animation_speed );

				$et_main_content.css({'display':'block'}).animate( { top : ( ( et_window_height - $('#temp_header #logo_area').innerHeight() ) / 2 ), width : '352px', height : $('#temp_header #logo_area').innerHeight(), marginLeft : '-176px' }, animation_speed+200, 'easeInOutCubic', function(){
					$('#temp_header #main_menu').css( { 'display' : 'none', 'opacity' : '1' } );
					$(this).css( 'height', 'auto' );
				}).addClass('home_content');

				$et_main_header = $('#temp_header #main_header');
				$main_menu = $('#temp_header #main_menu');

				big_arrow_left = parseInt( $('#big_arrow_left').css('left') );
				big_arrow_right = parseInt( $('#big_arrow_right').css('left') );
				$('#big_arrow_left').animate({opacity : 0,left: (big_arrow_left-70) }, 500, function(){ $(this).hide(); });
				$('#big_arrow_right').animate({opacity : 0,left: (big_arrow_right+70) }, 500, function(){ $(this).hide(); });
				go_to_standart_homepage = false;
				footer_menu_click = false;

				et_animate_background_images();
				et_init_scripts();

				return;
			}

			if ( !footer_menu_click ){
				$logo_area = $('#logo_area');

				if ( $('body').hasClass('et_fullscreen_mode') ) {
					$et_main_content.css( { 'display' : 'none' } );
					//$('.jspPane #main_header').css( { 'display' : 'none' } );
					$('#big_arrow_left, #big_arrow_right').hide();
					et_fullscreen_slideshow = true;
				}
				else $et_main_content.css( { 'display' : 'block' } );

				if ( menu == 'no_menu' ){
					$et_main_content.css( { top : '77px', width : '800px', height : ( et_window_height - 200 ), marginTop : 0, marginLeft : '-400px' } ).removeClass();
					$logo_area.hide();
					et_animate_background_images();
				} else {
					$et_main_content.animate( { top : '77px', width : '800px', height : ( et_window_height - 200 ), marginTop : 0, marginLeft : '-400px' }, animation_speed+200, 'easeInOutCubic', function(){
						et_animate_background_images();
					}).removeClass();
				}

				$logo_area.fadeOut( animation_speed );
				$('#temp_header').fadeOut( animation_speed );

				$et_footer_menu.css({'display':'block','opacity':0, bottom: '-48px'}).animate( { opacity : 1, bottom : '0' }, animation_speed, function(){
					$et_page_content.html( temp_content_html ).css( { 'display' : 'block' } ).animate( { opacity : 1 }, animation_speed );

					if ( typeof ( $et_main_content.data('jsp') ) !== 'undefined' ) {
						$('.jspContainer, #close_button').show();
						$et_main_content.data('jsp').reinitialise();
						$et_main_content.data('jsp').scrollTo(0,0);
					}

					$et_main_content.jScrollPane( { animateScroll: true } );
					if ( ! $('a#close_button').length ) $et_main_content.append('<a id="close_button" href="#"></a>');

					$et_main_content.find('.jspDrag').css( 'height', ( $('.jspDrag').height() - 18 ) );

					et_center_content_area();

					if ( $('#big_arrow_left').is(':hidden') && ! $('body').hasClass('et_fullscreen_mode') ){
						big_arrow_left = parseInt( $('#big_arrow_left').css('left') );
						big_arrow_right = parseInt( $('#big_arrow_right').css('left') );
						$('#big_arrow_left').css({'display':'block','opacity':0,left: (big_arrow_left+30)}).animate({opacity : 1,left:big_arrow_left}, 500);
						$('#big_arrow_right').css({'display':'block','opacity':0,left: (big_arrow_right-30)}).animate({opacity : 1,left:big_arrow_right}, 500);
					}

					et_init_scripts();
				} );
			} else {
				$et_main_content.animate( { top : '+=50' }, animation_speed, 'easeInOutQuad' ).animate( { top : '-1100px', opacity : 0 }, animation_speed, 'easeInOutQuad', function(){
					$et_page_content.css( { 'display' : 'block', 'opacity' : 1 } );

					if ( $('body').hasClass('et_fullscreen_mode') ) {
						$et_main_content.css( { 'display' : 'none' } );
						$('#big_arrow_left, #big_arrow_right').hide();
						et_fullscreen_slideshow = true;
						//$('.jspPane #main_header').css( { 'display' : 'none' } );
					}
					else {
						$et_main_content.css( { 'display' : 'block' } );

						if ( $('#big_arrow_left').is(':hidden') ){
							big_arrow_left = parseInt( $('#big_arrow_left').css('left') );
							big_arrow_right = parseInt( $('#big_arrow_right').css('left') );
							$('#big_arrow_left').css({'display':'block','opacity':0,left: (big_arrow_left+30)}).animate({opacity : 1,left:big_arrow_left}, 500);
							$('#big_arrow_right').css({'display':'block','opacity':0,left: (big_arrow_right-30)}).animate({opacity : 1,left:big_arrow_right}, 500);
						}
					}

					$et_page_content.html( temp_content_html );

					if ( typeof ( $et_main_content.data('jsp') ) !== 'undefined' ) {
						$et_main_content.data('jsp').reinitialise();
						$et_main_content.data('jsp').scrollTo(0,0);
					}
					else $et_main_content.jScrollPane( { animateScroll: true } );

					$et_main_content.find('.jspDrag').css( 'height', ( $('.jspDrag').height() - 18 ) );

					if ( ! $('a#close_button').length ) $et_main_content.append('<a id="close_button" href="#"></a>');

					et_init_scripts();
				} );
				$et_main_content.animate( { top : '150', opacity : 1 }, animation_speed, 'easeInOutQuad' ).animate( { top : '77' }, animation_speed, 'easeInOutQuad', function(){
					et_animate_background_images();
				} );
			}
		}

		$et_big_arrow.click(function(){
			var $current_footer_menu_item = $et_footer_menu.find('nav > ul > li.current-menu-item'),
				$footer_menu_li = $et_footer_menu.find('nav > ul > li'),
				footer_menu_links_num = $footer_menu_li.length,
				current_footer_item_index = $current_footer_menu_item.index(),
				$target_footer_li;

			if ( $(this).attr('id') == 'big_arrow_right' ) {
				if ( ( current_footer_item_index + 1 ) <= ( footer_menu_links_num - 1 ) ) $target_footer_li = $footer_menu_li.filter(':eq(' + (current_footer_item_index + 1) + ')');
				else $target_footer_li = $footer_menu_li.filter(':eq(0)');
			} else {
				if ( current_footer_item_index > 0 ) $target_footer_li = $footer_menu_li.filter(':eq(' + (current_footer_item_index - 1) + ')');
				else $target_footer_li = $footer_menu_li.filter(':eq(' + ( footer_menu_links_num - 1 ) + ')');
			}

			$target_footer_li.find(' > a').trigger('click');

			return false;
		});

		$('#big_arrow_left').hover( function(){
			$(this).stop(true, true).animate( { opacity : .5, left: ( $(this).data('left_x') - 15 ) }, 500 );
		}, function(){
			$(this).stop(true, true).animate( { opacity : 1, left: $(this).data('left_x') }, 500 );
		} );

		$('#big_arrow_right').hover( function(){
			$(this).stop(true, true).animate( { opacity : .5, left: ( $(this).data('right_x') + 15 ) }, 500 );
		}, function(){
			$(this).stop(true, true).animate( { opacity : 1, left: $(this).data('right_x') }, 500 );
		} );

		$('#main_content').delegate('a', 'click', function( event ) {
			var link_href = $(this).attr('href');

			if ( et_is_animated ) return;

			if ( $(this).hasClass('fancybox') ) return;

			if ( link_href.indexOf('wp-admin') != -1 ) return;

			if ( $(this).hasClass('external') ) return;

			if ( link_href.indexOf(et_site_data.site_url) != -1 ){
				// toggle page animation only if the link is not inside the main menu
				footer_menu_click = $(this).closest('#main_menu').length ? false : true;

				$.address.value( link_href.replace( et_site_data.site_url, '' ) );
				return false;
			}
		});

		$('body').delegate('#thumb_prev, #thumb_next', 'click', function(){
			var small_slide_num = $('#thumb_switcher li').length,
				active_index = $('#et_backgrounds li.active_slide').index(),
				go_to_index;

			if ( $(this).is('#thumb_next') ){
				if ( ( active_index + 1 ) >= small_slide_num ) go_to_index = 0;
				else go_to_index = active_index + 1;
			} else {
				if ( ( active_index - 1 ) < 0 ) go_to_index = small_slide_num - 1;
				else go_to_index = active_index - 1;
			}

			$('#thumb_switcher').find('a').filter(':eq('+go_to_index+')').trigger('click');

			return false;
		} );

		$('body').delegate('#thumb_switcher a', 'click', function(){
			var next_slide_index = $(this).parent('li').index(),
				current_slide_index = $('#et_backgrounds li.active_slide').index();

			if ( next_slide_index === current_slide_index ) return false;

			$(this).parent('li').addClass('active_small_slide').css('opacity',1).siblings().removeClass().css('opacity',0.5);

			$('#et_backgrounds.main_bg').find('li').removeClass('previous_slide');
			$('#et_backgrounds.main_bg').find('li:eq(' + current_slide_index + ')').removeClass('active_slide').addClass('previous_slide').end().find('li:eq(' + next_slide_index + ')').addClass('active_slide').css('opacity',0).animate({ opacity : 1 }, 400, function(){
				$('#et_backgrounds.main_bg').find('li.previous_slide').removeClass('previous_slide');
			} );

			$('#et_slideshow_fs > div').filter(':eq('+current_slide_index+')').hide();
			$('#et_slideshow_fs > div').filter(':eq('+next_slide_index+')').show();

			return false;
		} );

		$(window).load( function(){
			var fade_in_speed = 700,
				content_width = $et_main_content.width(),
				et_site_url = et_site_data.site_url;

			if ( ! et_is_ie7 ){
				$('#main_menu a, #footer_menu a').live({
					mouseenter: function(){
						if ( ! $(this).parent('li').hasClass('current-menu-item') || $(this).closest('#main_menu').length )
							$(this).find('span.main_text').animate( { 'marginTop' : '-45px' }, 400 );
					},
					mouseleave: function(){
						$(this).find('span.main_text').stop(true,true).animate( { 'marginTop' : '0' }, 400 );
					}
				});
			}

			$et_main_header.live( {
				mouseenter: function(){
					$main_menu.css({'display':'none','opacity':'1'}).stop(true,true).slideDown(menu_slide_speed);
				},
				mouseleave: function(){
					$main_menu.slideUp(menu_slide_speed);
				}
			} );

			$('.gleam_gallery_image').live({
				mouseenter: function(){
					$(this).find('.gleam_info').css({'display':'block', opacity:0,top:0}).stop(true,true).animate({opacity:1,top:68},500);
					$(this).find('.gallery_overlay').css({'display':'block', opacity:0}).stop(true,true).animate({opacity:1},500);
				},
				mouseleave: function(){
					$(this).find('.gleam_info').stop(true,true).animate({opacity:0,top:0},500);
					$(this).find('.gallery_overlay').stop(true,true).animate({opacity:0},500);
				}
			});

			$et_loader.animate( {'opacity' : 0}, fade_in_speed, function(){ $(this).css({'display':'none', 'backgroundColor':'rgba(0,0,0,0.8)'}); } );

			$et_main_header.css( { 'display' : 'block' } );
			$et_main_content.css( { 'display' : 'block', opacity : 0 } );
			$et_backgrounds.css( { 'display' : 'block', opacity : 0 } ).animate( {'opacity' : 1}, fade_in_speed );
			$et_backgrounds.find('li:first').addClass( 'active_slide' );

			$et_bg_image.each( function(){
				var $this_image = $(this);

				$this_image.data( 'width', $this_image.width() ).data( 'height', $this_image.height() );
			});

			if ( et_site_data.qtranslate_lang && window.location.href.search('lang=') !== -1 ) et_site_url += et_site_data.qtranslate_lang;

			if ( et_site_data.wpml_lang && window.location.href.search('lang=') !== -1 ) et_site_url += et_site_data.wpml_lang;

			if ( window.location.href.search('#!/') == -1 && window.location.href !== et_site_url && window.location.href !== ( et_site_url + '#' ) ) {
				access_without_ajax = true;
				$et_main_content.css( { opacity : 1, top : '77px', width : '800px', height : ( et_window_height - 200 ), marginTop : 0, marginLeft : '-400px' } );
				$logo_area.hide();
				$main_menu.hide();
				$et_footer_menu.css({'display':'block','opacity':1, bottom: '0'})
				$('#content').appendTo( $et_page_content );
				$et_main_content.jScrollPane( { animateScroll: true } );
				et_center_content_area();
				et_init_scripts();
			} else if ( window.location.href === et_site_url || window.location.href === ( et_site_url + '#' ) ) {
				$et_main_content.css( { 'margin-left' : '-' + ( content_width / 2 ) + 'px', top: ( ( et_window_height - $et_main_content.height() ) / 2 - 100 ) } ).animate( { opacity : 1, 'top' : ( ( et_window_height - $et_main_content.height() ) / 2 ) }, fade_in_speed, 'easeInOutQuad' );
			} else {
				$et_main_content.css( { 'margin-left' : '-' + ( content_width / 2 ) + 'px' } ).animate( { opacity : 1, 'top' : '77px' }, fade_in_speed, 'easeInOutQuad' );
			}

			et_resize_background_images();

			// center the bottom menu
			$('#footer_menu #secondary_menu').width( $( '#footer_menu #menu-new' ).innerWidth() );
		});

		$(window).resize( function(){
			et_window_width = $(window).width();
			et_window_height = $(window).height();
			et_resize_background_images();
			et_center_content_area();
		});

		function et_animate_background_images(){
			var small_images_output, $et_active_li, images_descriptions = '';

			if ( $('#temp_content').find('#et_backgrounds').length ){
				et_window_height = $(window).height();
				$et_backgrounds.find('li').addClass('to_remove');
				$('#temp_content').find('#et_backgrounds li').css('opacity','0').addClass('new_slide').appendTo( $et_backgrounds );

				$et_bg_image = $('#et_backgrounds.main_bg').find('img');
				$et_bg_image.each( function( index ){
					var $this_image = $(this);

					if ( typeof ( $this_image.data('width') ) === 'undefined' )
						$this_image.data( 'width', $this_image.width() ).data( 'height', $this_image.height() );

					// we detected original sizes for all images, now we can resize all images
					if ( ( index + 1 ) === $et_bg_image.length ) {
						et_resize_background_images();
						$('#et_backgrounds.main_bg li.active_slide').addClass('previous_slide');
					}
				});

				// wait until the first background image is finished loading
				$("<img/>").load(function(){
					$('#et_backgrounds.main_bg li.active_slide').removeClass('active_slide');

					$et_active_li = $('#et_backgrounds.main_bg li.active_slide').length ? $('#et_backgrounds.main_bg li.active_slide').removeClass('active_slide').siblings('li.new_slide:first') : $('#et_backgrounds.main_bg li.new_slide:first');

					$et_active_li.addClass('active_slide').css('opacity',0).animate({ opacity : 1 }, 500, function(){
						var small_thumbs_width;

						$(this).siblings('li.to_remove').remove();
						$('#et_backgrounds.main_bg li').removeClass('new_slide').css('opacity','1');

						if ( $('#et_backgrounds.main_bg li').length > 1 ){
							$('#et_backgrounds.main_bg li').each( function(){
								var $this_element_img = $(this).find('img');
								small_images_output += '<li><a href="#">' + '<img src="' + $this_element_img.attr('data-smallimage') + '" width="43" height="43" />' + '<span class="overlay"></span></a></li>';
								if ( et_fullscreen_slideshow )
									images_descriptions += '<div class="et_fs_slide">' + '<h2>' + $this_element_img.attr('data-image_title') + '</h2>'
															+ '<p>' + $this_element_img.attr('data-image_desc') + '</p>' + '</div>';
							} );
							$(small_images_output).appendTo('#thumb_switcher');
							if ( et_fullscreen_slideshow ) $('body').append('<div id="et_slideshow_fs">' + images_descriptions + '</div>');
							$('#et_slideshow_fs > div:first').show();

							$('#thumb_switcher li').css('opacity',0.5).filter(':first').css('opacity',1).addClass('active_small_slide');
							$('#thumb_switcher li:not(.active_small_slide)').hover( function(){
								$(this).stop(true,true).animate({opacity:1},500);
							}, function(){
								if ( ! $(this).hasClass('active_small_slide') ) $(this).stop(true,true).animate({opacity:0.5},500);
							} );

							small_thumbs_width = $('#small_thumbs').innerWidth();
							$('#small_thumbs').css({'display':'block','opacity':0,'marginLeft': '-'+(small_thumbs_width/2)+'px'}).animate({opacity:1},1000);
						}
					});
				}).attr("src", $('#et_backgrounds.main_bg li.new_slide:first img').attr("src"));
			}

			$('#temp_content').html('');
			et_is_animated = false;
			$et_loader.animate({opacity:0},500,function(){
				$(this).css( { 'display' : 'none' } );
			});
		}

		function et_resize_background_images(){
			$et_bg_image.each( function(){
				var $this_image = $(this),
					image_width = $this_image.data('width'),
					image_height = $this_image.data('height'),
					image_ratio = image_height / image_width,
					browser_window_ratio = et_window_height / et_window_width;

				if ( browser_window_ratio > image_ratio ){
					$this_image.height( et_window_height ).width( et_window_height / image_ratio );
				} else {
					$this_image.height( et_window_width * image_ratio ).width( et_window_width );
				}
			});
		}

		function et_center_content_area(){
			var content_width = $et_main_content.width(),
				content_height = $et_main_content.height(),
				main_menu_height = $main_menu.is(':visible') ? 0 : $main_menu.height();

			if ( $et_main_content.hasClass('home_content') ){
				$et_main_content.css( { 'margin-left' : '-' + ( content_width / 2 ) + 'px', 'margin-top' : '-' + ( (content_height + main_menu_height)  / 2 ) + 'px', 'top' : et_window_height / 2 } );
			} else {
				$et_main_content.css( 'height', ( et_window_height - 200 ) );
				if ( typeof ( $et_main_content.data('jsp') ) !== 'undefined' ) $et_main_content.data('jsp').reinitialise();
			}

			$et_main_content.find('.jspDrag').css( 'height', ( $('.jspDrag').height() - 18 ) );

			et_big_arrows_align();
		}

		function et_big_arrows_align() {
			var left_button_x,
				right_button_x;

			left_button_x = ( et_window_width / 2 ) - 530;
			right_button_x = ( et_window_width / 2 ) + 448;

			$('#big_arrow_left').css( 'left', left_button_x ).data( 'left_x', left_button_x );
			$('#big_arrow_right').css( 'left', right_button_x ).data( 'right_x', right_button_x );
		}

		function et_js_improvements(){
			var $main_menu_li = $main_menu.find('>ul>li');

			$main_menu_li.filter(':odd').addClass('odd');
			if ( $main_menu_li.length % 2 == 1 ) {
				$main_menu_li.filter(':eq('+($main_menu_li.length-2)+')').addClass( 'border_fix' );
				$main_menu_li.filter(':eq('+($main_menu_li.length-3)+')').addClass( 'border_fix' );
				$main_menu_li.filter(':eq('+($main_menu_li.length-1)+')').addClass( 'last_item' );
			}
		}

		function et_create_footer_menu(){
			$('<div id="footer_menu"></div>').appendTo('body');
			$et_footer_menu = $('#footer_menu');
			$main_menu.clone().appendTo('#footer_menu');
			$et_footer_menu.find('nav').attr('id','secondary_menu').find('a').bind('click', { menu: 'footer' }, et_load_link_with_ajax );
		}

		function et_init_scripts(){
			var $blog_image = $('.blog_image'),
				$comment_form = jQuery('form#commentform'),
				images_num;

			$blog_image.hover( function(){
				$(this).find('.description').stop(true,true).css({'display':'block',opacity:0}).animate( { opacity : 1, bottom: '0px' }, 500 );
			}, function(){
				$(this).find('.description').stop(true,true).animate( { opacity : 0, bottom: '-30px' }, 500 );
			} );

			$et_bg_image = $et_backgrounds.find('img');
			images_num = $et_bg_image.length;
			$et_bg_image.each( function( index ){
				var $this_image = $(this);

				if ( ! $this_image.data( 'width' ) ) $this_image.data( 'width', $this_image.width() ).data( 'height', $this_image.height() );
				if ( index === ( images_num - 1 ) ) et_resize_background_images();
			});

			$('.et-learn-more').not('.et-open').find('.learn-more-content').css( { 'visibility' : 'visible', 'display' : 'none' } );

			$('.gallery-item a').addClass('fancybox').attr('rel','group');
			$.getScript( et_site_data.theme_url + '/epanel/page_templates/js/et-ptemplates-frontend.js');
			et_init_plugin_fixes();

			et_shortcodes_init();

			if ( typeof et_gleam_lb !== 'undefined' ) $.getScript( et_gleam_lb.plugin_url + '/js/custom.js');

			$comment_form.find('input:text, textarea').each(function(index,domEle){
				var $et_current_input = jQuery(domEle),
					$et_comment_label = $et_current_input.siblings('label'),
					et_comment_label_value = $et_current_input.siblings('label').text();
				if ( $et_comment_label.length ) {
					$et_comment_label.hide();
					if ( $et_current_input.siblings('span.required') ) {
						et_comment_label_value += $et_current_input.siblings('span.required').text();
						$et_current_input.siblings('span.required').hide();
					}
					$et_current_input.val(et_comment_label_value);
				}
			}).live('focus',function(){
				var et_label_text = jQuery(this).siblings('label').text();
				if ( jQuery(this).siblings('span.required').length ) et_label_text += jQuery(this).siblings('span.required').text();
				if (jQuery(this).val() === et_label_text) jQuery(this).val("");
			}).live('blur',function(){
				var et_label_text = jQuery(this).siblings('label').text();
				if ( jQuery(this).siblings('span.required').length ) et_label_text += jQuery(this).siblings('span.required').text();
				if (jQuery(this).val() === "") jQuery(this).val( et_label_text );
			});

			// remove placeholder text before form submission
			$comment_form.submit(function(){
				$comment_form.find('input:text, textarea').each(function(index,domEle){
					var $et_current_input = jQuery(domEle),
						$et_comment_label = $et_current_input.siblings('label'),
						et_comment_label_value = $et_current_input.siblings('label').text();

					if ( $et_comment_label.length && $et_comment_label.is(':hidden') ) {
						if ( $et_comment_label.text() == $et_current_input.val() )
							$et_current_input.val( '' );
					}
				});
			});
		}
	});

	$.getScript( et_site_data.theme_url + '/epanel/shortcodes/js/et_shortcodes_frontend.dev.js');

	function et_shortcodes_init(){
		var $et_shortcodes_tabs = $('.et-tabs-container, .tabs-left, .et-simple-slider, .et-image-slider');
		$et_shortcodes_tabs.each(function(i){
			var et_shortcodes_tab_class = $(this).attr('class'),
				et_shortcodes_tab_autospeed_class_value = /et_sliderauto_speed_(\d+)/g,
				et_shortcodes_tab_autospeed = et_shortcodes_tab_autospeed_class_value.exec( et_shortcodes_tab_class ),
				et_shortcodes_tab_auto_class_value = /et_sliderauto_(\w+)/g,
				et_shortcodes_tab_auto = et_shortcodes_tab_auto_class_value.exec( et_shortcodes_tab_class ),
				et_shortcodes_tab_type_class_value = /et_slidertype_(\w+)/g,
				et_shortcodes_tab_type = et_shortcodes_tab_type_class_value.exec( et_shortcodes_tab_class ),
				et_shortcodes_tab_fx_class_value = /et_sliderfx_(\w+)/g,
				et_shortcodes_tab_fx = et_shortcodes_tab_fx_class_value.exec( et_shortcodes_tab_class ),
				et_shortcodes_tab_apply_to_element = '.et-tabs-content',
				et_shortcodes_tab_settings = {};

			et_shortcodes_tab_settings.linksNav = $(this).find('.et-tabs-control li a');
			et_shortcodes_tab_settings.findParent = true;
			et_shortcodes_tab_settings.fx = et_shortcodes_tab_fx[1];
			et_shortcodes_tab_settings.auto = 'false' === et_shortcodes_tab_auto[1] ? false : true;
			et_shortcodes_tab_settings.autoSpeed = et_shortcodes_tab_autospeed[1];

			if ( 'simple' === et_shortcodes_tab_type[1] ){
				et_shortcodes_tab_settings = {};
				et_shortcodes_tab_settings.fx = et_shortcodes_tab_fx[1];
				et_shortcodes_tab_settings.auto = 'false' === et_shortcodes_tab_auto[1] ? false : true;
				et_shortcodes_tab_settings.autoSpeed = et_shortcodes_tab_autospeed[1];
				et_shortcodes_tab_settings.sliderType = 'simple';
				et_shortcodes_tab_apply_to_element = '.et-simple-slides';
			} else if ( 'images' === et_shortcodes_tab_type[1] ){
				et_shortcodes_tab_settings.sliderType = 'images';
				et_shortcodes_tab_settings.linksNav = '#' + $(this).attr('id') + ' .controllers a.switch';
				et_shortcodes_tab_settings.findParent = false;
				et_shortcodes_tab_settings.lengthElement = '#' + $(this).attr('id') + ' a.switch';
				et_shortcodes_tab_apply_to_element = '.et-image-slides';
			}

			$(this).find(et_shortcodes_tab_apply_to_element).et_shortcodes_switcher( et_shortcodes_tab_settings );
		});
	}
})(jQuery)