	(function(window,undefined){
		
		var History = window.History;
		var State = History.getState();

		// Bind to State Change
		History.Adapter.bind(window,'statechange',function(){ // Note: We are using statechange instead of popstate
					// Log the State
			var State = History.getState(); // Note: We are using History.getState() instead of event.state
			//History.log('statechange:', State.data, State.title, State.url);
		});

	})(window);

jQuery(document).ready(function($) {

	var TITLE = $(document).attr("title");

	var $loader = $(".loader");
	var $blog = $('.wpts-blog-list');
	var $single = $('.wpts-single-post');
	var $back = $("#back-blog");

	function ajaxLinks() {

		$(".wpts-blog-list a").each(function(e, i) { 

			var $this = $(this);

			var href = $this.attr("href");

			if(href.search(BLOG_SLUG) == "-1") {
				//alert("O");
				href = href.replace("#", BLOG_SLUG);
				//alert(BLOG_SLUG + ' - ' + href);
			}

			if(href.search("&ajax=1") == "-1") {
				href = href.replace("ajax=1", "");
				href = href.replace('#', "&ajax=1" + "#");
			}
			
			href = href.replace("/&", "/?");
			href = href.replace("?&", "?");

			$this.attr("href", href);
		});

	}

	$(".blog-title #back-blog").each(function(e, i) { 

			var $this = $(this);

			var href = $this.attr("href");

			//alert(href.search(BLOG_SLUG));
			if(href.search(BLOG_SLUG) == "-1") {
				href = href.replace("#", BLOG_SLUG);
			}

			href = href.replace('#', "&ajax=1" + "#");
			href = href.replace("/&", "/?");

			$this.attr("href", href);
	});
	
	ajaxLinks();

	$(".pagination a").live("click", function() {

		var href = $(this).attr("href");

		$loader.css("display", "block");
		$blog.css("display", "none");

		$.get(href, function(data) {

			$loader.css("display", "none");

			$('.wpts-blog-list').html(data);
			ajaxLinks();
			$blog.css("display", "block");

			try
			{
			  History.pushState({}, TITLE, href.replace("&ajax=1", "").replace("ajax=1", "").replace("&paged", "paged") );
			}
			catch(err)
			{
			  // ...
			}		

		});

		return false;
	});

	$(".wpts-blog-list h1.title a, .wpts-blog-list .read-more a, .wpts-blog-list .thumb a").live("click", function() {

		var href = $(this).attr("href");

		$loader.css("display", "block");
		$blog.css("display", "none");

		$.get(href, function(data) {
			$loader.css("display", "none");
			$('.wpts-single-post').html(data);
			ajaxLinks();
			$single.css("display", "block");
			$back.css("display", "block");
			jQuery('.creative-content').jScrollPane();
		});

		try
		{
		  History.pushState({}, TITLE, href.replace("&ajax=1", "").replace("ajax=1", "") );
		}
		catch(err)
		{
		  //...
		}
		

		return false;
	});

	$(".blog-title a").live("click", function() {

		$single.css("display", "none");

		var href = $(this).attr("href");

		$loader.css("display", "block");
		$blog.css("display", "none");

		$.get(href, function(data) {

			$loader.css("display", "none");

			$('.wpts-blog-list').html(data);
			ajaxLinks();
			$back.css("display", "none");
			$single.css("display", "none");
			$blog.css("display", "block");

			ajaxLinks();

		});

		try
		{
		  History.pushState({}, TITLE, href.replace("&ajax=1", "").replace("ajax=1", "") );
		}
		catch(err)
		{
		  //...
		}

		return false;
	});	

});