<?php
	include_once(dirname(__FILE__).'/functions.php');
	htracer_ajax_admin_header();

	echo HTracer::Sanitarize($_POST['str']);
?>