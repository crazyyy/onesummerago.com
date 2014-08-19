<!DOCTYPE HTML>
<!--[if IE 7 ]>    <html <?php language_attributes(); ?> class="ie ie7"> <![endif]--> 
<!--[if IE 8 ]>    <html <?php language_attributes(); ?> class="ie ie8"> <![endif]--> 
<!--[if IE 9 ]>    <html <?php language_attributes(); ?> class="ie"> <![endif]--> 
<!--[if !IE]><!--> <html <?php language_attributes(); ?>> <!--<![endif]-->
<head>
	<meta charset="UTF-8">
	<title><?php wp_title(''); ?><?php if(wp_title('', false)) { echo ' :'; } ?> <?php bloginfo('name'); ?></title>

	<!-- Meta -->
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=0;">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-status-bar-style" content="black">
	
	<meta name="description" content="<?php bloginfo('description'); ?>">

	<link rel="shortcut icon" href="<?php echo get_template_directory_uri(); ?>/favicon.ico">

	<!-- CSS + jQuery + JavaScript -->
	<?php wp_head(); ?>
	
	<script>
		if ( typeof window.JSON === 'undefined' ) { document.write('<script src="http://onesummerago.com/wp-content/themes/xiaothemes_creative/js/history/json2.js"><\/script>'); }
	</script>
	
	<!-- WP_HEAD() -->
	<link rel='stylesheet' id='shortcodes-style-css'  href='<?php echo get_template_directory_uri(); ?>/wpts/shortcodes/assets/css/shortcodes68b3.css?ver=1' type='text/css' media='all' />
	<link href='http://fonts.googleapis.com/css?family=Didact+Gothic&subset=latin,cyrillic-ext,cyrillic' rel='stylesheet' type='text/css'>
	<link href='http://fonts.googleapis.com/css?family=Bad+Script&subset=latin,cyrillic' rel='stylesheet' type='text/css'>
	<link rel='stylesheet' id='jquery-vegas-css'  href='<?php echo get_template_directory_uri(); ?>/js/vegas/jquery.vegas.css?ver=1.0' type='text/css' media='all' />
	<link rel='stylesheet' id='galeria-css'  href='<?php echo get_template_directory_uri(); ?>/js/galleria/themes/classic/galleria.classic.css?ver=1.0' type='text/css' media='all' />
	<link rel='stylesheet' id='tipsy-css'  href='<?php echo get_template_directory_uri(); ?>/js/tipsy/tipsy.css?ver=1.0' type='text/css' media='all' />
	<link rel='stylesheet' id='prettyPhoto-css'  href='<?php echo get_template_directory_uri(); ?>/js/prettyPhoto/css/prettyPhoto.css?ver=1.0' type='text/css' media='all' />
	<link rel='stylesheet' id='styles-css'  href='<?php echo get_template_directory_uri(); ?>/css/photography.css?ver=1.0' type='text/css' media='all' />
	<link rel='stylesheet' id='jquery.jscrollpane-css'  href='<?php echo get_template_directory_uri(); ?>/css/jquery.jscrollpane.css?ver=1.0' type='text/css' media='all' />

	<script type="text/javascript">
		var THEME_DIR = "http://onesummerago.com/wp-content/themes/xiaothemes_creative/";
	</script>
	
	

	<script type='text/javascript' src='http://html5shim.googlecode.com/svn/trunk/html5.js'></script>
	
	<script type='text/javascript' src='<?php echo get_template_directory_uri(); ?>/js/jquery.easing.1.3.js'></script>
	<script type='text/javascript' src='<?php echo get_template_directory_uri(); ?>/js/jquery.form.js'></script>
	<script type='text/javascript' src='<?php echo get_template_directory_uri(); ?>/js/vegas/jquery.vegas.js'></script>
	<script type='text/javascript' src='<?php echo get_template_directory_uri(); ?>/js/galleria/galleria-1.2.6.min.js'></script>
	<script type='text/javascript' src='<?php echo get_template_directory_uri(); ?>/js/galleria/themes/classic/galleria.classic.min.js'></script>
	<script type='text/javascript' src='<?php echo get_template_directory_uri(); ?>/js/tipsy/jquery.tipsy.js'></script>
	<script type='text/javascript' src='<?php echo get_template_directory_uri(); ?>/js/prettyPhoto/js/jquery.prettyPhoto.js'></script>
	<script type='text/javascript' src='<?php echo get_template_directory_uri(); ?>/js/vimeo/froogaloop2.min.js'></script>
	<script type='text/javascript' src='<?php echo get_template_directory_uri(); ?>/js/global.js'></script>
	<script type='text/javascript' src='<?php echo get_template_directory_uri(); ?>/js/jquery.mousewheel.js'></script>
	<script type='text/javascript' src='<?php echo get_template_directory_uri(); ?>/js/jquery.jscrollpane.min.js'></script>
	<script type='text/javascript' src='<?php echo get_template_directory_uri(); ?>/js/history/jquery.history.js'></script>
	<script type='text/javascript' src='<?php echo get_template_directory_uri(); ?>/js/jquery.applyheight.js'></script>

</head>

<body <?php body_class(); ?>>
		
	<!-- HEADER --> 
	<div id="header">
		<span class="title"><?php bloginfo('name'); ?></span>
		<span class="subtitle"><?php bloginfo('description'); ?></span>
	</div>



	<!-- MENU --> 
	<div id="menu">
		<ul>
			<li><a href="#portfolio" id="menu_portfolio">Portfolio</a></li>
			
			<li><a href="#about" id="menu_about">About</a></li>
			
			<li><a href="#blog" id="menu_blog">Blog</a></li>
			<li><a href="http://onesummerago.com/kolbasa.htm" id="menu_blog">Колбаса</a></li>
<!--			<li><a href="#reel" id="menu_reel">Reel</a></li>  -->
			<li><a href="#slider" id="menu_slider">Slider</a></li>
			<li><a href="#contact" id="menu_contact">Contact</a></li>
<!--			<li><a href="#awards" id="menu_awards">Awards</a></li>   -->
		</ul>
	</div>
	
	
	<!-- MENU MARKER -->
	<div id="marker">
		<img src="<?php echo get_template_directory_uri(); ?>/images/menu_marker.png" alt="marker"/>
	</div>

	
	<!-- BASE --> 
	<div id="base">
		<span class="close"><a href="#" id="close_base_bt" title="Close"><img src="<?php echo get_template_directory_uri(); ?>/images/close_big.png" alt="close"/></a></span>
	</div>

	<div id="slider" class="creative-block">
		<div class="creative-content" id="slider-content">
			<div class="content-inner">
				<div class="builder-full">
					<div class="gallery">
						<img src="http://onesummerago.com/wp-content/uploads/2012/06/slider_31.jpg" alt="Sample of image with external link" title="Title Image" />
						<img src="http://onesummerago.com/wp-content/uploads/2012/06/slider_21.jpg" alt="Sample of image with external link" title="Image Title" />
						<img src="http://onesummerago.com/wp-content/uploads/2012/06/slider_1.jpg" alt="Sample of image with external link" title="Image title" />
					</div>
				</div>						
			</div>
		</div>
	</div><!-- slider -->