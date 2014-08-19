<?php

/**********************************
*********** META BOXES ************
**********************************/

	function wpts_layout_builder()
	{
		$dir = get_template_directory_uri();

		wp_enqueue_style('thickbox');		
		wp_enqueue_script('media-upload');
		wp_enqueue_script('thickbox');
		
		wp_enqueue_script('jquery-ui-core');
		wp_enqueue_script('jquery-ui-widget');
		wp_enqueue_script('	jquery-ui-mouse');
		wp_enqueue_script('jquery-ui-sortable');
		
		wp_enqueue_style("wpts-builder-css", $dir."/wpts/layout-builder/layout-builder.css", false, "1.0", "all");
				
		wp_register_script('wpts-builder-js', $dir.'/wpts/layout-builder/layout-builder.js', array('jquery','media-upload','thickbox'), "1.0", true);
		wp_enqueue_script('wpts-builder-js');
			
		wp_enqueue_script('editor');
		
	}
	
	add_action('admin_init', 'wpts_layout_builder');
	
	add_action('admin_head', 'ilc_add_tinymce');
	function ilc_add_tinymce() {
		?>
		<script type="text/javascript">
		jQuery(document).ready(function($) {
			id = 'tw';
			jQuery('#edButtonPreview').click(
				function() {
					tinyMCE.execCommand('mceAddControl', false, id);
					jQuery('#edButtonPreview').addClass('active');
					jQuery('#edButtonHTML').removeClass('active');
				}
			);
			jQuery('#edButtonHTML').click(
				function() {
					tinyMCE.execCommand('mceRemoveControl', false, id);
					jQuery('#edButtonPreview').removeClass('active');
					jQuery('#edButtonHTML').addClass('active');
				}
			);
		});
		</script>
		<style type='text/css'>
			#tw{ margin: 0}
			.mceLayout{
				border: 1px solid #ccc;
			}
		</style>
		<?php
	}
	

/**********************************
*********** META BOXES ************
**********************************/

	add_action("admin_init", "layout_builder_meta");     
      
    function layout_builder_meta(){  
		// PORTFOLIO META OPTIONS 
        add_meta_box("builder-meta", "Layout Builder", "layout_builder_options", "page", "normal", "high");  
        add_meta_box("builder-meta", "Layout Builder", "layout_builder_options", "post", "normal", "high");  
        add_meta_box("builder-meta", "Layout Builder", "layout_builder_options", "project", "normal", "high");  
		// PORTFOLIO THUMBNAILS
    }    
      
/*** PORTFOLIO META OPTIONS ***/
	  
    function layout_builder_options()
	{  
            global $post;  
            if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return $post_id;  
            $custom = get_post_custom($post->ID); 
			$el = $custom["elements"];
            $usebuilder = $custom["usebuilder"][0];  
			
			$elements = maybe_unserialize($custom["elements"][0]); 

			//- var_dump($elements);
						
			if (@base64_decode( $elements, true )) {
				
				$elements = base64_decode( $elements );
				$elements = maybe_unserialize( $elements );
				//- var_dump($elements);
			}
			else {
				
			}			
				if($custom["import"][0] != '' && $custom["import"][0] != null) {
					$imp = $custom["import"][0];
					$imp = base64_decode($imp);
					$imp = maybe_unserialize($imp);
					if (@base64_decode( $imp[0], true )) {
						$imp = base64_decode($imp[0]);
					}
					$imp = maybe_unserialize($imp);
					$elements = $imp;
					update_post_meta($post->ID, "import", null);
				}   
    ?>  
	
		<div class="wpts-title wpts-title-cyan"><h2>Layout Builder</h2></div>
		
		<div class="builder-enable">
			<input type="checkbox" name="usebuilder" <?php if($usebuilder) echo 'checked="checked"'; ?> /> Use Layout Builder on this page?
		</div>
		
		<div class="builder-clone">
			<?php require_once("clone.php"); ?>
		</div> 
		
		<div class="builder-controls">
			<?php require("select-all.php"); ?>
			<a href="#" class="add_widget" id="add_widget_top">Add Column(s)</a>
		</div> <!-- .builder-controls -->
		
		<div class="builder-modal">				
				<div class="text-rich-area">
				
				<div class="editor-name">
					<label>Widget Name:</label> <input type="text" id="editor-widget-name" />
				</div>
				
				<?php
					wp_editor( '', 'tw', 
						array( 
							'wpautop' => true,
							'textarea_rows' => 20,
							'tinymce' => array(
								'mode' => "exact",
								'forced_root_block' => false,
								'force_br_newlines' => true,
								'height' => 300,
								'force_p_newlines' => false,
								'convert_newlines_to_brs' => true
							)
						)
					);
				?>
				
				</div> <!-- .text-rich-area -->
				<div class="save-rich">
					<a href="#" class="save-rich-button">Save</a> <a href="#" class="cancel-rich-button">Cancel</a>
				</div> <!-- .save-rich -->
				
		</div> <!-- buider-modal -->
		
		<div class="builder-content">
		
			<?php require_once("models.php"); ?>
				
		</div> <!-- .builder-content -->
		
		<div class="builder-controls">
			<?php require("select-all.php"); ?>
			<a href="#" class="add_widget" id="add_widget_bottom">Add Column(s)</a>
		</div> <!-- .builder-controls -->
		
		<div class="export-layout">
		<h2><a href="#"><img src="<?php echo get_template_directory_uri(); ?>/wpts/layout-builder/images/seta.png" title="Export" /> Export Content</a></h2>
		<textarea class="export"><?php echo base64_encode(serialize($el)); ?></textarea>
		</div>
		
		<div class="import-layout">
		<h2><a href="#"><img src="<?php echo get_template_directory_uri(); ?>/wpts/layout-builder/images/seta2.png" title="Import" /> Import Content</a></h2>
		<textarea name="importing" class="import"></textarea>
		</div>
		
		<div class="clearboth"></div>
		
		<?php //var_dump($imp); ?>
		<?php //var_dump($elements); ?>
	
    <?php  
		//echo $elements[0][2];
		
    }  
	
	add_action('save_post', 'layout_builder_save');   
      
    function layout_builder_save(){  
        global $post;    
      
        if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ){  
            return $post_id;  
        }else{  
			if($_POST["importing"] != "")
			{	
				update_post_meta($post->ID, "import", $_POST["importing"]);
			}
			update_post_meta($post->ID, "usebuilder", $_POST["usebuilder"]);  

			$els = serialize($_POST["elements"]);
			//
			$els = base64_encode( $els );
			update_post_meta($post->ID, "elements", $els );  
             
        }  
    }  

?>