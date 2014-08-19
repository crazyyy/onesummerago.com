<?php

	function wpts_admin_init()
	{
		wp_enqueue_style('jquery');
		wp_enqueue_style("wpts-admin", get_template_directory_uri()."/wpts/admin/css/wpts-admin.css", false, "1.0", "all");
		wp_enqueue_style('thickbox');
		
		wp_enqueue_script( 'wpts-color-picker', get_template_directory_uri() . '/wpts/admin/js/colorpicker/colorpicker.js', array('jquery'), NULL );
		
		wp_enqueue_style( 'wpts-color-picker-style', get_template_directory_uri() . '/wpts/admin/js/colorpicker/colorpicker.css', array(), NULL, 'all' );
		wp_enqueue_script( 'wpts-color-picker-custom', get_template_directory_uri() . '/wpts/admin/js/colorpicker/customCP.js', array('wpts-color-picker'), NULL );	
		
		if(@$_GET["page"] == "theme_slider") {
			wp_enqueue_style( 'wpts-slidermanager', get_template_directory_uri() . '/wpts/admin/slider_manager/slider_manager.css', array(), NULL, 'all' );
			wp_enqueue_script( 'wpts-slidemanager', get_template_directory_uri() . '/wpts/admin/slider_manager/slider_manager.js', array('jquery'), NULL );
		}
		
		wp_enqueue_script('jquery-ui-core');
		wp_enqueue_script('jquery-ui-widget');
		wp_enqueue_script('	jquery-ui-mouse');
		wp_enqueue_script('jquery-ui-sortable');
		
		wp_enqueue_script('media-upload');
		wp_enqueue_script('thickbox');
		wp_register_script('my-upload', get_template_directory_uri().'/wpts/admin/js/upload-box.js', array('jquery','media-upload','thickbox'));
		wp_enqueue_script('my-upload');
	}
			
	function wpts_admin_setup()
	{
		
		// SET MAIN MENU
		add_menu_page("Creative", "Creative", "administrator", 'theme_general', 'get_option_page' );
		
		// SET SUB MENUS
		add_submenu_page('theme_general', 'General', 'General', "administrator", 'theme_general', 'get_option_page');
		add_submenu_page('theme_general', 'Pages', 'Pages', "administrator", 'theme_pages', 'get_option_page');
		add_submenu_page('theme_general', 'Colors', 'Colors', "administrator", 'theme_colors', 'get_option_page');
		//add_submenu_page('theme_general', 'Fonts', 'Fonts', "administrator", 'theme_fonts', 'get_option_page');
		add_submenu_page('theme_general', 'Slider Manager', 'Slider Manager', "administrator", 'theme_slider', 'get_option_page');
		//MARKER - DONT REMOVE THIS LINE //
	}
		
	add_action('admin_init', 'wpts_admin_init');
	add_action('admin_menu', 'wpts_admin_setup');
	
	function get_option_page()
	{
		include_once ('wptsGenerator.php');
				
		$page = include('pages' . "/" . $_GET['page'] . '.php');
				
		if($page['auto']){
			new wptsGenerator($page['name'],$page['options']);
		}
	}
	


?>