<?php
session_start();
$pp_url = 'http://onesummerago.com/wp-content/themes/Atlas/';

if(isset($_GET['pp_menu_style']))
{
	$_SESSION['pp_menu_style'] = $_GET['pp_menu_style'];
}

if(isset($_GET['pp_homepage_style']))
{
	$_SESSION['pp_homepage_style'] = $_GET['pp_homepage_style'];
	header( 'Location: '.$pp_url ) ;
	exit;
}

header( 'Location: '.$_SERVER['HTTP_REFERER'] ) ;
?>