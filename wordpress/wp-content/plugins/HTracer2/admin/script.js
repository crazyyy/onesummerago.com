jQuery.cookie = function (key, value, options) {
    if (arguments.length > 1 && (value === null || typeof value !== "object")) {
        options = jQuery.extend({}, options);

        if (value === null) {
            options.expires = -1;
        }
        if (typeof options.expires === 'number') {
            var days = options.expires, t = options.expires = new Date();
            t.setDate(t.getDate() + days);
        }
       return (document.cookie = [
            encodeURIComponent(key), '=',
            options.raw ? String(value) : encodeURIComponent(String(value)),
            options.expires ? '; expires=' + options.expires.toUTCString() : '', // use expires attribute, max-age is not supported by IE
            options.path ? '; path=' + options.path : '',
            options.domain ? '; domain=' + options.domain : '',
            options.secure ? '; secure' : ''
        ].join(''));
    }
    options = value || {};
    var result, decode = options.raw ? function (s) { return s; } : decodeURIComponent;
    return (result = new RegExp('(?:^|; )' + encodeURIComponent(key) + '=([^;]*)').exec(document.cookie)) ? decode(result[1]) : null;
};
	function calc_symb(t)
	{
		window.setTimeout(function (){
			$('#'+$(t).attr('name')+'_symb').html($(t).val().length);
		},100);
	}
	$(function() {
		$.each($(".calc_symb"), function()
		{ 
			$(this).keypress(function (){calc_symb(this)});
			$(this).click(function (){calc_symb(this)});
			$(this).change(function (){calc_symb(this)});
			$(this).mousedown(function (){calc_symb(this)});
			$(this).mouseup(function (){calc_symb(this)});
			calc_symb(this);
		});
	});
	
	function row_enabler(t)
	{
		window.setTimeout(function (){
			var tr= $($(t).parent().parent());
			if($(t).attr('checked'))
			{
				tr.removeClass('grayText');
				tr.find('a,span,td,div').removeClass('grayText');
				$.each(tr.find('input[type="text"],select,textarea'),function(){
						$(this).attr('disabled',false);
				});
			}
			else
			{
				tr.addClass('grayText');
				tr.find('a,span,td,div').addClass('grayText');
				$.each(tr.find('input[type="text"],select,textarea'),function(){
						$(this).attr('disabled',true);
				});
			}
			
		
		},100);
	}
	$(function() {
		$.each($(".row_enabler"), function()
		{ 
			$(this).change(function (){row_enabler(this)});
			row_enabler(this);
		});
	});

	$(function() {
		$( "#accordion" ).accordion({
			autoHeight: false,
			navigation: true
		});
	});
	$.fn.dataTableExt.afnSortData['dom-text'] = function  ( oSettings, iColumn )
	{
		var aData = [];
		$('td:eq('+iColumn+') input', oSettings.oApi._fnGetTrNodes(oSettings) ).each( function () 
		{
			if($(this).attr('type')!='hidden')
				aData.push( this.value );
		});
		return aData;
	}
	function ShowHintDialog(title,content)
	{
		title='Help';
		$("#dialog").attr('title',title);
		$("#dialog").html(content);
		$("#dialog").dialog({modal: true, resizable:false,minWidth:600});
		$("#dialog").dialog("open");
		$('.ui-widget-overlay').live('click', function() {
            $('#dialog').dialog("close");
        });
	}
	var zebra_index=0;
	
	$(function() {

		$.each($(".zebra"), function() 
		{
			zebra_index=0;
			$.each(this.getElementsByTagName('tr'), function()
			{			
				if(!this.className.match(/\bheader_line\b/))
				{
					zebra_index++;
					if(zebra_index%2)
						$(this).css('background-color','rgb(240,240,240)');
				}
			});
		});
		$.each($(".tabs"), function() 
		{
			$(this).tabs({
				cookie: {expires: 1},
				select: function(event, ui){ 
					if(isOptionsPage)
					{
						isLoading=true;
						Trigers();
						isLoading=false;
					}
				}
			});
		});
		$.each($(".hint"), function() 
		{
			$(this).click(function() {
				ShowHintDialog('',$(this).attr('hcontent'));
				return false;
			});
		});
		$("#goto_page_input").autocomplete({source: 'ajax/pages_autocomplete.php'});
		$("#add_page_input").autocomplete({source: 'ajax/new_pages_autocomplete.php'});
	});
	
		(function( $ ) {
		$.widget( "ui.combobox", {
			_create: function() {
				var self = this,
					select = this.element.hide(),
					selected = select.children( ":selected" ),
					value = selected.val() ? selected.text() : "";
				var input = this.input = $( "<input>" )
					.insertAfter( select )
					.val( value )
					.autocomplete({
						delay: 0,
						minLength: 0,
						source: function( request, response ) {
							var matcher = new RegExp( $.ui.autocomplete.escapeRegex(request.term), "i" );
							response( select.children( "option" ).map(function() {
								var text = $( this ).text();
								if ( this.value && ( !request.term || matcher.test(text) ) )
									return {
										label: text.replace(
											new RegExp(
												"(?![^&;]+;)(?!<[^<>]*)(" +
												$.ui.autocomplete.escapeRegex(request.term) +
												")(?![^<>]*>)(?![^&;]+;)", "gi"
											), "<strong>$1</strong>" ),
										value: text,
										option: this
									};
							}) );
						},
						select: function( event, ui ) {
							ui.item.option.selected = true;
							self._trigger( "selected", event, {
								item: ui.item.option
							});
						},
						change: function( event, ui ) {
							if ( !ui.item ) {
								var matcher = new RegExp( "^" + $.ui.autocomplete.escapeRegex( $(this).val() ) + "$", "i" ),
									valid = false;
								select.children( "option" ).each(function() {
									if ( $( this ).text().match( matcher ) ) {
										this.selected = valid = true;
										return false;
									}
								});
								if ( !valid ) {
									// remove invalid value, as it didn't match anything
									$( this ).val( "" );
									select.val( "" );
									input.data( "autocomplete" ).term = "";
									return false;
								}
							}
						}
					})
					.addClass( "ui-widget ui-widget-content ui-corner-left" );

				input.data( "autocomplete" )._renderItem = function( ul, item ) {
					return $( "<li></li>" )
						.data( "item.autocomplete", item )
						.append( "<a>" + item.label + "</a>" )
						.appendTo( ul );
				};

				this.button = $( "<button type='button'>&nbsp;</button>" )
					.attr( "tabIndex", -1 )
					.attr( "title", "Show All Items" )
					.insertAfter( input )
					.button({
						icons: {
							primary: "ui-icon-triangle-1-s"
						},
						text: false
					})
					.removeClass( "ui-corner-all" )
					.addClass( "ui-corner-right ui-button-icon" )
					.click(function() {
						// close if already visible
						if ( input.autocomplete( "widget" ).is( ":visible" ) ) {
							input.autocomplete( "close" );
							return;
						}

						// work around a bug (likely same cause as #5265)
						$( this ).blur();

						// pass empty string as value to search for, displaying all results
						input.autocomplete( "search", "" );
						input.focus();
					});
			},

			destroy: function() {
				this.input.remove();
				this.button.remove();
				this.element.show();
				$.Widget.prototype.destroy.call( this );
			}
		});
	})( jQuery );