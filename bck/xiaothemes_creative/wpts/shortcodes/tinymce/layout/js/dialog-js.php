<?php
header("Content-Type:text/javascript");

//Setup URL to WordPres
$absolute_path = __FILE__;
$path_to_wp = explode( 'wp-content', $absolute_path );
$wp_url = str_replace("\\", "/", $path_to_wp[0]);

//Access WordPress
require_once( $wp_url.'wp-load.php' );

//Path to TinyMCE plugin folder
$path_to_wp = explode( 'wp-content', dirname(__FILE__) );
$path_to_wp = str_replace("\\", "/", $path_to_wp);
$plugin_path = trailingslashit( '../wp-content' . substr( $path_to_wp[1], 0, -3 ) );
$plugin_path = str_replace("\\", "/", $plugin_path);

//URL to TinyMCE plugin folder
$plugin_url = get_template_directory_uri().'/wpts/shortcodes/tinymce/';
$plugin_url = str_replace("\\", "/", $plugin_url);
?>

var framework_url = '<?php echo str_replace("\\", "/",dirname( __FILE__ )); ?>';

var shortcode_generator_path = '<?php echo $plugin_path; ?>';
var shortcode_generator_url = '<?php echo $plugin_url; ?>';

var tb_dialog_helper = {
	
	// ---------------------------------------------------------
    // TickBox popup controls
    // ---------------------------------------------------------
    
    remove_linebreaks : false,
    needsPreview: false,
    setUpButtons: function () {
        var a = this;
        jQuery("#wpts-cancel-button").click(function () {
            a.closeDialog()
        });
        jQuery("#wpts-insert-button").click(function () {
            a.insertAction()
        });
    },
    loadShortcodeDetails: function () {
        if (wptsSelectedShortcodeType) {
            var a = this;
            jQuery.getScript(shortcode_generator_url + "shortcodes/" + wptsSelectedShortcodeType + ".js", function () {
                a.initializeDialog();
                
                // Set the default content to the highlighted text, for certain shortcode types.
                switch ( wptsSelectedShortcodeType ) {
					case 'box':
					case 'ilink':
					case 'quote':
					case 'button':
					case 'abbr':
					case 'unordered_list':
					case 'ordered_list':
					case 'typography':
						jQuery('input#wpts-value-content').val( selectedText );
					case 'toggle':
						jQuery('textarea#wpts-value-content').val( selectedText );
					break;
				
				} // End SWITCH Statement
                
                // Automatic preview generation on load.
                a.previewAction();
            })

        }

    },
    initializeDialog: function () {

        if (typeof wptsShortcodeAtts == "undefined") {
            jQuery("#wpts-shortcode-options").append("<p>Error loading details for shortcode: " + wptsSelectedShortcodeType + "</p>");
        } else {
		
			if(previewSrc != '')
			{
				jQuery("#preview-sc").css('display', 'block');
				jQuery("#preview-sc img").attr('src', shortcode_generator_url + 'preview/' + previewSrc);
			}
			else {
				jQuery("#preview-sc").css('display', 'none');
			}
			
			jQuery("#wpts-sample-button").click(function() { 
				
			jQuery("#sample-sc-p").css("display", "block");
				
			jQuery("#sample-sc").val(sample);
				return false;
			});
			
			jQuery(".tooltip-link").live('click', function() {
				jQuery(this).siblings(".tooltip").fadeToggle('fast');
			});
			
            var a = wptsShortcodeAtts.attributes,
                b = jQuery("#wpts-options-table");

            for (var c in a) {
                var f = "wpts-value-" + a[c].id,
                    d = a[c].isRequired ? "wpts-required" : "",
                    g = jQuery('<th valign="top" scope="row" class="first-col"></th>');

                var requiredSpan = '<span class="optional"></span>';
                if (a[c].isRequired) {
                    requiredSpan = '<span class="required">*</span>';
                } // End IF Statement
                jQuery("<label/>").attr("for", f).attr("class", d).html(a[c].label).append(requiredSpan).appendTo(g);
                f = jQuery("<td/>");
                d = (d = a[c].controlType) ? d : "text-control";
                switch (d) {
	                /*
	                case "tab-control":
	                    this.createTabControl(a[c], f, c == 0);
	                    break;
					*/
	                case "icon-control":
	                case "link-control":
	                case "text-control":
	                    this.createTextControl(a[c], f, c == 0);
	                    break;
	                    
	                case "textarea-control":
	                    this.createTextAreaControl(a[c], f, c == 0);
	                    break;
	
	                case "select-control":
	                    this.createSelectControl(a[c], f, c == 0);
	                    break;
	                }
	
	                jQuery("<tr/>").append(g).append(f).appendTo(b)
	            }
	            jQuery(".wpts-focus-here:first").focus()
	
				// Add additional wrappers, etc, to each select box.
				jQuery('#wpts-shortcode-options select').wrap( '<div class="select_wrapper"></div>' ).before('<span></span>');
				jQuery('#wpts-shortcode-options select option:selected').each( function () {
				jQuery(this).parents('.select_wrapper').find('span').text( jQuery(this).text() );
			});

        } // End IF Statement
    },
    
     /* Tab Generator Element */

    createTabControl: function (a, b, c) {
        new wptsTabMaker(b, 6, c ? "wpts-focus-here" : null);
        b.addClass("wpts-marker-tab-control")
    },

    // ---------------------------------------------------------
    // Generic Text Element
    // ---------------------------------------------------------

    createTextControl: function (a, b, c) {
		
        var f = a.validateLink ? "wpts-validation-marker" : "",
            d = a.isRequired ? "wpts-required" : "",
            g = "wpts-" + a.id,
			tipid = "tip-" + a.id,
            e = a.value; // Default text input value (Added 10/10/2011)

        jQuery('<input type="text">').attr("id", g).attr("name", g).val(e).addClass(f).addClass(d).addClass('txt input-text').addClass(c ? "wpts-focus-here" : "").appendTo(b);
		
        if (a = a.help) {
            //jQuery("<br/>").appendTo(b);
            jQuery("<span/>").addClass("wpts-input-help").html('<a href="#" class="tooltip-link"><img src="<?php echo $plugin_url; ?>/help.png" alt="Help" /></a><span class="tooltip"><span class="tool-text">'+a+'</span></span>').appendTo(b);			
        }

        var h = this;
        b.find("#" + g).bind("keydown focusout", function (e) {
            if (e.type == "keydown" && e.which != 13 && e.which != 9 && !e.shiftKey) h.needsPreview = true;
            else if (h.needsPreview && (e.type == "focusout" || e.which == 13)) {
                h.previewAction(e.target);
                h.needsPreview = false
            }
        })

    },
	
	// ---------------------------------------------------------
    // Generic TextArea Element
    // ---------------------------------------------------------
	
    createTextAreaControl: function (a, b, c) {

        var f = a.validateLink ? "wpts-validation-marker" : "",
            d = a.isRequired ? "wpts-required" : "",
            g = "wpts-" + a.id;

        jQuery('<textarea>').attr("id", g).attr("name", g).attr("rows", 10).attr("cols", 30).addClass(f).addClass(d).addClass('txt input-textarea').addClass(c ? "wpts-focus-here" : "").appendTo(b);
        b.addClass("wpts-marker-textarea-control");

        if (a = a.help) {
            //jQuery("<br/>").appendTo(b);
            jQuery("<span/>").addClass("wpts-input-help").html('<a href="#" class="tooltip-link"><img src="<?php echo $plugin_url; ?>/help.png" alt="Help" /></a><span class="tooltip"><span class="tool-text">'+a+'</span></span>').appendTo(b);
        }

        var h = this;
        b.find("#" + g).bind("keydown focusout", function (e) {
            if (e.type == "keydown" && e.which != 13 && e.which != 9 && !e.shiftKey) h.needsPreview = true;
            else if (h.needsPreview && (e.type == "focusout" || e.which == 13)) {
                h.previewAction(e.target);
                h.needsPreview = false
            }
        })

    },
	
	
	// ---------------------------------------------------------
    // Select Box Element
    // ---------------------------------------------------------

    createSelectControl: function (a, b, c) {

        var f = a.validateLink ? "wpts-validation-marker" : "",
            d = a.isRequired ? "wpts-required" : "",
            g = "wpts-" + a.id;

        var selectNode = jQuery('<select>').attr("id", g).attr("name", g).addClass(f).addClass(d).addClass('select input-select').addClass(c ? "wpts-focus-here" : "");

        b.addClass('wpts-marker-select-control');

        var selectBoxValues = a.selectValues;
        
        var labelValues = a.selectValues;

        for (v in selectBoxValues) {

            var value = selectBoxValues[v];
            var label = labelValues[v];
            var selected = '';

            if (value == '') {

                if (a.defaultValue == value) {

                    label = a.defaultText;

                } // End IF Statement
            } else {

                if (value == a.defaultValue) {
                    label = a.defaultText;
                } // End IF Statement
            } // End IF Statement
            if (value == a.defaultValue) {
                selected = ' selected="selected"';
            } // End IF Statement
            
            selectNode.append('<option value="' + value + '"' + selected + '>' + label + '</option>');

        } // End FOREACH Loop
        
        selectNode.appendTo(b);

        if (a = a.help) {
            //jQuery("<br/>").appendTo(b);
            jQuery("<span/>").addClass("wpts-input-help").html('<a href="#" class="tooltip-link"><img src="<?php echo $plugin_url; ?>/help.png" alt="Help" /></a><span class="tooltip"><span class="tool-text">'+a+'</span></span>').appendTo(b);	
        }

        var h = this;

        b.find("#" + g).bind("change", function (e) {

            if ((e.type == "change" || e.type == "focusout") || e.which == 9) {

                h.needsPreview = true;

            }

            if (h.needsPreview) {

                h.previewAction(e.target);

                h.needsPreview = false
            }
            
            // Update the text in the appropriate span tag.
            var newText = jQuery(this).children('option:selected').text();
            
            jQuery(this).parents('.select_wrapper').find('span').text( newText );
        })

    },
    
	getTextKeyValue: function (a) {
        var b = a.find("input");
        if (!b.length) return null;
        a = b.attr("id").substring(5);
        b = b.val();
        return {
            key: a,
            value: b
        }
    },

	getTextAreaKeyValue: function (a) {
        var b = a.find("textarea");
        if (!b.length) return null;
        a = b.attr("id").substring(5);
        b = b.val();
        return {
            key: a,
            value: b
        }
    },

    getColumnKeyValue: function (a) {
        var b = a.find("#wpts-column-text").text();
        if (a = Number(a.find("select option:selected").val())) return {
            key: "data",
            value: {
                content: b,
                numColumns: a
            }
        }
    },
    
    getTabKeyValue: function (a) {
        var b = a.find("#wpts-tab-text").text();
        if (a = Number(a.find("select option:selected").val())) return {
            key: "data",
            value: {
                content: b,
                numTabs: a
            }
        }
    },

    makeShortcode: function () {

        var a = {},
            b = this;

        jQuery("#wpts-options-table td").each(function () {

            var h = jQuery(this),
                e = null;

            if (e = h.hasClass("wpts-marker-select-control") ? b.getSelectKeyValue(h) : b.getTextKeyValue(h)) a[e.key] = e.value
            if (e = h.hasClass("wpts-marker-tab-control") ? b.getTabKeyValue(h) : b.getTextKeyValue(h)) a[e.key] = e.value
            if (e = h.hasClass("wpts-marker-textarea-control") ? b.getTextAreaKeyValue(h) : b.getTextKeyValue(h)) a[e.key] = e.value

        });

        if (wptsShortcodeAtts.customMakeShortcode) return wptsShortcodeAtts.customMakeShortcode(a);
        var c = a.content ? a.content : wptsShortcodeAtts.defaultContent,
            f = "";
        for (var d in a) {
            var g = a[d];
            if (g && d != "content") f += " " + d + '="' + g + '"'
        }
        
        // Customise the shortcode output for various shortcode types.
        
        switch ( wptsShortcodeAtts.shortcodeType ) {
        
        	case 'text-replace':
        	
        		var shortcode = "[" + wptsShortcodeAtts.shortcode + f + "]" + (c ? c + "[/" + wptsShortcodeAtts.shortcode + "]" : " ")
        	
        	break;
        	
        	default:
        	
        		var shortcode = "[" + wptsShortcodeAtts.shortcode + f + "]" + (c ? c + "[/" + wptsShortcodeAtts.shortcode + "] " : " ")
        	
        	break;
        
        } // End SWITCH Statement
        
        return shortcode;
    },

    getSelectKeyValue: function (a) {
        var b = a.find("select");
        if (!b.length) return null;
        a = b.attr("id").substring(5);
        b = b.val();
        return {
            key: a,
            value: b
        }
    },

    insertAction: function () {
        if (typeof wptsShortcodeAtts != "undefined") {
            var a = this.makeShortcode();
            tinyMCE.activeEditor.execCommand("mceInsertContent", false, a);
            this.closeDialog()
        }
    },

    closeDialog: function () {
        this.needsPreview = false;
        tb_remove();
        jQuery("#wpts-dialog").remove()
    },

    previewAction: function (a) {
    
    	var fontValue = '';
    	
    	jQuery('#wpts-options-table').find('select.input-select-font').each ( function () {
    	
    		fontValue = jQuery(this).val();
    	
    	});
    
        jQuery(a).hasClass("wpts-validation-marker") && this.validateLinkFor(a);
    },

    validateLinkFor: function (a) {
        var b = jQuery(a);
        b.removeClass("wpts-validation-error");
        b.removeClass("wpts-validated");
        if (a = b.val()) {
            b.addClass("wpts-validating");
            jQuery.ajax({
                url: ajaxurl,
                dataType: "json",
                data: {
                    action: "wpts_check_url_action",
                    url: a
                },
                error: function () {
                    b.removeClass("wpts-validating")
                },
                success: function (c) {
                    b.removeClass("wpts-validating");
                    c.error || b.addClass(c.exists ? "wpts-validated" : "wpts-validation-error")
                }
            })
        }
    }

};

tb_dialog_helper.setUpButtons();
tb_dialog_helper.loadShortcodeDetails();