var previewSrc = "";

var sample = '[video type="youtube" clip_id="1w7OgIMMRc4" width="640" height="480"]';

var content = '';
 
wptsShortcodeAtts={
	attributes:[
		{
			label:"Type",
			id:"type",
			controlType:"select-control", 
			selectValues:['youtube']
		},
		{
			label:"Clip ID",
			id:"clip_id",
			help:"The Youtube video clip id"
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
		],
	defaultContent:content,
	shortcode:"video"
};