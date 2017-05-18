(function($) { "use strict"; 

	jQuery(document).ready(function($) {  
		
		function getUrlParameter(sParam)
		{
			var sPageURL = window.location.hash.substring(1);
			var sURLVariables = sPageURL.split('&');
			for (var i = 0; i < sURLVariables.length; i++) 
			{
				var sParameterName = sURLVariables[i].split('=');
				if (sParameterName[0] == sParam) 
				{
					return sParameterName[1];
				}
			}
		}   
		
		var photoswipeParseHash = function() {
		  var hash = window.location.hash.substring(1),
				params = {};
	
			if(hash.length < 5) { // pid=1
				return params;
			}
	
			var vars = hash.split('&');
			for (var i = 0; i < vars.length; i++) {
				if(!vars[i]) {
					continue;
				}
				var pair = vars[i].split('=');  
				if(pair.length < 2) {
					continue;
				}           
				params[pair[0]] = pair[1];
			}
			if(!params.hasOwnProperty('pid')) {
				return params;
			}
			params.pid = parseInt(params.pid,10)-1;
			if( !(params.pid >= 0) ) {
				params.pid = 0;
			}
			return params;
		};
		
		var parseItemsFromElement = function(el) {
			var galleryDOM = el,
				galleryNodes = galleryDOM.childNodes,
				numNodes = galleryNodes.length,
				items = [],
				el,
				childElements,
				thumbnailEl,
				size,
				item;
	
			for(var i = 0; i < numNodes; i++) {
				el = galleryNodes[i];
	
				// include only element nodes 
				if(el.nodeType !== 1) {
					continue;
				}
	
				childElements = el.children;
				size = el.getAttribute('data-size').split('x');
	
				item = {
					src: el.getAttribute('href'),
					w: parseInt(size[0], 10),
					h: parseInt(size[1], 10),
					//author: el.getAttribute('data-author')
					attachmentURL: el.getAttribute('data-attachment-page') === null ? window.location.href : el.getAttribute('data-attachment-page')
				};
				var dataSrc = el.getAttribute('data-href');
				if(dataSrc) {
				  item.src = dataSrc;
				} else {
				  el.setAttribute('href', 'javascript:void');
				  el.setAttribute('data-href', item.src);
				}
				item.el = el;
	
				if(childElements.length > 0) {
					item.msrc = childElements[0].getAttribute('src'); // thumbnail url
					if(childElements.length > 1) {
						item.title = childElements[1].innerHTML; // caption
					}
				}
	
				var mediumSrc = el.getAttribute('data-med');
	
				if(mediumSrc) {
					size = el.getAttribute('data-med-size').split('x');
					// "medium-sized" image
					item.m = {
						src: mediumSrc,
						w: parseInt(size[0], 10),
						h: parseInt(size[1], 10)
					};
				}
				// original image
				item.o = {
					src: item.src,
					w: item.w,
					h: item.h
				};
	
				items.push(item);
			}
	
			return items;
		};
	
		
		var openGallery = function(index, showInstantly, container, gid) {
	
			var gallery;
			var items = parseItemsFromElement( container );
			var isiOS = /(iPad|iPhone|iPod)/g.test( navigator.userAgent );
	
			var options = {
				index: index,
				galleryUID: gid,
				getThumbBoundsFn: function(index) {
					var thumbnail = items[index].el;
					
					if(!thumbnail) { return; }
	
					thumbnail = thumbnail.children[0];
					var pageYScroll = window.pageYOffset || document.documentElement.scrollTop;
					var rect = thumbnail.getBoundingClientRect();
	
					return {x:rect.left, y:rect.top + pageYScroll, w:rect.width};
				},
				addCaptionHTMLFn: function(item, captionEl, isFake) {
				  if(!item.title) {
					captionEl.children[0].innerText = '';
					return false;
				  }
				  //captionEl.children[0].innerHTML = item.title +  '<br/><small>Photo: ' + item.author + '</small>';
				  captionEl.children[0].innerHTML = item.title;
				  return true;
				}//,
				//showHideOpacity: true
			   
			};
			
			// SET OPTIONS
			options.shareEl = !(container.getAttribute('data-share') === "false");
			options.bgOpacity = container.getAttribute('data-bg-opacity');
			options.fullscreenEl = !(container.getAttribute('data-fullscreen') === "false");
			options.getPageURLForShare = function( shareButtonData ) {
				// `pswp` is the PhotoSwipe instance object,
				// you should define it by yourself
				return pswp.currItem.attachmentURL;
			}
			
			var gallery_style = container.getAttribute('data-gallery-style');
			if(gallery_style ==="minimal") {
				options.mainClass = 'pswp--minimal--dark';
				options.barsSize = {top:0,bottom:0};
				//options.captionEl = false;
				options.tapToClose = true;
				options.tapToToggleControls = false;
				options.spacing = 0.0;
			}
	
			if(showInstantly) {
				options.showAnimationDuration = 0;
			}
	
			gallery = new PhotoSwipe( document.querySelectorAll('.pswp')[0], PhotoSwipeUI_Default, items, options);
			// TODO: implement dynamic changing on images, based on viewport size
			gallery.listen('gettingData', function(index, item) {
				// we want large image on all desktop devices
				
				// pixelwars edit : 20.02.2015
				// if( !gallery.likelyTouchDevice || screen.width > 1200 ) {
				if( screen.width > 767 ) {
					item.src = item.o.src;
					item.w = item.o.w;
					item.h = item.o.h;
				} else {
					// and medium-sized images for mobile
					item.src = item.m.src;
					item.w = item.m.w;
					item.h = item.m.h;
				}
			});
			gallery.init();
	
			window.pswp = gallery;
			
		};
		
		//console.log(getUrlParameter('gid'));
		
		//var container = document.querySelectorAll('.pw-gallery');
		
		var pwgallery = $('.pw-gallery');
		
		pwgallery.each(function(g_index, element) {
			
			
			var container = this;
			
		
			var closest = function closest(el, fn) {
				return el && ( fn(el) ? el : closest(el.parentNode, fn) );
			};
			var onThumbnailsClick = function(e) {
				e = e || window.event;
				e.preventDefault ? e.preventDefault() : e.returnValue = false;
		
				var eTarget = e.target || e.srcElement;
		
				var clickedListItem = closest(eTarget, function(el) {
					return el.tagName === 'A';
				});
		
				if(!clickedListItem) {
					return;
				}
		
				var childNodes = clickedListItem.parentNode.childNodes,
					numChildNodes = childNodes.length,
					nodeIndex = 0,
					index;
				for (var i = 0; i < numChildNodes; i++) {
						if(childNodes[i].nodeType !== 1) {
							continue;
						}
					if(childNodes[i] === clickedListItem) {
						index = nodeIndex;
						break;
					}
					nodeIndex++;
				}
		
				if(index >= 0) {
				var img = clickedListItem.children[0]; 
					openGallery(index, false, container, g_index+1 );
				}
			  return false;
			};
		   
		   if (typeof(container) != 'undefined' && container != null)
			{
			  container.onclick = onThumbnailsClick;
			}
			
			
			
		}); // end each
		
		
		var hashData = photoswipeParseHash();
	
		if(hashData.pid >= 0) {
			var gid = parseInt(getUrlParameter('gid'));
			var container = document.querySelectorAll('.pw-gallery')[gid-1];
			openGallery(hashData.pid, true, container, gid);
		}
		
		
	});

})(jQuery);