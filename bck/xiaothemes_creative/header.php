<?php

	require_once("wpts/layout-builder/init.php");

	get_template_part("ajax", "template");
?>
<!DOCTYPE HTML>
<!--[if IE 7 ]>    <html <?php language_attributes(); ?> class="ie ie7"> <![endif]--> 
<!--[if IE 8 ]>    <html <?php language_attributes(); ?> class="ie ie8"> <![endif]--> 
<!--[if IE 9 ]>    <html <?php language_attributes(); ?> class="ie"> <![endif]--> 
<!--[if !IE]><!--> <html <?php language_attributes(); ?>> <!--<![endif]-->

<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta charset="<?php bloginfo( 'charset' ); ?>" />

	<title><?php bloginfo( 'name' ); ?> - <?php is_home() ? bloginfo('description') : wp_title(''); ?></title>

	<meta name="description" content=""/>
	<meta name="keywords" content=""/>
	<meta name="robots" content="index, follow"/>
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

	<?php
    $favicon =  THEME_DIR .'/images/favicon.ico';
    if(wpts_get_option("general", "favicon") != "")
        $favicon = wpts_get_option("general", "favicon");
	?>

	<link rel="shortcut icon" href="<?php echo $favicon; ?>" />
	<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> RSS2 Feed" href="<?php bloginfo('rss2_url'); ?>" />
	<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />

	<script type="text/javascript">
		var THEME_DIR = "<?php echo THEME_DIR; ?>";
	</script>

	<script>if ( typeof window.JSON === 'undefined' ) { document.write('<script src="<?php echo THEME_DIR; ?>/js/history/json2.js"><\/script>'); }</script>
	
	<!-- WP_HEAD() -->
	<?php wp_head(); ?>

	<?php $counter = 14; ?>

	<style type="text/css">
		<?php
	
		$tpageids = wpts_get_option("pages", "ids");
				
		$tpages = explode ( ";" , $tpageids );

		foreach($tpages as $tID) {

			if($tID != '') {

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
						#<?php echo $tblockId; ?> {
							position: absolute;
							padding: 0px 0px 0px 30px;
							top: 210px;
							opacity: 0;
							display: none;
							z-index: <?php echo $counter; ?>;
						}
					<?php
						$counter++;
						endif;
				} // end if
			} // foreach
		?>

	</style>
	
</head>

<body <?php body_class( $class ); ?>>
		
	<!-- HEADER --> 
	<div id="header">
		<?php if(wpts_get_option("general", "logo") == '') : ?>
		<span class="title"><?php echo wpts_get_option("general", "main_text"); ?></span>
		<span class="subtitle"><?php echo wpts_get_option("general", "sub_text"); ?></span>
		<?php else : ?>
			<div class="logo"><img src="<?php echo wpts_get_option("general", "logo"); ?>" alt="<?php bloginfo("name"); ?>" /></div>
		<?php endif; ?>
	</div>


