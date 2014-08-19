var previewSrc = "";

var sample = '[video type="html5" mp4="http://video-js.zencoder.com/oceans-clip.mp4" webm="http://video-js.zencoder.com/oceans-clip.webm" ogg="http://video-js.zencoder.com/oceans-clip.ogv" poster="http://video-js.zencoder.com/oceans-clip.png" width="auto" height="auto" preload="true" links="false"]';

var content = '';
 
wptsShortcodeAtts={
	attributes:[
		{
			label:"Type",
			id:"type",
			controlType:"select-control", 
			selectValues:['html5']
		},
		{
			label:"MP4 URL",
			id:"mp4",
			help:"Video SRC - .mp4 version"
		},
		{
			label:"Webm URL",
			id:"webm",
			help:"Video SRC - .webm version"
		},
		{
			label:"OGG URL",
			id:"ogg",
			help:"Video SRC - .ogg version"
		},
		{
			label:"Poster",
			id:"poster",
			help:"Image displayed when video isn't playing"
		},
		{
			label:"Width",
			id:"width",
			help:"The image/block width, can be a percentage like 100%, 'auto', or a number without 'px'"
		},
		{
			label:"Height",
			id:"height",
			help:"The image/block height, can be a percentage like 100%, 'auto', or a number without 'px'"
		},
		{
			label:"Preload?",
			id:"preload",
			help:"Preload video?", 
			controlType:"select-control", 
			selectValues:['', 'true']
		},
		{
			label:"Autoplay?",
			id:"autoplay",
			help:"Autoplay video?", 
			controlType:"select-control", 
			selectValues:['', 'true']
		},
		{
			label:"Show Download Links?",
			id:"links",
			help:"Show video download links?", 
			controlType:"select-control", 
			selectValues:['', 'true']
		},
		],
	defaultContent:content,
	shortcode:"video"
};