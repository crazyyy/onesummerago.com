<?php 
header("Content-Type:text/javascript");

//Setup URL to WordPres
$absolute_path = __FILE__;
$path_to_wp = explode( 'wp-content', $absolute_path );
$wp_url = $path_to_wp[0];

//Access WordPress
require_once( $wp_url.'/wp-load.php' );

//URL to TinyMCE plugin folder
$plugin_url = get_template_directory_uri().'/wpts/shortcodes/tinymce/';
?>
(function(){
	
	var icon_url = '<?php echo $plugin_url; ?>' + '/tb_icon.png';

	tinymce.create(
		"tinymce.plugins.wptsShortcodes",
		{
			init: function(d,e) {
					
					
					
					d.addCommand( "wptsOpenDialog",function(a,c){
						
						// Grab the selected text from the content editor.
						selectedText = '';
					
						if ( d.selection.getContent().length > 0 ) {
					
							selectedText = d.selection.getContent();
							
						} // End IF Statement
						
						wptsSelectedShortcodeType = c.identifier;
						wptsSelectedShortcodeTitle = c.title;
						
						jQuery.get(e+"/dialog.php",function(b){
							
							var a;
							
							jQuery('#wpts-shortcode-options').addClass( 'shortcode-' + wptsSelectedShortcodeType );
							
							// Skip the popup on certain shortcodes.
							
							switch ( wptsSelectedShortcodeType ) {

								case 'raw':
								a = '[raw]'+selectedText+'[/raw]';
								tinyMCE.activeEditor.execCommand("mceInsertContent", false, a);
								break;
								
								case 'br':
								a  = '[br]';
								tinyMCE.activeEditor.execCommand("mceInsertContent", false, a);
								break;
								
								case 'spoiler':
								a = '[spoiler]'+selectedText+'[/spoiler]';
								tinyMCE.activeEditor.execCommand("mceInsertContent", false, a);
								break;
								
								case 'info':
								a = '[info]<br />'+selectedText+'<br />[/info]';
								tinyMCE.activeEditor.execCommand("mceInsertContent", false, a);
								break;
								
								case 'success':
								a = '[success]<br />'+selectedText+'<br />[/success]';
								tinyMCE.activeEditor.execCommand("mceInsertContent", false, a);
								break;
								
								case 'error':
								a = '[error]<br />'+selectedText+'<br />[/error]';
								tinyMCE.activeEditor.execCommand("mceInsertContent", false, a);
								break;
								
								case 'notice':
								a = '[notice]<br />'+selectedText+'<br />[/notice]';
								tinyMCE.activeEditor.execCommand("mceInsertContent", false, a);
								break;
								
								case 'blank':
								a  = '[blank]';
								tinyMCE.activeEditor.execCommand("mceInsertContent", false, a);
								break;
								
								case 'jslide':
								a = '[jslide]<br />'+selectedText+'<br />[/jslide]';
								tinyMCE.activeEditor.execCommand("mceInsertContent", false, a);
								break;
								
								case 'divider':
								a  = '[divider]';
								tinyMCE.activeEditor.execCommand("mceInsertContent", false, a);
								break;
								
								case 'divider_top':
								a  = '[divider_top]';
								tinyMCE.activeEditor.execCommand("mceInsertContent", false, a);
								break;
								
								case 'divider_empty':
								a  = '[divider_empty]';
								tinyMCE.activeEditor.execCommand("mceInsertContent", false, a);
								break;
								
								case 'tweet':
								a  = '[tweet]';
								tinyMCE.activeEditor.execCommand("mceInsertContent", false, a);
								break;
								
								case 'tabs':					
								a = '[raw]<br />[tabs]<br />';
								a += '[tab title="Title 1"]<br />';
								a += selectedText;
								a += '<br />[/tab]<br />';
								a += '[tab title="Title 2"]<br />';
								a += 'Your content here';
								a += '<br />[/tab]';
								a += '<br />[/tabs]<br />[/raw]';
								tinyMCE.activeEditor.execCommand("mceInsertContent", false, a);
								break;
								
								case 'tabs_framed':					
								a = '[raw]<br />[tabs_framed]<br />';
								a += '[tab title="Title 1"]<br />';
								a += selectedText;
								a += '<br />[/tab]<br />';
								a += '[tab title="Title 2"]<br />';
								a += 'Your content here';
								a += '<br />[/tab]';
								a += '<br />[/tabs_framed]<br />[/raw]';
								tinyMCE.activeEditor.execCommand("mceInsertContent", false, a);
								break;
								
								case 'tabs_button':					
								a = '[raw]<br />[tabs_button]<br />';
								a += '[tab title="Title 1"]<br />';
								a += selectedText;
								a += '<br />[/tab]<br />';
								a += '[tab title="Title 2"]<br />';
								a += 'Your content here';
								a += '<br />[/tab]';
								a += '<br />[/tabs_button]<br />[/raw]';
								tinyMCE.activeEditor.execCommand("mceInsertContent", false, a);
								break;
								
								case 'tabs_vertical':					
								a = '[raw]<br />[tabs_vertical]<br />';
								a += '[tab title="Title 1"]<br />';
								a += selectedText;
								a += '<br />[/tab]<br />';
								a += '[tab title="Title 2"]<br />';
								a += 'Your content here';
								a += '<br />[/tab]';
								a += '<br />[/tabs_vertical]<br />[/raw]';
								tinyMCE.activeEditor.execCommand("mceInsertContent", false, a);
								break;
								
								case 'accordions':					
								a = '[raw]<br />[accordions]<br />';
								a += '[accordion title="ACCORDION ONE"]<br />';
								a += 'Your content here';
								a += '<br />[/accordion]<br />';
								a += '[accordion title="ACCORDION TWO"]<br />';
								a += 'Your content here';
								a += '<br />[/accordion]';
								a += '<br />[/accordions]<br />[/raw]';
								tinyMCE.activeEditor.execCommand("mceInsertContent", false, a);
								break;
								
								case 'toggle':					
								a = '[raw]<br />[toggle title="Your Title"]<br />';
								a += 'Your content here';
								a += '<br />[/toggle]<br />[/raw]';
								tinyMCE.activeEditor.execCommand("mceInsertContent", false, a);
								break;
								
								case 'testimonial_widget':					
								a = '[testimonial_widget]<br />';
								a += 'Add Testimonial Slider Items here';
								a += '<br />[/testimonial_widget]';
								tinyMCE.activeEditor.execCommand("mceInsertContent", false, a);
								break;
								
								// -------------------------------------------------------------
								// Individual Column
								// -------------------------------------------------------------
								
								// one-sixth
								case 'one-sixth':
								a = '[one_sixth]'+selectedText+'[/one_sixth]';
								tinyMCE.activeEditor.execCommand("mceInsertContent", false, a);
								break;
								
								// one-fifth
								case 'one-fifth':
								a = '[one_fifth]'+selectedText+'[/one_fifth]';
								tinyMCE.activeEditor.execCommand("mceInsertContent", false, a);
								break;
								
								// two-fifth
								case 'two-fifth':
								a = '[two_fifth]'+selectedText+'[/two_fifth]';
								tinyMCE.activeEditor.execCommand("mceInsertContent", false, a);
								break;
								
								// three-fifth
								case 'three-fifth':
								a = '[three_fifth]'+selectedText+'[/three_fifth]';
								tinyMCE.activeEditor.execCommand("mceInsertContent", false, a);
								break;
								
								// four-fifth
								case 'four-fifth':
								a = '[four_fifth]'+selectedText+'[/four_fifth]';
								tinyMCE.activeEditor.execCommand("mceInsertContent", false, a);
								break;
								
								// one-fourth
								case 'one-fourth':
								a = '[one_fourth]'+selectedText+'[/one_fourth]';
								tinyMCE.activeEditor.execCommand("mceInsertContent", false, a);
								break;
								
								// three-fourth
								case 'three-fourth':
								a = '[three_fourth]'+selectedText+'[/three_fourth]';
								tinyMCE.activeEditor.execCommand("mceInsertContent", false, a);
								break;
								
								// one-third
								case 'one-third':
								a = '[one_third]'+selectedText+'[/one_third]';
								tinyMCE.activeEditor.execCommand("mceInsertContent", false, a);
								break;
								
								// two-third
								case 'two-third':
								a = '[two_third]'+selectedText+'[/two_third]';
								tinyMCE.activeEditor.execCommand("mceInsertContent", false, a);
								break;
								
								// one-half
								case 'one-half':
								a = '[one_half]'+selectedText+'[/one_half]';
								tinyMCE.activeEditor.execCommand("mceInsertContent", false, a);
								break;
								
								// -------------------------------------------------------------
								// 2 Columns
								// -------------------------------------------------------------
								
								// 50% | 50%
								case '2-col-50-50':
								a  = '[raw]<br />';
								a += '[one_half] content... [/one_half]<br />';
								a += '[one_half last] content... [/one_half]<br />';
								
								a += '[/raw]';		
								tinyMCE.activeEditor.execCommand("mceInsertContent", false, a);
								break;
								
								// 25% | 75%
								case '2-col-25-75':
								a  = '[raw]<br />';
								a += '[one_fourth]content...[/one_fourth]<br />';
								a += '[three_fourth last]content...[/three_fourth]<br />';
								
								a += '[/raw]';				
								tinyMCE.activeEditor.execCommand("mceInsertContent", false, a);
								break;
								
								// 75% | 25%
								case '2-col-75-25':
								a  = '[raw]<br />';
								a += '[three_fourth]content...[/three_fourth]<br />';
								a += '[one_fourth last]content...[/one_fourth]<br />';
								
								a += '[/raw]';					
								tinyMCE.activeEditor.execCommand("mceInsertContent", false, a);
								break;
								
								// 33% | 66%
								case '2-col-33-66':
								a  = '[raw]<br />';
								a += '[one_third]content...[/one_third]<br />';
								a += '[two_third last]content...[/two_third]<br />';
								
								a += '[/raw]';				
								tinyMCE.activeEditor.execCommand("mceInsertContent", false, a);
								break;
								
								// 66% | 33%
								case '2-col-66-33':
								a  = '[raw]<br />';
								a += '[two_third]content...[/two_third]<br />';
								a += '[one_third last]content...[/one_third]<br />';
								
								a += '[/raw]';	
								tinyMCE.activeEditor.execCommand("mceInsertContent", false, a);
								break;
								
								// 20% | 80%
								case '2-col-20-80':
								a  = '[raw]<br />';
								a += '[one_fifth]content...[/one_fifth]<br />';
								a += '[four_fifth last]content...[/four_fifth]<br />';
								
								a += '[/raw]';	
								tinyMCE.activeEditor.execCommand("mceInsertContent", false, a);
								break;
								
								// 80% | 20%
								case '2-col-80-20':
								a  = '[raw]<br />';
								a += '[four_fifth]content...[/four_fifth]<br />';
								a += '[one_fifth last]content...[/one_fifth]<br />';
								
								a += '[/raw]';		
								tinyMCE.activeEditor.execCommand("mceInsertContent", false, a);
								break;
								
								// -------------------------------------------------------------
								// 3 Columns
								// -------------------------------------------------------------
								
								// 33% | 33% | 33%
								case '3-col-33-33-33':
								a  = '[raw]<br />';
								a += '[one_third]content...[/one_third]<br />';
								a += '[one_third]content...[/one_third]<br />';
								a += '[one_third last]content...[/one_third]<br />';
								
								a += '[/raw]';					
								tinyMCE.activeEditor.execCommand("mceInsertContent", false, a);
								break;
								
								// 25% | 25% | 50%
								case '3-col-25-25-50':
								a  = '[raw]<br />';
								a += '[one_fourth]content...[/one_fourth]<br />';
								a += '[one_fourth]content...[/one_fourth]<br />';
								a += '[one_half last]content...[/one_half]<br />';
								
								a += '[/raw]';					
								tinyMCE.activeEditor.execCommand("mceInsertContent", false, a);
								break;
								
								// 25% | 50% | 25%
								case '3-col-25-50-25':
								a  = '[raw]<br />';
								a += '[one_fourth]content...[/one_fourth]<br />';
								a += '[one_half]content...[/one_half]<br />';
								a += '[one_fourth last]content...[/one_fourth]<br />';
								
								a += '[/raw]';					
								tinyMCE.activeEditor.execCommand("mceInsertContent", false, a);
								break;
								
								// 50% | 25% | 25%
								case '3-col-50-25-25':
								a  = '[raw]<br />';
								a += '[one_half]content...[/one_half]<br />';
								a += '[one_fourth]content...[/one_fourth]<br />';
								a += '[one_fourth last]content...[/one_fourth]<br />';
								
								a += '[/raw]';					
								tinyMCE.activeEditor.execCommand("mceInsertContent", false, a);
								break;
								
								// 20% | 20% | 60%
								case '3-col-20-20-60':
								a  = '[raw]<br />';
								a += '[one_fifth]content...[/one_fifth]<br />';
								a += '[one_fifth]content...[/one_fifth]<br />';
								a += '[three_fifth last]content...[/three_fifth]<br />';
								
								a += '[/raw]';					
								tinyMCE.activeEditor.execCommand("mceInsertContent", false, a);
								break;
								
								// 20% | 60% | 20%
								case '3-col-20-60-20':
								a  = '[raw]<br />';
								a += '[one_fifth]content...[/one_fifth]<br />';
								a += '[three_fifth]content...[/three_fifth]<br />';
								a += '[one_fifth last]content...[/one_fifth]<br />';
								
								a += '[/raw]';					
								tinyMCE.activeEditor.execCommand("mceInsertContent", false, a);
								break;
								
								// 60% | 20% | 20%
								case '3-col-60-20-20':
								a  = '[raw]<br />';
								a += '[three_fifth]content...[/three_fifth]<br />';
								a += '[one_fifth]content...[/one_fifth]<br />';
								a += '[one_fifth last]content...[/one_fifth]<br />';
								
								a += '[/raw]';					
								tinyMCE.activeEditor.execCommand("mceInsertContent", false, a);
								break;										
								
								// -------------------------------------------------------------
								// 4 Columns
								// -------------------------------------------------------------
								
								// 25% | 25% | 25% | 25%
								case '4-col-25-25-25-25':
								a  = '[raw]<br />';
								a += '[one_fourth]content...[/one_fourth]<br />';
								a += '[one_fourth]content...[/one_fourth]<br />';
								a += '[one_fourth]content...[/one_fourth]<br />';
								a += '[one_fourth last]content...[/one_fourth]<br />';
								
								a += '[/raw]';					
								tinyMCE.activeEditor.execCommand("mceInsertContent", false, a);
								break;
								
								// 20% | 20% | 20% | 40%
								case '4-col-20-20-20-40':
								a  = '[raw]<br />';
								a += '[one_fifth]content...[/one_fifth]<br />';
								a += '[one_fifth]content...[/one_fifth]<br />';
								a += '[one_fifth]content...[/one_fifth]<br />';
								a += '[two_fifth last]content...[/two_fifth]<br />';
								
								a += '[/raw]';					
								tinyMCE.activeEditor.execCommand("mceInsertContent", false, a);
								break;
								
								// 20% | 20% | 40% | 20%
								case '4-col-20-20-40-20':
								a  = '[raw]<br />';
								a += '[one_fifth]content...[/one_fifth]<br />';
								a += '[one_fifth]content...[/one_fifth]<br />';
								a += '[two_fifth]content...[/two_fifth]<br />';
								a += '[one_fifth last]content...[/one_fifth]<br />';
								
								a += '[/raw]';					
								tinyMCE.activeEditor.execCommand("mceInsertContent", false, a);
								break;
								
								// 20% | 40% | 20% | 20%
								case '4-col-20-40-20-20':
								a  = '[raw]<br />';
								a += '[one_fifth]content...[/one_fifth]<br />';
								a += '[two_fifth]content...[/two_fifth]<br />';
								a += '[one_fifth]content...[/one_fifth]<br />';
								a += '[one_fifth last]content...[/one_fifth]<br />';
								
								a += '[/raw]';					
								tinyMCE.activeEditor.execCommand("mceInsertContent", false, a);
								break;
								
								// 40% | 20% | 20% | 20%
								case '4-col-40-20-20-20':
								a  = '[raw]<br />';
								a += '[two_fifth]content...[/two_fifth]<br />';
								a += '[one_fifth]content...[/one_fifth]<br />';
								a += '[one_fifth]content...[/one_fifth]<br />';
								a += '[one_fifth last]content...[/one_fifth]<br />';
								
								a += '[/raw]';					
								tinyMCE.activeEditor.execCommand("mceInsertContent", false, a);
								break;
								
								// -------------------------------------------------------------
								// 5 Columns
								// -------------------------------------------------------------
								
								// 20% | 20% | 20% | 20% | 20%
								case '5-col-20-20-20-20-20':
								a  = '[raw]<br />';
								a += '[one_fifth]content...[/one_fifth]<br />';
								a += '[one_fifth]content...[/one_fifth]<br />';
								a += '[one_fifth]content...[/one_fifth]<br />';
								a += '[one_fifth]content...[/one_fifth]<br />';
								a += '[one_fifth last]content...[/one_fifth]<br />';
								
								a += '[/raw]';	
								tinyMCE.activeEditor.execCommand("mceInsertContent", false, a);
								break;
								
								// -------------------------------------------------------------
								// 6 Columns
								// -------------------------------------------------------------
								
								// 15% | 15% | 15% | 15% | 15% | 15%
								case '6-col-15-15-15-15-15-15':
								a  = '[raw]<br />';
								a += '[one_sixth]content...[/one_sixth]<br />';
								a += '[one_sixth]content...[/one_sixth]<br />';
								a += '[one_sixth]content...[/one_sixth]<br />';
								a += '[one_sixth]content...[/one_sixth]<br />';
								a += '[one_sixth]content...[/one_sixth]<br />';
								a += '[one_sixth last]content...[/one_sixth]<br />';
								
								a += '[/raw]';	
								tinyMCE.activeEditor.execCommand("mceInsertContent", false, a);
								break;
								
								default:
								
								jQuery("#wpts-dialog").remove();
								jQuery("body").append(b);
								jQuery("#wpts-dialog").hide();
								var f=jQuery(window).width();
								b=jQuery(window).height();
								f=720<f?720:f;
								f-=80;
								b-=120;
							
							tb_show("WPTS - Insert "+ wptsSelectedShortcodeTitle +" Shortcode", "#TB_inline?width="+f+"&height="+b+"&inlineId=wpts-dialog");jQuery("#wpts-shortcode-options h3:first").text(""+c.title+" Shortcode Settings");
							
								break;
							
							} // End SWITCH Statement
						
						}
												 
					)
					 
					} 
				);

				},
					
				createControl:function(d,e){
				
						if(d=="wpts_shortcodes_button"){
						
							d=e.createMenuButton("wpts_shortcodes_button",{
								title:"WPTS - Insert Shortcode",
								image:icon_url,
								icons:false
								});
								
								var a=this;d.onRenderMenu.add(function(c,b){
																		
									c=b.addMenu({title:"Buttons"});
										a.addWithDialog(c,"Normal Button","button");
										c.addSeparator();
										a.addWithDialog(c,"Clean Button","clean_button");
										c.addSeparator();
										a.addWithDialog(c,"Social Button","social_button");
										
									// ----------------------
									b.addSeparator();
									// ----------------------
									
									c=b.addMenu({title:"Utils"});
										a.addWithDialog(c,"Escape Auto WP / raw","raw");
										c.addSeparator();
										a.addWithDialog(c,"Embed PDF","pdf");
										c.addSeparator();
										a.addWithDialog(c,"SyntaxHighlight","sh");
										c.addSeparator();
										a.addWithDialog(c,"Snapshot","snapshot");
										c.addSeparator();
										a.addWithDialog(c,"P Element","p");
										c.addSeparator();
										a.addWithDialog(c,"DIV Element","div");
										c.addSeparator();
										a.addWithDialog(c,"BR Element","br");
										c.addSeparator();
										a.addWithDialog(c,"Spoiler","spoiler");
										c.addSeparator();
										a.addWithDialog(c,"Popup","popup");
										c.addSeparator();
										a.addWithDialog(c,"Embed File","show_file");
										c.addSeparator();
										a.addWithDialog(c,"Donate Link","donate");
										c.addSeparator();
										a.addWithDialog(c,"Blank","blank");
										c.addSeparator();
										a.addWithDialog(c,"Tooltip","tooltip");
										
									// ----------------------
									b.addSeparator();
									// ----------------------

									c=b.addMenu({title:"Widgets"});
										/*a.addWithDialog(c,"Google Chart","gchart");
										c.addSeparator();*/
										a.addWithDialog(c,"Google Maps","gmap");
										c.addSeparator();
										a.addWithDialog(c,"Google Chart","gchart");
										c.addSeparator();
										a.addWithDialog(c,"Related Posts","related_posts");
										c.addSeparator();
										a.addWithDialog(c,"Popular Posts","popular_posts");
										c.addSeparator();
										a.addWithDialog(c,"Recent Posts","recent_posts");
										c.addSeparator();
										a.addWithDialog(c,"Twitter Feeds","twitter");
										c.addSeparator();
										a.addWithDialog(c,"Flickr Images","flickr");
										c.addSeparator();
										a.addWithDialog(c,"RSS Feeds","rss");
										c.addSeparator();
										a.addWithDialog(c,"Testimonial Slider","testimonial_widget");
										a.addWithDialog(c,"Testimonial Slider Item","testimonial");
										c.addSeparator();
										a.addWithDialog(c,"Testimonial Block","testimonial_block");
										
									// ----------------------
									b.addSeparator();
									// ----------------------
									c=b.addMenu({title:"Typography"});
										a.addWithDialog(c,"Color Header","color_header");
										c.addSeparator();
										a.addWithDialog(c,"Image Header","image_header");
										c.addSeparator();
										a.addWithDialog(c,"Dropcap","dropcap");
										c.addSeparator();
										a.addWithDialog(c,"Highlight","highlight");
										c.addSeparator();
										a.addWithDialog(c,"Pullquote","pullquote");
										c.addSeparator();
										a.addWithDialog(c,"Blockquote","blockquote");
									
									// ----------------------
									b.addSeparator();
									// ----------------------
									
									c=b.addMenu({title:"Messages"});
										a.addWithDialog(c,"Info Box","info");
										a.addWithDialog(c,"Success Box","success");
										a.addWithDialog(c,"Error Box","error");
										a.addWithDialog(c,"Notice Box","notice");
										c.addSeparator();
										a.addWithDialog(c,"Notification","notification");
									
									// ----------------------
									b.addSeparator();
									// ----------------------
									
									c=b.addMenu({title:"Individual Column"});
										
										a.addWithDialog(c,"1/6 - [one_sixth]","one-sixth");
										c.addSeparator();
										a.addWithDialog(c,"1/5 - [one_fifth]","one-fifth");
										a.addWithDialog(c,"2/5 - [two_fifth]","two-fifth");
										a.addWithDialog(c,"3/5 - [three_fifth]","three-fifth");
										a.addWithDialog(c,"4/5 - [four_fifth]","four-fifth");
										a.addWithDialog(c,"4/5 - [four_fifth]","four-fifth");
										c.addSeparator();
										a.addWithDialog(c,"1/4 - [one_fourth]","one-fourth");
										a.addWithDialog(c,"3/4 - [three_fourth]","three-fourth");
										c.addSeparator();
										a.addWithDialog(c,"1/3 - [one_third]","one-third");
										a.addWithDialog(c,"2/3 - [two_third]","two-third");
										c.addSeparator();
										a.addWithDialog(c,"1/2 - [one_half]","one-half");
									
									// ----------------------
									b.addSeparator();
									// ----------------------
									
									c=b.addMenu({title:"2 Columns"});
										a.addWithDialog(c,"50% | 50%","2-col-50-50");
										a.addWithDialog(c,"25% | 75%","2-col-25-75");
										a.addWithDialog(c,"75% | 25%","2-col-75-25");
										a.addWithDialog(c,"33% | 66%","2-col-33-66");
										a.addWithDialog(c,"66% | 33%","2-col-66-33");
										a.addWithDialog(c,"20% | 80%","2-col-20-80");
										a.addWithDialog(c,"80% | 20%","2-col-80-20");
									c=b.addMenu({title:"3 Columns"});
										a.addWithDialog(c,"33% | 33% | 33%","3-col-33-33-33");
										a.addWithDialog(c,"25% | 25% | 50%","3-col-25-25-50");
										a.addWithDialog(c,"25% | 50% | 25%","3-col-25-50-25");
										a.addWithDialog(c,"50% | 25% | 25%","3-col-50-25-25");
										a.addWithDialog(c,"20% | 20% | 60%","3-col-20-20-60");
										a.addWithDialog(c,"20% | 60% | 20%","3-col-20-60-20");
										a.addWithDialog(c,"60% | 20% | 20%","3-col-60-20-20");
									c=b.addMenu({title:"4 Columns"});
										a.addWithDialog(c,"25% | 25% | 25% | 25%","4-col-25-25-25-25");
										a.addWithDialog(c,"20% | 20% | 20% | 40%","4-col-20-20-20-40");
										a.addWithDialog(c,"20% | 20% | 40% | 20%","4-col-20-20-40-20");
										a.addWithDialog(c,"20% | 40% | 20% | 20%","4-col-20-40-20-20");
										a.addWithDialog(c,"40% | 20% | 20% | 20%","4-col-40-20-20-20");
									c=b.addMenu({title:"5 Columns"});
										a.addWithDialog(c,"20% | 20% | 20% | 20% | 20%","5-col-20-20-20-20-20");
									c=b.addMenu({title:"6 Columns"});
										a.addWithDialog(c,"15% | 15% | 15% | 15% | 15% | 15%","6-col-15-15-15-15-15-15");

									// ----------------------
									b.addSeparator();
									// ----------------------
									
									c=b.addMenu({title:"Images"});
										a.addWithDialog(c,"Shadows","shadow");
										c.addSeparator();
										a.addWithDialog(c,"Image Frame","image_frame");
										c.addSeparator();
										a.addWithDialog(c,"Lightbox","lightbox");
										c.addSeparator();
										a.addWithDialog(c,"Mosaic Hover","mosaic");
									
									// ----------------------
									b.addSeparator();
									// ----------------------
									
									c=b.addMenu({title:"Sliders"});
										a.addWithDialog(c,"Flex Slider","flex_slider");
										a.addWithDialog(c,"Flex Item", "flex_item");
										c.addSeparator();
										a.addWithDialog(c,"jCarousel","jcarousel");
										a.addWithDialog(c,"jCarousel Slide","jslide");
										
									// ----------------------
									b.addSeparator();
									// ----------------------
									
									c=b.addMenu({title:"Icons"});
										a.addWithDialog(c,"Payment Icons","pay_icon");
										c.addSeparator();
										a.addWithDialog(c,"Social Icon","social");
										c.addSeparator();
										a.addWithDialog(c,"List Icon","list");
										a.addWithDialog(c,"Li Icon","li");
										c.addSeparator();
										a.addWithDialog(c,"Entypo B&W Pictograms","icon");
										a.addWithDialog(c,"Color Icons","color_icon");
									
									// ----------------------
									b.addSeparator();
									// ----------------------
									
									c=b.addMenu({title:"Dividers"});
										a.addWithDialog(c,"Divider Line","divider");
										c.addSeparator();
										a.addWithDialog(c,"Divider Top","divider_top");
										c.addSeparator();
										a.addWithDialog(c,"Divider Empty","divider_empty");
									
									// ----------------------
									b.addSeparator();
									// ----------------------
									
									c=b.addMenu({title:"Social"});
										a.addWithDialog(c,"Retweet","tweet_button");
										c.addSeparator();
										a.addWithDialog(c,"Twitter Counter","twitter_follow");
										c.addSeparator();
										a.addWithDialog(c,"TweetMeme","tweet");
										c.addSeparator();
										a.addWithDialog(c,"Pinterest","pin");
										c.addSeparator();
										a.addWithDialog(c,"Facebook Likes","fb_like");
										c.addSeparator();
										a.addWithDialog(c,"Google Plus","gplus");
									
									// ----------------------
									b.addSeparator();
									// ----------------------
									
									c=b.addMenu({title:"Organizer"});
										a.addWithDialog(c,"Table","table");
										c.addSeparator();
										a.addWithDialog(c,"Toggle","toggle");
										c.addSeparator();
										a.addWithDialog(c,"Accordions","accordions");
										c.addSeparator();
										a.addWithDialog(c,"Normal Tabs","tabs");
										a.addWithDialog(c,"Framed Tabs","tabs_framed");
										a.addWithDialog(c,"Button Tabs","tabs_button");
										a.addWithDialog(c,"Vertical Tabs","tabs_vertical");
									
									// ----------------------
									b.addSeparator();
									// ----------------------
									
									c=b.addMenu({title:"Video"});
										a.addWithDialog(c,"HTML5","html5");
										c.addSeparator();
										a.addWithDialog(c,"Flash","flash");
										c.addSeparator();
										a.addWithDialog(c,"Vimeo","vimeo");
										c.addSeparator();
										a.addWithDialog(c,"Youtube","youtube");
										c.addSeparator();
										a.addWithDialog(c,"Dailymotion","dailymotion");
									
									// ----------------------
									b.addSeparator();
									// ----------------------
									
									c=b.addMenu({title:"Price Tables"});
										a.addWithDialog(c,"Pricing Table","pricing_table");
										c.addSeparator();
										a.addWithDialog(c,"Pricing Section","pricing_section");
									
									// ----------------------
									b.addSeparator();
									// ----------------------

							});
							
							return d
						
						} // End IF Statement
						
						return null
					},
		
				addImmediate:function(d,e,a){d.add({title:e,onclick:function(){tinyMCE.activeEditor.execCommand("mceInsertContent",false,a)}})},
				
				addWithDialog:function(d,e,a){d.add({title:e,onclick:function(){tinyMCE.activeEditor.execCommand("wptsOpenDialog",false,{title:e,identifier:a})}})},
		
				getInfo:function(){ return{longname:"wpts Shortcode Generator",author:"VisualShortcodes.com",authorurl:"http://visualshortcodes.com",infourl:"http://visualshortcodes.com/shortcode-ninja",version:"1.0"} }
			}
		);
		
		tinymce.PluginManager.add("wptsShortcodes",tinymce.plugins.wptsShortcodes)
	}
)();
