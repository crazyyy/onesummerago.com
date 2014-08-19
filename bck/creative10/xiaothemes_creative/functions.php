<?php
	
	// DEFINE THEME DIRECTORY
	define("THEME_DIR", get_template_directory_uri());

	if ( ! isset( $content_width ) ) $content_width = 900;
	
	/*--- REMOVE GENERATOR META TAG ---*/
	remove_action('wp_head', 'wp_generator');
	
	/*--- SUPPORT TO LANGUAGES ---*/
	
	load_theme_textdomain( 'creative', TEMPLATEPATH.'/languages' );

	$locale = get_locale();
	$locale_file = TEMPLATEPATH."/languages/$locale.php";
	if ( is_readable($locale_file) )
		require_once($locale_file);
	
	function is_login_page() {
		return in_array($GLOBALS['pagenow'], array('wp-login.php', 'wp-register.php'));
	}
	
	/*--- REQUIRES ---*/
	require("wpts/wpts_admin_config.php"); 
	
	require("wpts/admin/wpts_init_admin.php"); 
 
	require("wpts/wpts_shortcodes.php"); 

	require("wpts/layout-builder/layout-builder.php"); 
	
	// PORTFOLIO
	
	require("wpts/inc/_portfolio.php"); 
	
	
	
	/*== ENQUEUE STYLES ==**/
	
	function enqueue_styles() {
		if(!is_admin() && !is_login_page()) :

			$base_theme = wpts_get_option("colors", "base_skin");
			
			//wp_enqueue_style("main-style", THEME_DIR ."/style.css", false, "1.0", "all");
			wp_enqueue_style("google-fonts", "http://fonts.googleapis.com/css?family=Signika:300,400,600,700", false, "1.0", "all");	
			wp_enqueue_style("jquery-vegas", THEME_DIR ."/js/vegas/jquery.vegas.css", false, "1.0", "all");
			wp_enqueue_style("galeria", THEME_DIR ."/js/galleria/themes/classic/galleria.classic.css", false, "1.0", "all");
			wp_enqueue_style("tipsy", THEME_DIR ."/js/tipsy/tipsy.css", false, "1.0", "all");
			wp_enqueue_style("prettyPhoto", THEME_DIR ."/js/prettyPhoto/css/prettyPhoto.css", false, "1.0", "all");
			wp_enqueue_style("styles", THEME_DIR ."/css/".$base_theme.".css", false, "1.0", "all");
			wp_enqueue_style("jquery.jscrollpane", THEME_DIR ."/css/jquery.jscrollpane.css", false, "1.0", "all");

			wp_enqueue_style("main-style", THEME_DIR ."/style.css", false, "1.0", "all");


		endif;
	}
	add_action( 'init', 'enqueue_styles' );
	
	/*== ENQUEUE SCRIPTS ==**/
	
	function enqueue_scripts() {
		if(!is_admin() && !is_login_page()) :
			/** REGISTER js/jquery.js **/
			wp_enqueue_script( 'jquery' );
			
			wp_register_script( 'html5-shim', 'http://html5shim.googlecode.com/svn/trunk/html5.js', array(), null, false );
			wp_enqueue_script( 'html5-shim' );

			wp_enqueue_script( 'jquery.easing', get_template_directory_uri() . '/js/jquery.easing.1.3.js', array('jquery'), NULL );
			wp_enqueue_script( 'jquery.form', get_template_directory_uri() . '/js/jquery.form.js', array('jquery'), NULL );
			wp_enqueue_script( 'jquery.vegas', get_template_directory_uri() . '/js/vegas/jquery.vegas.js', array('jquery'), NULL );
			wp_enqueue_script( 'galleria', get_template_directory_uri() . '/js/galleria/galleria-1.2.6.min.js', array('jquery'), NULL );
			wp_enqueue_script( 'galleria.classic', get_template_directory_uri() . '/js/galleria/themes/classic/galleria.classic.min.js', array('jquery'), NULL );
			wp_enqueue_script( 'jquery.tipsy', get_template_directory_uri() . '/js/tipsy/jquery.tipsy.js', array('jquery'), NULL );
			wp_enqueue_script( 'jquery.prettyPhoto', get_template_directory_uri() . '/js/prettyPhoto/js/jquery.prettyPhoto.js', array('jquery'), NULL );
			wp_enqueue_script( 'froogaloop2', get_template_directory_uri() . '/js/vimeo/froogaloop2.min.js', array('jquery'), NULL );
			wp_enqueue_script( 'global', get_template_directory_uri() . '/js/global.js', array('jquery'), NULl );
			wp_enqueue_script( 'jquery.mousewheel', get_template_directory_uri() . '/js/jquery.mousewheel.js', array('jquery'), NULl );
			wp_enqueue_script( 'jquery.jscrollpane.min', get_template_directory_uri() . '/js/jquery.jscrollpane.min.js', array('jquery'), NULl );

			wp_enqueue_script( 'jquery.history', get_template_directory_uri() . '/js/history/jquery.history.js', array('jquery'), NULl );

			wp_enqueue_script( 'jquery-applyheight', get_template_directory_uri() . '/js/jquery.applyheight.js', array('jquery'), NULL );

			wp_register_script( 'load-posts', get_template_directory_uri() . '/js/load-posts.js', array('jquery'), NULL );					
			
		endif;
	}
	add_action( 'init', 'enqueue_scripts' );

	/*== ENABLE CUSTOM MENUS ==**/
	if ( function_exists( 'register_nav_menu' ) ) {
	
		register_nav_menu( 'main_menu', 'Main Menu' );

	}
	
	/*== SIDEBARS WIDGET AREAS ==**/
	
	register_sidebar( array(
		'id'          => 'right-sidebar',
		'name'        => 'Right Sidebar',
		'description' => 'Displayed at content\'s right on pages like: Blog, Single Post and Page with Sidebar.',
		'before_title'  => '<h3 class="widgettitle"><span>',
		'after_title'   => '</span></h3>',
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>'
	) );
	
	add_filter('widget_text', 'do_shortcode');
	
	/**== BLOG POSTS ==**/
	
	if (function_exists( 'add_theme_support' ) ) {
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'automatic-feed-links' );
	}
	
	if ( function_exists( 'add_image_size' ) ) { 
		/* TEMPLATE - add_image_size( 'blog-size', 700, 290 );	*/
		add_image_size( 'portfolio-thumb-size', 122, 107, true );
		add_image_size( 'portfolio-recent-size', 149, 43, true );
		add_image_size( 'blog-size', 180, 140, true );
	}
	
	/**== BREADCRUMBS ==**/
	
	function the_breadcrumbs() {
 
		$showOnHome = 0; // 1 - show breadcrumbs on the homepage, 0 - don't show
		$delimiter = ''; // delimiter between crumbs
		$home = 'Home'; // text for the 'Home' link
		$showCurrent = 1; // 1 - show current post/page title in breadcrumbs, 0 - don't show
		$before = '<em class="active">'; // tag before the current crumb
		$after = '</em>'; // tag after the current crumb
		 
		global $post;
		$homeLink = get_bloginfo('url');
		 
		if (is_home() || is_front_page()) {
		 
			if ($showOnHome == 1) echo '<div id="crumbs"><a href="' . $homeLink . '">' . $home . '</a></div>';
		 
		} else {
		 
			echo '<p id="breadcrumbs"><a href="' . $homeLink . '">' . $home . '</a> ' . $delimiter . ' ';
		 
			if ( is_category() ) {
			  global $wp_query;
			  $cat_obj = $wp_query->get_queried_object();
			  $thisCat = $cat_obj->term_id;
			  $thisCat = get_category($thisCat);
			  $parentCat = get_category($thisCat->parent);
			  if ($thisCat->parent != 0) echo(get_category_parents($parentCat, TRUE, ' ' . $delimiter . ' '));
			  echo $before . __('Archive by category "', 'creative') . single_cat_title('', false) . '"' . $after;
		 
			} elseif ( is_day() ) {
			  echo '<a href="' . get_year_link(get_the_time('Y')) . '">' . get_the_time('Y') . '</a> ' . $delimiter . ' ';
			  echo '<a href="' . get_month_link(get_the_time('Y'),get_the_time('m')) . '">' . get_the_time('F') . '</a> ' . $delimiter . ' ';
			  echo $before . get_the_time('d') . $after;
		 
			} elseif ( is_month() ) {
			  echo '<a href="' . get_year_link(get_the_time('Y')) . '">' . get_the_time('Y') . '</a> ' . $delimiter . ' ';
			  echo $before . get_the_time('F') . $after;
		 
			} elseif ( is_year() ) {
			  echo $before . get_the_time('Y') . $after;
		 
			} elseif ( is_single() && !is_attachment() ) {
			  if ( get_post_type() != 'post' ) {
				$post_type = get_post_type_object(get_post_type());
				$slug = $post_type->rewrite;
				echo '<a href="' . $homeLink . '/' . $slug['slug'] . '/">' . $post_type->labels->singular_name . '</a> ' . $delimiter . ' ';
				if ($showCurrent == 1) echo $before . get_the_title() . $after;
			  } else {
				$cat = get_the_category(); $cat = $cat[0];
				echo get_category_parents($cat, TRUE, ' ' . $delimiter . ' ');
				if ($showCurrent == 1) echo $before . get_the_title() . $after;
			  }
		 
			} elseif ( !is_single() && !is_page() && get_post_type() != 'post' && !is_404() ) {
			  $post_type = get_post_type_object(get_post_type());
			  echo $before . $post_type->labels->singular_name . $after;
		 
			} elseif ( is_attachment() ) {
			  $parent = get_post($post->post_parent);
			  $cat = get_the_category($parent->ID); $cat = $cat[0];
			  echo get_category_parents($cat, TRUE, ' ' . $delimiter . ' ');
			  echo '<a href="' . get_permalink($parent) . '">' . $parent->post_title . '</a> ' . $delimiter . ' ';
			  if ($showCurrent == 1) echo $before . get_the_title() . $after;
		 
			} elseif ( is_page() && !$post->post_parent ) {
			  if ($showCurrent == 1) echo $before . get_the_title() . $after;
		 
			} elseif ( is_page() && $post->post_parent ) {
			  $parent_id  = $post->post_parent;
			  $breadcrumbs = array();
			  while ($parent_id) {
				$page = get_page($parent_id);
				$breadcrumbs[] = '<a href="' . get_permalink($page->ID) . '">' . get_the_title($page->ID) . '</a>';
				$parent_id  = $page->post_parent;
			  }
			  $breadcrumbs = array_reverse($breadcrumbs);
			  foreach ($breadcrumbs as $crumb) echo $crumb . ' ' . $delimiter . ' ';
			  if ($showCurrent == 1) echo $before . get_the_title() . $after;
		 
			} elseif ( is_search() ) {
			  echo $before . __('Search results for "', 'creative') . get_search_query() . '"' . $after;
		 
			} elseif ( is_tag() ) {
			  echo $before .  __('Posts tagged "', 'creative') . single_tag_title('', false) . '"' . $after;
		 
			} elseif ( is_author() ) {
			   global $author;
			  $userdata = get_userdata($author);
			  echo $before .  __('Articles posted by ', 'creative') . $userdata->display_name . $after;
		 
			} elseif ( is_404() ) {
			  echo $before .  __('Error 404', 'creative') . $after;
			}

			echo '</p>';
		  }
	}
	
	/*== PAGINATION ==*/
	
	function nav_pagination($range = 2, $pages = '' )
	{  
		$showitems = ($range * 2)+1;  

		global $paged;
		global $bid;
		if(empty($paged)) $paged = 1;

		if($pages == '')
		{
			global $wp_query;
			$pages = $wp_query->max_num_pages;
			if(!$pages)
			{
				$pages = 1;
			}
		}   

		if(1 != $pages)
		{
			echo '<nav>';
			echo '<p class="pagination">';
			/*if($paged > 2 && $paged > $range+1 && $showitems < $pages) echo "<a href='".get_pagenum_link(1)."'>&lt;</a>";
			if($paged > 1 && $showitems < $pages) echo "<a href='".get_pagenum_link($paged - 1)."'>&gt;</a>";*/

			for ($i=1; $i <= $pages; $i++)
			{
				if (1 != $pages &&( !($i >= $paged+$range+1 || $i <= $paged-$range-1) || $pages <= $showitems ))
				{
					 echo ($paged == $i)? "<span class='active'>".$i."</span>":"<a href='".get_pagenum_link($i)."#".$bid."'>".$i."</a>";
				}
			}

			/*if ($paged < $pages && $showitems < $pages) echo "<a href='".get_pagenum_link($paged + 1)."'>&rsaquo;</a>";  
			if ($paged < $pages-1 &&  $paged+$range-1 < $pages && $showitems < $pages) echo "<a href='".get_pagenum_link($pages)."'>&raquo;</a>";*/
			echo "</p>\n";
			echo '</nav>';
		}
	}
		
?>