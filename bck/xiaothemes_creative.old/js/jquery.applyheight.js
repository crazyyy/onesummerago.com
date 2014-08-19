
	var t = null;

	function applyHeight() {
		var contentH = jQuery(window).height() - 230;
		
		jQuery(".creative-content").each( function(i, e) { 
			jQuery(this).height(contentH);
		});

		jQuery('.creative-content').jScrollPane();

		clearTimeout(t);
	}

	jQuery(document).ready( function($) {

		applyHeight();

		t = setTimeout("applyHeight()", 800);

		jQuery(window).resize(function() {
			applyHeight();
		});
		
	});
