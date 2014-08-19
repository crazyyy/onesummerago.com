<?php

/**
*	Setup Page side bar
**/
if ( function_exists('register_sidebar') )
    register_sidebar(array('name' => 'Page Sidebar'));
    
/**
*	Setup Blog side bar
**/
if ( function_exists('register_sidebar') )
    register_sidebar(array('name' => 'Blog Sidebar'));
    
/**
*	Setup Contact side bar
**/
if ( function_exists('register_sidebar') )
    register_sidebar(array('name' => 'Contact Sidebar'));
    
/**
*	Setup Single Post side bar
**/
if ( function_exists('register_sidebar') )
    register_sidebar(array('name' => 'Single Post Sidebar'));
    
if ( function_exists('register_sidebar') )
    register_sidebar(array('name' => 'Archives Sidebar'));
    
if ( function_exists('register_sidebar') )
    register_sidebar(array('name' => 'Category Sidebar'));
    
if ( function_exists('register_sidebar') )
    register_sidebar(array('name' => 'Search Sidebar'));
    
if ( function_exists('register_sidebar') )
    register_sidebar(array('name' => 'Tag Sidebar'));


//Register dynamic sidebar
$dynamic_sidebar = get_option('pp_sidebar');

if(!empty($dynamic_sidebar))
{
	foreach($dynamic_sidebar as $sidebar)
	{
		if ( function_exists('register_sidebar') )
	    register_sidebar(array('name' => $sidebar));
	}
}

?>