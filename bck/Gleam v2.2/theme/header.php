<!DOCTYPE html>
<!--[if IE 6]>
<html id="ie6" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 7]>
<html id="ie7" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 8]>
<html id="ie8" <?php language_attributes(); ?>>
<![endif]-->
<!--[if !(IE 6) | !(IE 7) | !(IE 8)  ]><!-->
<html <?php language_attributes(); ?>>
<!--<![endif]-->
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
	<title><?php elegant_titles(); ?></title>
	<?php elegant_description(); ?>
	<?php elegant_keywords(); ?>
	<?php elegant_canonical(); ?>

	<?php do_action('et_head_meta'); ?>

	<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="screen" />
	<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />

	<!--[if lt IE 7]>
		<link rel="stylesheet" type="text/css" href="<?php echo get_template_directory_uri(); ?>/css/ie6style.css" />
		<script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/js/DD_belatedPNG_0.0.8a-min.js"></script>
		<script type="text/javascript">DD_belatedPNG.fix('img#logo, span.overlay, a.zoom-icon, a.more-icon, #menu, #menu-right, #menu-content, ul#top-menu ul, #menu-bar, .footer-widget ul li, span.post-overlay, #content-area, .avatar-overlay, .comment-arrow, .testimonials-item-bottom, #quote, #bottom-shadow, #quote .container');</script>
	<![endif]-->
	<!--[if IE 7]>
		<link rel="stylesheet" type="text/css" href="<?php echo get_template_directory_uri(); ?>/css/ie7style.css" />
	<![endif]-->
	<!--[if IE 8]>
		<link rel="stylesheet" type="text/css" href="<?php echo get_template_directory_uri(); ?>/css/ie8style.css" />
	<![endif]-->
	<!--[if lt IE 9]>
		<script src="<?php echo get_template_directory_uri(); ?>/js/html5.js" type="text/javascript"></script>
	<![endif]-->

	<script type="text/javascript">
		document.documentElement.className = 'js';
	</script>

	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
	<div id="wrapper">
		<div id="main_content"<?php if ( is_home() ) echo ' class="home_content"'; ?>>
			<header id="main_header">
				<div id="logo_area">
					<?php if ( ( $logo = get_option('gleam_logo') ) && '' != $logo ) { ?>
						<a href="<?php echo esc_url( home_url( '/' ) ); ?>">
							<img src="<?php echo esc_attr( $logo ); ?>" alt="<?php echo esc_attr(get_bloginfo('name')); ?>" id="logo"/>
						</a>
					<?php } else { ?>
						<h1>
							<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php echo get_option('gleam_logo_text'); ?></a>
						</h1>
					<?php } ?>
					<p><?php echo esc_html( get_bloginfo('description') ); ?></p>
				</div> <!-- #logo_area -->

				<?php do_action('et_header_top'); ?>

				<nav id="main_menu">
					<?php
						$menuClass = 'clearfix';
						if ( get_option('gleam_disable_toptier') == 'on' ) $menuClass .= ' et_disable_top_tier';
						$primaryNav = '';
						if (function_exists('wp_nav_menu')) {
							$primaryNav = wp_nav_menu( array( 'theme_location' => 'primary-menu', 'container' => '', 'depth' => '1', 'fallback_cb' => '', 'menu_class' => $menuClass, 'echo' => false ) );
						}
						if ($primaryNav == '') { ?>
							<ul class="<?php echo esc_attr( $menuClass ); ?>">
								<?php if (get_option('gleam_home_link') == 'on') { ?>
									<li <?php if (is_home()) echo('class="current_page_item"') ?>><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e('Home','Gleam') ?></a></li>
								<?php }; ?>

								<?php show_page_menu($menuClass,false,false); ?>
								<?php show_categories_menu($menuClass,false); ?>
							</ul>
						<?php }
						else echo($primaryNav);
					?>
				</nav>
			</header> <!-- end #main_header -->
			<div id="content" <?php body_class(); ?>>