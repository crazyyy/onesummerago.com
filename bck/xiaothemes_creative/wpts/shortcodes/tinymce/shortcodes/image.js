var previewSrc = ".png";

var sample = '';

var content = 'Sample Text';
if(selectedText != '') {
	content = selectedText;
}
 
wptsShortcodeAtts={
	attributes:[
		{
			label:"Image Source",
			id:"src",
			help:"ex: http://google.com",
			value: ""
		},
		
		{
			label:"Image Width",
			id:"width",
			help:"ex: http://google.com",
			value: ""
		},
		
		{
			label:"Image Height",
			id:"height",
			help:"ex: http://google.com",
			value: ""
		},
		
		{
			label:"Padding CSS Rule (optional)",
			id:"padding",
			help:"ex: http://google.com",
			value: ""
		},
		{
			label:"Align",
			id:"align",
			help:"Select the color of the button.", 
			controlType:"select-control", 
			selectValues:['', 'left', 'right','center']
		},
		],
	defaultContent:content,
	shortcode:"image"
};