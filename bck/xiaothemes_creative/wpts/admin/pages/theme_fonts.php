<?php
$options = array(
	array(
		"name" => "Fonts",
		"type" => "title",
		"color" => "cyan"
	),
	
			array(
		"name" => "Base Skin",
		"type" => "start"
		),
		
			array(
		"name" => "Main Font",
		"desc" => "Select a font to use instead default",
		"id" => "Main Font",
		"default" => "crimson",
		"options" => array(
			"signika" => "Signika",  
		),
		"type" => "select",
		),
		
		
		array(
	"type" => "end"
	),
	
		

	
	/*MARK*/
);
return array(
	'auto' => true,
	'name' => 'fonts',
	'options' => $options
);