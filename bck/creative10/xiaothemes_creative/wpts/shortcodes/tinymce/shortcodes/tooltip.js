var previewSrc = ".png";

var sample = '';

var content = 'Sample Text';
if(selectedText != '') {
	content = selectedText;
}
 
wptsShortcodeAtts={
	attributes:[
		{
			label:"Href",
			id:"href",
			help:"ex: http://google.com"
		},
		
		{
			label:"Tooltip's Text",
			id:"title",
			help:"ex: http://google.com"
		},
		{
			label:"Target",
			id:"target",
			help:"example", 
			controlType:"select-control", 
			selectValues:['', '_blank']
		},
		],
	defaultContent:content,
	shortcode:"tooltip"
};