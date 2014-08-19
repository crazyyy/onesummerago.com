var previewSrc = "";

var sample = '[video type="flash" src="http://www.youtube.com/v/PoTEnaAI9Fo" width="640" height="480"]';

var content = '';
 
wptsShortcodeAtts={
	attributes:[
		{
			label:"Type",
			id:"type",
			controlType:"select-control", 
			selectValues:['flash']
		},
		{
			label:"Source",
			id:"src",
			help:"Video SRC, the file address"
		},
		{
			label:"Width",
			id:"width",
			help:"The image/block width, can be a percentage like 100% or a number without 'px'"
		},
		{
			label:"Height",
			id:"height",
			help:"The image/block height, can be a percentage like 100% or a number without 'px'"
		},
		{
			label:"Autoplay?",
			id:"play",
			help:"Autoplay video?", 
			controlType:"select-control", 
			selectValues:['', 'true']
		},
		{
			label:"Flash Vars",
			id:"flashvars",
			help:"Use it to pass flashvars"
		},
		],
	defaultContent:content,
	shortcode:"video"
};