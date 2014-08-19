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
			help:"ex: http://google.com"
		},
		
		{
			label:"Image Description",
			id:"description",
			help:"ex: http://google.com"
		},
		{
			label:"Image Title",
			id:"title",
			help:"ex: http://google.com"
		},
		
		
		],
	defaultContent:content,
	shortcode:"slide"
};