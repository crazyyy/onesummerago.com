<?php

	define('THEME_SLUG', "");

	global $theme_options;
	$theme_options = array();
	$option_files = array(
		'theme_general',
		'theme_pages',
		'theme_colors',
		'theme_slider',
		/*!MARK!*/
	);
			
	foreach($option_files as $file)
	{
		$page = include ("admin/pages/" . $file.'.php');
		$theme_options[$page['name']] = array();
		foreach($page['options'] as $option) {
			if (isset($option['default'])) {
				$theme_options[$page['name']][$option['id']] = $option['default'];
			}
		}
		
		$theme_options[$page['name']] = array_merge((array) $theme_options[$page['name']], (array) get_option(THEME_SLUG . '_' . $page['name']));
	}
	
	function wpts_get_option($page, $name = NULL) 
	{
		global $theme_options;
		
		if ($name == NULL) {
			if (isset($theme_options[$page])) {
				return $theme_options[$page];
			} else {
				return false;
			}
		} else {
			if (isset($theme_options[$page][$name])) {
				return $theme_options[$page][$name];
			} else {
				return false;
			}
		}
	}

?>