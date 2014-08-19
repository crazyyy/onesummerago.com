<?php
$options = array(
	array(
		"name" => "General",
		"type" => "title",
		"color" => "gray"
	),
	
	/*** # LOGO # ***/
		array(
	"name" => "Logo",
	"type" => "start"
	),

		array(
		"name" => "Main Text",
		"desc" => "Type the big text content",
		"id" => "main_text",
		"default" => "Your Name",
		"type" => "text",
		),
		
		array(
		"name" => "Subtitle / Slogan",
		"desc" => "Type the small text (slogan) text",
		"id" => "sub_text",
		"default" => "Type here your occupation or something else.",
		"type" => "text",
		),
		
		array(
		"name" => "Image",
		"desc" => "Upload a custom image to display instead text",
		"id" => "logo",
		"default" => "",
		"type" => "upload",
		),
		
		
	array(
	"type" => "end"
	),
	
	/*** # LOGO END # ***/
	
		
	array(
	"name" => "Email",
	"type" => "start"
	),

		array(
		"name" => "Contact Mail",
		"desc" => "Type your email where you will receive the messages.",
		"id" => "email",
		"default" => "",
		"type" => "text",
		),
	
	array(
	"type" => "end"
	),
	
	/*** # EMAIL  END# ***/
	
	array(
	"name" => "Social",
	"type" => "start"
	),
	
		array(
		"name" => "Bebo",
		"desc" => "Bebo Page Address.",
		"id" => "bebo",
		"default" => "",
		"type" => "text",
		),
		
		array(
		"name" => "Blogger",
		"desc" => "Blogger Page Address.",
		"id" => "blogger",
		"default" => "",
		"type" => "text",
		),
		
		array(
		"name" => "Delicious",
		"desc" => "Delicious Page Address.",
		"id" => "delicious",
		"default" => "",
		"type" => "text",
		),
		
		array(
		"name" => "Designmoo",
		"desc" => "Designmoo Page Address.",
		"id" => "designmoo",
		"default" => "",
		"type" => "text",
		),
		
		array(
		"name" => "Deviantart",
		"desc" => "Deviantart Page Address.",
		"id" => "deviantart",
		"default" => "",
		"type" => "text",
		),
		
		array(
		"name" => "Digg",
		"desc" => "Digg Page Address.",
		"id" => "digg",
		"default" => "",
		"type" => "text",
		),
		
		array(
		"name" => "Facebook",
		"desc" => "Facebook Page Address.",
		"id" => "facebook",
		"default" => "",
		"type" => "text",
		),
		
		array(
		"name" => "Flickr",
		"desc" => "Flickr Page Address.",
		"id" => "flickr",
		"default" => "",
		"type" => "text",
		),
		
		array(
		"name" => "Google",
		"desc" => "Google Page Address.",
		"id" => "google",
		"default" => "",
		"type" => "text",
		),
		
		array(
		"name" => "Google Wave",
		"desc" => "Google Wave Page Address.",
		"id" => "google_wave",
		"default" => "",
		"type" => "text",
		),
		
		array(
		"name" => "Linkedin",
		"desc" => "Linkdin Page Address.",
		"id" => "linkedin",
		"default" => "",
		"type" => "text",
		),
		
		array(
		"name" => "Picasa",
		"desc" => "Picasa Page Address.",
		"id" => "picasa",
		"default" => "",
		"type" => "text",
		),
		
		array(
		"name" => "Reddit",
		"desc" => "Reddit Page Address.",
		"id" => "reddit",
		"default" => "",
		"type" => "text",
		),
		
		array(
		"name" => "RSS",
		"desc" => "RSS Page Address.",
		"id" => "rss",
		"default" => "",
		"type" => "text",
		),
		
		array(
		"name" => "Stumbleupon",
		"desc" => "Stumbleupon Page Address.",
		"id" => "stumbleupon",
		"default" => "",
		"type" => "text",
		),
		
		array(
		"name" => "Tumblr",
		"desc" => "Tumblr Page Address.",
		"id" => "tumblr",
		"default" => "",
		"type" => "text",
		),
		
		array(
		"name" => "Twitter",
		"desc" => "Twitter Page Address.",
		"id" => "twitter",
		"default" => "",
		"type" => "text",
		),
		
		array(
		"name" => "Vimeo",
		"desc" => "Vimeo Page Address.",
		"id" => "vimeo",
		"default" => "",
		"type" => "text",
		),
		
		array(
		"name" => "Wordpress",
		"desc" => "Wordpress Page Address.",
		"id" => "wordpress",
		"default" => "",
		"type" => "text",
		),
		
		array(
		"name" => "Yahoo",
		"desc" => "Yahoo Page Address.",
		"id" => "yahoo",
		"default" => "",
		"type" => "text",
		),
		
		array(
		"name" => "Youtube",
		"desc" => "youtube Page Address.",
		"id" => "youtube",
		"default" => "",
		"type" => "text",
		),
		
		
	
	array(
	"type" => "end"
	),
	/*** # SOCIAL  END # ***/
	
	array(
	"name" => "Message",
	"type" => "start"
	),
		array(
		"name" => "Enable Message Box?",
		"desc" => "Check to enable alert message on website.",
		"id" => "enable_msg",
		"default" => true,
		"type" => "toggle",
		),
		
		array(
		"name" => "Title",
		"desc" => "Text display at top.",
		"id" => "title_msg",
		"default" => "Title of message here",
		"type" => "text",
		),
		
		array(
		"name" => "Content",
		"desc" => "Big text displayed as message content.",
		"id" => "content_msg",
		"default" => "Message Content",
		"type" => "textarea",
		),
		
	array(
	"type" => "end"
	),
	/*** # MESSAGE  END # ***/
	
	array(
	"name" => "Favicon",
	"type" => "start"
	),
	
		array(
			"name" => "Custom Favicon",
			"desc" => "Upload a custom favicon (.ico) to display instead default",
			"id" => "favicon",
			"default" => "",
			"type" => "upload",
			),

	array(
	"type" => "end"
	),
	/*** # FAVICON  END # ***/
	
	
	/*MARK*/
);
return array(
	'auto' => true,
	'name' => 'general',
	'options' => $options
);