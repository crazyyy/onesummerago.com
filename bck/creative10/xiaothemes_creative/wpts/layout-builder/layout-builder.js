
	function replaceAll(string, token, newtoken) {
		while (string.indexOf(token) != -1) {
			string = string.replace(token, newtoken);
		}
		return string;
	}

	jQuery(document).ready( function($) {
	
		var currentWidget;
		var tid = 'tw';
		
		$( ".builder-content" ).sortable();
		//$( ".builder-content" ).disableSelection();
		
		$( ".layout-content" ).sortable();
		//$( ".layout-content" ).disableSelection();
		
		/** SAVE RICH TEXT **/
		$(".save-rich-button").live("click", function() {
			tinyMCE.execCommand('mceRemoveControl', false, tid);
			var rich = $("#tw").val();
			
			/*rich = rich.replace("<ul>\t", "<ul>");
			rich = rich.replace("<ul>\r", "<ul>");
			rich = rich.replace("<ul>\n", "<ul>");*/
			rich = replaceAll(rich, "<ul>\n", "<ul>");
			
			/*rich = rich.replace("\n	<li>", "<li>");
			rich = rich.replace("	<li>", "<li>");
			rich = rich.replace("	<li>", "<li>");
			rich = rich.replace("	<li>", "<li>");*/
			rich = replaceAll(rich, "	<li>", "<li>");
			
			/*rich = rich.replace("</li>\t", "</li>");
			rich = rich.replace("</li>	", "</li>");
			rich = rich.replace("</li>\n", "</li>");
			rich = rich.replace("</li>\n", "</li>");*/
			rich = replaceAll(rich, "</li>\n", "</li>");
			
			/*rich = rich.replace("\n</ul>", "</ul>");*/
			rich = replaceAll(rich, "\n</ul>", "</ul>");
			
			currentWidget.find("textarea").val( rich );
			var title = $("#editor-widget-name").val();
			
			currentWidget.find(".wid-title").val( title );
			currentWidget.find(".edit-widget").html( '# ' + title );
			
			tinyMCE.execCommand('mceAddControl', false, tid);
			$('.builder-modal').css("display", "none");
			return false;
		});
		
		/** CANCEL RICH TEXT **/	
		$(".cancel-rich-button").live("click", function() {
			$('.builder-modal').css("display", "none");
			return false;
		});
		
		/** EDIT RICH TEXT **/		
		$(".edit-widget").live("click", function() {
			currentWidget = $(this).parents(".wpts-widget");
			var c = currentWidget.find("textarea").val();
			var title = currentWidget.find(".wid-title").val();
			tinyMCE.execCommand('mceRemoveControl', false, tid);
			$("#tw").val(c);
			$("#editor-widget-name").val(title);
			tinyMCE.execCommand('mceAddControl', false, tid);
			$('.builder-modal').css("display", "block");
			return false;
		});
		
		/** REMOVE ROW **/	
		$(".remove-row").live("click", function() {
			if(confirm("Have sure you need delete this row?")) {
				$(this).parents(".row").remove();
			}
			return false;
		});
		
		/** REMOVE WIDGET **/	
		$(".remove-widget").live("click", function() {
			if(confirm("Have sure you need delete this row?")) {
				$(this).parents(".wpts-widget").remove();
			}
			return false;
		});
		
		$("#add_widget_top").live("click", function() {
			var type = $(this).siblings(".widget_selector").val();
			
				var row = $("#"+type + " .row").clone();
				eval("layout(row)");
			
			return false;
		});
		
		$("#add_widget_bottom").live("click", function() {
			var type = $(this).siblings(".widget_selector").val();
			
				var row = $("#"+type + " .row").clone();
				eval("layout_bottom(row)");
			
			return false;
		});

		/** ADD LAYOUT WIDGET **/	
		$(".add_widget_layout").live("click", function() {
			var type = $(this).siblings(".widget_selector").val();
				var row = $("#"+type + " .row ."+type+"").clone();
				var parent = $(this).parents(".layout");
				var elp = parent.find("input:first").attr("name");
				
				row.find(".wid-parent").remove();
				row.find(".wid-1").attr("name", elp + "[2]["+idn+"]");
				idn++;
				row.find(".wid-2").attr("name", elp + "[2]["+idn+"]");
				idn++;
				row.find(".wid-3").attr("name", elp + "[2]["+idn+"]");
				idn++;
				row.find(".wid-title").attr("name", elp + "[2]["+idn+"]");
				idn++;
				parent.children(".layout-content").append(row);
				idn++;
				$( ".layout-content" ).sortable();
				$( ".layout-content" ).disableSelection();
			return false;
		});
				
		/******* widgets ********/
					
			function addtolayout(parent, row) {
				
				$(".builder-content").prepend(row);
			}
			
			/** rich_text **/
			function rich_text(row) {
				row.find(".wid-parent").attr("name", "elements["+idn+"]");
				row.find(".wid-1").attr("name", "elements["+idn+"][0]");
				row.find(".wid-2").attr("name", "elements["+idn+"][1]");
				row.find(".wid-3").attr("name", "elements["+idn+"][2]");
				row.find(".wid-title").attr("name", "elements["+idn+"][3]");
				$(".builder-content").prepend(row);
			}
		
		/******* layouts ********/
		
			/** layout **/
			function layout(row) {
				row.find(".layout").each(function(i, e) {
					var t = $(this);
					t.find(".wid-parent").attr("name", "elements["+idn+"]");
					t.find(".wid-1").attr("name", "elements["+idn+"][0]");
					t.find(".wid-2").attr("name", "elements["+idn+"][1]");
					idn++;
				});
				$(".builder-content").prepend(row);
			}
			
			/** layout **/
			function layout_bottom(row) {
				row.find(".layout").each(function(i, e) {
					var t = $(this);
					t.find(".wid-parent").attr("name", "elements["+idn+"]");
					t.find(".wid-1").attr("name", "elements["+idn+"][0]");
					t.find(".wid-2").attr("name", "elements["+idn+"][1]");
					idn++;
				});
				$(".builder-content").append(row);
			}
			
		/*** TOGGLE IMPORT/EXPORT ***/
		
		$(".export-layout a, .import-layout a").live("click", function() {
			
			$(this).parent().siblings("textarea").slideToggle("fast");
			
			return false;
		});
	
	});