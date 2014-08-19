			</div> <!-- end #content -->
		</div> <!-- end #main_content -->

		<div id="et_loader"></div>

		<?php
			$bg_image = '';
			$image_ids = '';
			$et_used_images = '';
			$et_is_ajax_request = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

			echo '<ul id="et_backgrounds">';

			if ( ( is_home() || ( is_single() && ! $et_is_ajax_request ) ) && ( $homepage_bg = get_option('gleam_homepage_bg') ) && '' != $homepage_bg ) $bg_image = $homepage_bg;
			if ( is_page() ) {
				$et_used_images = get_post_meta( get_the_ID(), '_et_used_images', true );
			}

			if ( '' != $bg_image || $et_used_images ) {
				if ( '' != $bg_image ){
					echo 	'<li>
								<img src="' . esc_attr( $bg_image ) . '" alt="" />
							</li>';
				} else {
					foreach( $et_used_images as $et_attachment_id => $et_used_image ){
						$et_fullimage_array = wp_get_attachment_image_src( $et_attachment_id, 'full' );
						if ( $et_fullimage_array ){
							$et_fullimage = $et_fullimage_array[0];
							echo 	'<li>
										<img src="' . esc_attr( $et_fullimage ) . '" data-smallimage="' . esc_attr( et_new_thumb_resize( et_multisite_thumbnail($et_fullimage ), 43, 43, '', true ) ) . '" data-image_title="' . esc_attr( $et_used_image['image_title'] ) . '" data-image_desc="' . esc_attr( $et_used_image['image_description'] ) . '" alt="" />
									</li>';
						}
					}
				}
			}

			echo '</ul>';
		?>

		<div id="pattern_overlay"></div>

		<?php wp_footer(); ?>
	</div> <!-- #wrapper -->
</body>
</html>