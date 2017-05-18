<form class="shortcodes-wrap">
	<br>
	<label for="shortcodes_list">Shortcodes</label>
	<br>
	
	<select id="shortcodes_list" name="shortcodes_list" class="widefat shortcodes-list" style="width: 50%;">
		<option></option>
		<option value="[row]column shortcode here.[/row]">row</option>
		<option value="[column width=&quot;&quot;]Content here.[/column]">column</option>
		<option value="[section_title text=&quot;&quot;]">section_title</option>
		<option value="[button text=&quot;&quot; url=&quot;&quot;]">button</option>
		<option value="[social_icon_wrap]social_icon shortcode here.[/social_icon_wrap]">social_icon_wrap</option>
		<option value="[social_icon type=&quot;&quot; url=&quot;&quot;]">social_icon</option>
		<option value="[intro]Text here.[/intro]">intro</option>
		<option value="[rotate_words interval=&quot;3000&quot; titles=&quot;&quot;]">rotate_words</option>
		<option value="[drop_cap]Text here.[/drop_cap]">drop_cap</option>
		<option value="[quote align=&quot;&quot; name=&quot;&quot;]Text here.[/quote]">quote</option>
		<option value="[full_width_image]Image here.[/full_width_image]">full_width_image</option>
		<option value="[alert]Text here.[/alert]">alert</option>
		<option value="[contact_form to=&quot;&quot; subject=&quot;&quot;]">contact_form</option>
		<option value="[tab_wrap titles=&quot;&quot; active=&quot;&quot;]tab shortcode here.[/tab_wrap]">tab_wrap</option>
		<option value="[tab]Text here.[/tab]">tab</option>
		<option value="[accordion_wrap]accordion shortcode here.[/accordion_wrap]">accordion_wrap</option>
		<option value="[accordion title=&quot;&quot;]Text here.[/accordion]">accordion</option>
		<option value="[toggle_wrap]toggle shortcode here.[/toggle_wrap]">toggle_wrap</option>
		<option value="[toggle title=&quot;&quot;]Text here.[/toggle]">toggle</option>
		<option value="[fun_fact icon=&quot;&quot; text=&quot;&quot;]">fun_fact</option>
		<option value="[testimonial_wrap]testimonial shortcode here.[/testimonial_wrap]">testimonial_wrap</option>
		<option value="[testimonial image=&quot;&quot; title=&quot;&quot; sub_title=&quot;&quot;]Text here.[/testimonial]">testimonial</option>
		<option value="[slider items=&quot;1&quot; loop=&quot;true&quot; center=&quot;false&quot; mouse_drag=&quot;true&quot; nav=&quot;true&quot; dots=&quot;true&quot; autoplay=&quot;false&quot; speed=&quot;600&quot; timeout=&quot;2000&quot;]slide shortcode here.[/slider]">slider</option>
		<option value="[slide title=&quot;&quot; image=&quot;&quot;]">slide</option>
		<option value="[ken_slider_wrap speed=&quot;5000&quot; animation=&quot;kenburns&quot;]ken_slide shortcode here.[/ken_slider_wrap]">ken_slider_wrap</option>
		<option value="[ken_slide image=&quot;&quot;]">ken_slide</option>
		<option value="[photo_wall animation=&quot;random&quot; interval=&quot;1600&quot; max_step=&quot;3&quot;]">photo_wall</option>
		<option value="[pricing_table]Text here.[/pricing_table]">pricing_table</option>
	</select>
	
	<br>
	<br>
	
	<button type="button" class="button button-primary button-large button-insert-shortcode">Insert Shortcode</button>
</form>

<script>
	jQuery(document).ready(function($)
	{
		var selected_shortcode = "";
		
		$( '.shortcodes-list' ).change( function()
		{
			selected_shortcode = $( '.shortcodes-list' ).val();
		});
		
		$( '.button-insert-shortcode' ).click( function()
		{
			// add shortcode to content editor
			if ( window.tinyMCE )
			{
				var tmce_ver = window.tinyMCE.majorVersion;
				
				if ( tmce_ver < "4" )
				{
					window.tinyMCE.execInstanceCommand( 'content', 'mceInsertContent', false, selected_shortcode );
				}
				else
				{
					parent.tinyMCE.execCommand( 'mceInsertContent', false, selected_shortcode );
				}
				
				tb_remove();
			}
			// end add shortcode to content editor
		});
	});
</script>