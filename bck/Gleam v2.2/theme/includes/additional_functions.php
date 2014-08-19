<?php

/* Meta boxes */

function gleam_settings(){
	add_meta_box("et_post_meta", __('ET Settings','Gleam'), "gleam_display_options", "page", "normal", "high");
}
add_action("admin_init", "gleam_settings");

function gleam_display_options($callback_args) {
	global $post, $themename;
	wp_nonce_field( basename( __FILE__ ), 'et_settings_nonce' ); ?>

	<div id="et_custom_settings" style="margin: 13px 0 17px 4px;">
		<?php
			$et_media_query = new WP_Query(
				array(
					'post_type' => 'attachment',
					'post_status' => 'inherit',
					'post_mime_type' => 'image',
					'posts_per_page' => 15,
				)
			);

			$et_used_images = get_post_meta( $post->ID, '_et_used_images', true );
			$et_fullscreen_mode = get_post_meta( $post->ID, '_et_fullscreen_mode', true ) ? get_post_meta( $post->ID, '_et_fullscreen_mode', true ) : 'off';

			echo '<div class="et_settings_box et_single_option">' . '<label for="et_fullscreen_mode">' . '<input type="checkbox" id="et_fullscreen_mode" name="et_fullscreen_mode" ' . checked( $et_fullscreen_mode, 'on', false ) . ' /> ' . __('Enable Fullscreen Mode','Gleam') . '</label>' . '</div>';

			echo '<div class="et_settings_box">';
				echo '<h2>' . __('Currently used images','Gleam') . '</h2>';
				echo '<p id="et_no_images">' . __('Please, add some images','Gleam') . '</p>';
				echo '<ul id="et_used_images" style="overflow: hidden; ">';
					if ( $et_used_images ){
						foreach( $et_used_images as $et_attachment_id => $et_used_image ){
							$saved_image = wp_get_attachment_image( $et_attachment_id );
							if ( '' != $saved_image )
								echo	'<li data-attachment_id="' . esc_attr( $et_attachment_id  ) . '" style="float: left; margin: 0 10px 10px 0;">'
											. $saved_image
											. '<span class="et_delete">x</span> <span class="et_image_edit">' . __('Edit','Gleam') . '</span>'
											. '<div class="et_image_options">'
												. '<input type="hidden" name="et_used_image_id[]" value="' . esc_attr( $et_attachment_id  ) . '">'
												. '<label>' . __('Image title','Gleam') . ':</label>'
												. '<input type="text" name="et_used_image_title[]" value="' . esc_attr( $et_used_image['image_title'] ) . '">'
												. '<label>' . __('Image description','Gleam') . ':</label>'
												. '<textarea name="et_used_image_description[]">' . esc_textarea( $et_used_image['image_description'] ) . '</textarea>'
												. '<a href="#" class="et_image_save">' . __('Save','Gleam') . '</a>'
											. '</div>'
										. '</li>';
						}
					}
				echo '</ul>';
			echo '</div>';

			echo '<div class="et_settings_box et_last_box">';
				echo '<h2>' . __('Choose image(s) for page background','Gleam') . '</h2>';

				echo '<ul id="et_available_images" style="overflow: hidden; ">';
					foreach ($et_media_query->posts as $et_attachment) {
						$added_class = ( $et_used_images && array_key_exists( $et_attachment->ID, $et_used_images ) ) ? ' class="et_added"' : '';
						echo '<li data-attachment_title="' . esc_attr( $et_attachment->post_title ) . '" data-attachment_description="' . esc_attr( $et_attachment->post_content ) . '" data-attachment_id="' . esc_attr( $et_attachment->ID ) . '"' . $added_class . '>' . wp_get_attachment_image( $et_attachment->ID ) . '<span class="et_delete">x</span> <span class="et_image_edit">' . __('Edit','Gleam') . '</span>' . '</li>';
					}
				echo '</ul>';

				if ( $et_media_query->max_num_pages > 1 ){
					echo '<div id="et_attachments_pagination">';
						for ( $i=1; $i <= $et_media_query->max_num_pages; $i++ ){
							echo '<a href="#"' . ( 1 == $i ? ' class="et_active_page"' : '' ) . '>' . $i . '</a>';
						}
					echo '</div>';
				}
			echo '</div>';

			wp_reset_postdata();
		?>
	</div> <!-- #et_custom_settings -->

	<?php
}

add_action( 'save_post', 'gleam_save_details', 10, 2 );
function gleam_save_details( $post_id, $post ){
	global $pagenow;
	$used_images = array();

	if ( 'post.php' != $pagenow ) return $post_id;

	if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE )
		return $post_id;

	$post_type = get_post_type_object( $post->post_type );
	if ( !current_user_can( $post_type->cap->edit_post, $post_id ) )
		return $post_id;

	if ( !isset( $_POST['et_settings_nonce'] ) || !wp_verify_nonce( $_POST['et_settings_nonce'], basename( __FILE__ ) ) )
        return $post_id;

	$et_fullscreen_mode = isset( $_POST['et_fullscreen_mode'] ) ? 'on' : 'off';

	if ( isset( $_POST['et_used_image_id'] ) ){
		for ( $i = 0; $i < count( $_POST['et_used_image_id'] ); $i++ ){
			$used_images[$_POST['et_used_image_id'][$i]] = array( 'image_title' => $_POST['et_used_image_title'][$i], 'image_description' => $_POST['et_used_image_description'][$i] );
		}
	}

	update_post_meta( $post_id, '_et_used_images', $used_images );
	update_post_meta( $post_id, '_et_fullscreen_mode', $et_fullscreen_mode );
}

add_action( 'admin_enqueue_scripts', 'gleam_metabox_upload_scripts' );
function gleam_metabox_upload_scripts( $hook_suffix ) {
	if ( 'post.php' == $hook_suffix || 'post-new.php' == $hook_suffix ) {
		wp_enqueue_script( 'jquery-ui-core' );
		wp_enqueue_script( 'jquery-ui-sortable' );
		wp_enqueue_script('et-upload_images', get_template_directory_uri().'/js/et-upload_images.js', array('jquery'));
		wp_enqueue_style( 'et_upload_images_css', get_template_directory_uri() . '/css/et-upload_images.css' );
		wp_localize_script( 'et-upload_images', 'et_attachment_info', array( 'image_title' => __('Image title','Gleam'), 'image_desc' => __('Image description','Gleam') ) );
	}
}

add_action('wp_ajax_et_show_attachments_page', 'et_show_attachments_page');
function et_show_attachments_page() {
	if ( ! wp_verify_nonce( $_POST['et_settings_nonce'], basename( __FILE__ ) ) ) die(-1);

	$et_page = intval( $_POST['et_page'] );

	$et_media_query = new WP_Query(
		array(
			'post_type' => 'attachment',
			'post_status' => 'inherit',
			'post_mime_type' => 'image',
			'posts_per_page' => 15,
			'offset' => ( 15 * ( $et_page - 1 ) ),
		)
	);

	foreach ($et_media_query->posts as $et_attachment) {
		echo '<li data-attachment_title="' . esc_attr( $et_attachment->post_title ) . '" data-attachment_description="' . esc_attr( $et_attachment->post_content ) . '" data-attachment_id="' . esc_attr( $et_attachment->ID ) . '">' . wp_get_attachment_image( $et_attachment->ID ) . '<span class="et_delete">x</span> <span class="et_image_edit">' . __('Edit','Gleam') . '</span>' . '</li>';
	}

	die();
}