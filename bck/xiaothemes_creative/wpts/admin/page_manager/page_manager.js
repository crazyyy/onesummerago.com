jQuery(document).ready( function($) {


	var $final = $(".final-ids");

	$("#pages-custom").sortable({
		stop: function(event, ui) { getPages(); return false; }
	});

	function getPages() {

		var f = '';

		$(".pages-custom .single-page").each(function(i, e) {
			f += $(this).find("span").attr('rel') + ";";
		});

		$final.val(f);

		$("#pages-custom").sortable({
			stop: function(event, ui) { getPages(); }
		});
	}

	getPages();
	
	$(".pages-custom .single-page a").live("click", function() {

		if(confirm("Do you have sure you want delete this page from menu?"))
		{
			$(this).parent().parent().remove();
		}

		getPages();

		return false;
	});

	$("#new-page").live("click", function() {
		var id = $("#page_id").val();
		var txt = $("#page_id option:selected").text();

		if(id != "") {

			$(".pages-custom").append('<div class="single-page"><div><span rel="'+ id + '">' + txt + '</span><a href="#">Remove</a></div></div>');


			getPages();
		}
		else {
			alert("Select a page, please.");
		}

		return false;
	});
	
});