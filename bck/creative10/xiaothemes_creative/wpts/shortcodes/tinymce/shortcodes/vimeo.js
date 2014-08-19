var previewSrc = "";

var sample = '[video type="vimeo" clip_id="15068747" width="600" height="400"]';

var content = '';
 
wptsShortcodeAtts={
	attributes:[
		{
			label:"Type",
			id:"type",
			controlType:"select-control", 
			selectValues:['vimeo']
		},
		{
			label:"Clip ID",
			id:"clip_id",
			help:"The vimeo video clip id"
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
			label:"Title",
			id:"title",
			help:"The vimeo video title"
		},
		],
	defaultContent:content,
	shortcode:"video"
};