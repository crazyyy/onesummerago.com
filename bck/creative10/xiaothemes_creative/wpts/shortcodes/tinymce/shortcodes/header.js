var previewSrc = ".png";

var sample = '';

var content = 'Sample Text';
if(selectedText != '') {
	content = selectedText;
}
 
wptsShortcodeAtts={
	attributes:[
		{
			label:"Title 1st Line",
			id:"title",
			help:"ex: http://google.com"
		},
		
		{
			label:"Title 2nd Line",
			id:"subtitle",
			help:"ex: http://google.com"
		},

		],
	defaultContent:content,
	shortcode:"header"
};