var previewSrc = ".png";

var sample = '';

var content = 'Sample Text';
if(selectedText != '') {
	content = selectedText;
}
 
wptsShortcodeAtts={
	attributes:[
		{
			label:"Number of Projects",
			id:"number",
			help:"ex: http://google.com"
		},	
		],
	defaultContent:content,
	shortcode:"recent_projects"
};