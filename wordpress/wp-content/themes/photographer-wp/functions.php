<?php

	function pixelwars_theme_enqueue_login()
	{
		wp_enqueue_script('jquery');
	}
	
	add_action( 'login_enqueue_scripts', 'pixelwars_theme_enqueue_login' );


/* ============================================================================================================================================= */


	function pixelwars_theme_enqueue_admin()
	{
		wp_enqueue_style( 'admin', get_template_directory_uri() . '/admin/admin.css' );
		wp_enqueue_style( 'thickbox' );
		
		wp_enqueue_script( 'thickbox' );
		wp_enqueue_script( 'media-upload' );
	}
	
	add_action( 'admin_enqueue_scripts', 'pixelwars_theme_enqueue_admin' );


/* ============================================================================================================================================= */


	function pixelwars_theme_enqueue()
	{
		global $pixelwars_subset;
		$extra_char_set = false;
		$pixelwars_subset = '&subset=';
		
		
		if ( get_option( 'char_set_latin', false ) ) { $pixelwars_subset .= 'latin,'; $extra_char_set = true; }
		if ( get_option( 'char_set_latin_ext', false ) ) { $pixelwars_subset .= 'latin-ext,'; $extra_char_set = true; }
		if ( get_option( 'char_set_cyrillic', false ) ) { $pixelwars_subset .= 'cyrillic,'; $extra_char_set = true; }
		if ( get_option( 'char_set_cyrillic_ext', false ) ) { $pixelwars_subset .= 'cyrillic-ext,'; $extra_char_set = true; }
		if ( get_option( 'char_set_greek', false ) ) { $pixelwars_subset .= 'greek,'; $extra_char_set = true; }
		if ( get_option( 'char_set_greek_ext', false ) ) { $pixelwars_subset .= 'greek-ext,'; $extra_char_set = true; }
		if ( get_option( 'char_set_vietnamese', false ) ) { $pixelwars_subset .= 'vietnamese,'; $extra_char_set = true; }
		if ( $extra_char_set == false ) { $pixelwars_subset = ""; } else { $pixelwars_subset = substr( $pixelwars_subset, 0, -1 ); }
		
		
		wp_enqueue_style( 'roboto', '//fonts.googleapis.com/css?family=Roboto:400,400italic,700,700italic' . $pixelwars_subset, null, null );
		wp_enqueue_style( 'montserrat', get_template_directory_uri() . '/css/fonts/montserrat/montserrat.css', null, null );
		wp_enqueue_style( 'bootstrap', get_template_directory_uri() . '/css/bootstrap.min.css', null, null );
		wp_enqueue_style( 'fontello', get_template_directory_uri() . '/css/fonts/fontello/css/fontello.css', null, null );
		wp_enqueue_style( 'uniform', get_template_directory_uri() . '/js/jquery.uniform/uniform.default.css', null, null );
		wp_enqueue_style( 'fluidbox', get_template_directory_uri() . '/js/jquery.fluidbox/fluidbox.css', null, null );
		wp_enqueue_style( 'owl-carousel', get_template_directory_uri() . '/js/owl-carousel/owl.carousel.css', null, null );
		wp_enqueue_style( 'photoswipe', get_template_directory_uri() . '/js/photo-swipe/photoswipe.css', null, null );
		wp_enqueue_style( 'photoswipe-default-skin', get_template_directory_uri() . '/js/photo-swipe/default-skin/default-skin.css', null, null );
		wp_enqueue_style( 'magnific-popup', get_template_directory_uri() . '/js/jquery.magnific-popup/magnific-popup.css', null, null );
		wp_enqueue_style( 'slippry', get_template_directory_uri() . '/js/slippry/slippry.css', null, null );
		wp_enqueue_style( 'main', get_template_directory_uri() . '/css/main.css', null, null );
		wp_enqueue_style( '768', get_template_directory_uri() . '/css/768.css', null, null );
		wp_enqueue_style( '992', get_template_directory_uri() . '/css/992.css', null, null );
		wp_enqueue_style( 'wp-fix', get_template_directory_uri() . '/css/wp-fix.css', null, null );
		wp_enqueue_style( 'theme-style', get_stylesheet_uri(), null, null );
		
		
		if ( is_singular() && comments_open() && get_option( 'thread_comments' ) )
		{
			wp_enqueue_script( 'comment-reply' );
		}
		
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'modernizr', get_template_directory_uri() . '/js/modernizr.min.js', null, null );
		wp_enqueue_script( 'fastclick', get_template_directory_uri() . '/js/fastclick.js', null, null, true );
		wp_enqueue_script( 'fitvids', get_template_directory_uri() . '/js/jquery.fitvids.js', null, null, true );
		wp_enqueue_script( 'validate', get_template_directory_uri() . '/js/jquery.validate.min.js', null, null, true );
		wp_enqueue_script( 'uniform', get_template_directory_uri() . '/js/jquery.uniform/jquery.uniform.min.js', null, null, true );
		wp_enqueue_script( 'imagesloaded', get_template_directory_uri() . '/js/imagesloaded.pkgd.min.js', null, null, true );
		wp_enqueue_script( 'fluidbox', get_template_directory_uri() . '/js/jquery.fluidbox/jquery.fluidbox.min.js', null, null, true );
		wp_enqueue_script( 'owl-carousel', get_template_directory_uri() . '/js/owl-carousel/owl.carousel.min.js', null, null, true );
		wp_enqueue_script( 'socialstream', get_template_directory_uri() . '/js/socialstream.jquery.js', null, null, true );
		wp_enqueue_script( 'collageplus', get_template_directory_uri() . '/js/jquery.collagePlus/jquery.collagePlus.min.js', null, null, true );
		wp_enqueue_script( 'photoswipe', get_template_directory_uri() . '/js/photo-swipe/photoswipe.min.js', null, null, true );
		wp_enqueue_script( 'photoswipe-ui-default', get_template_directory_uri() . '/js/photo-swipe/photoswipe-ui-default.min.js', null, null, true );
		wp_enqueue_script( 'photoswipe-run', get_template_directory_uri() . '/js/photo-swipe/photoswipe-run.js', null, null, true );
		wp_enqueue_script( 'gridrotator', get_template_directory_uri() . '/js/jquery.gridrotator.js', null, null, true );
		wp_enqueue_script( 'slippry', get_template_directory_uri() . '/js/slippry/slippry.min.js', null, null, true );
		wp_enqueue_script( 'magnific-popup', get_template_directory_uri() . '/js/jquery.magnific-popup/jquery.magnific-popup.min.js', null, null, true );
		wp_enqueue_script( 'jquery-masonry', get_template_directory_uri() . '/js/masonry.pkgd.min.js', null, null, true );
		wp_enqueue_script( 'view', get_template_directory_uri() . '/js/view.min.js?auto', null, null, true );
		wp_enqueue_script( 'main', get_template_directory_uri() . '/js/main.js', null, null, true );
		wp_enqueue_script( 'wp-fix', get_template_directory_uri() . '/js/wp-fix.js', null, null, true );
	}


/* ============================================================================================================================================= */


	function pixelwars_theme_setup()
	{
		add_action( 'wp_enqueue_scripts', 'pixelwars_theme_enqueue' );
		
		$lang_dir = get_template_directory() . '/languages';
		load_theme_textdomain( 'read', $lang_dir ); 
		
		$locale = get_locale();
		$locale_file = get_template_directory() . "/languages/$locale.php";
		
		if ( is_readable( $locale_file ) )
		{
			require_once( $locale_file );
		}
	}
	
	add_action( 'after_setup_theme', 'pixelwars_theme_setup' );


/* ============================================================================================================================================= */


	function pixelwars_theme_favicons()
	{
		$favicon = get_option( 'favicon', "" );
		
		if ( $favicon != "" )
		{
			?>

<link rel="shortcut icon" href="<?php echo esc_url( $favicon ); ?>">

			<?php
		}
		
		$apple_touch_icon = get_option( 'apple_touch_icon', "" );
		
		if ( $apple_touch_icon != "" )
		{
			?>

<link rel="apple-touch-icon-precomposed" href="<?php echo esc_url( $apple_touch_icon ); ?>">

			<?php
		}
	}
	
	add_action( 'wp_head', 'pixelwars_theme_favicons' );
	add_action( 'admin_head', 'pixelwars_theme_favicons' );
	add_action( 'login_head', 'pixelwars_theme_favicons' );


/* ============================================================================================================================================= */


	function pixelwars_custom_login_logo_url( $url )
	{
		return esc_url( home_url( '/' ) );
	}
	
	function pixelwars_custom_login_logo_title()
	{
		return get_bloginfo( 'name' );
	}
	
	function pixelwars_theme_login_logo()
	{
		$logo_login_hide = get_option( 'logo_login_hide', false );
		$logo_login = get_option( 'logo_login', "" );
		
		
		if ( $logo_login_hide )
		{
			echo '<style type="text/css"> h1 { display: none; } </style>';
		}
		else
		{
			if ( $logo_login != "" )
			{
				add_filter( 'login_headerurl', 'pixelwars_custom_login_logo_url' );
				add_filter( 'login_headertitle', 'pixelwars_custom_login_logo_title' );
				
				
				echo '<style type="text/css">
						h1 a
						{
							background-image: url( "' . esc_url( $logo_login ) . '" ) !important;
						}
					</style>';
			}
		}
	}
	
	add_action( 'login_head', 'pixelwars_theme_login_logo' );


/* ============================================================================================================================================= */


	function pixelwars_theme_wp_title( $title, $sep )
	{
		global $paged, $page;
		
		if ( is_feed() )
		{
			return $title;
		}
		
		$title .= get_bloginfo( 'name' );
		$site_description = get_bloginfo( 'description', 'display' );
		
		if ( $site_description && ( is_home() || is_front_page() ) )
		{
			$title = "$title $sep $site_description";
		}
		
		if ( $paged >= 2 || $page >= 2 )
		{
			$title = "$title $sep " . sprintf( __( 'Page %s', 'read' ), max( $paged, $page ) );
		}
		
		return $title;
	}
	
	add_filter( 'wp_title', 'pixelwars_theme_wp_title', 10, 2 );


/* ============================================================================================================================================= */


	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'post-formats', array( 'aside', 'audio', 'chat', 'gallery', 'image', 'link', 'quote', 'status', 'video' ) );
	add_theme_support( 'html5', array( 'comment-list', 'comment-form', 'search-form', 'gallery', 'caption' ) );
	add_theme_support( 'post-thumbnails', array( 'post', 'portfolio', 'page' ) );


/* ============================================================================================================================================= */


	add_image_size( 'pixelwars_theme_image_size_1', 768, 512, true ); // related posts
	add_image_size( 'pixelwars_theme_image_size_2', 220, 220, true ); // photowall
	
	add_image_size( 'pixelwars_theme_image_size_1000', 1000 ); // (Blog Regular feat-img), (Post Single feat-img)
	add_image_size( 'pixelwars_theme_image_size_760x507', 760, 507, true ); // (Portfolio Page feat-img)
	add_image_size( 'pixelwars_theme_image_size_nullx500', null, 400 ); // (Portfolio Single thumbnail for: Photo Gallery, Photo Gallery 2, Photo Gallery 3) --- (Note: Decreased to 400px.)
	
	add_image_size( 'pixelwars_theme_image_size_1600', 1600 ); // (Photo Gallery medium-size width)
	add_image_size( 'pixelwars_theme_image_size_nullx1600', null, 1600 ); // (Photo Gallery medium-size height)
	
	add_image_size( 'pixelwars_theme_image_size_1920', 1920 ); // (Photo Gallery 2 full-size width), (Photo Gallery 3 full-size width), (Full-size Image Resizing = 2K - 2MP - 1920px)
	add_image_size( 'pixelwars_theme_image_size_nullx1080', null, 1080 ); // (Photo Gallery 2 full-size height), (Photo Gallery 3 full-size height)
	
	add_image_size( 'pixelwars_theme_image_size_1400', 1400 ); // (Full-size Image Resizing = Medium - 1400px)
	add_image_size( 'pixelwars_theme_image_size_3840', 3840 ); // (Full-size Image Resizing = 4K - 8MP - 3840px Default)


/* ============================================================================================================================================= */


	if ( ! isset( $content_width ) )
	{
		$content_width = 740;
	}

	
/* ============================================================================================================================================= */


	if ( function_exists( 'add_editor_style' ) )
	{
		add_editor_style( 'custom-editor-style.css' );
	}


/* ============================================================================================================================================= */


	function pixelwars_theme_new_post_column_add( $columns )
	{
		return array_merge( $columns, array( 'pixelwars_post_feat_img' => __( 'Featured Image', 'read' ) ) );
	}
	
	add_filter( 'manage_posts_columns' , 'pixelwars_theme_new_post_column_add' );
	
	
	function pixelwars_theme_new_post_column_show( $column, $post_id )
	{
		if ( $column == 'pixelwars_post_feat_img' )
		{
			if ( has_post_thumbnail() )
			{
				the_post_thumbnail( 'thumbnail' );
			}
		}
	}
	
	add_action( 'manage_posts_custom_column' , 'pixelwars_theme_new_post_column_show', 10, 2 );


/* ============================================================================================================================================= */


	if ( function_exists( 'register_nav_menus' ) )
	{
		register_nav_menus( array( 'pixelwars_theme_menu_location_1' => __( 'Theme Navigation Menu', 'read' ) ) );
	}
	
	
	function pixelwars_wp_page_menu2( $args = array() )
	{
		$defaults = array(  'sort_column' => 'menu_order, post_title',
							'menu_class' => 'menu',
							'echo' => true,
							'link_before' => "",
							'link_after' => "" );
							
		$args = wp_parse_args( $args, $defaults );
		$args = apply_filters( 'wp_page_menu_args', $args );
		
		$menu = "";
		
		$list_args = $args;
		
		// Show Home in the menu
		if ( ! empty( $args['show_home'] ) )
		{
			if ( true === $args['show_home'] || '1' === $args['show_home'] || 1 === $args['show_home'] )
			{
				$text = __( 'Home', 'read' );
			}
			else
			{
				$text = $args['show_home'];
			}
			
			
			$class = "";
			
			if ( is_front_page() && !is_paged() )
			{
				$class = 'class="current_page_item"';
			}
			
			$menu .= '<li ' . $class . '><a href="' . home_url( '/' ) . '" title="' . esc_attr( $text ) . '">' . $args['link_before'] . $text . $args['link_after'] . '</a></li>';
			
			// If the front page is a page, add it to the exclude list
			if ( get_option( 'show_on_front' ) == 'page' )
			{
				if ( ! empty( $list_args['exclude'] ) )
				{
					$list_args['exclude'] .= ',';
				}
				else
				{
					$list_args['exclude'] = '';
				}
				
				$list_args['exclude'] .= get_option('page_on_front');
			}
		}
		
		$list_args['echo'] = false;
		$list_args['title_li'] = "";
		$menu .= str_replace( array( "\r", "\n", "\t" ), "", wp_list_pages( $list_args ) );
		
		if ( $menu )
		{
			$menu = '<ul class="menu-default">' . $menu . '</ul>';
		}
		
		$menu = $menu . "\n";
		$menu = apply_filters( 'wp_page_menu', $menu, $args );
		
		if ( $args['echo'] )
		{
			echo $menu;
		}
		else
		{
			return $menu;
		}
	}


/* ============================================================================================================================================= */


	if ( ! function_exists( 'pixelwars_theme_comments' ) ) :
	
		/*
			Template for comments and pingbacks.
			
			To override this walker in a child theme without modifying the comments template
			simply create your own pixelwars_theme_comments(), and that function will be used instead.
			
			Used as a callback by wp_list_comments() for displaying the comments.
		*/
		
		function pixelwars_theme_comments( $comment, $args, $depth )
		{
			$GLOBALS['comment'] = $comment;
			
			
			switch ( $comment->comment_type ) :
			
				case 'pingback' :
				
				case 'trackback' :
				
					// Display trackbacks differently than normal comments.
					?>
						<li id="comment-<?php comment_ID(); ?>" <?php comment_class(); ?>>
							<p>
								<?php
									_e( 'Pingback:', 'read' ); ?> <?php comment_author_link(); ?> <?php edit_comment_link( __( '(Edit)', 'read' ), '<span class="edit-link">', '</span>' );
								?>
							</p>
					<?php
				break;
				
				default :
				
					// Proceed with normal comments.
					global $post;
					
					?>
					
					<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
						<article id="comment-<?php comment_ID(); ?>" class="comment">
							<header class="comment-meta comment-author vcard">
								<?php
									echo get_avatar( $comment, 150 );
									
									
									printf( '<cite class="fn">%1$s %2$s</cite>',
											get_comment_author_link(),
											// If current post author is also comment author, make it known visually.
											( $comment->user_id === $post->post_author ) ? '<span></span>' : "" );
									
									
									printf( '<span class="comment-date">%3$s</span>',
											esc_url( get_comment_link( $comment->comment_ID ) ),
											get_comment_time( 'c' ),
											/* translators: 1: date, 2: time */
											sprintf( __( '%1$s at %2$s', 'read' ), get_comment_date(), get_comment_time() ) );
								?>
							</header>
							
							
							<?php
								if ( '0' == $comment->comment_approved ) :
									?>
										<p class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.', 'read' ); ?></p>
									<?php
								endif;
							?>
							
							
							<section class="comment-content comment">
								<?php
									comment_text();
								?>
								
								<?php
									edit_comment_link( __( 'Edit', 'read' ), '<p class="edit-link">', '</p>' );
								?>
							</section>
							
							<div class="reply">
								<?php
									comment_reply_link( array_merge( $args, array( 'reply_text' => __( 'Reply', 'read' ), 'after' => ' <span>&darr;</span>', 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) );
								?>
							</div>
						</article>
					<?php
				break;
				
			endswitch;
		}
		
	endif;


/* ============================================================================================================================================= */


	function pixelwars_theme_password_form()
	{
		global $post;
		
		$label = 'pwbox-'.( empty( $post->ID ) ? rand() : $post->ID );
		
		$o = '<form class="password-form" action="' . esc_url( site_url( 'wp-login.php?action=postpass', 'login_post' ) ) . '" method="post"><p>' . __( "This content is password protected. To view it please enter the password below:", 'read' ) . '</p><label for="' . $label . '">' . __( "Password:", 'read' ) . ' </label><input type="password" id="' . $label . '" name="post_password" class="post-password" size="20" maxlength="20" /><input type="submit" name="Submit" class="btn" value="' . esc_attr__( "Submit", 'read' ) . '" /></form>';
		
		return $o;
	}
	
	add_filter( 'the_password_form', 'pixelwars_theme_password_form' );


/* ============================================================================================================================================= */


	function pixelwars_theme_excerpt_password_form( $excerpt )
	{
		if ( post_password_required() )
		{
			$excerpt = get_the_password_form();
		}
		
		return $excerpt;
	}
	
	add_filter( 'the_excerpt', 'pixelwars_theme_excerpt_password_form' );


/* ============================================================================================================================================= */


	function pixelwars__title_format( $title )
	{
		return '%s';
	}
	
	add_filter( 'private_title_format', 'pixelwars__title_format' );
	add_filter( 'protected_title_format', 'pixelwars__title_format' );


/* ============================================================================================================================================= */


	function pixelwars_theme_excerpt_more( $more )
	{
		return '... <span class="more"><a class="more-link" href="'. get_permalink( get_the_ID() ) . '">' . __( 'Read More', 'read' ) . '</a></span>';
	}
	
	add_filter( 'excerpt_more', 'pixelwars_theme_excerpt_more' );


/* ============================================================================================================================================= */


	function pixelwars_theme_excerpt_max_charlength( $charlength )
	{
		$excerpt = get_the_excerpt();
		$charlength++;
		
		if ( mb_strlen( $excerpt ) > $charlength )
		{
			$subex = mb_substr( $excerpt, 0, $charlength - 5 );
			$exwords = explode( ' ', $subex );
			$excut = - ( mb_strlen( $exwords[ count( $exwords ) - 1 ] ) );
			
			if ( $excut < 0 )
			{
				echo mb_substr( $subex, 0, $excut );
			}
			else
			{
				echo $subex;
			}
			
			echo '...';
		}
		else
		{
			echo $excerpt;
		}
	}


/* ============================================================================================================================================= */


	function pixelwars_theme_custom_box_show_post_title_visibility( $post )
	{
		?>
			<div class="admin-inside-box">
				<?php
					wp_nonce_field( 'pixelwars_theme_custom_box_show_post_title_visibility', 'pixelwars_theme_custom_box_nonce_post_title_visibility' );
				?>
				
				<p>
					<?php
						$hide_post_title = get_option( $post->ID . 'hide_post_title', false );
						
						if ( $hide_post_title )
						{
							$hide_post_title_out = 'checked="checked"';
						}
						else
						{
							$hide_post_title_out = "";
						}
					?>
					<label for="hide_post_title"><input type="checkbox" id="hide_post_title" name="hide_post_title" <?php echo $hide_post_title_out; ?>> Hide title</label>
				</p>
			</div>
		<?php
	}
	
	function pixelwars_theme_custom_box_add_post_title_visibility()
	{
		add_meta_box( 'pixelwars_theme_custom_box_post_title_visibility_post', __( 'Title Visibility', 'read' ), 'pixelwars_theme_custom_box_show_post_title_visibility', 'post', 'side', 'high' );
		
		add_meta_box( 'pixelwars_theme_custom_box_post_title_visibility_page', __( 'Title Visibility', 'read' ), 'pixelwars_theme_custom_box_show_post_title_visibility', 'page', 'side', 'high' );
	}
	
	add_action( 'add_meta_boxes', 'pixelwars_theme_custom_box_add_post_title_visibility' );
	
	
	function pixelwars_theme_custom_box_save_post_title_visibility( $post_id )
	{
		if ( ! isset( $_POST['pixelwars_theme_custom_box_nonce_post_title_visibility'] ) )
		{
			return $post_id;
		}
		
		
		$nonce = $_POST['pixelwars_theme_custom_box_nonce_post_title_visibility'];
		
		if ( ! wp_verify_nonce( $nonce, 'pixelwars_theme_custom_box_show_post_title_visibility' ) )
        {
			return $post_id;
		}
		
		
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
        {
			return $post_id;
		}
		
		
		if ( 'page' == $_POST['post_type'] )
		{
			if ( ! current_user_can( 'edit_page', $post_id ) )
			{
				return $post_id;
			}
		}
		else
		{
			if ( ! current_user_can( 'edit_post', $post_id ) )
			{
				return $post_id;
			}
		}
		
		
		update_option( $post_id . 'hide_post_title', $_POST['hide_post_title'] );
	}
	
	add_action( 'save_post', 'pixelwars_theme_custom_box_save_post_title_visibility' );


/* ============================================================================================================================================= */


	if ( function_exists( 'register_sidebar' ) )
	{
		register_sidebar( array('name'          => __( 'Blog Sidebar', 'read' ),
								'id'            => 'pixelwars_blog_sidebar',
								'before_widget' => '<aside id="%1$s" class="widget %2$s">',
								'after_widget'  => '</aside>',
								'before_title'  => '<h3 class="widget-title"><span>',
								'after_title'   => '</span></h3>' ) );
		
		
		register_sidebar( array('name'          => __( 'Page Sidebar', 'read' ),
								'id'            => 'pixelwars_page_sidebar',
								'before_widget' => '<aside id="%1$s" class="widget %2$s">',
								'after_widget'  => '</aside>',
								'before_title'  => '<h3 class="widget-title"><span>',
								'after_title'   => '</span></h3>' ) );
		
		
		register_sidebar( array('name'          => __( 'Footer Social Icons', 'read' ),
								'id'            => 'pixelwars_footer_sidebar',
								'description'   => 'Use social media shortcodes with the Text widget in this widget location to add icons to your footer.',
								'before_widget' => "",
								'after_widget'  => "",
								'before_title'  => '<span style="display: none;">',
								'after_title'   => '</span>' ) );
		
		
		register_sidebar( array('name'          => __( 'Author Social Icons', 'read' ),
								'id'            => 'pixelwars_author_social_icons',
								'description'   => 'Use social media shortcodes with the Text widget in this widget location to add icons under the author info.',
								'before_widget' => "",
								'after_widget'  => "",
								'before_title'  => '<span style="display: none;">',
								'after_title'   => '</span>' ) );
		
		
		$sidebars_with_commas = get_option( 'sidebars_with_commas' );
		
		if ( $sidebars_with_commas != "" )
		{
			$sidebars = preg_split("/[\s]*[,][\s]*/", $sidebars_with_commas);
			
			foreach ( $sidebars as $sidebar_name )
			{
				register_sidebar( array('name'          => $sidebar_name,
										'id'            => $sidebar_name,
										'before_widget' => '<aside id="%1$s" class="widget %2$s">',
										'after_widget'  => '</aside>',
										'before_title'  => '<h3 class="widget-title">',
										'after_title'   => '</h3>' ) );
			}
		}
	}


/* ============================================================================================================================================= */


	function pixelwars_theme_custom_box_show_sidebar( $post )
	{
		?>
			<div class="admin-inside-box">
				<?php
					wp_nonce_field( 'pixelwars_theme_custom_box_show_sidebar', 'pixelwars_theme_custom_box_nonce_sidebar' );
				?>
				
				<p>
					<?php
						$my_sidebar = get_option( $post->ID . 'my_sidebar', 'pixelwars_page_sidebar' );
					?>
					<select name="my_sidebar">
						<option <?php if ( $my_sidebar == 'pixelwars_page_sidebar' ) { echo 'selected="selected"'; } ?> value="pixelwars_page_sidebar"><?php echo __( 'Page Sidebar', 'read' ); ?></option>
						<option <?php if ( $my_sidebar == 'pixelwars_blog_sidebar' ) { echo 'selected="selected"'; } ?> value="pixelwars_blog_sidebar"><?php echo __( 'Blog Sidebar', 'read' ); ?></option>
						
						<?php
							$sidebars_with_commas = get_option( 'sidebars_with_commas' );
							
							if ( $sidebars_with_commas != "" )
							{
								$sidebars = preg_split( "/[\s]*[,][\s]*/", $sidebars_with_commas );

								foreach ( $sidebars as $sidebar_name )
								{
									$selected = "";
									
									if ( $my_sidebar == $sidebar_name )
									{
										$selected = 'selected="selected"';
									}
									
									echo '<option ' . $selected . ' value="' . $sidebar_name . '">' . $sidebar_name . '</option>';
								}
							}
						?>
					</select>
				</p>
				
				<p class="howto">
					Select Page with Sidebar template.
				</p>
			</div>
		<?php
	}
	
	function pixelwars_theme_custom_box_add_sidebar()
	{
		add_meta_box( 'pixelwars_theme_custom_box_sidebar', __( 'Sidebar', 'read' ), 'pixelwars_theme_custom_box_show_sidebar', 'page', 'side', 'low' );
	}
	
	add_action( 'add_meta_boxes', 'pixelwars_theme_custom_box_add_sidebar' );
	
	
	function pixelwars_theme_custom_box_save_sidebar( $post_id )
	{
		if ( ! isset( $_POST['pixelwars_theme_custom_box_nonce_sidebar'] ) )
		{
			return $post_id;
		}
		
		
		$nonce = $_POST['pixelwars_theme_custom_box_nonce_sidebar'];
		
		if ( ! wp_verify_nonce( $nonce, 'pixelwars_theme_custom_box_show_sidebar' ) )
        {
			return $post_id;
		}
		
		
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
        {
			return $post_id;
		}
		
		
		if ( 'page' == $_POST['post_type'] )
		{
			if ( ! current_user_can( 'edit_page', $post_id ) )
			{
				return $post_id;
			}
		}
		else
		{
			if ( ! current_user_can( 'edit_post', $post_id ) )
			{
				return $post_id;
			}
		}
		
		
		update_option( $post_id . 'my_sidebar', $_POST['my_sidebar'] );
	}
	
	add_action( 'save_post', 'pixelwars_theme_custom_box_save_sidebar' );


/* ============================================================================================================================================= */


	class pixelwars_Flickr_Widget extends WP_Widget
	{
		public function __construct()
		{
			parent::__construct('pixelwars_flickr_widget',
								__( '- Flickr', 'read' ),
								array( 'description' => __( 'Flickr widget.', 'read' ) ) );
		}
		
		
		public function form( $instance )
		{
			if ( isset( $instance[ 'title' ] ) ) { $title = $instance[ 'title' ]; } else { $title = ""; }
			
			
			if ( isset( $instance[ 'user' ] ) ) { $user = $instance[ 'user' ]; } else { $user = ""; }
			
			if ( isset( $instance[ 'number_of_items' ] ) ) { $number_of_items = $instance[ 'number_of_items' ]; } else { $number_of_items = '8'; }
			
			
			?>
				<p>
					<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php echo __( 'Title:', 'read' ); ?></label>
					
					<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" value="<?php echo esc_attr( $title ); ?>">
				</p>
				
				
				<p>
					<label for="<?php echo esc_attr( $this->get_field_id( 'user' ) ); ?>"><?php echo __( 'User:', 'read' ); ?></label>
					
					<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'user' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'user' ) ); ?>" value="<?php echo esc_attr( $user ); ?>">
				</p>
				
				
				<p>
					<label for="<?php echo esc_attr( $this->get_field_id( 'number_of_items' ) ); ?>"><?php echo __( 'Number of items to show:', 'read' ); ?></label>
					
					<input type="text" id="<?php echo esc_attr( $this->get_field_id( 'number_of_items' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'number_of_items' ) ); ?>" size="3" value="<?php echo esc_attr( $number_of_items ); ?>">
				</p>
			<?php
		}
		
		
		public function update( $new_instance, $old_instance )
		{
			$instance = array();
			
			
			$instance['title'] = strip_tags( $new_instance['title'] );
			
			
			$instance['user'] = strip_tags( $new_instance['user'] );
			
			$instance['number_of_items'] = strip_tags( $new_instance['number_of_items'] );
			
			
			return $instance;
		}
		
		
		public function widget( $args, $instance )
		{
			extract( $args );
			
			
			$title = apply_filters( 'widget_title', $instance['title'] );
			
			
			$user = apply_filters( 'widget_user', $instance['user'] );
			
			$number_of_items = apply_filters( 'widget_number_of_items', $instance['number_of_items'] );
			
			
			echo $before_widget;
			
			
				if ( ! empty( $title ) )
				{
					echo $before_title . $title . $after_title;
				}
				
				
				?>
					<div class="flickr-badges flickr-badges-s">
						<script src="http://www.flickr.com/badge_code_v2.gne?size=s&amp;count=<?php echo $number_of_items; ?>&amp;display=random&amp;layout=x&amp;source=user&amp;user=<?php echo $user; ?>"></script>
					</div>
				<?php
			
			echo $after_widget;
		}
	}
	
	add_action( 'widgets_init', create_function( '', 'register_widget( "pixelwars_flickr_widget" );' ) );


/* ============================================================================================================================================= */


	class pixelwars_Social_Feed_Widget extends WP_Widget
	{
		public function __construct()
		{
			parent::__construct('pixelwars_social_feed_widget',
								__( '- Social Feed', 'read' ),
								array( 'description' => __( 'Social feed widget.', 'read' ) ) );
		}
		
		
		public function form( $instance )
		{
			if ( isset( $instance[ 'title' ] ) ) { $title = $instance[ 'title' ]; } else { $title = ""; }
			
			
			if ( isset( $instance[ 'network' ] ) ) { $network = $instance[ 'network' ]; } else { $network = ""; }
			
			if ( isset( $instance[ 'user' ] ) ) { $user = $instance[ 'user' ]; } else { $user = ""; }
			
			if ( isset( $instance[ 'number_of_items' ] ) ) { $number_of_items = $instance[ 'number_of_items' ]; } else { $number_of_items = '8'; }
			
			
			?>
				<p>
					<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php echo __( 'Title:', 'read' ); ?></label>
					
					<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" value="<?php echo esc_attr( $title ); ?>">
				</p>
				
				<p>
					<label for="<?php echo esc_attr( $this->get_field_id( 'network' ) ); ?>"><?php echo __( 'Network:', 'read' ); ?></label>
					
					<select class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'network' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'network' ) ); ?>">
						<option></option>
						<option <?php if ( $network == 'pinterest' ) { echo 'selected="selected"'; } ?> value="pinterest">Pinterest</option>
						<option <?php if ( $network == 'picasa' ) { echo 'selected="selected"'; } ?> value="picasa">Picasa</option>
					</select>
				</p>
				
				<p>
					<label for="<?php echo esc_attr( $this->get_field_id( 'user' ) ); ?>"><?php echo __( 'User:', 'read' ); ?></label>
					
					<input type="text" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'user' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'user' ) ); ?>" value="<?php echo esc_attr( $user ); ?>">
				</p>
				
				<p>
					<label for="<?php echo esc_attr( $this->get_field_id( 'number_of_items' ) ); ?>"><?php echo __( 'Number of items to show:', 'read' ); ?></label>
					
					<input type="text" id="<?php echo esc_attr( $this->get_field_id( 'number_of_items' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'number_of_items' ) ); ?>" size="3" value="<?php echo esc_attr( $number_of_items ); ?>">
				</p>
			<?php
		}
		
		
		public function update( $new_instance, $old_instance )
		{
			$instance = array();
			
			
			$instance['title'] = strip_tags( $new_instance['title'] );
			
			
			$instance['network'] = strip_tags( $new_instance['network'] );
			
			$instance['user'] = strip_tags( $new_instance['user'] );
			
			$instance['number_of_items'] = strip_tags( $new_instance['number_of_items'] );
			
			
			return $instance;
		}
		
		
		public function widget( $args, $instance )
		{
			extract( $args );
			
			
			$title = apply_filters( 'widget_title', $instance['title'] );
			
			
			$network = apply_filters( 'widget_network', $instance['network'] );
			
			$user = apply_filters( 'widget_user', $instance['user'] );
			
			$number_of_items = apply_filters( 'widget_number_of_items', $instance['number_of_items'] );
			
			
			echo $before_widget;
			
			
				if ( ! empty( $title ) )
				{
					echo $before_title . $title . $after_title;
				}
				
				
				?>
					<div class="social-feed" data-social-network="<?php echo esc_attr( $network ); ?>" data-username="<?php echo esc_attr( $user ); ?>" data-limit="<?php echo esc_attr( $number_of_items ); ?>"></div>
				<?php
			
			echo $after_widget;
		}
	}
	
	add_action( 'widgets_init', create_function( '', 'register_widget( "pixelwars_social_feed_widget" );' ) );


/* ============================================================================================================================================= */


	function pixelwars_create_post_type_portfolio()
	{
		$labels = array('name'               => __( 'Portfolio', 'read' ),
						'singular_name'      => __( 'Portfolio Item', 'read' ),
						'add_new'            => __( 'Add New', 'read' ),
						'add_new_item'       => __( 'Add New', 'read' ),
						'edit_item'          => __( 'Edit', 'read' ),
						'new_item'           => __( 'New', 'read' ),
						'all_items'          => __( 'All', 'read' ),
						'view_item'          => __( 'View', 'read' ),
						'search_items'       => __( 'Search', 'read' ),
						'not_found'          => __( 'No Items found', 'read' ),
						'not_found_in_trash' => __( 'No Items found in Trash', 'read' ),
						'parent_item_colon'  => '',
						'menu_name'          => 'Portfolio' );
		
		
		$args = array(  'labels' => $labels,
						'public' => true,
						'exclude_from_search' => false,
						'publicly_queryable'  => true,
						'show_ui'             => true,
						'query_var'           => true,
						'show_in_nav_menus'   => true,
						'capability_type'     => 'post',
						'hierarchical'        => false,
						'menu_position'       => 5,
						'supports'            => array( 'title', 'editor', 'thumbnail', 'comments', 'revisions' ),
						'rewrite'             => array( 'slug' => 'portfolio', 'with_front' => false ) );
		
		
		register_post_type( 'portfolio' , $args );
	}
	
	add_action( 'init', 'pixelwars_create_post_type_portfolio' );
	
	
	function pixelwars_updated_messages_portfolio( $messages )
	{
		global $post, $post_ID;
		
		$messages['portfolio'] = array( 0 => "", // Unused. Messages start at index 1.
										
										1 => sprintf( __( '<strong>Updated.</strong> <a target="_blank" href="%s">View</a>', 'read' ), esc_url( get_permalink( $post_ID) ) ),
										
										2 => __( 'Custom field updated.', 'read' ),
										
										3 => __( 'Custom field deleted.', 'read' ),
										
										4 => __( 'Updated.', 'read' ),
										
										// translators: %s: date and time of the revision
										5 => isset( $_GET['revision'] ) ? sprintf( __( 'Restored to revision from %s', 'read' ), wp_post_revision_title( ( int ) $_GET['revision'], false ) ) : false,
										
										6 => sprintf( __( '<strong>Published.</strong> <a target="_blank" href="%s">View</a>', 'read' ), esc_url( get_permalink( $post_ID) ) ),
										
										7 => __( 'Saved.', 'read' ),
										
										8 => sprintf( __( 'Submitted. <a target="_blank" href="%s">Preview</a>', 'read' ), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
										
										9 => sprintf( __( 'Scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview</a>', 'read' ),
										
										// translators: Publish box date format, see http://php.net/date
										date_i18n( __( 'M j, Y @ G:i', 'read' ), strtotime( $post->post_date ) ), esc_url( get_permalink( $post_ID) ) ),
										
										10 => sprintf( __( '<strong>Item draft updated.</strong> <a target="_blank" href="%s">Preview</a>', 'read' ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) ) );
		
		
		return $messages;
	}
	
	add_filter( 'post_updated_messages', 'pixelwars_updated_messages_portfolio' );
	
	
	function pixelwars_portfolio_columns( $pf_columns )
	{
		$pf_columns = array('cb'                   => '<input type="checkbox">',
							'title'                => __( 'Title', 'read' ),
							'pf_featured_image'    => __( 'Featured Image', 'read' ),
							'portfolio_type'       => __( 'Type', 'read' ),
							'departments'          => __( 'Departments', 'read' ),
							'pf_short_description' => __( 'Short Description', 'read' ),
							'author'               => __( 'Author', 'read' ),
							'comments'             => '<span class="vers"><div title="Comments" class="comment-grey-bubble"></div></span>',
							'date'                 => __( 'Date', 'read' ) );
		
		
		return $pf_columns;
	}
	
	add_filter( 'manage_edit-portfolio_columns', 'pixelwars_portfolio_columns' );
	
	
	function pixelwars_custom_columns_portfolio( $pf_column )
	{
		global $post, $post_ID;
		
		switch ( $pf_column )
		{
			case 'pf_featured_image':
			
				if ( has_post_thumbnail() )
				{
					the_post_thumbnail( 'thumbnail' );
				}
				
			break;
			
			case 'portfolio_type':
			
				$pf_type = get_option( $post->ID . 'pf_type', 'Standard' );
				
				echo $pf_type;
				
			break;
			
			case 'departments':
			
				$taxonomy = 'department';
				
				$terms_list = get_the_terms( $post_ID, $taxonomy );
				
				if ( ! empty( $terms_list ) )
				{
					$out = array();
					
					foreach ( $terms_list as $term_list )
					{
						$out[] = '<a href="edit.php?post_type=portfolio&department=' . $term_list->slug . '">' . $term_list->name . ' </a>';
					}
					
					echo join( ', ', $out );
				}
				
			break;
			
			case 'pf_short_description':
			
				$pf_short_description = stripcslashes( get_option( $post->ID . 'pf_short_description', "" ) );
				
				echo $pf_short_description;
				
			break;
		}
	}
	
	add_action( 'manage_posts_custom_column',  'pixelwars_custom_columns_portfolio' );
	
	
	function pixelwars_taxonomy_portfolio()
	{
		$labels_cat = array('name'              => __( 'Departments', 'read' ),
							'singular_name'     => __( 'Department', 'read' ),
							'search_items'      => __( 'Search', 'read' ),
							'all_items'         => __( 'All', 'read' ),
							'parent_item'       => __( 'Parent', 'read' ),
							'parent_item_colon' => __( 'Parent:', 'read' ),
							'edit_item'         => __( 'Edit', 'read' ),
							'update_item'       => __( 'Update', 'read' ),
							'add_new_item'      => __( 'Add New', 'read' ),
							'new_item_name'     => __( 'New Name', 'read' ),
							'menu_name'         => __( 'Departments', 'read' ) );
		
		
		register_taxonomy(  'department',
							array( 'portfolio' ),
							array(  'hierarchical' => true,
									'labels'       => $labels_cat,
									'show_ui'      => true,
									'public'       => true,
									'query_var'    => true,
									'rewrite'      => array( 'slug' => 'department' ) ) );
		
		
		$labels_tag = array('name'              => __( 'Portfolio Tags', 'read' ),
							'singular_name'     => __( 'Portfolio Tag', 'read' ),
							'search_items'      => __( 'Search', 'read' ),
							'all_items'         => __( 'All', 'read' ),
							'parent_item'       => __( 'Parent Tag', 'read' ),
							'parent_item_colon' => __( 'Parent Tag:', 'read' ),
							'edit_item'         => __( 'Edit', 'read' ),
							'update_item'       => __( 'Update', 'read' ),
							'add_new_item'      => __( 'Add New', 'read' ),
							'new_item_name'     => __( 'New Tag Name', 'read' ),
							'menu_name'         => __( 'Portfolio Tags', 'read' ) );
		
		
		register_taxonomy(  'portfolio_tag',
							array( 'portfolio' ),
							array(  'hierarchical' => false,
									'labels'       => $labels_tag,
									'show_ui'      => true,
									'public'       => true,
									'query_var'    => true,
									'rewrite'      => array( 'slug' => 'portfolio_tag' ) ) );
	}
	
	add_action( 'init', 'pixelwars_taxonomy_portfolio' );
	
	
	function pixelwars_taxonomy_filter_portfolio()
	{
		global $typenow;
		
		if ( $typenow == 'portfolio' )
		{
			$filters = array( 'department' );
			
			foreach ( $filters as $tax_slug )
			{
				$tax_obj = get_taxonomy( $tax_slug );
				
				$tax_name = $tax_obj->labels->name;
				
				$terms = get_terms( $tax_slug );
				
				echo '<select name="' .$tax_slug .'" id="' .$tax_slug .'" class="postform">';
				
					echo '<option value="">' . __( 'Show All', 'read' ) . ' ' .$tax_name .'</option>';
					
					foreach ( $terms as $term )
					{
						echo '<option value='. $term->slug, @$_GET[$tax_slug] == $term->slug ? ' selected="selected"' : '','>' . $term->name .' (' . $term->count .')</option>';
					}
				
				echo '</select>';
			}
		}
	}
	
	add_action( 'restrict_manage_posts', 'pixelwars_taxonomy_filter_portfolio' );
	
	
	function pixelwars_theme_custom_box_show_portfolio($post)
	{
		?>
			<?php
				wp_nonce_field('pixelwars_theme_custom_box_show_portfolio', 'pixelwars_theme_custom_box_nonce_portfolio');
			?>
			
			<h4>
				<?php
					echo __('Type', 'read');
				?>
			</h4>
			<p class="pf-type-wrap">
				<?php
					$pf_type = get_option($post->ID . 'pf_type', 'Standard');
				?>
				<label>
					<input type="radio" name="pf_type" <?php if ($pf_type == 'Standard') { echo 'checked="checked"'; } ?> value="Standard"> <?php echo __('Standard', 'read'); ?>
				</label>
				<br>
				<label>
					<input type="radio" name="pf_type" <?php if ($pf_type == 'Photo Gallery') { echo 'checked="checked"'; } ?> value="Photo Gallery"> <?php echo __('Photo Gallery', 'read'); ?>
				</label>
				<br>
				<label>
					<input type="radio" name="pf_type" <?php if ($pf_type == 'Photo Gallery 2') { echo 'checked="checked"'; } ?> value="Photo Gallery 2"> <?php echo __('Photo Gallery 2', 'read'); ?>
				</label>
				<br>
				<label>
					<input type="radio" name="pf_type" <?php if ($pf_type == 'Photo Gallery 3') { echo 'checked="checked"'; } ?> value="Photo Gallery 3"> <?php echo __('Photo Gallery 3', 'read'); ?>
				</label>
			</p>
			
			<hr>
			
			<h4>
				<?php
					echo __('Short Description', 'read');
				?>
			</h4>
			<p>
				<?php
					$pf_short_description = stripcslashes(get_option($post->ID . 'pf_short_description'));
				?>
				<textarea id="pf_short_description" name="pf_short_description" rows="4" cols="46" class="widefat"><?php echo $pf_short_description; ?></textarea>
			</p>
		<?php
	}
	
	function pixelwars_theme_custom_box_add_portfolio()
	{
		add_meta_box('pixelwars_theme_custom_box_portfolio', __('Details', 'read'), 'pixelwars_theme_custom_box_show_portfolio', 'portfolio', 'side', 'low');
	}
	
	add_action('add_meta_boxes', 'pixelwars_theme_custom_box_add_portfolio');
	
	
	function pixelwars_theme_custom_box_save_portfolio( $post_id )
	{
		if ( ! isset( $_POST['pixelwars_theme_custom_box_nonce_portfolio'] ) )
		{
			return $post_id;
		}
		
		
		$nonce = $_POST['pixelwars_theme_custom_box_nonce_portfolio'];
		
		if ( ! wp_verify_nonce( $nonce, 'pixelwars_theme_custom_box_show_portfolio' ) )
        {
			return $post_id;
		}
		
		
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
        {
			return $post_id;
		}
		
		
		if ( 'page' == $_POST['post_type'] )
		{
			if ( ! current_user_can( 'edit_page', $post_id ) )
			{
				return $post_id;
			}
		}
		else
		{
			if ( ! current_user_can( 'edit_post', $post_id ) )
			{
				return $post_id;
			}
		}
		
		
		update_option( $post_id . 'pf_type', $_POST['pf_type'] );
		update_option( $post_id . 'pf_short_description', $_POST['pf_short_description'] );
	}
	
	add_action( 'save_post', 'pixelwars_theme_custom_box_save_portfolio' );


/* ============================================================================================================================================= */


	/*
		This function filters the post content when viewing a post with the "chat" post format.  It formats the 
		content with structured HTML markup to make it easy for theme developers to style chat posts. The 
		advantage of this solution is that it allows for more than two speakers (like most solutions). You can 
		have 100s of speakers in your chat post, each with their own, unique classes for styling.
		
		@author David Chandra
		@link http://www.turtlepod.org
		@author Justin Tadlock
		@link http://justintadlock.com
		@copyright Copyright (c) 2012
		@license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
		@link http://justintadlock.com/archives/2012/08/21/post-formats-chat
		
		@global array $_post_format_chat_ids An array of IDs for the chat rows based on the author.
		@param string $content The content of the post.
		@return string $chat_output The formatted content of the post.
	*/
	
	
	function pixelwars_theme_post_format_chat_content( $content )
	{
		global $_post_format_chat_ids;
		
		
		/* If this is not a 'chat' post, return the content. */
		if ( !has_post_format( 'chat' ) )
		{
			return $content;
		}
		
		
		/* Set the global variable of speaker IDs to a new, empty array for this chat. */
		$_post_format_chat_ids = array();
		
		/* Allow the separator (separator for speaker/text) to be filtered. */
		$separator = apply_filters( 'my_post_format_chat_separator', ':' );
		
		/* Open the chat transcript div and give it a unique ID based on the post ID. */
		$chat_output = "\n\t\t\t" . '<div id="chat-transcript-' . esc_attr( get_the_ID() ) . '" class="chat-transcript">';
		
		/* Split the content to get individual chat rows. */
		$chat_rows = preg_split( "/(\r?\n)+|(<br\s*\/?>\s*)+/", $content );
		
		
		/* Loop through each row and format the output. */
		foreach ( $chat_rows as $chat_row )
		{
			/* If a speaker is found, create a new chat row with speaker and text. */
			if ( strpos( $chat_row, $separator ) )
			{
				/* Split the chat row into author/text. */
				$chat_row_split = explode( $separator, trim( $chat_row ), 2 );
				
				
				/* Get the chat author and strip tags. */
				$chat_author = strip_tags( trim( $chat_row_split[0] ) );
				
				
				/* Get the chat text. */
				$chat_text = trim( $chat_row_split[1] );
				
				
				/* Get the chat row ID (based on chat author) to give a specific class to each row for styling. */
				$speaker_id = pixelwars_theme_post_format_chat_row_id( $chat_author );
				
				
				/* Open the chat row. */
				$chat_output .= "\n\t\t\t\t" . '<div class="chat-row ' . sanitize_html_class( "chat-speaker-{$speaker_id}" ) . '">';
				
				
				/* Add the chat row author. */
				$chat_output .= "\n\t\t\t\t\t" . '<div class="chat-author ' . sanitize_html_class( strtolower( "chat-author-{$chat_author}" ) ) . ' vcard"><cite class="fn">' . apply_filters( 'my_post_format_chat_author', $chat_author, $speaker_id ) . '</cite>' . $separator . '</div>';
				
				
				/* Add the chat row text. */
				$chat_output .= "\n\t\t\t\t\t" . '<div class="chat-text"><p>' . str_replace( array( "\r", "\n", "\t" ), '', apply_filters( 'my_post_format_chat_text', $chat_text, $chat_author, $speaker_id ) ) . '</p></div>';
				
				
				/* Close the chat row. */
				$chat_output .= "\n\t\t\t\t" . '</div><!-- .chat-row -->';
			}
			/*
				If no author is found, assume this is a separate paragraph of text that belongs to the
				previous speaker and label it as such, but let's still create a new row.
			*/
			else
			{
				/* Make sure we have text. */
				if ( !empty( $chat_row ) )
				{
					/* Open the chat row. */
					$chat_output .= "\n\t\t\t\t" . '<div class="chat-row ' . sanitize_html_class( "chat-speaker-{$speaker_id}" ) . '">';
					
					
					/* Don't add a chat row author.  The label for the previous row should suffice. */
					
					
					/* Add the chat row text. */
					$chat_output .= "\n\t\t\t\t\t" . '<div class="chat-text"><p>' . str_replace( array( "\r", "\n", "\t" ), '', apply_filters( 'my_post_format_chat_text', $chat_row, $chat_author, $speaker_id ) ) . '</p></div>';
					
					
					/* Close the chat row. */
					$chat_output .= "\n\t\t\t</div><!-- .chat-row -->";
				}
			}
		}
		
		
		/* Close the chat transcript div. */
		$chat_output .= "\n\t\t\t</div><!-- .chat-transcript -->\n";
		
		
		/* Return the chat content and apply filters for developers. */
		return apply_filters( 'my_post_format_chat_content', $chat_output );
	}
	
	
	/*
		This function returns an ID based on the provided chat author name. It keeps these IDs in a global 
		array and makes sure we have a unique set of IDs.  The purpose of this function is to provide an "ID"
		that will be used in an HTML class for individual chat rows so they can be styled. So, speaker "John" 
		will always have the same class each time he speaks. And, speaker "Mary" will have a different class 
		from "John" but will have the same class each time she speaks.
		
		@author David Chandra
		@link http://www.turtlepod.org
		@author Justin Tadlock
		@link http://justintadlock.com
		@copyright Copyright (c) 2012
		@license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
		@link http://justintadlock.com/archives/2012/08/21/post-formats-chat
		
		@global array $_post_format_chat_ids An array of IDs for the chat rows based on the author.
		@param string $chat_author Author of the current chat row.
		@return int The ID for the chat row based on the author.
	*/
	
	
	function pixelwars_theme_post_format_chat_row_id( $chat_author )
	{
		global $_post_format_chat_ids;
		
		
		/* Let's sanitize the chat author to avoid craziness and differences like "John" and "john". */
		$chat_author = strtolower( strip_tags( $chat_author ) );
		
		
		/* Add the chat author to the array. */
		$_post_format_chat_ids[] = $chat_author;
		
		
		/* Make sure the array only holds unique values. */
		$_post_format_chat_ids = array_unique( $_post_format_chat_ids );
		
		
		/* Return the array key for the chat author and add "1" to avoid an ID of "0". */
		return absint( array_search( $chat_author, $_post_format_chat_ids ) ) + 1;
	}
	
	
	/* Filter the content of chat posts. */
	add_filter( 'the_content', 'pixelwars_theme_post_format_chat_content' );


/* ============================================================================================================================================= */


	add_filter( 'the_excerpt', 'do_shortcode' );
	
	add_filter( 'widget_text', 'do_shortcode' );


/* ============================================================================================================================================= */


	function row( $atts, $content = "" )
	{
		$row = '<div class="row">' . do_shortcode( $content ) . '</div>';
		
		
		return $row;
	}
	
	add_shortcode( 'row', 'row' );


/* ============================================================================================================================================= */


	function column( $atts, $content = "" )
	{
		extract( shortcode_atts( array( 'width'    => "",
										'width_xs' => "",
										'width_md' => "",
										'width_lg' => "" ), $atts ) );
		
		
		if ( $width != "" )
		{
			$width = 'col-sm-' . $width;
		}
		
		
		if ( $width_xs != "" )
		{
			$width_xs = 'col-xs-' . $width_xs;
		}
		
		
		if ( $width_md != "" )
		{
			$width_md = 'col-md-' . $width_md;
		}
		
		
		if ( $width_lg != "" )
		{
			$width_lg = 'col-lg-' . $width_lg;
		}
		
		
		$column = '<div class="' . $width . ' ' . $width_xs . ' ' . $width_md . ' ' . $width_lg . '">' . do_shortcode( $content ) . '</div>';
		
		
		return $column;
	}
	
	add_shortcode( 'column', 'column' );


/* ============================================================================================================================================= */


	function alert( $atts, $content = "" )
	{
		extract( shortcode_atts( array( 'type'  => "" ), $atts ) );
		
		
		$alert = '<div class="alert ' . $type . '">' . do_shortcode( $content ) . '</div>';
		
		
		return $alert;
	}
	
	add_shortcode( 'alert', 'alert' );


/* ============================================================================================================================================= */


	function button( $atts, $content = "" )
	{
		extract( shortcode_atts( array( 'text'   => "",
										'url'    => "",
										'target' => "",
										'color'  => "",
										'size'   => "",
										'icon'   => "" ), $atts ) );
		
		
		if ( $target != "" )
		{
			$target = ' target="' . $target . '"';
		}
		
		
		if ( $icon != "" )
		{
			$icon = '<i class="pw-icon-' . $icon . '"></i>';
		}
		
		
		$output = '<a' . $target . ' href="' . $url . '" class="button ' . $color . ' ' . $size . '">' . $icon . $text . '</a>';
		
		
		return $output;
	}
	
	add_shortcode( 'button', 'button' );


/* ============================================================================================================================================= */


	function social_icon_wrap( $atts, $content = "" )
	{
		$social_icon_wrap = '<ul class="social">' . do_shortcode( $content ) . '</ul>';
		
		
		return $social_icon_wrap;
	}
	
	add_shortcode( 'social_icon_wrap', 'social_icon_wrap' );


/* ============================================================================================================================================= */


	function social_icon( $atts, $content = "" )
	{
		extract( shortcode_atts( array( 'type' => "",
										'url'  => "" ), $atts ) );
		
		
		$social_icon = '<li><a target="_blank" class="' . $type . '" href="' . $url . '"></a></li>';
		
		
		return $social_icon;
	}
	
	add_shortcode( 'social_icon', 'social_icon' );


/* ============================================================================================================================================= */


	function toggle_wrap( $atts, $content = "" )
	{
		$toggle_wrap = '<div class="toggle-group">' . do_shortcode( $content ) . '</div>';
		
		
		return $toggle_wrap;
	}
	
	add_shortcode( 'toggle_wrap', 'toggle_wrap' );


/* ============================================================================================================================================= */


	function toggle( $atts, $content = "" )
	{
		extract( shortcode_atts( array( 'title' => "" ), $atts ) );
		
		
		$toggle = '<div class="toggle"><h4>' . $title . '</h4><div class="toggle-content">' . do_shortcode( $content ) . '</div></div>';
		
		
		return $toggle;
	}
	
	add_shortcode( 'toggle', 'toggle' );


/* ============================================================================================================================================= */


	function accordion_wrap( $atts, $content = "" )
	{
		$accordion_wrap = '<div class="toggle-group accordion">' . do_shortcode( $content ) . '</div>';
		
		
		return $accordion_wrap;
	}
	
	add_shortcode( 'accordion_wrap', 'accordion_wrap' );


/* ============================================================================================================================================= */


	function accordion( $atts, $content = "" )
	{
		extract( shortcode_atts( array( 'title' => "" ), $atts ) );
		
		
		$accordion = '<div class="toggle"><h4>' . $title . '</h4><div class="toggle-content">' . do_shortcode( $content ) . '</div></div>';
		
		
		return $accordion;
	}
	
	add_shortcode( 'accordion', 'accordion' );


/* ============================================================================================================================================= */


	function tab_wrap( $atts, $content = "" )
	{
		extract( shortcode_atts( array( 'titles' => "",
										'active' => "" ), $atts ) );
		
		
		$titles_with_commas = $titles;
		$titles_with_markup = "";
		
		if ( $titles_with_commas != "" )
		{
			$titles_array = preg_split("/[\s]*[,][\s]*/", $titles_with_commas);
			
			foreach ( $titles_array as $title_name )
			{
				if ( $active == $title_name )
				{
					$titles_with_markup .= '<li><a class="active">' . $title_name . '</a></li>';
				}
				else
				{
					$titles_with_markup .= '<li><a>' . $title_name . '</a></li>';
				}
			}
		}
		
		
		$tab_wrap = '<div class="tabs"><ul class="tab-titles">' . $titles_with_markup . '</ul><div class="tab-content">' . do_shortcode( $content ) . '</div></div>';
		
		
		return $tab_wrap;
	}
	
	add_shortcode( 'tab_wrap', 'tab_wrap' );


/* ============================================================================================================================================= */


	function tab( $atts, $content = "" )
	{
		$tab = '<div>' . do_shortcode( $content ) . '</div>';
		
		
		return $tab;
	}
	
	add_shortcode( 'tab', 'tab' );


/* ============================================================================================================================================= */


	function contact_form( $atts, $content = "" )
	{
		extract( shortcode_atts( array( 'to'      => "",
										'subject' => "",
										'captcha' => "" ), $atts ) );
		
		
		if ( $to != "" )
		{
			update_option( 'contact_form_to', $to );
		}
		else
		{
			$admin_email = get_bloginfo( 'admin_email' );
			
			update_option( 'contact_form_to', $admin_email );
		}
		
		
		if ( $captcha == "yes" )
		{
			$random1 = rand( 1, 5 );
			$random2 = rand( 1, 5 );
			$sum_random = $random1 + $random2;
			
			$captcha_out = '<p>';
			$captcha_out .= '<input type="hidden" id="captcha" name="captcha" value="yes">';
			$captcha_out .= '<label for="sum_user">' . $random1 . ' + ' . $random2 . ' = ?</label>';
			$captcha_out .= '<input type="text" id="sum_user" name="sum_user" class="required" placeholder="' . __( 'What is the sum?', 'read' ) . '">';
			$captcha_out .= '<input type="hidden" id="sum_random" name="sum_random" value="' . $sum_random . '">';
			$captcha_out .= '</p>';
		}
		else
		{
			$captcha_out = '<p style="padding: 0px; margin: 0px;"><input type="hidden" id="captcha" name="captcha" value="no"></p>';
		}
		
		
		// Get the site domain and get rid of www.
		$site_url = strtolower( $_SERVER['SERVER_NAME'] );
		
		if ( substr( $site_url, 0, 4 ) == 'www.' )
		{
			$site_url = substr( $site_url, 4 );
		}
		
		$sender_domain = 'server@' . $site_url;
		
		
		$contact_form = '<div class="contact-form"><form id="contact-form" class="validate-form" method="post" action="' . get_template_directory_uri() . '/send-mail.php">';
		$contact_form .= '<input type="hidden" id="sender_domain" name="sender_domain" value="' . $sender_domain . '">';
		$contact_form .= '<input type="hidden" id="subject" name="subject" value="' . $subject . '">';
		$contact_form .= '<p><label for="name">' . __( 'NAME', 'read' ) . '</label><input type="text" id="name" name="name" class="required"></p>';
		$contact_form .= '<p><label for="email">' . __( 'EMAIL', 'read' ) . '</label><input type="text" id="email" name="email" class="required email"></p>';
		$contact_form .= '<p><label for="message">' . __( 'MESSAGE', 'read' ) . '</label><textarea id="message" name="message" class="required"></textarea></p>';
		$contact_form .= $captcha_out;
		$contact_form .= '<p><button class="submit button"><span class="submit-label">' . __( 'Submit', 'read' ) . '</span><span class="submit-status"></span></button></p>';
		$contact_form .= '</form></div>';
		
		
		return $contact_form;
	}
	
	add_shortcode( 'contact_form', 'contact_form' );


/* ============================================================================================================================================= */


	function section_title( $atts, $content = "" )
	{
		extract( shortcode_atts( array( 'text' => "",
										'align' => "" ), $atts ) );
		
		
		$section_title = '<h2 class="section-title ' . $align . '">' . $text . '</h2>';
		
		
		return $section_title;
	}
	
	add_shortcode( 'section_title', 'section_title' );


/* ============================================================================================================================================= */


	function fun_fact( $atts, $content = "" )
	{
		extract( shortcode_atts( array( 'icon' => "",
										'text' => "" ), $atts ) );
		
		
		$fun_fact = '<div class="fun-fact"><i class="pw-icon-' . $icon . '"></i><h4>' . $text . '</h4></div>';
		
		
		return $fun_fact;
	}
	
	add_shortcode( 'fun_fact', 'fun_fact' );


/* ============================================================================================================================================= */


	function intro( $atts, $content = "" )
	{
		$intro = '<div class="intro"><h2>' . do_shortcode( $content ) . '</h2></div>';
		
		
		return $intro;
	}
	
	add_shortcode( 'intro', 'intro' );


/* ============================================================================================================================================= */


	function rotate_words( $atts, $content = "" )
	{
		extract( shortcode_atts( array( 'titles' => "",
										'interval' => "3000" ), $atts ) );
		
		
		$titles_with_commas = $titles;
		$titles_with_markup = "";
		
		if ( $titles_with_commas != "" )
		{
			$titles_array = preg_split("/[\s]*[,][\s]*/", $titles_with_commas);
			
			foreach ( $titles_array as $title_name )
			{
				$titles_with_markup .= '<span>' . $title_name . '</span>';
			}
		}
		
		
		$rotate_words = '<span class="rotate-words" data-interval="' . $interval . '">' . $titles_with_markup . '</span>';
		
		
		return $rotate_words;
	}
	
	add_shortcode( 'rotate_words', 'rotate_words' );


/* ============================================================================================================================================= */


	function testimonial_wrap( $atts, $content = "" )
	{
		$testimonial_wrap = '<div class="testo-group">' . do_shortcode( $content ) . '</div>';
		
		
		return $testimonial_wrap;
	}
	
	add_shortcode( 'testimonial_wrap', 'testimonial_wrap' );


/* ============================================================================================================================================= */


	function testimonial( $atts, $content = "" )
	{
		extract( shortcode_atts( array( 'image' => "",
										'title' => "",
										'sub_title' => "" ), $atts ) );
		
		
		$testimonial = '<div class="testo"><img alt="" src="' . $image . '"><h4>' . $title . '<span>' . $sub_title . '</span></h4><p>' . do_shortcode( $content ) . '</p></div>';
		
		
		return $testimonial;
	}
	
	add_shortcode( 'testimonial', 'testimonial' );


/* ============================================================================================================================================= */


	function slider( $atts, $content = "" )
	{
		extract( shortcode_atts( array( 'items'      => '1',
										'loop'       => 'true',
										'center'     => 'false',
										'mouse_drag' => 'true',
										'nav'        => 'true',
										'dots'       => 'true',
										'autoplay'   => 'false',
										'speed'      => '600',
										'timeout'    => '2000' ), $atts ) );
		
		
		$slider = '<div class="owl-carousel owl-loading" data-items="' . $items . '" data-loop="' . $loop . '" data-center="' . $center . '" data-mouse-drag="' . $mouse_drag . '" data-nav="' . $nav . '" data-dots="' . $dots . '" data-autoplay="' . $autoplay . '" data-autoplay-speed="' . $speed . '" data-autoplay-timeout="' . $timeout . '" data-nav-text-prev="' . __( 'prev', 'read' ) . '" data-nav-text-next="' . __( 'next', 'read' ) . '">' . do_shortcode( $content ) . '</div>';
		
		
		return $slider;
	}
	
	add_shortcode( 'slider', 'slider' );


/* ============================================================================================================================================= */


	function slide( $atts, $content = "" )
	{
		extract( shortcode_atts( array( 'title' => "",
										'image' => "" ), $atts ) );
		
		
		if ( $title != "" )
		{
			$title = '<p class="owl-title">' . $title . '</p>';
		}
		else
		{
			$title = "";
		}
		
		
		$slide = '<div><img alt="" src="' . $image . '">' . $title . '</div>';
		
		
		return $slide;
	}
	
	add_shortcode( 'slide', 'slide' );


/* ============================================================================================================================================= */


	function quote( $atts, $content = "" )
	{
		extract( shortcode_atts( array( 'name'  => "",
										'align' => "" ), $atts ) );
		
		
		$quote = '<blockquote class="' . $align . '">' . do_shortcode( $content ) . '<cite>' . $name . '</cite></blockquote>';
		
		
		return $quote;
	}
	
	add_shortcode( 'quote', 'quote' );


/* ============================================================================================================================================= */


	function media_wrap( $atts, $content = "" )
	{
		$media_wrap = '<div class="media-wrap">' . do_shortcode( $content ) . '</div>';
		
		
		return $media_wrap;
	}
	
	add_shortcode( 'media_wrap', 'media_wrap' );


/* ============================================================================================================================================= */


	function theme_audio( $atts, $content = "" )
	{
		extract( shortcode_atts( array( 'url' => "",
										'poster' => "" ), $atts ) );
		
		
		if ( $poster != "" )
		{
			$poster = '<img alt="" src="' . $poster . '">';
		}
		
		
		$theme_audio = $poster . '<audio src="' . $url . '" preload="none" style="width: 100%;"></audio>';
		
		
		return $theme_audio;
	}
	
	add_shortcode( 'theme_audio', 'theme_audio' );


/* ============================================================================================================================================= */


	function theme_video( $atts, $content = "" )
	{
		extract( shortcode_atts( array( 'url' => "",
										'poster' => "" ), $atts ) );
		
		
		$theme_video = '<video src="' . $url . '" poster="' . $poster . '" preload="none" style="width: 100%; height: 100%;"></video>';
		
		
		return $theme_video;
	}
	
	add_shortcode( 'theme_video', 'theme_video' );


/* ============================================================================================================================================= */


	function drop_cap( $atts, $content = "" )
	{
		$drop_cap = '<p class="drop-cap">' . do_shortcode( $content ) . '</p>';
		
		
		return $drop_cap;
	}
	
	add_shortcode( 'drop_cap', 'drop_cap' );


/* ============================================================================================================================================= */


	function full_width_image( $atts, $content = "" )
	{
		$full_width_image = '<div class="full-width-image">' . do_shortcode( $content ) . '</div>';
		
		
		return $full_width_image;
	}
	
	add_shortcode( 'full_width_image', 'full_width_image' );


/* ============================================================================================================================================= */


	function pricing_table( $atts, $content = "" )
	{
		$pricing_table = '<div class="pricing-table">' . do_shortcode( $content ) . '</div>';
		
		
		return $pricing_table;
	}
	
	add_shortcode( 'pricing_table', 'pricing_table' );


/* ============================================================================================================================================= */


	function photo_wall( $atts, $content = "" )
	{
		extract( shortcode_atts( array( 'animation' => "random",
										'interval'  => "1600",
										'max_step'  => "3" ), $atts ) );
		
		$output = "";
		$images = "";
		$items_count = 1;
		
		$args = array(  'post_type'      => 'portfolio',
						'orderby'        => 'rand',
						'posts_per_page' => -1 );
		
		$loop = new WP_Query( $args );
		
		if ( $loop->have_posts() ) :
			while ( $loop->have_posts() ) : $loop->the_post();
			
				$post_content = get_the_content();
				
				if ( preg_match( '/\[gallery.*ids=.(.*).\]/', $post_content, $ids ) )
				{
					$ids_in_array = explode( ',', $ids[1] );
					
					foreach ( $ids_in_array as $item )
					{
						$image = wp_get_attachment_image_src( $item, 'pixelwars_theme_image_size_2' );
						$image_alt = get_post_meta( $item, '_wp_attachment_image_alt', true );
						
						if ( $image[0] != "" )
						{
							if ( $items_count <= 100 )
							{
								$images .= '<li>';
								$images .= '<a href="#">';
								$images .= '<img alt="' . esc_attr( $image_alt ) . '" src="' . esc_url( $image[0] ) . '">';
								$images .= '</a>';
								$images .= '</li>';
								
								$items_count++;
							}
						}
					}
				}
			
			endwhile;
			
			if ( $images != "" )
			{
				$output .= '<div class="ri-grid ri-grid-loading" data-animation="' . esc_attr( $animation ) . '" data-interval="' . esc_attr( $interval ) . '" data-max-step="' . esc_attr( $max_step ) . '">';
				$output .= '<ul>' . $images . '</ul>';
				$output .= '</div>';
			}
		
		endif;
		wp_reset_query();
		
		return $output;
	}
	
	add_shortcode( 'photo_wall', 'photo_wall' );


/* ============================================================================================================================================= */


	function ken_slider_wrap( $atts, $content = "" )
	{
		extract( shortcode_atts( array( 'speed'     => '5000',
										'animation' => 'kenburns' ), $atts ) );
		
		
		$ken_slider_wrap = '<ul class="ken-slider" data-speed="' . $speed . '" data-animation="' . $animation . '">' . do_shortcode( $content ) . '</ul>';
		
		
		return $ken_slider_wrap;
	}
	
	add_shortcode( 'ken_slider_wrap', 'ken_slider_wrap' );


/* ============================================================================================================================================= */


	function ken_slide( $atts, $content = "" )
	{
		extract( shortcode_atts( array( 'image' => "" ), $atts ) );
		
		
		$ken_slide = '<li><img alt="" src="' . $image . '"></li>';
		
		
		return $ken_slide;
	}
	
	add_shortcode( 'ken_slide', 'ken_slide' );


/* ============================================================================================================================================= */


	function pixelwars_theme_run_shortcode( $content )
	{
		global $shortcode_tags;
		
		
		// Backup current registered shortcodes and clear them all out
		$orig_shortcode_tags = $shortcode_tags;
		
		remove_all_shortcodes();
		
		
		add_shortcode( 'row', 'row' );
		add_shortcode( 'column', 'column' );
		add_shortcode( 'alert', 'alert' );
		add_shortcode( 'contact_form', 'contact_form' );
		add_shortcode( 'section_title', 'section_title' );
		add_shortcode( 'fun_fact', 'fun_fact' );
		add_shortcode( 'button', 'button' );
		add_shortcode( 'intro', 'intro' );
		add_shortcode( 'rotate_words', 'rotate_words' );
		add_shortcode( 'social_icon_wrap', 'social_icon_wrap' );
		add_shortcode( 'social_icon', 'social_icon' );
		add_shortcode( 'toggle_wrap', 'toggle_wrap' );
		add_shortcode( 'toggle', 'toggle' );
		add_shortcode( 'accordion_wrap', 'accordion_wrap' );
		add_shortcode( 'accordion', 'accordion' );
		add_shortcode( 'tab_wrap', 'tab_wrap' );
		add_shortcode( 'tab', 'tab' );
		add_shortcode( 'testimonial_wrap', 'testimonial_wrap' );
		add_shortcode( 'testimonial', 'testimonial' );
		add_shortcode( 'slider', 'slider' );
		add_shortcode( 'slide', 'slide' );
		add_shortcode( 'quote', 'quote' );
		add_shortcode( 'media_wrap', 'media_wrap' );
		add_shortcode( 'theme_video', 'theme_video' );
		add_shortcode( 'theme_audio', 'theme_audio' );
		add_shortcode( 'drop_cap', 'drop_cap' );
		add_shortcode( 'full_width_image', 'full_width_image' );
		add_shortcode( 'pricing_table', 'pricing_table' );
		add_shortcode( 'photo_wall', 'photo_wall' );
		add_shortcode( 'ken_slider_wrap', 'ken_slider_wrap' );
		add_shortcode( 'ken_slide', 'ken_slide' );
		
		
		// Do the shortcode ( only the one above is registered )
		$content = do_shortcode( $content );
		
		// Put the original shortcodes back
		$shortcode_tags = $orig_shortcode_tags;
		
		
		return $content;
	}
	
	add_filter( 'the_content', 'pixelwars_theme_run_shortcode', 7 );


/* ============================================================================================================================================= */


	function pixelwars__photo_gallery( $atts )
	{
		extract(shortcode_atts(array('ids'     => "",
									 'orderby' => "",
									 'size'    => 'thumbnail'), $atts));
		
		$output = "";
		$items_with_commas = $ids;
		
		if ($items_with_commas != "")
		{
			global $wpdb;
			$items_in_array = preg_split("/[\s]*[,][\s]*/", $items_with_commas);
			
			if ($orderby == 'rand')
			{
				shuffle($items_in_array);
			}
			
			$output .= '<div class="media-grid-wrap">';
			
				$gallery_loading 				  = get_option('pixelwars_portfolio_gallery_loading', 'wait-for-all-images');
				$gallery_style 					  = get_option('gallery_style', 'minimal');
				$gallery_share_button 			  = get_option('gallery_share_button', 'true');
				$gallery_fullscreen_button 		  = get_option('gallery_fullscreen_button', 'true');
				$gallery_background_color_opacity = get_option('gallery_background_color_opacity', '1.00');
				$gallery_row_height 			  = get_option('gallery_row_height', '360');
				$gallery_image_load_effect 		  = get_option('gallery_image_load_effect', 'effect-4');
				
				$output .= '<div class="pw-gallery pw-collage pw-collage-loading ' . esc_attr($gallery_loading) . '" data-gallery-style="' . esc_attr($gallery_style) . '" data-share="' . esc_attr($gallery_share_button) . '" data-fullscreen="' . esc_attr($gallery_fullscreen_button) . '" data-bg-opacity="' . esc_attr($gallery_background_color_opacity) . '" data-row-height="' . esc_attr($gallery_row_height) . '" data-effect="' . esc_attr($gallery_image_load_effect) . '" data-mobile-row-height="120">';
				
					$image_resizing = get_option('pixelwars_full_size_image_resizing', '4k');
					
					foreach ($items_in_array as $item)
					{
						$image_big = "";
						
						if ($image_resizing == '2k')
						{
							$image_big = wp_get_attachment_image_src($item, 'pixelwars_theme_image_size_1920');
						}
						elseif ($image_resizing == 'medium')
						{
							$image_big = wp_get_attachment_image_src($item, 'pixelwars_theme_image_size_1400');
						}
						elseif ($image_resizing == 'no_resizing')
						{
							$image_big = wp_get_attachment_image_src($item, 'full');
						}
						else
						{
							$image_big = wp_get_attachment_image_src($item, 'pixelwars_theme_image_size_3840');
						}
						
						$image_medium = "";
						$image_width_cropped = wp_get_attachment_image_src($item, 'pixelwars_theme_image_size_1600');
						
						if ($image_width_cropped[1] > $image_width_cropped[2])
						{
							$image_medium = $image_width_cropped;
						}
						else
						{
							$image_height_cropped = wp_get_attachment_image_src($item, 'pixelwars_theme_image_size_nullx1600');
							$image_medium = $image_height_cropped;
						}
						
						$image_small 	 = wp_get_attachment_image_src($item, 'pixelwars_theme_image_size_nullx500');
						$image_alt 		 = get_post_meta($item, '_wp_attachment_image_alt', true);
						$image_caption   = $wpdb->get_var($wpdb->prepare("SELECT post_excerpt FROM $wpdb->posts WHERE ID = %s", $item));
						$attachment_page = get_attachment_link($item);
						
						$output .= '<a href="' . esc_url($image_big[0]) . '" data-size="' . esc_attr($image_big[1]) . 'x' . esc_attr($image_big[2]) . '" data-med="' . esc_url($image_medium[0]) . '" data-med-size="' . esc_attr($image_medium[1]) . 'x' . esc_attr($image_medium[2]) . '" data-attachment-page="' . esc_url($attachment_page) . '">';
						
						$output .= '<img alt="' . esc_attr($image_alt) . '" src="' . esc_url($image_small[0]) . '" width="' . esc_attr($image_small[1]) . '" height="' . esc_attr($image_small[2]) . '">';
						
						if ($image_caption != "")
						{
							$output .= '<figure>'. $image_caption . '</figure>';
						}
						
						$output .= '</a>';
					}
				
				$output .= '</div>';
			$output .= '</div>';
		}
		
		return $output;
	}
	
	
	function pixelwars__photo_gallery_2($atts)
	{
		extract(shortcode_atts(array('ids'     => "",
									 'orderby' => "",
									 'size'    => 'thumbnail'), $atts));
		
		$output = "";
		$items_with_commas = $ids;
		
		if ($items_with_commas != "")
		{
			global $wpdb;
			$items_in_array = preg_split("/[\s]*[,][\s]*/", $items_with_commas);
			
			if ($orderby == 'rand')
			{
				shuffle($items_in_array);
			}
			
			$output .= '<div class="media-grid-wrap">';
			
				$gallery_loading 		   = get_option('pixelwars_portfolio_gallery_loading', 'wait-for-all-images');
				$gallery_row_height 	   = get_option('gallery_row_height', '360');
				$gallery_image_load_effect = get_option('gallery_image_load_effect', 'effect-4');
				
				$output .= '<div class="mfp-gallery pw-collage pw-collage-loading ' . esc_attr($gallery_loading) . '" data-row-height="' . esc_attr($gallery_row_height) . '" data-effect="' . esc_attr($gallery_image_load_effect) . '" data-mobile-row-height="120">';
				
					foreach ($items_in_array as $item)
					{
						$image_big_url = "";
						$image_width_cropped = wp_get_attachment_image_src($item, 'pixelwars_theme_image_size_1920');
						
						if ($image_width_cropped[1] > $image_width_cropped[2])
						{
							$image_big_url = $image_width_cropped[0];
						}
						else
						{
							$image_height_cropped = wp_get_attachment_image_src($item, 'pixelwars_theme_image_size_nullx1080');
							$image_big_url = $image_height_cropped[0];
						}
						
						$image_small   = wp_get_attachment_image_src($item, 'pixelwars_theme_image_size_nullx500');
						$image_alt 	   = get_post_meta($item, '_wp_attachment_image_alt', true);
						$image_caption = $wpdb->get_var($wpdb->prepare("SELECT post_excerpt FROM $wpdb->posts WHERE ID = %s", $item));
						
						$title_out = "";
						
						if ($image_caption != "")
						{
							$title_out = 'title="' . esc_attr($image_caption) . '"';
						}
						
						$output .= '<a href="' . esc_url($image_big_url) . '" ' . $title_out . '>';
						$output .= '<img alt="' . esc_attr($image_alt) . '" src="' . esc_url($image_small[0]) . '" width="' . esc_attr($image_small[1]) . '" height="' . esc_attr($image_small[2]) . '">';
						$output .= '</a>';
					}
				
				$output .= '</div>';
			$output .= '</div>';
		}
		
		return $output;
	}
	
	
	function pixelwars__photo_gallery_3($atts)
	{
		extract(shortcode_atts(array('ids'     => "",
									 'orderby' => "",
									 'size'    => 'thumbnail'), $atts));
		
		$output = "";
		$items_with_commas = $ids;
		
		if ($items_with_commas != "")
		{
			global $wpdb;
			$items_in_array = preg_split("/[\s]*[,][\s]*/", $items_with_commas);
			
			if ($orderby == 'rand')
			{
				shuffle($items_in_array);
			}
			
			$output .= '<section class="viewjs-gallery">';
				$output .= '<figure>';
					$output .= '<ul>';
					
						foreach ($items_in_array as $item)
						{
							$image_big_url = "";
							$image_width_cropped = wp_get_attachment_image_src($item, 'pixelwars_theme_image_size_1920');
							
							if ($image_width_cropped[1] > $image_width_cropped[2])
							{
								$image_big_url = $image_width_cropped[0];
							}
							else
							{
								$image_height_cropped = wp_get_attachment_image_src($item, 'pixelwars_theme_image_size_nullx1080');
								$image_big_url = $image_height_cropped[0];
							}
							
							$image_small   = wp_get_attachment_image_src($item, 'pixelwars_theme_image_size_nullx500');
							$image_alt 	   = get_post_meta($item, '_wp_attachment_image_alt', true);
							$image_caption = $wpdb->get_var($wpdb->prepare("SELECT post_excerpt FROM $wpdb->posts WHERE ID = %s", $item));
							
							$title_out = "";
							
							if ($image_caption != "")
							{
								$title_out = 'title="' . esc_attr($image_caption) . '"';
							}
							
							$output .= '<li>';
							$output .= '<a class="view" ' . $title_out . ' href="' . esc_url($image_big_url) . '" rel="photos">';
							$output .= '<img alt="' . esc_attr($image_alt) . '" src="' . esc_url($image_small[0]) . '">';
							$output .= '</a>';
							$output .= '</li>';
						}
					
					$output .= '</ul>';
				$output .= '</figure>';
			$output .= '</section> <!-- .viewjs-gallery -->';
		}
		
		return $output;
	}
	
	
	function pixelwars__homepage_photowall_gallery( $atts )
	{
		extract( shortcode_atts( array( 'ids'     => "",
										'orderby' => "",
										'size'    => 'thumbnail' ), $atts ) );
		
		$output = "";
		$items_with_commas = $ids;
		
		if ( $items_with_commas != "" )
		{
			$items_in_array = preg_split( "/[\s]*[,][\s]*/", $items_with_commas );
			
			if ( $orderby == 'rand' )
			{
				shuffle( $items_in_array );
			}
			
			$animation = get_option( 'pixelwars_homepage_photowall_animation', 'random' );
			$interval  = stripcslashes( get_option( 'pixelwars_homepage_photowall_interval', '1600' ) );
			$max_step  = get_option( 'pixelwars_homepage_photowall_max_step', '3' );
			
			$output .= '<div class="ri-grid ri-grid-loading" data-animation="' . esc_attr( $animation ) . '" data-interval="' . esc_attr( $interval ) . '" data-max-step="' . esc_attr( $max_step ) . '">';
			$output .= '<ul>';
			
			foreach ( $items_in_array as $item )
			{
				$image = wp_get_attachment_image_src( $item, 'pixelwars_theme_image_size_2' );
				$image_alt = get_post_meta( $item, '_wp_attachment_image_alt', true );
				
				$output .= '<li>';
				$output .= '<a href="#">';
				$output .= '<img alt="' . esc_attr( $image_alt ) . '" src="' . esc_url( $image[0] ) . '">';
				$output .= '</a>';
				$output .= '</li>';
			}
			
			$output .= '</ul>';
			$output .= '</div>';
		}
		
		return $output;
	}
	
	
	function pixelwars__homepage_landing_2_gallery( $atts )
	{
		extract( shortcode_atts( array( 'ids'     => "",
										'orderby' => "",
										'size'    => 'thumbnail' ), $atts ) );
		
		$output = "";
		$items_with_commas = $ids;
		
		if ( $items_with_commas != "" )
		{
			$items_in_array = preg_split( "/[\s]*[,][\s]*/", $items_with_commas );
			
			if ( $orderby == 'rand' )
			{
				shuffle( $items_in_array );
			}
			
			$loop 			  = get_option( 'pixelwars_homepage_owl_carousel_loop', 'true' );
			$center 		  = get_option( 'pixelwars_homepage_owl_carousel_center', 'true' );
			$autoplay 		  = get_option( 'pixelwars_homepage_owl_carousel_autoplay', 'true' );
			$autoplay_speed   = get_option( 'pixelwars_homepage_owl_carousel_autoplay_speed', '500' );
			$autoplay_timeout = get_option( 'pixelwars_homepage_owl_carousel_autoplay_timeout', '3000' );
			
			$output .= '<div class="owl-carousel fs-slider owl-loading" data-items="1" data-loop="' . esc_attr( $loop ) . '" data-center="' . esc_attr( $center ) . '" data-mouse-drag="false" data-nav="false" data-dots="false" data-autoplay="' . esc_attr( $autoplay ) . '" data-autoplay-speed="' . esc_attr( $autoplay_speed ) . '" data-autoplay-timeout="' . esc_attr( $autoplay_timeout ) . '" data-nav-text-prev="' . __( 'prev', 'read' ) . '" data-nav-text-next="' . __( 'next', 'read' ) . '">';
			
			foreach ( $items_in_array as $item )
			{
				$image = wp_get_attachment_image_src( $item, 'pixelwars_theme_image_size_1920' );
				
				$output .= '<div class="fs-slide" style="background-image: url( ' . esc_url( $image[0] ) . ' );"></div>';
			}
			
			$output .= '</div>';
		}
		
		return $output;
	}
	
	
	function pixelwars__homepage_landing_4_gallery($atts)
	{
		extract(shortcode_atts(array('ids'     => "",
									 'orderby' => "",
									 'size'    => 'thumbnail'), $atts));
		
		$output = "";
		$items_with_commas = $ids;
		
		if ($items_with_commas != "")
		{
			$items_in_array = preg_split("/[\s]*[,][\s]*/", $items_with_commas);
			
			if ($orderby == 'rand')
			{
				shuffle($items_in_array);
			}
			
			$output .= '<div class="fs-slider fade-slider">';
			
				foreach ($items_in_array as $item)
				{
					$image_url = "";
					$image_width_cropped = wp_get_attachment_image_src($item, 'pixelwars_theme_image_size_1920');
					
					if ($image_width_cropped[1] > $image_width_cropped[2])
					{
						$image_url = $image_width_cropped[0];
					}
					else
					{
						$image_height_cropped = wp_get_attachment_image_src($item, 'pixelwars_theme_image_size_nullx1080');
						$image_url = $image_height_cropped[0];
					}
					
					$image_alt = get_post_meta($item, '_wp_attachment_image_alt', true);
					
					$output .= '<div class="fs-slide">';
					$output .= '<img alt="' . esc_attr($image_alt) . '" src="' . esc_url($image_url) . '">';
					$output .= '</div>';
				}
			
			$output .= '</div> <!-- .fs-slider .fade-slider -->';
		}
		
		return $output;
	}
	
	
	function pixelwars__homepage_alternate_gallery( $atts )
	{
		extract( shortcode_atts( array( 'ids'     => "",
										'orderby' => "",
										'size'    => 'thumbnail' ), $atts ) );
		
		$output = "";
		$items_with_commas = $ids;
		
		if ( $items_with_commas != "" )
		{
			$items_in_array = preg_split( "/[\s]*[,][\s]*/", $items_with_commas );
			
			if ( $orderby == 'rand' )
			{
				shuffle( $items_in_array );
			}
			
			$animation = get_option( 'pixelwars_homepage_ken_slider_animation', 'kenburns' );
			$speed 	   = get_option( 'pixelwars_homepage_ken_slider_interval', '5000' );
			
			$output .= '<ul class="ken-slider" data-animation="' . esc_attr( $animation ) . '" data-speed="' . esc_attr( $speed ) . '">';
			
				foreach ( $items_in_array as $item )
				{
					$image 	   = wp_get_attachment_image_src( $item, $size );
					$image_alt = get_post_meta( $item, '_wp_attachment_image_alt', true );
					
					$output .= '<li>';
					$output .= '<img alt="' . esc_attr( $image_alt ) . '" src="' . esc_url( $image[0] ) . '">';
					$output .= '</li>';
				}
			
			$output .= '</ul>';
		}
		
		return $output;
	}
	
	
	function pixelwars__homepage_alternate_2_gallery( $atts )
	{
		extract( shortcode_atts( array( 'ids'     => "",
										'orderby' => "",
										'size'    => 'thumbnail' ), $atts ) );
		
		$output = "";
		$items_with_commas = $ids;
		
		if ( $items_with_commas != "" )
		{
			$items_in_array = preg_split( "/[\s]*[,][\s]*/", $items_with_commas );
			
			if ( $orderby == 'rand' )
			{
				shuffle( $items_in_array );
			}
			
			$items = get_option( 'pixelwars_homepage_owl_carousel_items', '4' );
			$loop = get_option( 'pixelwars_homepage_owl_carousel_loop', 'true' );
			$center = get_option( 'pixelwars_homepage_owl_carousel_center', 'true' );
			$mouse_drag = get_option( 'pixelwars_homepage_owl_carousel_mouse_drag', 'true' );
			$nav_links = get_option( 'pixelwars_homepage_owl_carousel_nav_links', 'true' );
			$nav_dots = get_option( 'pixelwars_homepage_owl_carousel_nav_dots', 'false' );
			$autoplay = get_option( 'pixelwars_homepage_owl_carousel_autoplay', 'true' );
			$autoplay_speed = get_option( 'pixelwars_homepage_owl_carousel_autoplay_speed', '500' );
			$autoplay_timeout = get_option( 'pixelwars_homepage_owl_carousel_autoplay_timeout', '3000' );
			
			$output .= '<div class="owl-carousel owl-loading" data-items="' . esc_attr( $items ) . '" data-loop="' . esc_attr( $loop ) . '" data-center="' . esc_attr( $center ) . '" data-mouse-drag="' . esc_attr( $mouse_drag ) . '" data-nav="' . esc_attr( $nav_links ) . '" data-dots="' . esc_attr( $nav_dots ) . '" data-autoplay="' . esc_attr( $autoplay ) . '" data-autoplay-speed="' . esc_attr( $autoplay_speed ) . '" data-autoplay-timeout="' . esc_attr( $autoplay_timeout ) . '" data-nav-text-prev="' . __( 'prev', 'read' ) . '" data-nav-text-next="' . __( 'next', 'read' ) . '">';
			
				foreach ( $items_in_array as $item )
				{
					$image 	   = wp_get_attachment_image_src( $item, $size );
					$image_alt = get_post_meta( $item, '_wp_attachment_image_alt', true );
					
					$output .= '<div>';
					$output .= '<img alt="' . esc_attr( $image_alt ) . '" src="' . esc_url( $image[0] ) . '">';
					$output .= '</div>';
				}
			
			$output .= '</div>';
		}
		
		return $output;
	}
	
	
	function pixelwars__post_gallery($output = "", $atts, $content = false, $tag = false)
	{
		$new_output = $output;
		
		if (is_singular('portfolio'))
		{
			$pf_type = get_option(get_the_ID() . 'pf_type', 'Standard');
			
			if ($pf_type == 'Photo Gallery')
			{
				$new_output = pixelwars__photo_gallery($atts);
			}
			elseif ($pf_type == 'Photo Gallery 2')
			{
				$new_output = pixelwars__photo_gallery_2($atts);
			}
			elseif ($pf_type == 'Photo Gallery 3')
			{
				$new_output = pixelwars__photo_gallery_3($atts);
			}
		}
		elseif (is_page_template('template-homepage.php'))
		{
			$new_output = pixelwars__homepage_photowall_gallery($atts);
		}
		elseif (is_page_template('template-homepage_landing.php'))
		{
			$new_output = pixelwars__homepage_photowall_gallery($atts);
		}
		elseif (is_page_template('template-homepage_landing_2.php'))
		{
			$new_output = pixelwars__homepage_landing_2_gallery($atts);
		}
		elseif (is_page_template('template-homepage_landing_4.php'))
		{
			$new_output = pixelwars__homepage_landing_4_gallery($atts);
		}
		elseif (is_page_template('template-homepage_alternate.php'))
		{
			$new_output = pixelwars__homepage_alternate_gallery($atts);
		}
		elseif (is_page_template('template-homepage_alternate_2.php'))
		{
			$new_output = pixelwars__homepage_alternate_2_gallery($atts);
		}
		elseif (is_singular('post') || is_page() || is_home())
		{
			$gallery_type = get_option('pixelwars__gallery_type_for_posts_and_pages', 'Photo Gallery 2');
			
			if ($gallery_type == 'Photo Gallery')
			{
				$new_output = pixelwars__photo_gallery($atts);
			}
			elseif ($gallery_type == 'Photo Gallery 2')
			{
				$new_output = pixelwars__photo_gallery_2($atts);
			}
		}
		
		return $new_output;
	}
	
	add_filter('post_gallery', 'pixelwars__post_gallery', 10, 4);


/* ============================================================================================================================================= */


	// https://github.com/franz-josef-kaiser/Easy-Pagination-Deamon
	// https://github.com/marke123/Easy-Pagination-Deamon
	
	
	if ( ! class_exists('WP') ) 
	{
		header( 'Status: 403 Forbidden' );
		header( 'HTTP/1.1 403 Forbidden' );
		exit;
	}
	
	
	/**
	 * TEMPLATE TAG
	 * 
	 * A wrapper/template tag for the pagination builder inside the class.
	 * Write a call for this function with a "range" 
	 * inside your template to display the pagination.
	 * 
	 * @param integer $range
	 */
	
	function oxo_pagination( $args ) 
	{
		return new oxoPagination( $args );
	}
	
	
	if ( ! class_exists( 'oxoPagination' ) ) 
	{
		class oxoPagination 
		{
			/**
			 * Plugin root path
			 * @var unknown_type
			 */
			protected $path;
			
			/**
			 * Plugin version
			 * @var integer
			 */
			protected $version;
			
			/**
			 * Default arguments
			 * @var array
			 */
			protected $defaults = array( 'classes'			=> ""
										,'range'			=> 5
										,'wrapper'			=> 'li' // element in which we wrap the link 
										,'highlight'		=> 'current' // class for the current page
										,'before'			=> ""
										,'after'			=> ""
										,'link_before'		=> ""
										,'link_after'		=> ""
										,'next_or_number'	=> 'number'
										,'nextpagelink'		=> 'Next'
										,'previouspagelink'	=> 'Prev'
										,'pagelink'			=> '%'
										# only for attachment img pagination/navigation
										,'attachment_size'	=> 'thumbnail'
										,'show_attachment'	=> true );

			/**
			 * Input arguments
			 * @var array
			 */
			protected $args;
			
			/**
			 * Constant for the texdomain (i18n)
			 */
			const LANG = 'read';
			
			
			public function __construct( $args ) 
			{
				// Set root path variable
				$this->path = $this->get_root_path();

				// Set version
				# $this->version = get_plugin_data();

				# >>>> defaults & arguments

					// apply the "wp_list_pages_args" wordpress native filter also to the custom "page_links" function.
					$this->defaults = apply_filters( 'wp_link_pages_args', $this->defaults );

					// merge defaults with input arguments
					$this->args = wp_parse_args( $args, $this->defaults );

				# <<<< defaults & arguments

				// Help placing the template tag at the right position (inside/outside loop).
				$this->help();

				// Css
				$this->register_styles();
				// Load stylesheet into the 'wp_head()' hook of your theme.
				add_action( 'wp_head', array( &$this, 'print_styles' ) );

				// RENDER
				$this->render( $this->args );
			}


			/**
			 * Plugin root
			 */
			function get_root_path() 
			{
				$path = trailingslashit( WP_PLUGIN_URL.'/'.str_replace( basename( __FILE__ ), "", plugin_basename( __FILE__ ) ) );
				$path = apply_filters( 'config_pagination_url', $path );

				return $this->path = $path;
			}


			/**
			 * Return plugin comment data
			 * 
			 * @since 0.1.3.3
			 * 
			 * @param $value string | default = 'Version' (Other input values: Name, PluginURI, Version, Description, Author, AuthorURI, TextDomain, DomainPath, Network, Title)
			 * 
			 * @return string
			 */
			private function get_plugin_data( $value = 'Version' )
			{	
				$plugin_data = get_plugin_data( __FILE__ );

				return $plugin_data[ $value ];
			}

			/**
			 * Register styles
			 */
			function register_styles() 
			{
				if ( ! is_admin() )
				{
					// Search for a stylesheet
					$name = '/pagination.css';

					if ( file_exists( get_stylesheet_directory() . $name ) )
					{
						$file = get_stylesheet_directory() . $name;
					}
					elseif ( file_exists( get_template_directory() . $name ) )
					{
						$file = get_template_directory() . $name;
					}
					elseif ( file_exists( $this->path.$name ) )
					{
						$file = $this->path.$name;
					}
					else 
					{
						return;
					}

					// try to avoid caching stylesheets if they changed
					$version = filemtime( $file );
					
					// If no change was found, use the plugins version number
					if ( ! $version )
						$version = $this->version;

					wp_register_style( 'pagination', $file, false, $version, 'screen' );
				}
			}

			/**
			 * Print styles
			 */
			function print_styles() 
			{
				if ( ! is_admin() )
				{
					wp_enqueue_style( 'pagination' );
				}
			}

			/**
			 * Help with placing the template tag right
			 */
			function help() 
			{
				/*
				if ( is_single() && ! in_the_loop() )
				{
					$output = sprintf( __( 'You should place the %1$s template tag inside the loop on singular templates.', self::LANG ), __CLASS__ );
				}
				else

				_doing_it_wrong( 'Class: '.__CLASS__.' function: '.__FUNCTION__, 'error message' );
				*/
				if ( ! is_single() && in_the_loop() )
				{
					// $output = sprintf( __( 'You shall not place the %1$s template tag inside the loop on list/archives/search/etc templates.', self::LANG ), __CLASS__ );
					
					$output = sprintf( __( 'You shall not place the %1$s template tag inside the loop on list/archives/search/etc templates.', 'read' ), __CLASS__ );
				}
				
				if ( ! isset( $output ) )
					return;

				// error
				$message = new WP_Error( 
					 __CLASS__
					,$output 
				);

				// render
				if ( is_wp_error( $message ) ) 
				{ 
				?>
					<div id="oxo-error-<?php echo esc_attr( $message->get_error_code() ); ?>" class="error oxo-error prepend-top clear">
						<strong>
							<?php echo $message->get_error_message(); ?>
						</strong>
					</div>
				<?php 
				}
			}


			/**
			 * Replacement for the native wp_link_page() function
			 * 
			 * @author original version: Thomas Scholz (toscho.de)
			 * @link http://wordpress.stackexchange.com/questions/14406/how-to-style-current-page-number-wp-link-pages/14460#14460
			 * 
			 * @param (mixed) array $args
			 */
			public function page_links( $args )
			{
				global $page, $numpages, $multipage, $more, $pagenow;

				$args = wp_parse_args( $args, $this->defaults );
				extract( $args, EXTR_SKIP );

				if ( ! $multipage )
					return;

				# ============================================== #

				# >>>> css classes wrapper
				$start_classes = isset( $classes ) ? ' class="' : '';
				$end_classes = isset( $classes ) ? '"' : '';
				# <<<< css classes wrapper

				$output  = $before;
				
				switch ( $next_or_number ) 
				{
					case 'next' :
					
						if ( $more ) 
						{
							# >>>> [prev]
							$i = $page - 1;
							if ( $i && $more ) 
							{
								# >>>> <li class="custom-class">
								$output .= '<'.$wrapper.$start_classes.$classes.$end_classes.'>';
									$output .= _wp_link_page( $i ).$link_before.$previouspagelink.$link_after.'</a>';
								$output .= '</'.$wrapper.'>';
								# <<<< </li>
							}
							# <<<< [prev]

							# >>>> [next]
							$i = $page + 1;
							if ( $i <= $numpages && $more ) 
							{
								# >>>> <li class="custom-class">
								$output .= '<'.$wrapper.$start_classes.$classes.$end_classes.'>';
									$output .= _wp_link_page( $i ).$link_before.$nextpagelink.$link_after.'</a>';
								$output .= '</'.$wrapper.'>';
								# <<<< </li>
							}
							# <<<< [next]
						}
						
						break;

					case 'number' :
					
						for ( $i = 1; $i < ( $numpages + 1 ); $i++ )
						{
							$classes = isset( $this->args['classes'] ) ? $this->args['classes'] : '';
							
							if ( $page === $i && isset( $this->args['highlight'] ) )
								 $classes .= ' '.$this->args['highlight'];

							# >>>> <li class="current custom-class">
							$output .= '<'.$wrapper.$start_classes.$classes.$end_classes.'>';

								# >>>> [1] [2] [3] [4]
								$j = str_replace( '%', $i, $pagelink );

								if ( $page !== $i || ( ! $more && $page == true ) )
								{
									$output .= _wp_link_page( $i ).$link_before.$j.$link_after.'</a>';
								}

								// the current page must not have a link to itself
								else
								{
									$output .= $link_before.'<span>'.$j.'</span>'.$link_after;
								}
								# <<<< [next]/[prev] | [1] [2] [3] [4]

							$output .= '</'.$wrapper.'>';
							# <<<< </li>
						}
						
						break;

					default :
					
						// in case you can imagine some funky way to paginate
						do_action( 'hook_pagination_next_or_number', $page_links, $classes );
						break;
				}
				
				$output .= $after;

				return $output;
			}


			/**
			 * Navigation for image attachments
			 * 
			 * @param unknown_type $args
			 */
			public function attachment_links( $args )
			{
				global $post, $page;

				$args = wp_parse_args( $args, $this->defaults );
				extract( $args, EXTR_SKIP );

				# ============================================== #

				$attachments = array_values( get_children( array( 
					 'post_parent'		=> $post->post_parent
					,'post_status'		=> 'inherit'
					,'post_type'		=> 'attachment'
					,'post_mime_type'	=> 'image'
					,'order'			=> 'ASC'
					,'orderby'			=> 'menu_order ID' 
				) ) );

				// setup the keys for our links
				foreach ( $attachments as $key => $attachment ) {
					if ( $attachment->ID == $post->ID )
						break;
				}

				# ============================================== #
				# @todo implement rel="next/prev" for links

				# >>>> css classes wrapper
				$start_classes = isset( $classes ) ? ' class="' : '';
					$classes = isset( $classes ) ? ' '.$classes : '';
				$end_classes = isset( $classes ) ? '"' : '';
				# <<<< css classes wrapper

				$output  = $before;
					# >>>> [prev]
					if ( isset( $attachments[ $key - 1 ] ) )
					{
						$prev_href = get_attachment_link( $attachments[ $key - 1 ]->ID );

						$prev_title = str_replace( "_", " ", $attachments[ $key - 1 ]->post_title );
						$prev_title = str_replace( "-", " ", $prev_title );

						if ( $show_attachment === true )
						{
							if ( ( is_int( $attachment_size ) && $attachment_size != 0 ) || ( is_string( $attachment_size ) && $attachment_size != 'none' ) || $attachment_size != false )
								$prev_img = wp_get_attachment_image( $attachments[ $key - 1 ]->ID, $attachment_size, false );
						}

						# >>>> <li class="custom-class">
						$output .= '<'.$wrapper. $start_classes.$classes.$end_classes .'>';
							$output .= $link_before.'<a href="'.$prev_href.'" title="'.esc_attr( $prev_title ).'" rel="attachment prev">'.$prev_img.$previouspagelink.'</a>'.$link_after;
						$output .= '</'.$wrapper.'>';
						# <<<< </li>
					}
					# <<<< [prev]

					# >>>> [next]
					if ( isset( $attachments[ $key + 1 ] ) )
					{
						$next_href = get_attachment_link( $attachments[ $key + 1 ]->ID );

						$next_title = str_replace( "_", " ", $attachments[ $key + 1 ]->post_title );
						$next_title = str_replace( "-", " ", $next_title );

						if ( $show_attachment === true )
						{
							if ( ( is_int( $attachment_size ) && $attachment_size != 0 ) || ( is_string( $attachment_size ) && $attachment_size != 'none' ) || $attachment_size != false )
								$next_img = wp_get_attachment_image( $attachments[ $key + 1 ]->ID, $attachment_size, false );
						}

						# >>>> <li class="custom-class">
						$output .= '<'.$wrapper. $start_classes.$classes.$end_classes .'>';
							$output .= $link_before.'<a href="'.$next_href.'" title="'.esc_attr( $next_title ).'" rel="attachment prev">'.$next_img.$nextpagelink.'</a>'.$link_after;
						$output .= '</'.$wrapper.'>';
						# <<<< </li>
					}
					# <<<< [next]
				$output .= $after;

				#echo '<pre>';print_r($k);echo '</pre>';
				return $output;
			}


			/**
			 * Wordpress pagination for archives/search/etc.
			 * 
			 * Semantically correct pagination inside an unordered list
			 * 
			 * Displays: [First] [<<] [1] [2] [3] [4] [>>] [Last]
			 *	+ First/Last only appears if not on first/last page
			 *	+ Shows next/previous links [<<]/[>>]
			 * 
			 * Accepts a range attribute (default = 5) to adjust the number
			 * of direct page links that link to the pages above/below the current one.
			 * 
			 * @param (integer) $range
			 */
			function render( $args = array( 'classes', 'range' ) ) 
			{
				// $paged - number of the current page
				global $wp_query, $paged, $numpages;

				extract( $args, EXTR_SKIP );

				# ============================================== #

				// How much pages do we have?
				$max_page = (int) $wp_query->max_num_pages;

				// We need the pagination only if there is more than 1 page
				if ( $max_page > (int) 1 )
					$paged = ! $wp_query->query_vars['paged'] ? (int) 1 : $wp_query->query_vars['paged'];

				$classes = isset( $classes ) ? ' '.$classes : '';
				?>

				<ul class="pagination">

					<?php 
					// *******************************************************
					// To the first / previous page
					// On the first page, don't put the first / prev page link
					// *******************************************************
					if ( $paged !== (int) 1 && $paged !== (int) 0 && ! is_page() ) 
					{
						?>
						<li class="pagination-first <?php echo esc_attr( $classes ); ?>">
							<?php
							$first_post_link = get_pagenum_link( 1 ); 
							?>
							<a href=<?php echo esc_url( $first_post_link ); ?> rel="first">
								<?php _e( 'First', 'read' ); ?>
							</a>
						</li>

						<li class="pagination-prev <?php echo esc_attr( $classes ); ?>">
							<?php 
								# let's use the native fn instead of the previous_/next_posts_link() alias
								# get_adjacent_post( $in_same_cat = false, $excluded_categories = '', $previous = true )

								// Get the previous post object
								$in_same_cat	= is_category() || is_tag() || is_tax() ? true : false;
								$prev_post_obj	= get_adjacent_post( $in_same_cat );
								// Get the previous posts ID
								$prev_post_ID	= isset( $prev_post_obj->ID ) ? $prev_post_obj->ID : '';

								// Set title & link for the previous post
								if ( is_single() )
								{
									if ( isset( $prev_post_obj ) )
									{
										$prev_post_link		= get_permalink( $prev_post_ID );
										$prev_post_title	= '&laquo;';
										// $prev_post_title	= __( 'Prev', self::LANG ) . ': ' . mb_substr( $prev_post_obj->post_title, 0, 6 );
									}
								}
								else
								{
									$prev_post_link		= home_url().'/?s='.get_search_query().'&paged='.( $paged-1 );
									$prev_post_title	= '&laquo;';
								}
								?>
							<!-- Render Link to the previous post -->
							<a href="<?php echo esc_url( $prev_post_link ); ?>" rel="prev">
								<?php echo $prev_post_title; ?>
							</a>
							<?php # previous_posts_link(' &laquo; '); // ?>
						</li>
						<?php 
					}

					// Render, as long as there are more posts found, than we display per page
					if ( ! $wp_query->query_vars['posts_per_page'] < $wp_query->found_posts )
					{

						// *******************************************************
						// We need the sliding effect only if there are more pages than is the sliding range
						// *******************************************************
						if ( $max_page > $range ) 
						{
							// When closer to the beginning
							if ( $paged < $range ) 
							{
								for ( $i = 1; $i <= ( $range+1 ); $i++ ) 
								{ 
									$current = '';
									// Apply the css class "current" if it's the current post
									if ( $paged === (int) $i )
									{
										$current = ' current';
										# echo _wp_link_page( $i ).'</a>';
									}
									?>
									<li class="pagination-num<?php echo esc_attr( $classes.$current ); ?>">
										<!-- Render page number Link -->
										<a href="<?php echo esc_url( get_pagenum_link( $i ) ); ?>">
											<?php echo $i; ?>
										</a>
									</li>
									<?php 
								}
							}
							// When closer to the end
							elseif ( $paged >= ( $max_page - ceil ( $range/2 ) ) ) 
							{
								for ( $i = $max_page - $range; $i <= $max_page; $i++ )
								{ 
									$current = '';
									// Apply the css class "current" if it's the current post
									$current = ( $paged === (int) $i ) ? ' current' : '';

									?>
									<li class="pagination-num<?php echo esc_attr( $classes.$current ); ?>">
										<!-- Render page number Link -->
										<a href="<?php echo esc_url( get_pagenum_link( $i ) ); ?>">
											<?php echo $i; ?>
										</a>
									</li>
									<?php 
								}
							}
							// Somewhere in the middle
							elseif ( $paged >= $range && $paged < ( $max_page - ceil( $range/2 ) ) ) 
							{
								for ( $i = ( $paged - ceil( $range/2 ) ); $i <= ( $paged + ceil( $range/2 ) ); $i++ ) 
								{
									$current = '';
									// Apply the css class "current" if it's the current post
									$current = ( $paged === (int) $i ) ? ' current' : '';

									?>
									<li class="pagination-num<?php echo esc_attr( $classes.$current ); ?>">
										<!-- Render page number Link -->
										<a href="<?php echo esc_url( get_pagenum_link( $i ) ); ?>">
											<?php echo $i; ?>
										</a>
									</li>
									<?php 
								}
							}
						}
						// Less pages than the range, no sliding effect needed
						else 
						{
							for ( $i = 1; $i <= $max_page; $i++ ) 
							{
								$current = '';
								// Apply the css class "current" if it's the current post
								$current = ( $paged === (int) $i ) ? ' current' : '';

								?>
								<li class="pagination-num<?php echo esc_attr( $classes.$current ); ?>">
									<!-- Render page number Link -->
									<a href="<?php echo esc_url( get_pagenum_link( $i ) ); ?>">
										<?php echo $i; ?>
									</a>
								</li>
								<?php 
							}
						} // endif;
					} // endif; there are more posts found, than we display per page 


					// *******************************************************
					// to the last / next page of a paged post
					// This only get's used on posts/pages that use the <!--nextpage--> quicktag
					// *******************************************************
					if ( is_singular() )
					{
						$echo = false;

						if ( wp_attachment_is_image() === true )
						{ 
							echo $this->attachment_links( $this->args );
						}
						elseif ( $numpages > 1 )
						{
							echo $this->page_links( $this->args );
						}
					}


					// *******************************************************
					// to the last / next page
					// On the last page: don't show the link to the last/next page
					// *******************************************************
					if ( $paged !== (int) 0 && $paged !== (int) $max_page && $max_page !== (int) 0 && ! is_page() )
					{
						?>
						<li class="pagination-next<?php echo esc_attr( $classes ); ?>">
							<?php 
							# let's use the native fn instead of the previous_/next_posts_link() alias
							# get_adjacent_post( $in_same_cat = false, $excluded_categories = '', $previous = true )

							// Get the next post object
							$in_same_cat	= is_category() || is_tag() || is_tax() ? true : false;
							$next_post_obj	= get_adjacent_post( $in_same_cat, '', false );
							// Get the next posts ID
							$next_post_ID	= isset( $next_post_obj->ID ) ? $next_post_obj->ID : '';

							// Set title & link for the next post
							if ( is_single() )
							{
								if ( isset( $next_post_obj ) )
								{
									# $next_post_link = get_next_posts_link();
									# $next_post_paged_link = get_next_posts_page_link();
									$next_post_link		= get_permalink( $next_post_ID );
									$next_post_title	= '&raquo;';
									// $next_post_title	= __( 'Next', self::LANG ) . mb_substr( $next_post_obj->post_title, 0, 6 );
								}
							}
							else 
							{
								$next_post_link		= home_url().'/?s='.get_search_query().'&paged='.( $paged+1 );
								$next_post_title	= '&raquo;';
							}

							if ( isset ( $next_post_obj ) )
							{
								?>
								<!-- Render Link to the next post -->
								<a href="<?php echo esc_url( $next_post_link ); ?>" rel="next">
									<?php echo $next_post_title; ?>
								</a>
								<?php
							} 
							else 
							{
								next_posts_link(' &raquo; ');
							}
							?>
						</li>

						<li class="pagination-last<?php echo esc_attr( $classes ); ?>">
							<?php
							$last_post_link = get_pagenum_link( $max_page ); 
							?>
							<!-- Render Link to the last post -->
							<a href="<?php echo esc_url( $last_post_link ); ?>" rel="last">
								<?php _e( 'Last', 'read' ); ?>
							</a>
						</li>
						<?php 
					}
					// endif;
				?>
				</ul>
				<?php
			}
		}
	}


/* ============================================================================================================================================= */
/* ============================================================================================================================================= */


	if ( is_admin() )
	{
		include_once 'theme-options.php';
	}


/* ============================================================================================================================================= */


	include_once 'shortcode-generator.php';


/* ============================================================================================================================================= */
/* ============================================================================================================================================= */


	function pixelwars_theme_customize_register( $wp_customize )
	{
		/* ================================================== */
		
		
		$wp_customize->add_section( 'section_colors' , array(   'title'    => __( 'Colors', 'read' ),
																'priority' => 30 ) );
		
		
		$wp_customize->add_setting( 'setting_link_color', array(    'default'   => '#ab977a',
																	'transport' => 'refresh' ) );
		
		$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'control_link_color', array( 'label'    => __( 'Link Color', 'read' ),
																												'section'  => 'section_colors',
																												'settings' => 'setting_link_color' ) ) );
		
		
		$wp_customize->add_setting( 'setting_link_hover_color', array(  'default'   => '#c9b69b',
																		'transport' => 'refresh' ) );
		
		$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'control_link_hover_color', array(   'label'    => __( 'Link Hover Color', 'read' ),
																														'section'  => 'section_colors',
																														'settings' => 'setting_link_hover_color' ) ) );
		
		
		/* ================================================== */
		
		
		$wp_customize->add_section( 'section_fonts' , array(    'title'    => __( 'Fonts', 'read' ),
																'priority' => 31 ) );
		
		
		include_once 'fonts.php';
		
		
		$wp_customize->add_setting( 'setting_content_font', array(  'default'   => 'Roboto',
																	'transport' => 'refresh' ) );
		
		$wp_customize->add_control( 'control_content_font', array(	'label'    => 'Body Font',
																	'section'  => 'section_fonts',
																	'settings' => 'setting_content_font',
																	'type'     => 'select',
																	'choices'  => $all_fonts ) );
		
		
		$wp_customize->add_setting( 'setting_heading_font', array(  'default'   => 'Montserrat',
																	'transport' => 'refresh' ) );
		
		$wp_customize->add_control( 'control_heading_font', array(	'label'    => 'Heading Font',
																	'section'  => 'section_fonts',
																	'settings' => 'setting_heading_font',
																	'type'     => 'select',
																	'choices'  => $all_fonts ) );
		
		
		$wp_customize->add_setting( 'setting_menu_font', array( 'default'   => 'Montserrat',
																'transport' => 'refresh' ) );
		
		$wp_customize->add_control( 'control_menu_font', array(	'label'    => 'Menu Font',
																'section'  => 'section_fonts',
																'settings' => 'setting_menu_font',
																'type'     => 'select',
																'choices'  => $all_fonts ) );
		
		
		$wp_customize->add_setting( 'setting_text_logo_font', array(    'default'   => 'Montserrat',
																		'transport' => 'refresh' ) );
		
		$wp_customize->add_control( 'control_text_logo_font', array(    'label'    => 'Text Logo Font',
																		'section'  => 'section_fonts',
																		'settings' => 'setting_text_logo_font',
																		'type'     => 'select',
																		'choices'  => $all_fonts ) );
		
		
		/* ================================================== */
		
		
		$wp_customize->get_setting( 'blogname' )->transport = 'postMessage';
		$wp_customize->get_setting( 'blogdescription' )->transport = 'postMessage';
		
		$wp_customize->get_setting( 'setting_link_color' )->transport = 'postMessage';
		$wp_customize->get_setting( 'setting_link_hover_color' )->transport = 'postMessage';
		
		$wp_customize->get_setting( 'setting_content_font' )->transport = 'postMessage';
		$wp_customize->get_setting( 'setting_heading_font' )->transport = 'postMessage';
		$wp_customize->get_setting( 'setting_menu_font' )->transport = 'postMessage';
		$wp_customize->get_setting( 'setting_text_logo_font' )->transport = 'postMessage';
	}
	
	add_action( 'customize_register', 'pixelwars_theme_customize_register' );
	
	
	function pixelwars_theme_customize_css()
	{
		global $pixelwars_subset;
		
		
		$extra_font_styles = get_option( 'extra_font_styles', 'No' );
		
		if ( $extra_font_styles == 'Yes' )
		{
			$font_styles = ':300,400,600,700,800,900,300italic,400italic,600italic,700italic,800italic,900italic';
		}
		else
		{
			$font_styles = ':400,700,400italic,700italic';
		}
		
		
		/* ================================================== */
		
		
		$setting_content_font = get_theme_mod( 'setting_content_font', "" );
		
		if ( $setting_content_font != "" )
		{
			?>

<!-- Body Font -->
<link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=<?php echo str_replace( ' ', '+', $setting_content_font ) . $font_styles . $pixelwars_subset; ?>">
<style type="text/css">body, input, textarea, select, button { font-family: "<?php echo $setting_content_font; ?>"; }</style>
			<?php
		}
		
		
		$setting_heading_font = get_theme_mod( 'setting_heading_font', "" );
		
		if ( $setting_heading_font != "" )
		{
			?>

<!-- Heading Font -->
<link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=<?php echo str_replace( ' ', '+', $setting_heading_font ) . $font_styles . $pixelwars_subset; ?>">
<style type="text/css">
h1, h2, h3, h4, h5, h6,
.entry-meta,
.entry-title,
.navigation,
.post-pagination,
tr th,
dl dt,
input[type=submit],
input[type=button],
button,
.button,
label,
.comment .reply,
.comment-meta,
.yarpp-thumbnail-title,
.tab-titles,
.owl-theme .owl-nav [class*='owl-'],
.tptn_title,
.widget_categories ul li.cat-item,
.widget_recent_entries ul li {
	font-family: "<?php echo $setting_heading_font; ?>";
}
</style>
			<?php
		}
		
		
		$setting_menu_font = get_theme_mod( 'setting_menu_font', "" );
		
		if ( $setting_menu_font != "" )
		{
			?>

<!-- Menu Font -->
<link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=<?php echo str_replace( ' ', '+', $setting_menu_font ) . $font_styles . $pixelwars_subset; ?>">
<style type="text/css">.nav-menu { font-family: "<?php echo $setting_menu_font; ?>"; }</style>
			<?php
		}
		
		
		$setting_text_logo_font = get_theme_mod( 'setting_text_logo_font', "" );
		
		if ( $setting_text_logo_font != "" )
		{
			?>

<!-- Text Logo Font -->
<link rel="stylesheet" type="text/css" href="//fonts.googleapis.com/css?family=<?php echo str_replace( ' ', '+', $setting_text_logo_font ) . $font_styles . $pixelwars_subset; ?>">
<style type="text/css">.site-title { font-family: "<?php echo $setting_text_logo_font; ?>"; }</style>
			<?php
		}
		
		
		/* ================================================== */
		
		
		$setting_link_color = get_theme_mod( 'setting_link_color', "" );
		
		if ( $setting_link_color != "" )
		{
			?>

<!-- Link Color -->
<style type="text/css">a { color: <?php echo $setting_link_color; ?>; }</style>
			<?php
		}
		
		
		$setting_link_hover_color = get_theme_mod( 'setting_link_hover_color', "" );
		
		if ( $setting_link_hover_color != "" )
		{
			?>

<!-- Link Hover Color -->
<style type="text/css">a:hover, .nav-menu ul li a:hover { color: <?php echo $setting_link_hover_color; ?>; }</style>
			<?php
		}
		
		
		/* ================================================== */
	}
	
	add_action( 'wp_head', 'pixelwars_theme_customize_css' );
	
	
	function pixelwars_theme_customize_preview_js()
	{
		wp_enqueue_script( 'pixelwars_theme_customizer', get_template_directory_uri() . '/js/wp-theme-customizer.js', null, null, true );
	}
	
	add_action( 'customize_preview_init', 'pixelwars_theme_customize_preview_js' );


/* ============================================================================================================================================= */


	require_once get_template_directory() . '/class-tgm-plugin-activation.php';
	
	function pixelwars_plugins()
	{
		$plugins = array( array('name'     => esc_html__('One Click Demo Import', 'read'),
								'slug'     => 'one-click-demo-import',
								'required' => false));
		
		$config = array('id'           => 'photographer_tgmpa',
						'default_path' => "",
						'menu'         => 'photographer-install-plugins',
						'parent_slug'  => 'themes.php',
						'capability'   => 'edit_theme_options',
						'has_notices'  => true,
						'dismissable'  => true,
						'dismiss_msg'  => 'Install Plugins',
						'is_automatic' => true,
						'message'      => "",
						'strings'      => array('nag_type' => 'updated'));
		
		tgmpa($plugins, $config);
	}
	
	add_action('tgmpa_register', 'pixelwars_plugins');


/* ============================================================================================================================================= */


	function pixelwars_ocdi_import_files()
	{
		return array(array( 'import_file_name'         => 'Demo Import 1',
							'local_import_file'        => trailingslashit(get_template_directory()) . 'demo/photographer.wordpress.2016-11-21.xml',
							'local_import_widget_file' => trailingslashit(get_template_directory()) . 'demo/themes.pixelwars.org-photographer-widgets.wie'));
	}
	
	add_filter('pt-ocdi/import_files', 'pixelwars_ocdi_import_files');


/* ============================================================================================================================================= */


	function pixelwars_options_wp_head()
	{
		?>

<!--[if lt IE 9]>
	<script src="<?php echo get_template_directory_uri(); ?>/js/ie.js"></script>
<![endif]-->
		<?php
		
		$image_logo_height = get_option('image_logo_height', '100');
		
		if ($image_logo_height != '100')
		{
			?>

<style type="text/css">.site-title img { max-height: <?php echo $image_logo_height; ?>px; }</style>
			<?php
		}
		
		$custom_css = stripcslashes( get_option( 'custom_css', "" ) );
	
		if ($custom_css != "")
		{
			echo '<style type="text/css">' . "\n";
			
				echo $custom_css;
			
			echo "\n" . '</style>' . "\n";
		}
		
		$external_css = stripcslashes( get_option( 'external_css', "" ) );
		echo $external_css;
		
		$tracking_code_head = stripcslashes( get_option( 'tracking_code_head', "" ) );
		echo $tracking_code_head;
	}
	
	add_action( 'wp_head', 'pixelwars_options_wp_head' );


/* ============================================================================================================================================= */


	function pixelwars_options_wp_footer()
	{
		$external_js = stripcslashes( get_option( 'external_js', "" ) );
		echo $external_js;
		
		$tracking_code_body = stripcslashes( get_option( 'tracking_code_body', "" ) );
		echo $tracking_code_body;
	}
	
	add_action( 'wp_footer', 'pixelwars_options_wp_footer' );

?>