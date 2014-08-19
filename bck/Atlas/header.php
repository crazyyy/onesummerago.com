<?php
/**
 * The Header for the template.
 *
 * @package WordPress
 */
 
$pp_theme_version = THEMEVERSION;
session_start();
 
?>
<!doctype html>
<html lang="ru-RU" prefix="og: http://ogp.me/ns# fb: http://ogp.me/ns/fb#" class="no-js">
	<head>
		<meta charset="UTF-8">
		<title><?php wp_title(''); ?><?php if(wp_title('', false)) { echo ' :'; } ?> <?php bloginfo('name'); ?></title>
		<!-- dns prefetch -->
		<link href="//www.google-analytics.com" rel="dns-prefetch">
		<link rel="profile" href="http://gmpg.org/xfn/11" />
		<link rel="stylesheet" type="text/css" media="all" href="http://onesummerago.com/wp-content/themes/Atlas/style.css" />
		<link href='http://fonts.googleapis.com/css?family=PT+Sans:400,700,400italic,700italic&subset=latin,cyrillic' rel='stylesheet' type='text/css'>
		<!-- icons -->
		<link href="http://onesummerago.com/wp-content/themes/Atlas/img/favicon.ico" rel="shortcut icon">
		<!-- meta -->
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<meta name="viewport" content="width=device-width,initial-scale=1.0">
		<meta name="description" content="<?php bloginfo('description'); ?>" />
		<!-- Template stylesheet -->
		<link rel='stylesheet' id='screen_css-css'  href='http://onesummerago.com/wp-content/themes/Atlas/css/screen.min.css' type='text/css' media='all' />
		<link rel='stylesheet' id='fancybox_css-css'  href='http://onesummerago.com/wp-content/themes/Atlas/js/fancybox/jquery.fancybox-1.3.0.css?ver=1.8' type='text/css' media='all' />
		<link rel='stylesheet' id='videojs_css-css'  href='http://onesummerago.com/wp-content/themes/Atlas/js/video-js.css?ver=1.8' type='text/css' media='all' />
		
		<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
		<script type="text/javascript" charset="utf-8" src="http://ajax.googleapis.com/ajax/libs/swfobject/2.1/swfobject.js"></script>

	<?php
		wp_enqueue_script("jquery", "http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js", false, $pp_theme_version);
		wp_enqueue_script("jquery.ui_js", "http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js", false, $pp_theme_version);
		wp_enqueue_script("fancybox_js", get_stylesheet_directory_uri()."/js/fancybox/jquery.fancybox-1.3.0.pack.js", false, $pp_theme_version);
		wp_enqueue_script("jQuery_easing", get_stylesheet_directory_uri()."/js/jquery.easing.js", false, $pp_theme_version);
		wp_enqueue_script("jQuery_nivo", get_stylesheet_directory_uri()."/js/jquery.nivoslider.js", false, $pp_theme_version);
		wp_enqueue_script("jQuery_gmap", get_stylesheet_directory_uri()."/js/gmap.js", false, $pp_theme_version);
		wp_enqueue_script("jQuery_validate", get_stylesheet_directory_uri()."/js/jquery.validate.js", false, $pp_theme_version);
		
		wp_enqueue_script("jquery.tubular.js", get_stylesheet_directory_uri()."/js/jquery.tubular.js", false, $pp_theme_version);
		
		wp_enqueue_script("browser_js", get_stylesheet_directory_uri()."/js/browser.js", false, $pp_theme_version);
		wp_enqueue_script("video_js", get_stylesheet_directory_uri()."/js/video.js", false, $pp_theme_version);
		wp_enqueue_script("jquery_backstretch", get_stylesheet_directory_uri()."/js/jquery.backstretch.js", false, $pp_theme_version);
		wp_enqueue_script("hint.js", get_stylesheet_directory_uri()."/js/hint.js", false, $pp_theme_version);
		wp_enqueue_script("supersized.3.1.3.min.js", get_stylesheet_directory_uri()."/js/supersized.3.1.3.min.js", false, $pp_theme_version);
		wp_enqueue_script("jquery.flip.min.js", get_stylesheet_directory_uri()."/js/jquery.flip.min.js", false, $pp_theme_version);
		wp_enqueue_script("jquery.mousewheel.min.js", get_stylesheet_directory_uri()."/js/jquery-mousewheel-3.0.4/jquery.mousewheel.min.js", false, $pp_theme_version);
		wp_enqueue_script("custom_js", get_stylesheet_directory_uri()."/js/custom.js", false, $pp_theme_version);
	?> 

	<?php wp_head(); ?>

	<!--[if IE]>
	<link rel="stylesheet" href="<?php echo get_stylesheet_directory_uri(); ?>/css/ie.css" type="text/css" media="all"/>
	<![endif]-->

	<!--[if IE 7]>
	<link rel="stylesheet" href="<?php echo get_stylesheet_directory_uri(); ?>/css/ie7.css" type="text/css" media="all"/>
	<![endif]-->
</head>

<body <?php body_class(); ?>>

	<!-- Begin template wrapper -->
	<div id="wrapper">
		<div id="menu_wrapper">
		    <!-- Begin main nav -->
		    <?php 	
				//Get page nav
				wp_nav_menu( 
						array( 
							'menu_id'			=> 'main_menu',
							'menu_class'		=> 'nav',
							'theme_location' 	=> 'primary-menu',
						) 
				); 
		    ?>
		    <!-- End main nav -->
		</div>