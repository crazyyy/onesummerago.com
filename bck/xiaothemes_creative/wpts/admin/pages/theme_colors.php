<?php
$options = array(
	array(
		"name" => "Colors",
		"type" => "title",
		"color" => "salmon"
	),
	
			array(
		"name" => "Base Skin",
		"type" => "start"
		),
		
			array(
		"name" => "Base Skin",
		"desc" => "Select a base skin",
		"id" => "base_skin",
		"default" => "photography", 
		"options" => array(
			"photography" => "Photography", "architect" => "Architect",  
		),
		"type" => "select",
		),
		
		
		array(
	"type" => "end"
	),
	
		array(
		"name" => "Base Skin",
		"type" => "start"
		),
		
		array(
		"name" => "Base Color",
		"desc" => "Select a color to use instead the default one..",
		"id" => "custom_base",
		"default" => "",
		"type" => "color",
		),
		
		
		
		
		array(
	"type" => "end"
	),
	

	
	/*MARK*/
);
return array(
	'auto' => true,
	'name' => 'colors',
	'options' => $options
);