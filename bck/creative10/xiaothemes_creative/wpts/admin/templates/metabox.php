<?php
	
	/*** META DEFINITION ***/
	
	function slider_meta_box(){  
        add_meta_box("sliders", "Select Slider", "slider_meta_options", "page", "normal", "high");  	
	}
	
	/*** META OPTIONS ***/
	  
    function slider_meta_options()
	{  
            global $post;  
            if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return $post_id;  
            $custom = get_post_custom($post->ID);  
            $slider = $custom["slider-item"][0];  
    ?>  
		<div class="wpts-metabox">
			<div class="wpts-title wpts-title-yellow"><h2>Avaliable Sliders</h2></div>
			
			<div class="wpts_input">
				<label>Slider: </label>
				<select name="slider">
					<option value="">No use any slider</option>
					<?php
					$options = get_option("slider");
					if($options != null) {
						foreach($options as $slide) {
							if($slide[0] != "") :
								$name = $slide[2];
								?>
								<option value="<?php echo $name; ?>" <?php if($slider == $name) echo 'selected="selected"'; ?>><?php echo $name; ?></option>
								<?php
							endif;
						}
					}
					?>
				</select>
			</div>
			
			<div class="wpts_input">
				<label>Select: </label>
				<select name="">
					<option value="">Option</option>
					<option value="">Option</option>
					<option value="">Option</option>
					<option value="">Option</option>
				</select>
			</div>
			
			<div class="wpts_input">
				<label>Text: </label>
				<input type="text" name="" />
			</div>
			
			<div class="wpts_input">
				<label>Textarea: </label>
				<textarea name=""></textarea>
			</div>
			
			<div class="wpts_input rm_checkbox">
				<label>Checkbox: </label>
				<input type="checkbox" name="" /> Some checkbox text here
			</div>
			
		</div>
    <?php  
    }
	
	/*** SAVE OPTIONS ***/

	add_action('save_post', 'save_slider');   
      
    function save_slider(){  
        global $post;    
      
        if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ){  
            return $post_id;  
        }else{  
            update_post_meta($post->ID, "slider-item", $_POST["slider"]);  
        }  
    }
	
?>