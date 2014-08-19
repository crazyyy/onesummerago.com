var previewSrc = ".png";

var sample = '';

var content = 'Sample Text';
if(selectedText != '') {
	content = selectedText;
}
 
wptsShortcodeAtts={
	attributes:[
		{
			label:"Link URL",
			id:"link",
			help:"ex: http://google.com"
		},
		{
			label:"Color",
			id:"color",
			help:"Select the color of the button.", 
			controlType:"select-control", 
			selectValues:['default', 'black']
		},
		],
	defaultContent:content,
	shortcode:""
};