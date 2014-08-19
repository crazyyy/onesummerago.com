<?php
add_action( 'after_setup_theme', 'et_setup_theme' );
if ( ! function_exists( 'et_setup_theme' ) ){
	function et_setup_theme(){
		global $themename, $shortname, $default_colorscheme;
		$themename = "Gleam";
		$shortname = "gleam";
		$default_colorscheme = "Default";

		$template_dir = get_template_directory();

		require_once($template_dir . '/epanel/custom_functions.php');

		require_once($template_dir . '/includes/functions/comments.php');

		require_once($template_dir . '/includes/functions/sidebars.php');

		load_theme_textdomain('Gleam',$template_dir.'/lang');

		require_once($template_dir . '/epanel/core_functions.php');

		require_once($template_dir . '/epanel/post_thumbnails_gleam.php');

		require_once($template_dir . '/includes/additional_functions.php');

		include($template_dir . '/includes/widgets.php');

		add_theme_support( 'automatic-feed-links' );

		if ((get_option($shortname.'_enable_dropdowns') <> 'false') || (get_option($shortname.'_enable_dropdowns_categories') <> 'false')) {
			update_option($shortname.'_enable_dropdowns','false');
			update_option($shortname.'_enable_dropdowns_categories','false');
		}
	}
}

function register_main_menus() {
	register_nav_menus(
		array(
			'primary-menu' => __( 'Primary Menu', 'Gleam' )
		)
	);
}
if (function_exists('register_nav_menus')) add_action( 'init', 'register_main_menus' );

// add Home link to the custom menu WP-Admin page
function et_add_home_link( $args ) {
	$args['show_home'] = true;
	return $args;
}
add_filter( 'wp_page_menu_args', 'et_add_home_link' );

add_action( 'wp_enqueue_scripts', 'et_load_dailyjournal_scripts' );
function et_load_dailyjournal_scripts(){
	if ( !is_admin() ){
		$template_dir = get_template_directory_uri();

		if ( get_option( 'thread_comments' ) ) wp_enqueue_script( 'comment-reply' );

		wp_enqueue_script('easing', $template_dir . '/js/jquery.easing.1.3.js', array('jquery'), '1.0', true);
		wp_enqueue_script('jquery_address', $template_dir . '/js/jquery.address-1.5.min.js', array('jquery'), '1.0', true);
		wp_enqueue_script('mousewheel', $template_dir . '/js/jquery.mousewheel.js', array('jquery'), '1.0', true);
		wp_enqueue_script('jscrollpane', $template_dir . '/js/jquery.jscrollpane.min.js', array('jquery'), '1.0', true);
		wp_enqueue_script('gleam_plugin_fixes', $template_dir . '/js/gleam_plugin_fixes.js', array('jquery'), '1.0', true);
		wp_enqueue_script('custom_script', $template_dir . '/js/custom.js', array('jquery'), '1.0', true);

		$data = array( 'site_url' => trailingslashit( site_url() ), 'theme_url' => $template_dir );
		if ( function_exists('qtrans_getLanguage') ) $data['qtranslate_lang'] = '?lang='.qtrans_getLanguage();
		if ( function_exists( 'icl_object_id' ) ) $data['wpml_lang'] = '?lang=' . ICL_LANGUAGE_CODE;

		wp_localize_script( 'custom_script', 'et_site_data', $data );
		wp_localize_script( 'custom_script', 'et_shortcodes_strings', array( 'previous' => __( 'Previous', 'Gleam' ), 'next' => __( 'Next', 'Gleam' ) ) );
		if ( defined( 'ET_LB_PLUGIN_URI' ) ) wp_localize_script( 'custom_script', 'et_gleam_lb', array( 'plugin_url' => ET_LB_PLUGIN_URI ) );
		wp_localize_script( 'gleam_plugin_fixes', 'et_plugin_data', array( 'folder' => plugins_url() ) );
	}
}

if ( ! function_exists( 'et_list_pings' ) ){
	function et_list_pings($comment, $args, $depth) {
		$GLOBALS['comment'] = $comment; ?>
		<li id="comment-<?php comment_ID(); ?>"><?php comment_author_link(); ?> - <?php comment_excerpt(); ?>
	<?php }
}

if ( ! function_exists( 'et_get_the_author_posts_link' ) ){
	function et_get_the_author_posts_link(){
		global $authordata, $themename;
		$link = sprintf(
			'<a href="%1$s" title="%2$s" rel="author">%3$s</a>',
			esc_url( get_author_posts_url( $authordata->ID, $authordata->user_nicename ) ),
			esc_attr( sprintf( __( 'Posts by %s', $themename ), get_the_author() ) ),
			get_the_author()
		);
		return apply_filters( 'the_author_posts_link', $link );
	}
}

if ( ! function_exists( 'et_get_comments_popup_link' ) ){
	function et_get_comments_popup_link( $zero = false, $one = false, $more = false ){
		global $themename;

		$id = (int) get_the_ID();
		$number = (int) get_comments_number( $id );

		if ( 0 == $number && !comments_open() && !pings_open() ) return;

		if ( $number > 1 )
			$output = str_replace('%', number_format_i18n($number), ( false === $more ) ? __('% Comments', $themename) : $more);
		elseif ( $number == 0 )
			$output = ( false === $zero ) ? __('No Comments',$themename) : $zero;
		else // must be one
			$output = ( false === $one ) ? __('1 Comment', $themename) : $one;

		return '<span class="comments-number">' . '<a href="' . esc_url( get_permalink() . '#respond' ) . '">' . apply_filters('comments_number', $output, $number) . '</a>' . '</span>';
	}
}

if ( ! function_exists( 'et_postinfo_meta' ) ){
	function et_postinfo_meta( $postinfo, $date_format, $comment_zero, $comment_one, $comment_more ){
		global $themename;

		$postinfo_meta = '';

		if ( in_array( 'author', $postinfo ) ){
			$postinfo_meta .= ' ' . esc_html__('By',$themename) . ' ' . et_get_the_author_posts_link();
		}

		if ( in_array( 'date', $postinfo ) )
			$postinfo_meta .= ' ' . esc_html__('on',$themename) . ' ' . get_the_time( $date_format );

		if ( in_array( 'categories', $postinfo ) )
			$postinfo_meta .= ' ' . esc_html__('in',$themename) . ' ' . get_the_category_list(', ');

		if ( in_array( 'comments', $postinfo ) )
			$postinfo_meta .= ' | ' . et_get_comments_popup_link( $comment_zero, $comment_one, $comment_more );

		if ( '' != $postinfo_meta ) $postinfo_meta = __('Posted',$themename) . ' ' . $postinfo_meta;

		echo $postinfo_meta;
	}
}

add_action('et_head_meta','et_add_google_fonts');
function et_add_google_fonts(){
	echo "<link href='http://fonts.googleapis.com/css?family=Droid+Sans:regular,bold' rel='stylesheet' type='text/css' />";
	echo "<link href='http://fonts.googleapis.com/css?family=Open+Sans&subset=latin,cyrillic' rel='stylesheet' type='text/css' />";
}

add_filter('body_class', 'et_add_fullscreen_class');
function et_add_fullscreen_class( $classes )
{
    global $post;

	if ( 'on' == get_post_meta($post->ID,'_et_fullscreen_mode',true) ) $classes[] = 'et_fullscreen_mode';

    return $classes;
}

add_filter( 'et_get_additional_color_scheme', 'et_remove_additional_stylesheet' );
function et_remove_additional_stylesheet( $stylesheet ){
	global $default_colorscheme;
	return $default_colorscheme;
}

function et_epanel_custom_colors_css(){
	global $shortname; ?>

	<style type="text/css">
		body { color: #<?php echo esc_html(get_option($shortname.'_color_mainfont')); ?>; }
		a { color: #<?php echo esc_html(get_option($shortname.'_color_mainlink')); ?>; }
		ul.nav li a { color: #<?php echo esc_html(get_option($shortname.'_color_pagelink')); ?> !important; }
		ul.nav > li.current-menu-item > a { color: #<?php echo esc_html(get_option($shortname.'_color_pagelink_active')); ?> !important; }
		h1, h2, h3, h4, h5, h6, h1 a, h2 a, h3 a, h4 a, h5 a, h6 a { color: #<?php echo esc_html(get_option($shortname.'_color_headings')); ?> !important; }

		#sidebar a { color:#<?php echo esc_html(get_option($shortname.'_color_sidebar_links')); ?>; }
		.footer-widget { color:#<?php echo esc_html(get_option($shortname.'_footer_text')); ?> }
		#footer a, ul#bottom-menu li a { color:#<?php echo esc_html(get_option($shortname.'_color_footerlinks')); ?> }
	</style>

<?php }