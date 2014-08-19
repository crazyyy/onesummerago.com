<?php

class wptsGenerator {
	var $name;
	var $options;
	var $saved_options;
	
	var $slider_ops;
	
	var $message = "";

	function wptsGenerator($name, $options) {
		
		$this->name = $name;
		$this->options = $options;
		
		$this->save_options();
		$this->render();
	}
	
	function save_options() {
	
		if($this->name != "slider") :
	
			$options = get_option(THEME_SLUG . '_' . $this->name);
					
			if (isset($_POST['save_theme_options'])) {
				
				foreach($this->options as $value) {
					
					if (isset($value['id']) && ! empty($value['id'])) {
						if (isset($_POST[$value['id']])) {
							if ($value['type'] == 'multidropdown') {
								if(empty($_POST[$value['id']])){
									$options[$value['id']] = array();
								}else{
									$options[$value['id']] = array_unique(explode(',', $_POST[$value['id']]));
								}
							} else {
								$options[$value['id']] = $_POST[$value['id']];
							}
						} else {
							$options[$value['id']] = false;
						}
					}
					if (isset($value['process']) && function_exists($value['process'])) {
						$options[$value['id']] = $value['process']($value,$options[$value['id']]);
					}
				}
				
				if ($options != $this->options) {
					update_option(THEME_SLUG . '_' . $this->name, $options);
					global $theme_options;
					$theme_options[$this->name] = $options;
					
					$this->save_skin_style();
					
				}
				$this->message = '<div id="message" class="wpts-updated"><p><strong>Updated Successfully</strong></p></div>';
			}
			
			$this->saved_options = $options;
		else :
		
			$options = get_option("slider");
			
			if(isset($_POST["sliders"])) {
				update_option('slider', $_POST["sliders"]);
				
				$options = get_option("slider");
				
				global $slider_options;
				$slider_options["slider"] = $options;
				$this->message = '<div id="message" class="wpts-updated"><p><strong>Updated Successfully</strong></p></div>';
			}
			
			$this->slider_ops = $options;
			
		endif;
	}
	
	function save_skin_style() {
			
	        $fhandle = fopen(ABSPATH.'wp-content/themes/xiaothemes_creative/css/photography.css', 'w+');
			$content =  $this->get_include_contents(ABSPATH.'wp-content/themes/xiaothemes_creative/css/photography.php');
			
            if ($fhandle) fwrite($fhandle, $content, strlen($content));

            $fhandle = fopen(ABSPATH.'wp-content/themes/xiaothemes_creative/css/architect.css', 'w+');
			$content =  $this->get_include_contents(ABSPATH.'wp-content/themes/xiaothemes_creative/css/architect.php');
			
            if ($fhandle) fwrite($fhandle, $content, strlen($content));
			
			/*$fhandle = fopen(ABSPATH.'wp-content/themes/atom/js/ordernow.js', 'w+');
			$content =  $this->get_include_contents(ABSPATH.'wp-content/themes/atom/js/ordernow.php');
			
            if ($fhandle) fwrite($fhandle, $content, strlen($content));*/
		
        return false;
    }
		
	function get_include_contents($filename) {
    if (is_file($filename)) {
        ob_start();
        include $filename;
        return ob_get_clean();
    }
    return false;
	}
		
	function render() {
		if($this->name == "slider") :
		
			//update_option("slider", null);
		
			$id = 0;
			$idinner = 0;
			
			$ops = $this->slider_ops;
			
			if(isset($_POST["sliders"])) {
				$ops = $_POST["sliders"];
			}
			
			//var_dump($ops);
		
			echo '<div class="wpts_wrap theme-options-page">';
			?>
			<?php
			foreach($this->options as $option) {
				if (method_exists($this, $option['type'])) {
					$this->$option['type']($option);
				}
			}
			?>
			<script>
				var id = 0;
				var idinner = 0;
			</script>
			
			<?php require_once("slider_manager/slider_clones.php"); ?>
			<?php require_once("slider_manager/slide_read.php"); ?>
			<?php require_once("slider_manager/slide_clones.php"); ?>
			
			<?php
			echo '<form method="post" action="" class="slider-form">';
			?>
				<div class="slider-submit">
					<input type="submit" name="save" class="button-primary autowidth" value="Save All Changes" />
				</div>
				<div class="all-sliders">
			<?php
				if($ops != null) {
					foreach($ops as $slide) {
						if($slide[0] != "") :
						?>
						<div class="slider-wrap">
						<div class="slider">
						<?php //var_dump($slide); ?>
						<input type="hidden" name="sliders[<?php echo $id; ?>]" />
							<input type="hidden" class="slider-id" name="sliders[<?php echo $id; ?>][0]" value="<?php echo $id; ?>" />
							<select name="sliders[<?php echo $id; ?>][1]">
								<option value="0" <?php if($slide[1] == "0") echo 'selected="selected"';?>>Fullscreen Slider</option>
							</select>
							<?php 
								$name = $slide[2];
								if($name == "")
									$name = "Slider Name";
							?>
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" name="sliders[<?php echo $id; ?>][2]" value="<?php echo $name; ?>" disabled="disabled" />
							
							<a href="#" class="edit"><img src="<?php echo get_template_directory_uri(); ?>/wpts/admin/css/images/pencil.png" alt="Edit" title="Edit" /></a>
							<a href="#" class="deleted"><img src="<?php echo get_template_directory_uri(); ?>/wpts/admin/css/images/reclycle.png" alt="Delete" title="Delete" /></a>
						</div> <!-- slider -->
						<div class="slides">
							<?php 
								if($slide[1] == 0)
									full_slider($slide[3], $id, $idinner); 
							?>
						</div> <!-- slides -->
						<div class="add_new_slide" title="<?php echo $slide[1]; ?>"><a href="#">ADD NEW SLIDE</a></div>
						</div> <!-- slider-wrap -->
						<?php
						$id++;
						echo '<script>
							id++;
						</script>';
						endif;
					} // end foreach
				} // end if
				else {
					?>
					<div class="slider-wrap">
						<div class="slider">
							<input type="hidden" name="sliders[0]" class="parent">
										<input type="hidden" name="sliders[0][0]" value="0" class="slider-id">
										<select class="type" name="sliders[0][1]">
											<option value="0">Fullscreen Slider</option>
										</select>
										&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" name="sliders[0][2]" class="name" value="FullScreen Slider" disabled="disabled" />
										
										<a href="#" class="edit"><img src="http://127.0.0.1:8888/creative/wp-content/themes/creative/wpts/admin/css/images/pencil.png" alt="Edit" title="Edit"></a>
										<a href="#" class="deleted"><img src="http://127.0.0.1:8888/creative/wp-content/themes/creative/wpts/admin/css/images/reclycle.png" alt="Delete" title="Delete"></a>
						</div> <!-- slider -->
									<div class="slides ui-sortable" style="">
													
									</div> <!-- slides -->
									<div title="" class="add_new_slide"><a href="#">ADD NEW SLIDE</a></div>
					</div>
					<?php
				}
			?>
			</div> <!-- .all-sliders -->				
				
				<div class="slider-submit">
					<input type="submit" name="save" class="button-primary autowidth" value="Save All Changes" />
				</div>
			<?php
			echo '</form>';
			echo '</div>';
		
		else :
		
			echo '<div class="wpts-wrapper">';
			
			echo '<form method="post" action="">';
			
			foreach($this->options as $option) {
				if (method_exists($this, $option['type'])) {
					$this->$option['type']($option);
				}
			}
			echo '</form>';
			echo '</div>';
		endif;
	}
	
	/**
	 * prints the options page title
	 */
	function title($value) {
		echo '<div class="wpts-title wpts-title-'.$value['color'].'">';
		
		echo '<div class="social-icons">
				<a href="https://xiaothemes.zendesk.com/" target="_blank"><img src="'.get_template_directory_uri().'/wpts/admin/css/images/help.png" alt="XiaoThemes - Help" title="Help" /></a> 
				<a href="http://twitter.com/xiaothemes" target="_blank"><img src="'.get_template_directory_uri().'/wpts/admin/css/images/twitter.png" alt="XiaoThemes - Twitter" title="Twitter" /></a> 
				<a href="http://www.facebook.com/XiaoThemes" target="_blank"><img src="'.get_template_directory_uri().'/wpts/admin/css/images/facebook_like.png" alt="XiaoThemes - Facebook" title="Facebook" /></a> 
				<a href="https://plus.google.com/u/0/103013683480359296760" target="_blank"><img src="'.get_template_directory_uri().'/wpts/admin/css/images/google_plus.png" alt="XiaoThemes - Google+" title="Google+" /></a>
			  </div>';
		echo '<div class="title-wrapper">
				<h2>' . $value['name'] . '</h2>
			</div>';
		echo '<div class="clearboth"></div>';
		echo '</div>';
		if($this->message != "")
			echo $this->message;
	}
	
	/**
	 * begins the group section
	 */
	function start($value) {
		echo '<div class="setting-block">';
		echo '<div class="block-title"><h2>' . $value['name'] . '</h2></div>';

	}
	
	function desc($value) {
		//echo '<tr><td scope="row" colspan="2">' . $value['desc'] . '</td></tr>';
	}
	
	/**
	 * closes the group section
	 */
	function end($value) {
		echo '<div class="save-bottom"><input type="submit" name="save_theme_options" class="button-primary autowidth" value="Save All Changes" />	w p l o ck  e r .c om </div></div>';
	}
	
	/**
	 * displays a text input
	 */
	function text($value) {
	
	?>
	
	<div class="wpts_input wpts_text">
	<label for="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></label>
 	<input name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" type="text" value="<?php if (isset($this->saved_options[$value['id']])) {	echo stripslashes($this->saved_options[$value['id']]);	} else { echo $value['default'];} ?>" />
	<small><?php echo $value['desc']; ?></small><div class="clearfix"></div>
	</div>
	
	<?php
	}
	
	/**
	 * displays a upload input
	 */
	function upload($value) {
	
	?>
	
	<div class="wpts_input wpts_text">
	<label for="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></label>
	<input type="text" class="upload-admin-input" name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" value="<?php if (isset($this->saved_options[$value['id']])) {	echo stripslashes($this->saved_options[$value['id']]);	} else { echo $value['default'];} ?>" /> <input class="upload-admin" type="button" value="Upload" />
	<div class="clearfix"></div>
	<small><?php echo $value['desc']; ?></small><div class="clearfix"></div>
	</div>

	<?php
	}
	
	/**
	 * displays a colorpicker
	 */
	function color($value) {
	?>
	
	<div class="wpts_input wpts_color">
	<label for="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></label>
 	<span><input name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" type="text" value="<?php if (isset($this->saved_options[$value['id']])) {	echo stripslashes($this->saved_options[$value['id']]);	} else { echo $value['default'];} ?>" /></span>
	<script type="text/javascript">addCP("#<?php echo $value['id']; ?>");</script>
	<small><?php echo $value['desc']; ?></small><div class="clearfix"></div>
	</div>
	
	<?php
	}
	
	/**
	 * displays a textarea
	 */
	function textarea($value) {
	
	?>
	
	<div class="wpts_input wpts_textarea">
	<label for="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></label>
 	<textarea name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" type="textarea"><?php if (isset($this->saved_options[$value['id']])) {	echo stripslashes($this->saved_options[$value['id']]);	} else { echo $value['default'];} ?></textarea>
	<small><?php echo $value['desc']; ?></small><div class="clearfix"></div>
	</div>

	<?php 
	
	}
	
	/**
	 * displays a select
	 */
	function select($value) {
	
	?>
		<div class="wpts_input wpts_select">
		<label for="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></label>

		<select name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>">
		<?php
			if(isset($value['prompt'])){
				echo '<option value="">'.$value['prompt'].'</option>';
			}
			if (isset($value['options'])) {
				foreach($value['options'] as $key => $option) {
					echo "<option value='" . $key . "'";
					if (isset($this->saved_options[$value['id']])) {
						if (stripslashes($this->saved_options[$value['id']]) == $key) {
							echo ' selected="selected"';
						}
					} else if ($key == $value['default']) {
						echo ' selected="selected"';
					}
				
					echo '>' . $option . '</option>';
				}
			}
		?>
		</select>

			<small><?php echo $value['desc']; ?></small><div class="clearfix"></div>
		</div>
	<?php

	}
	
	/**
	 * displays a toggle checkbox
	 */
	function toggle($value) {
	
	?>
	<div class="wpts_input rm_checkbox">
	<label for="<?php echo $value['id']; ?>"><?php echo $value['name']; ?></label>

	<?php $checked = '';
		if (isset($this->saved_options[$value['id']])) {
			if ($this->saved_options[$value['id']] == true) {
				$checked = 'checked="checked"';
			}
		} elseif ($value['default'] == true) {
			$checked = 'checked="checked"';
		} ?>
		<input type="checkbox" name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" value="true" <?php echo $checked; ?> />&nbsp;&nbsp;&nbsp;<span><?php echo $value['desc']; ?></span>

		<div class="clearfix"></div>
	 </div>
	<?php
		
	}

	function page_selector($value) {
		wp_enqueue_style( 'wpts-pagemanager', get_template_directory_uri() . '/wpts/admin/page_manager/page_manager.css', array(), NULL, 'all' );
		wp_enqueue_script( 'wpts-pagemanager', get_template_directory_uri() . '/wpts/admin/page_manager/page_manager.js', array('jquery-ui-sortable'), NULL );
	?>
	<div class="pages-custom" id="pages-custom">
		<?php 
			if (isset($this->saved_options[$value['id']])) {	
				$sides = stripslashes($this->saved_options[$value['id']]);	

				$sides = explode(";", $sides);

				foreach ($sides as $side) {
					if($side != '') {
						echo '<div class="single-page ui-sortable"><div><span rel="'.$side.'">'.get_the_title($side).'</span><a href="#">Remove</a></div></div>';
					}
				}
			}
		?>
	</div>


	<div class="wpts_input wpts_text">
	<label for="new-page">Add Page to Menu</label>
 	<!-- <input type="text" id="avaliable-pages" name="avaliable-pages" value="" /> -->
 	<?php wp_dropdown_pages(); ?> <a id="new-page" href="#">Add Page to Menu</a>
	</div>

	<input name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" class="final-ids" type="hidden" value="<?php if (isset($this->saved_options[$value['id']])) {	echo stripslashes($this->saved_options[$value['id']]);	} else { echo $value['default'];} ?>" />
		
	<?php
	}
	
}
