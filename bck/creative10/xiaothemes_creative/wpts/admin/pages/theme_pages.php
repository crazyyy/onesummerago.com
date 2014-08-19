<?php
$options = array(
	array(
		"name" => "Pages Ids",
		"type" => "title",
		"color" => "green"
	),
	
			array(
		"name" => "Pages IDS",
		"type" => "start"
		),
		
			array(
		"name" => "Page IDs",
		"desc" => "Type page ids separated by comma, ex.: 40;20;10",
		"id" => "ids",
		"default" => "",
		"type" => "page_selector",
		),
		
		
		
		array(
	"type" => "end"
	),
		
	/*MARK*/
);
return array(
	'auto' => true,
	'name' => 'pages',
	'options' => $options
);