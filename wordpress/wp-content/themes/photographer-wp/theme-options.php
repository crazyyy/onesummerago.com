<?php


/* ============================================================================================================================================ */


	function create_tabs( $current = 'general' )
	{
		$tabs = array(  'general'   => 'General',
						'style'     => 'Style',
						'blog'      => 'Blog',
						'portfolio' => 'Portfolio',
						'homepage'  => 'Homepage',
						'sidebar'   => 'Sidebar',
						'seo'       => 'SEO' );
		
		?>
			<h1>Theme Options</h1>
			
			<h2 class="nav-tab-wrapper">
				<?php
					foreach ( $tabs as $tab => $name )
					{
						$class = ( $tab == $current ) ? ' nav-tab-active' : "";
						
						echo "<a class='nav-tab$class' href='?page=theme-options&tab=$tab'>$name</a>";
					}
				?>
			</h2>
		<?php
	}


/* ============================================================================================================================================ */


	function theme_options_page()
	{
		global $pagenow;
		
		?>
			<div class="wrap wrap2">
				<div class="status">
					<img alt="..." src="<?php echo get_template_directory_uri(); ?>/admin/ajax-loader.gif">
					
					<strong></strong>
				</div>
				
				
				<script>
					jQuery(document).ready(function($)
					{
					// -------------------------------------------------------------------------
					
						var uploadID = '',
							uploadImg = '';

						jQuery( '.upload-button' ).click(function()
						{
							uploadID = jQuery(this).prev( 'input' );
							uploadImg = jQuery(this).next( 'img' );
							formfield = jQuery( '.upload' ).attr( 'name' );
							tb_show( "", 'media-upload.php?post_id=0&amp;type=image&amp;TB_iframe=true' );
							return false;
						});
						
						window.send_to_editor = function( html )
						{
							imgurl = jQuery( 'img', html ).attr( 'src' );
							uploadID.val( imgurl );
							uploadImg.attr('src', imgurl);
							tb_remove();
						}
					
					
					// -------------------------------------------------------------------------
					
					
						$( ".alert-success p" ).click(function()
						{
							$(this).fadeOut( "slow", function()
							{
								$( ".alert-success" ).slideUp( "slow" );
							});
						});
					
					
					// -------------------------------------------------------------------------
					
					
						$( '.color' ).change( function()
						{
							var myColor = $( this ).val();
							
							$( this ).prev( 'div' ).find( 'div' ).css( 'backgroundColor', '#' + myColor );
						});
						
						
						$( '.color' ).keypress( function()
						{
							var myColor = $( this ).val();
							
							$( this ).prev( 'div' ).find( 'div' ).css( 'backgroundColor', '#' + myColor );
						});
					
					
					// -------------------------------------------------------------------------
					
					
						$( 'form.ajax-form' ).submit(function()
						{
							$.ajax(
							{
								data: $( this ).serialize(),
								type: "POST",
								beforeSend: function()
								{
									$( '.status' ).removeClass( 'status-done' );
									$( '.status img' ).show();
									$( '.status strong' ).html( 'Saving...' );
									$( '.status' ).fadeIn();
								},
								success: function(data)
								{
									$( '.status img' ).hide();
									$( '.status' ).addClass( 'status-done' );
									$( '.status strong' ).html( 'Done.' );
									$( '.status' ).delay( 1000 ).fadeOut();
								}
							});
							
							return false;
						});
					
					
					// -------------------------------------------------------------------------
					});
				</script>
				
				
				<?php
					
					if ( isset( $_GET['tab'] ) )
					{
						create_tabs( $_GET['tab'] );
					}	
					else
					{
						create_tabs( 'general' );
					}
					
				?>
				
				
				<div id="poststuff">
					<?php
					
						// theme options page
						if ( $pagenow == 'themes.php' && $_GET['page'] == 'theme-options' )
						{
							// tab from url
							if ( isset( $_GET['tab'] ) )
							{
								$tab = $_GET['tab'];
							}
							else
							{
								$tab = 'general'; 
							}
							
							
							switch ( $tab )
							{
								case 'general' :
									
									if ( esc_attr( @$_GET['saved'] ) == 'true' )
									{
										echo '<div class="alert-success" title="Click to close"><p><strong>Saved.</strong></p></div>';
									}
									
									?>
										<div class="postbox">
											<div class="inside">
												<form method="post" class="ajax-form" action="<?php admin_url( 'themes.php?page=theme-options' ); ?>">
													<?php
														wp_nonce_field( "settings-page" );
													?>
													
													
													<table>
														<tr>
															<td class="option-left">
																<h4>Logo Type</h4>
																
																<?php
																	$logo_type = get_option( 'logo_type', 'Text Logo' );
																?>
																<select id="logo_type" name="logo_type" style="width: 100%;">
																	<option <?php if ( $logo_type == 'Text Logo' ) { echo 'selected="selected"'; } ?>>Text Logo</option>
																	
																	<option <?php if ( $logo_type == 'Image Logo' ) { echo 'selected="selected"'; } ?>>Image Logo</option>
																</select>
															</td>
															
															<td class="option-right">
																Select logo type.
															</td>
														</tr>
														
														<tr>
															<td class="option-left">
																<h4>Image Logo</h4>
																
																<?php
																	$logo_image = get_option( 'logo_image' );
																?>
																<input type="text" id="logo_image" name="logo_image" class="upload code2" style="width: 100%;" value="<?php echo esc_url( $logo_image ); ?>">
																
																<input type="button" class="button upload-button" style="margin-top: 10px;" value="Browse">
																
																<img style="margin-top: 10px; max-height: 50px;" align="right" alt="" src="<?php echo esc_url( $logo_image ); ?>">
															</td>
															<td class="option-right">
																Upload a logo or specify an image address of your online logo.
															</td>
														</tr>
														
														<tr>
															<td class="option-left">
																<h4>Image Logo Height</h4>
																<?php
																	$image_logo_height = get_option('image_logo_height', '100');
																?>
																<input type="number" min="50" max="1000" step="1" name="image_logo_height" value="<?php echo $image_logo_height; ?>">
																<span style="font-size: 11px; color: #666;">Default: 100 px</span>
															</td>
															<td class="option-right">
																Image height.
															</td>
														</tr>
														
														<tr>
															<td class="option-left">
																<h4>Text Logo</h4>
																
																<?php
																	$select_text_logo = get_option( 'select_text_logo', 'WordPress Site Title' );
																?>
																<select id="select_text_logo" name="select_text_logo" style="width: 100%;">
																	<option <?php if ( $select_text_logo == 'WordPress Site Title' ) { echo 'selected="selected"'; } ?>>WordPress Site Title</option>
																	
																	<option <?php if ( $select_text_logo == 'Theme Site Title' ) { echo 'selected="selected"'; } ?>>Theme Site Title</option>
																</select>
																
																<h4>Theme Site Title</h4>
																
																<?php
																	$theme_site_title = stripcslashes( get_option( 'theme_site_title', "" ) );
																?>
																<textarea id="theme_site_title" name="theme_site_title" rows="1" cols="50"><?php echo $theme_site_title; ?></textarea>
															</td>
															
															<td class="option-right">
																Site title.
															</td>
														</tr>
														
														<tr>
															<td class="option-left">
																<h4>Tagline</h4>
																
																<?php
																	$select_tagline = get_option( 'select_tagline', 'WordPress Tagline' );
																?>
																<select id="select_tagline" name="select_tagline" style="width: 100%;">
																	<option <?php if ( $select_tagline == 'WordPress Tagline' ) { echo 'selected="selected"'; } ?>>WordPress Tagline</option>
																	
																	<option <?php if ( $select_tagline == 'Theme Tagline' ) { echo 'selected="selected"'; } ?>>Theme Tagline</option>
																</select>
																
																<h4>Theme Tagline</h4>
																
																<?php
																	$theme_tagline = stripcslashes( get_option( 'theme_tagline', "" ) );
																?>
																<textarea id="theme_tagline" name="theme_tagline" rows="2" cols="50"><?php echo $theme_tagline; ?></textarea>
															</td>
															
															<td class="option-right">
																In a few words, explain what this site is about.
															</td>
														</tr>
														
														<tr>
															<td class="option-left">
																<h4>Login Logo</h4>
																
																<?php
																	$logo_login = get_option( 'logo_login' );
																?>
																<input type="text" id="logo_login" name="logo_login" class="upload code2" style="width: 100%;" value="<?php echo esc_url( $logo_login ); ?>">
																
																<input type="button" class="button upload-button" style="margin-top: 10px;" value="Browse">
																
																<img style="margin-top: 10px; max-height: 50px;" align="right" alt="" src="<?php echo esc_url( $logo_login ); ?>">
																
																<br>
																
																<?php
																	$logo_login_hide = get_option( 'logo_login_hide', false );
																?>
																<label><input type="checkbox" id="logo_login_hide" name="logo_login_hide" <?php if ( $logo_login_hide ) { echo 'checked="checked"'; } ?>> Hide login logo module</label>
															</td>
															
															<td class="option-right">
																A PNG image.
															</td>
														</tr>
														
														<tr>
															<td class="option-left">
																<h4>Favicon</h4>
																
																<?php
																	$favicon = get_option( 'favicon', "" );
																?>
																<input type="text" id="favicon" name="favicon" class="upload code2" style="width: 100%;" value="<?php echo esc_url( $favicon ); ?>">
																
																<input type="button" class="button upload-button" style="margin-top: 10px;" value="Browse">
																
																<img style="margin-top: 10px; max-height: 16px;" align="right" alt="" src="<?php echo esc_url( $favicon ); ?>">
															</td>
															
															<td class="option-right">
																(16x16)px ICO, PNG or GIF format.
															</td>
														</tr>
														
														<tr>
															<td class="option-left">
																<h4>Apple Touch Icon</h4>
																
																<?php
																	$apple_touch_icon = get_option( 'apple_touch_icon', "" );
																?>
																<input type="text" id="apple_touch_icon" name="apple_touch_icon" class="upload code2" style="width: 100%;" value="<?php echo esc_url( $apple_touch_icon ); ?>">
																
																<input type="button" class="button upload-button" style="margin-top: 10px;" value="Browse">
																
																<img style="margin-top: 10px; max-height: 50px;" align="right" alt="" src="<?php echo esc_url( $apple_touch_icon ); ?>">
															</td>
															
															<td class="option-right">
																A PNG image that will represent your website's favicon for Apple devices such as the iPod Touch, iPhone and iPad, as well as some Android devices.
															</td>
														</tr>
														
														<tr>
															<td class="option-left">
																<h4>Copyright Text</h4>
																
																<?php
																	$copyright_text = stripcslashes( get_option( 'copyright_text', "" ) );
																?>
																<textarea id="copyright_text" name="copyright_text" rows="5" cols="50"><?php echo $copyright_text; ?></textarea>
															</td>
															
															<td class="option-right">
																Copyright text in the footer.
															</td>
														</tr>
														
														<tr>
															<td class="option-left">
																<input type="submit" name="submit" class="button button-primary button-large" value="Save Changes">
																
																<input type="hidden" name="settings-submit" value="Y">
															</td>
															
															<td class="option-right">
																
															</td>
														</tr>
													</table>
												</form>
											</div>
											<!-- end .inside -->
										</div>
										<!-- end .postbox -->
									<?php
								break;
								
								
								case 'style' :
									
									if ( esc_attr( @$_GET['saved'] ) == 'true' )
									{
										echo '<div class="alert-success" title="Click to close"><p><strong>Saved.</strong></p></div>';
									}
									
									?>
										<div class="postbox">
											<div class="inside">
												<form class="ajax-form" method="post" action="<?php admin_url( 'themes.php?page=theme-options' ); ?>">
													<?php
														wp_nonce_field( "settings-page" );
													?>
													
													<table>
														<tr>
															<td class="option-left">
																<h4>Fonts and Colors</h4>
																
																<?php
																	echo '<a href="' . admin_url( 'customize.php' ) . '">Customize</a>';
																?>
															</td>
															
															<td class="option-right">
																Select from theme customizer.
															</td>
														</tr>
														
														<tr>
															<td class="option-left">
																<h4>Character Sets</h4>
																
																<label><input type="checkbox" id="char_set_latin" name="char_set_latin" checked="checked" disabled="disabled"> Latin</label>
																
																<br>
																
																<label><input type="checkbox" id="char_set_latin_ext" name="char_set_latin_ext" <?php if ( get_option( 'char_set_latin_ext' ) ) { echo 'checked="checked"'; } ?>> Latin Extended</label>
																
																<br>
																
																<label><input type="checkbox" id="char_set_cyrillic" name="char_set_cyrillic" <?php if ( get_option( 'char_set_cyrillic' ) ) { echo 'checked="checked"'; } ?>> Cyrillic</label>
																
																<br>
																
																<label><input type="checkbox" id="char_set_cyrillic_ext" name="char_set_cyrillic_ext" <?php if ( get_option( 'char_set_cyrillic_ext' ) ) { echo 'checked="checked"'; } ?>> Cyrillic Extended</label>
																
																<br>
																
																<label><input type="checkbox" id="char_set_greek" name="char_set_greek" <?php if ( get_option( 'char_set_greek' ) ) { echo 'checked="checked"'; } ?>> Greek</label>
																
																<br>
																
																<label><input type="checkbox" id="char_set_greek_ext" name="char_set_greek_ext" <?php if ( get_option( 'char_set_greek_ext' ) ) { echo 'checked="checked"'; } ?>> Greek Extended</label>
																
																<br>
																
																<label><input type="checkbox" id="char_set_vietnamese" name="char_set_vietnamese" <?php if ( get_option( 'char_set_vietnamese' ) ) { echo 'checked="checked"'; } ?>> Vietnamese</label>
															</td>
															
															<td class="option-right">
																Select any of them to include to the Google Fonts if the selected fonts have ones of them in their family.
																<br>
																<br>
																To see the supported character sets visit Google Fonts online.
															</td>
														</tr>
														
														<tr>
															<td class="option-left">
																<h4>Font Styles</h4>
																
																<?php
																	$extra_font_styles = get_option( 'extra_font_styles', 'No' );
																?>
																<select id="extra_font_styles" name="extra_font_styles" style="width: 100%;">
																	<option <?php if ( $extra_font_styles == 'Yes' ) { echo 'selected="selected"'; } ?>>Yes</option>
																	
																	<option <?php if ( $extra_font_styles == 'No' ) { echo 'selected="selected"'; } ?>>No</option>
																</select>
															</td>
															
															<td class="option-right">
																Bold and italic styles.
															</td>
														</tr>
														
														
														<tr>
															<td class="option-left">
																<h4>Mobile Zoom</h4>
																
																<?php
																	$mobile_zoom = get_option( 'mobile_zoom', 'Yes' );
																?>
																<select id="mobile_zoom" name="mobile_zoom" style="width: 100%;">
																	<option <?php if ( $mobile_zoom == 'Yes' ) { echo 'selected="selected"'; } ?>>Yes</option>
																	
																	<option <?php if ( $mobile_zoom == 'No' ) { echo 'selected="selected"'; } ?>>No</option>
																</select>
															</td>
															
															<td class="option-right">
																Enable/disable.
															</td>
														</tr>
														
														
														<tr>
															<td class="option-left">
																<h4>Gallery Type For Posts And Pages</h4>
																<?php
																	$pixelwars__gallery_type_for_posts_and_pages = get_option( 'pixelwars__gallery_type_for_posts_and_pages', 'Photo Gallery 2' );
																?>
																<select name="pixelwars__gallery_type_for_posts_and_pages">
																	<option <?php if ( $pixelwars__gallery_type_for_posts_and_pages == 'None' ) { echo 'selected="selected"'; } ?>>None</option>
																	<option <?php if ( $pixelwars__gallery_type_for_posts_and_pages == 'Fluidbox' ) { echo 'selected="selected"'; } ?>>Fluidbox</option>
																	<option <?php if ( $pixelwars__gallery_type_for_posts_and_pages == 'Photo Gallery' ) { echo 'selected="selected"'; } ?>>Photo Gallery</option>
																	<option <?php if ( $pixelwars__gallery_type_for_posts_and_pages == 'Photo Gallery 2' ) { echo 'selected="selected"'; } ?>>Photo Gallery 2</option>
																</select>
															</td>
															<td class="option-right">
																Select gallery type.
															</td>
														</tr>
														
														
														<tr>
															<td class="option-left">
																<h4>Custom CSS</h4>
																
																<?php
																	$custom_css = stripcslashes( get_option( 'custom_css', "" ) );
																?>
																<textarea id="custom_css" name="custom_css" class="code2" rows="8" cols="50"><?php echo $custom_css; ?></textarea>
															</td>
															
															<td class="option-right">
																Quickly add custom css.
															</td>
														</tr>
														
														
														<tr>
															<td class="option-left">
																<h4>External CSS</h4>
																
																<?php
																	$external_css = stripcslashes( get_option( 'external_css', "" ) );
																?>
																<textarea id="external_css" name="external_css" class="code2" rows="8" cols="50"><?php echo $external_css; ?></textarea>
															</td>
															
															<td class="option-right">
																Add your custom external (.css) file.
																<br>
																<br>
																Sample (.css):
																<br>
																<br>
																<span class="code2">&lt;link rel="stylesheet" type="text/css" href="yourstyle.css"&gt;</span>
															</td>
														</tr>
														
														<tr>
															<td class="option-left">
																<h4>External JS</h4>
																
																<?php
																	$external_js = stripcslashes( get_option( 'external_js', "" ) );
																?>
																<textarea id="external_js" name="external_js" class="code2" rows="8" cols="50"><?php echo $external_js; ?></textarea>
															</td>
															
															<td class="option-right">
																Add your custom external (.js) file.
																<br>
																<br>
																Sample (.js):
																<br>
																<br>
																<span class="code2">&lt;script src="yourscript.js"&gt;&lt;/script&gt;</span>
															</td>
														</tr>
														
														<tr>
															<td class="option-left">
																<input type="submit" name="submit" class="button button-primary button-large" value="Save Changes">
																
																<input type="hidden" name="settings-submit" value="Y">
															</td>
															
															<td class="option-right">
																
															</td>
														</tr>
													</table>
												</form>
											</div>
											<!-- end .inside -->
										</div>
										<!-- end .postbox -->
									<?php
								
								break;
								
								case 'homepage' :
									
									if ( esc_attr( @$_GET['saved'] ) == 'true' )
									{
										echo '<div class="alert-success" title="Click to close"><p><strong>Saved.</strong></p></div>';
									}
									
									?>
										<div class="postbox">
											<div class="inside">
												<form class="ajax-form" method="post" action="<?php admin_url( 'themes.php?page=theme-options' ); ?>">
													<?php
														wp_nonce_field( "settings-page" );
													?>
													
													<table class="homepage">
														<tr>
															<td class="option-left">
																<h4>Photowall</h4>
																
																<?php
																	$pixelwars_homepage_photowall_animation = get_option( 'pixelwars_homepage_photowall_animation', 'random' );
																?>
																<label>Animation</label>
																<select name="pixelwars_homepage_photowall_animation">
																	<option <?php if ( $pixelwars_homepage_photowall_animation == 'random' ) { echo 'selected="selected"'; } ?>>random</option>
																	<option <?php if ( $pixelwars_homepage_photowall_animation == 'showHide' ) { echo 'selected="selected"'; } ?>>showHide</option>
																	<option <?php if ( $pixelwars_homepage_photowall_animation == 'fadeInOut' ) { echo 'selected="selected"'; } ?>>fadeInOut</option>
																	<option <?php if ( $pixelwars_homepage_photowall_animation == 'slideLeft' ) { echo 'selected="selected"'; } ?>>slideLeft</option>
																	<option <?php if ( $pixelwars_homepage_photowall_animation == 'slideRight' ) { echo 'selected="selected"'; } ?>>slideRight</option>
																	<option <?php if ( $pixelwars_homepage_photowall_animation == 'slideTop' ) { echo 'selected="selected"'; } ?>>slideTop</option>
																	<option <?php if ( $pixelwars_homepage_photowall_animation == 'slideBottom' ) { echo 'selected="selected"'; } ?>>slideBottom</option>
																	<option <?php if ( $pixelwars_homepage_photowall_animation == 'rotateLeft' ) { echo 'selected="selected"'; } ?>>rotateLeft</option>
																	<option <?php if ( $pixelwars_homepage_photowall_animation == 'rotateRight' ) { echo 'selected="selected"'; } ?>>rotateRight</option>
																	<option <?php if ( $pixelwars_homepage_photowall_animation == 'rotateTop' ) { echo 'selected="selected"'; } ?>>rotateTop</option>
																	<option <?php if ( $pixelwars_homepage_photowall_animation == 'rotateBottom' ) { echo 'selected="selected"'; } ?>>rotateBottom</option>
																	<option <?php if ( $pixelwars_homepage_photowall_animation == 'scale' ) { echo 'selected="selected"'; } ?>>scale</option>
																	<option <?php if ( $pixelwars_homepage_photowall_animation == 'rotate3d' ) { echo 'selected="selected"'; } ?>>rotate3d</option>
																	<option <?php if ( $pixelwars_homepage_photowall_animation == 'rotateLeftScale' ) { echo 'selected="selected"'; } ?>>rotateLeftScale</option>
																	<option <?php if ( $pixelwars_homepage_photowall_animation == 'rotateRightScale' ) { echo 'selected="selected"'; } ?>>rotateRightScale</option>
																	<option <?php if ( $pixelwars_homepage_photowall_animation == 'rotateTopScale' ) { echo 'selected="selected"'; } ?>>rotateTopScale</option>
																	<option <?php if ( $pixelwars_homepage_photowall_animation == 'rotateBottomScale' ) { echo 'selected="selected"'; } ?>>rotateBottomScale</option>
																</select>
																
																<?php
																	$pixelwars_homepage_photowall_interval = get_option( 'pixelwars_homepage_photowall_interval', '1600' );
																?>
																<label style="display: block; max-width: 150px;">Photos Change Interval</label>
																<input type="number" min="500" max="5000" step="100" name="pixelwars_homepage_photowall_interval" value="<?php echo $pixelwars_homepage_photowall_interval; ?>">
																<span style="font-size: 11px; color: #666;">
																	Default: 1600 milliseconds
																</span>
																
																<?php
																	$pixelwars_homepage_photowall_max_step = get_option( 'pixelwars_homepage_photowall_max_step', '3' );
																?>
																<label style="display: block; max-width: 65px;">Max Step</label>
																<input type="number" min="1" max="3" step="1" name="pixelwars_homepage_photowall_max_step" value="<?php echo $pixelwars_homepage_photowall_max_step; ?>">
																<span style="font-size: 11px; color: #666;">
																	Default: 3
																</span>
															</td>
															<td class="option-right">
																Homepage with Photowall
																<br>
																Homepage Landing with Photowall
															</td>
														</tr>
														
														<tr>
															<td class="option-left">
																<h4>Ken Slider</h4>
																
																<?php
																	$pixelwars_homepage_ken_slider_animation = get_option( 'pixelwars_homepage_ken_slider_animation', 'kenburns' );
																?>
																<label>Animation</label>
																<select name="pixelwars_homepage_ken_slider_animation">
																	<option <?php if ( $pixelwars_homepage_ken_slider_animation == 'kenburns' ) { echo 'selected="selected"'; } ?>>kenburns</option>
																	<option <?php if ( $pixelwars_homepage_ken_slider_animation == 'fade' ) { echo 'selected="selected"'; } ?>>fade</option>
																	<option <?php if ( $pixelwars_homepage_ken_slider_animation == 'horizontal' ) { echo 'selected="selected"'; } ?>>horizontal</option>
																	<option <?php if ( $pixelwars_homepage_ken_slider_animation == 'false' ) { echo 'selected="selected"'; } ?>>false</option>
																</select>
																
																<?php
																	$pixelwars_homepage_ken_slider_interval = get_option( 'pixelwars_homepage_ken_slider_interval', '5000' );
																?>
																<label style="display: block; max-width: 85px;">Slide Interval</label>
																<input type="number" min="500" max="10000" step="250" name="pixelwars_homepage_ken_slider_interval" value="<?php echo $pixelwars_homepage_ken_slider_interval; ?>">
																<span style="font-size: 11px; color: #666;">
																	Default: 5000 milliseconds
																</span>
															</td>
															<td class="option-right">
																Homepage Alternate with Slideshow
															</td>
														</tr>
														
														<tr>
															<td class="option-left">
																<h4>Carousel Slider</h4>
																
																<?php
																	$pixelwars_homepage_owl_carousel_items = get_option( 'pixelwars_homepage_owl_carousel_items', '4' );
																?>
																<label style="display: block; max-width: 32px;">Items</label>
																<input type="number" min="1" max="10" step="1" name="pixelwars_homepage_owl_carousel_items" value="<?php echo $pixelwars_homepage_owl_carousel_items; ?>">
																<span style="font-size: 11px; color: #666;">
																	Default: 4
																</span>
																
																<?php
																	$pixelwars_homepage_owl_carousel_loop = get_option( 'pixelwars_homepage_owl_carousel_loop', 'true' );
																?>
																<label style="display: block; max-width: 30px;">Loop</label>
																<select name="pixelwars_homepage_owl_carousel_loop">
																	<option <?php if ( $pixelwars_homepage_owl_carousel_loop == 'true' ) { echo 'selected="selected"'; } ?>>true</option>
																	<option <?php if ( $pixelwars_homepage_owl_carousel_loop == 'false' ) { echo 'selected="selected"'; } ?>>false</option>
																</select>
																
																<?php
																	$pixelwars_homepage_owl_carousel_center = get_option( 'pixelwars_homepage_owl_carousel_center', 'true' );
																?>
																<label>Center</label>
																<select name="pixelwars_homepage_owl_carousel_center">
																	<option <?php if ( $pixelwars_homepage_owl_carousel_center == 'true' ) { echo 'selected="selected"'; } ?>>true</option>
																	<option <?php if ( $pixelwars_homepage_owl_carousel_center == 'false' ) { echo 'selected="selected"'; } ?>>false</option>
																</select>
																
																<?php
																	$pixelwars_homepage_owl_carousel_mouse_drag = get_option( 'pixelwars_homepage_owl_carousel_mouse_drag', 'true' );
																?>
																<label>Mouse Drag</label>
																<select name="pixelwars_homepage_owl_carousel_mouse_drag">
																	<option <?php if ( $pixelwars_homepage_owl_carousel_mouse_drag == 'true' ) { echo 'selected="selected"'; } ?>>true</option>
																	<option <?php if ( $pixelwars_homepage_owl_carousel_mouse_drag == 'false' ) { echo 'selected="selected"'; } ?>>false</option>
																</select>
																
																<?php
																	$pixelwars_homepage_owl_carousel_nav_links = get_option( 'pixelwars_homepage_owl_carousel_nav_links', 'true' );
																?>
																<label>Prev/Next Links</label>
																<select name="pixelwars_homepage_owl_carousel_nav_links">
																	<option <?php if ( $pixelwars_homepage_owl_carousel_nav_links == 'true' ) { echo 'selected="selected"'; } ?>>true</option>
																	<option <?php if ( $pixelwars_homepage_owl_carousel_nav_links == 'false' ) { echo 'selected="selected"'; } ?>>false</option>
																</select>
																
																<?php
																	$pixelwars_homepage_owl_carousel_nav_dots = get_option( 'pixelwars_homepage_owl_carousel_nav_dots', 'false' );
																?>
																<label>Nav Dots</label>
																<select name="pixelwars_homepage_owl_carousel_nav_dots">
																	<option <?php if ( $pixelwars_homepage_owl_carousel_nav_dots == 'true' ) { echo 'selected="selected"'; } ?>>true</option>
																	<option <?php if ( $pixelwars_homepage_owl_carousel_nav_dots == 'false' ) { echo 'selected="selected"'; } ?>>false</option>
																</select>
																
																<?php
																	$pixelwars_homepage_owl_carousel_autoplay = get_option( 'pixelwars_homepage_owl_carousel_autoplay', 'true' );
																?>
																<label>Autoplay</label>
																<select name="pixelwars_homepage_owl_carousel_autoplay">
																	<option <?php if ( $pixelwars_homepage_owl_carousel_autoplay == 'true' ) { echo 'selected="selected"'; } ?>>true</option>
																	<option <?php if ( $pixelwars_homepage_owl_carousel_autoplay == 'false' ) { echo 'selected="selected"'; } ?>>false</option>
																</select>
																
																<?php
																	$pixelwars_homepage_owl_carousel_autoplay_speed = get_option( 'pixelwars_homepage_owl_carousel_autoplay_speed', '500' );
																?>
																<label style="display: block; max-width: 140px;">Slide Transiton Speed</label>
																<input type="number" min="100" max="1000" step="100" name="pixelwars_homepage_owl_carousel_autoplay_speed" value="<?php echo $pixelwars_homepage_owl_carousel_autoplay_speed; ?>">
																<span style="font-size: 11px; color: #666;">
																	Default: 500 milliseconds
																</span>
																
																<?php
																	$pixelwars_homepage_owl_carousel_autoplay_timeout = get_option( 'pixelwars_homepage_owl_carousel_autoplay_timeout', '3000' );
																?>
																<label style="display: block; max-width: 120px;">Slide Interval Time</label>
																<input type="number" min="500" max="10000" step="250" name="pixelwars_homepage_owl_carousel_autoplay_timeout" value="<?php echo $pixelwars_homepage_owl_carousel_autoplay_timeout; ?>">
																<span style="font-size: 11px; color: #666;">
																	Default: 3000 milliseconds
																</span>
															</td>
															<td class="option-right">
																Homepage Alternate with Slider
																<br>
																Homepage Landing with Slideshow
															</td>
														</tr>
														
														<tr>
															<td class="option-left">
																<input type="submit" name="submit" class="button button-primary button-large" value="Save Changes">
																
																<input type="hidden" name="settings-submit" value="Y">
															</td>
															<td class="option-right">
																
															</td>
														</tr>
													</table>
												</form>
											</div>
										</div>
									<?php
								break;
								
								case 'portfolio' :
									
									if ( esc_attr( @$_GET['saved'] ) == 'true' )
									{
										echo '<div class="alert-success" title="Click to close"><p><strong>Saved.</strong></p></div>';
									}
									
									?>
										<div class="postbox">
											<div class="inside">
												<form class="ajax-form" method="post" action="<?php admin_url( 'themes.php?page=theme-options' ); ?>">
													<?php
														wp_nonce_field( 'settings-page' );
													?>
													
													<table>
														<tr>
															<td class="option-left">
																<h4>Page Content</h4>
																<?php
																	$pixelwars_portfolio_page_content = get_option( 'pixelwars_portfolio_page_content', 'Top' );
																?>
																<select name="pixelwars_portfolio_page_content">
																	<option <?php if ( $pixelwars_portfolio_page_content == 'Top' ) { echo 'selected="selected"'; } ?>>Top</option>
																	<option <?php if ( $pixelwars_portfolio_page_content == 'Bottom' ) { echo 'selected="selected"'; } ?>>Bottom</option>
																</select>
															</td>
															<td class="option-right">
																Above / below location.
															</td>
														</tr>
														
														<tr>
															<td class="option-left">
																<h4>Gallery Loading</h4>
																<?php
																	$pixelwars_portfolio_gallery_loading = get_option( 'pixelwars_portfolio_gallery_loading', 'wait-for-all-images' );
																?>
																<select name="pixelwars_portfolio_gallery_loading">
																	<option <?php if ( $pixelwars_portfolio_gallery_loading == 'wait-for-all-images' ) { echo 'selected="selected"'; } ?> value="wait-for-all-images">Wait for all images</option>
																	<option <?php if ( $pixelwars_portfolio_gallery_loading == 'load-one-by-one' ) { echo 'selected="selected"'; } ?> value="load-one-by-one">Load one by one</option>
																</select>
															</td>
															<td class="option-right">
																Select loading type.
															</td>
														</tr>
														
														<tr>
															<td class="option-left">
																<h4>Effect</h4>
																
																<?php
																	$gallery_image_load_effect = get_option( 'gallery_image_load_effect', 'effect-4' );
																?>
																<select name="gallery_image_load_effect">
																	<option <?php if ( $gallery_image_load_effect == 'effect-1' ) { echo 'selected="selected"'; } ?> value="effect-1">Effect 1</option>
																	<option <?php if ( $gallery_image_load_effect == 'effect-2' ) { echo 'selected="selected"'; } ?> value="effect-2">Effect 2</option>
																	<option <?php if ( $gallery_image_load_effect == 'effect-3' ) { echo 'selected="selected"'; } ?> value="effect-3">Effect 3</option>
																	<option <?php if ( $gallery_image_load_effect == 'effect-4' ) { echo 'selected="selected"'; } ?> value="effect-4">Effect 4</option>
																	<option <?php if ( $gallery_image_load_effect == 'effect-5' ) { echo 'selected="selected"'; } ?> value="effect-5">Effect 5</option>
																	<option <?php if ( $gallery_image_load_effect == 'effect-6' ) { echo 'selected="selected"'; } ?> value="effect-6">Effect 6</option>
																</select>
															</td>
															<td class="option-right">
																Image loading effect.
															</td>
														</tr>
														
														<tr>
															<td class="option-left">
																<h4>Row Height</h4>
																<?php
																	$gallery_row_height = get_option( 'gallery_row_height', '360' );
																?>
																<input type="number" min="50" max="1000" step="1" name="gallery_row_height" value="<?php echo $gallery_row_height; ?>">
																<span style="font-size: 11px; color: #666;">
																	Default: 360 px
																</span>
															</td>
															<td class="option-right">
																Image height.
															</td>
														</tr>
														
														<tr>
															<td class="option-left">
																<h4>Full-size Image Resizing</h4>
																<?php
																	$pixelwars_full_size_image_resizing = get_option( 'pixelwars_full_size_image_resizing', '4k' );
																?>
																<select name="pixelwars_full_size_image_resizing">
																	<option <?php if ( $pixelwars_full_size_image_resizing == '4k' ) { echo 'selected="selected"'; } ?> value="4k">4K - 8MP - 3840px (Default)</option>
																	<option <?php if ( $pixelwars_full_size_image_resizing == '2k' ) { echo 'selected="selected"'; } ?> value="2k">2K - 2MP - 1920px</option>
																	<option <?php if ( $pixelwars_full_size_image_resizing == 'medium' ) { echo 'selected="selected"'; } ?> value="medium">Medium - 1400px</option>
																	<option <?php if ( $pixelwars_full_size_image_resizing == 'no_resizing' ) { echo 'selected="selected"'; } ?> value="no_resizing">No resizing (Full size)</option>
																</select>
															</td>
															<td class="option-right">
																Image size in zoom-in.
															</td>
														</tr>
														
														<tr>
															<td class="option-left">
																<h4>Gallery Style</h4>
																<?php
																	$gallery_style = get_option( 'gallery_style', 'minimal' );
																?>
																<select name="gallery_style">
																	<option <?php if ( $gallery_style == 'standard' ) { echo 'selected="selected"'; } ?> value="standard">Standard</option>
																	<option <?php if ( $gallery_style == 'minimal' ) { echo 'selected="selected"'; } ?> value="minimal">Minimal</option>
																</select>
															</td>
															<td class="option-right">
																Photo gallery style.
															</td>
														</tr>
														
														<tr>
															<td class="option-left">
																<h4>Share Button</h4>
																<?php
																	$gallery_share_button = get_option( 'gallery_share_button', 'true' );
																?>
																<select name="gallery_share_button">
																	<option <?php if ( $gallery_share_button == 'true' ) { echo 'selected="selected"'; } ?> value="true">Yes</option>
																	<option <?php if ( $gallery_share_button == 'false' ) { echo 'selected="selected"'; } ?> value="false">No</option>
																</select>
															</td>
															<td class="option-right">
																Enable/disable.
															</td>
														</tr>
														
														<tr>
															<td class="option-left">
																<h4>Fullscreen Button</h4>
																<?php
																	$gallery_fullscreen_button = get_option( 'gallery_fullscreen_button', 'true' );
																?>
																<select name="gallery_fullscreen_button">
																	<option <?php if ( $gallery_fullscreen_button == 'true' ) { echo 'selected="selected"'; } ?> value="true">Yes</option>
																	<option <?php if ( $gallery_fullscreen_button == 'false' ) { echo 'selected="selected"'; } ?> value="false">No</option>
																</select>
															</td>
															<td class="option-right">
																Enable/disable.
															</td>
														</tr>
														
														<tr>
															<td class="option-left">
																<h4>Background Opacity</h4>
																<?php
																	$gallery_background_color_opacity = get_option( 'gallery_background_color_opacity', '1.00' );
																?>
																<input type="number" min="0.00" max="1.00" step="0.05" name="gallery_background_color_opacity" value="<?php echo $gallery_background_color_opacity; ?>">
																<span style="font-size: 11px; color: #666;">
																	Default: 1.00
																</span>
															</td>
															<td class="option-right">
																Background color opacity.
															</td>
														</tr>
														
														<tr>
															<td class="option-left">
																<input type="submit" name="submit" class="button button-primary button-large" value="Save Changes">
																
																<input type="hidden" name="settings-submit" value="Y">
															</td>
															<td class="option-right">
																
															</td>
														</tr>
													</table>
												</form>
											</div>
										</div>
									<?php
								break;
								
								case 'sidebar' :
								
									if ( esc_attr( @$_GET['saved'] ) == 'true' )
									{
										$no_sidebar_name = get_option( 'no_sidebar_name' );
										
										if ( $no_sidebar_name == "" )
										{
											echo '<div class="alert-success" title="Click to close"><p><strong>Enter a text for new sidebar name.</strong></p></div>';
										}
										else
										{
											echo '<div class="alert-success" title="Click to close"><p><strong>Created.</strong></p></div>';
										}
										// end if
									}
									elseif ( esc_attr( @$_GET['deleted'] ) == 'true' )
									{
										delete_option( 'sidebars_with_commas' );
										
										echo '<div class="alert-success" title="Click to close"><p><strong>Deleted.</strong></p></div>';
									}
									// end if
									
									?>
										<div class="postbox">
											<div class="inside">
												<?php
													$wp_admin_url = admin_url( 'themes.php?page=theme-options&tab=sidebar' );
												?>
												
												<form method="post" action="<?php echo $wp_admin_url; ?>">
													<?php
														wp_nonce_field( "settings-page" );
													?>
													
													<table>
														<tr>
															<td class="option-left">
																<h4>New Sidebar</h4>
																
																<input type="text" id="new_sidebar_name" name="new_sidebar_name" required="required" style="width: 100%;" value="">
															</td>
															
															<td class="option-right">
																Enter a text for a new sidebar name.
															</td>
														</tr>
														
														<tr>
															<td class="option-left">
																<input type="submit" name="submit" class="button button-primary button-large" value="Create">
																
																<input type="hidden" name="settings-submit" value="Y">
															</td>
															
															<td class="option-right">
																Create new sidebar.
															</td>
														</tr>
														
														<tr>
															<td class="option-left">
																<h4>Sidebars</h4>
																
																<select id="sidebars" name="sidebars" style="width: 100%;" size="10" disabled="disabled">
																	<?php
																		$sidebars_with_commas = get_option( 'sidebars_with_commas' );
																		
																		if ( $sidebars_with_commas != "" )
																		{
																			$sidebars = preg_split("/[\s]*[,][\s]*/", $sidebars_with_commas);

																			foreach ( $sidebars as $sidebar_name )
																			{
																				echo '<option>' . $sidebar_name . '</option>';
																			}
																			// end for
																		}
																		// end if
																	?>
																</select>
															</td>
															
															<td class="option-right">
																New sidebar name must be different from created sidebar names.
															</td>
														</tr>
														
														<tr>
															<td class="option-left">
																<?php
																	$wp_admin_url = admin_url( 'themes.php?page=theme-options&tab=sidebar&deleted=true' );
																?>
																<a href="<?php echo $wp_admin_url; ?>" class="button button-primary button-large" style="margin-top: 20px;">Delete</a>
															</td>
															
															<td class="option-right">
																Remove.
															</td>
														</tr>
													</table>
												</form>
											</div>
											<!-- end .inside -->
										</div>
										<!-- end .postbox -->
									<?php
								break;
								
								case 'blog' :
									
									if ( esc_attr( @$_GET['saved'] ) == 'true' )
									{
										echo '<div class="alert-success" title="Click to close"><p><strong>Saved.</strong></p></div>';
									}
									
									?>
										<div class="postbox">
											<div class="inside">
												<form class="ajax-form" method="post" action="<?php admin_url( 'themes.php?page=theme-options' ); ?>">
													<?php
														wp_nonce_field( 'settings-page' );
													?>
													
													<table>
														<tr>
															<td class="option-left">
																<h4>Blog Type</h4>
																<?php
																	$blog_type = get_option( 'blog_type', 'Regular' );
																?>
																<select id="blog_type" name="blog_type" style="width: 100%;">
																	<option <?php if ( $blog_type == 'Regular' ) { echo 'selected="selected"'; } ?>>Regular</option>
																	<option <?php if ( $blog_type == 'Simple' ) { echo 'selected="selected"'; } ?>>Simple</option>
																</select>
																
																<h4>Category Archive Type</h4>
																<?php
																	$category_archive_type = get_option( 'category_archive_type', 'Regular' );
																?>
																<select id="category_archive_type" name="category_archive_type" style="width: 100%;">
																	<option <?php if ( $category_archive_type == 'Regular' ) { echo 'selected="selected"'; } ?>>Regular</option>
																	<option <?php if ( $category_archive_type == 'Simple' ) { echo 'selected="selected"'; } ?>>Simple</option>
																</select>
																
																<h4>Tag Archive Type</h4>
																<?php
																	$tag_archive_type = get_option( 'tag_archive_type', 'Regular' );
																?>
																<select id="tag_archive_type" name="tag_archive_type" style="width: 100%;">
																	<option <?php if ( $tag_archive_type == 'Regular' ) { echo 'selected="selected"'; } ?>>Regular</option>
																	<option <?php if ( $tag_archive_type == 'Simple' ) { echo 'selected="selected"'; } ?>>Simple</option>
																</select>
																
																<h4>Author Archive Type</h4>
																<?php
																	$author_archive_type = get_option( 'author_archive_type', 'Regular' );
																?>
																<select id="author_archive_type" name="author_archive_type" style="width: 100%;">
																	<option <?php if ( $author_archive_type == 'Regular' ) { echo 'selected="selected"'; } ?>>Regular</option>
																	<option <?php if ( $author_archive_type == 'Simple' ) { echo 'selected="selected"'; } ?>>Simple</option>
																</select>
																
																<h4>Date Archive Type</h4>
																<?php
																	$date_archive_type = get_option( 'date_archive_type', 'Regular' );
																?>
																<select id="date_archive_type" name="date_archive_type" style="width: 100%;">
																	<option <?php if ( $date_archive_type == 'Regular' ) { echo 'selected="selected"'; } ?>>Regular</option>
																	<option <?php if ( $date_archive_type == 'Simple' ) { echo 'selected="selected"'; } ?>>Simple</option>
																</select>
																
																<h4>Search Result Type</h4>
																<?php
																	$search_result_type = get_option( 'search_result_type', 'Regular' );
																?>
																<select id="search_result_type" name="search_result_type" style="width: 100%;">
																	<option <?php if ( $search_result_type == 'Regular' ) { echo 'selected="selected"'; } ?>>Regular</option>
																	<option <?php if ( $search_result_type == 'Simple' ) { echo 'selected="selected"'; } ?>>Simple</option>
																</select>
															</td>
															<td class="option-right">
																Select layout type.
															</td>
														</tr>
														
														<tr>
															<td class="option-left">
																<h4>Sidebar</h4>
																
																<?php
																	$blog_sidebar = get_option( 'blog_sidebar', 'Yes' );
																?>
																<label>Blog Sidebar</label>
																<select name="blog_sidebar">
																	<option <?php if ( $blog_sidebar == 'Yes' ) { echo 'selected="selected"'; } ?>>Yes</option>
																	<option <?php if ( $blog_sidebar == 'No' ) { echo 'selected="selected"'; } ?>>No</option>
																</select>
																
																<?php
																	$post_sidebar = get_option( 'post_sidebar', 'Yes' );
																?>
																<label>Post Sidebar</label>
																<select name="post_sidebar">
																	<option <?php if ( $post_sidebar == 'Yes' ) { echo 'selected="selected"'; } ?>>Yes</option>
																	<option <?php if ( $post_sidebar == 'No' ) { echo 'selected="selected"'; } ?>>No</option>
																</select>
															</td>
															<td class="option-right">
																Enable/disable.
															</td>
														</tr>
														
														<tr>
															<td class="option-left">
																<h4>Automatic Excerpt</h4>
																<?php
																	$theme_excerpt = get_option('theme_excerpt', 'standard');
																?>
																<select name="theme_excerpt">
																	<option <?php if ($theme_excerpt == 'standard') { echo 'selected="selected"'; } ?> value="standard">Yes - Only for standard format</option>
																	<option <?php if ($theme_excerpt == 'Yes') { echo 'selected="selected"'; } ?> value="Yes">Yes - For all post formats</option>
																	<option <?php if ($theme_excerpt == 'No') { echo 'selected="selected"'; } ?>>No</option>
																</select>
															</td>
															<td class="option-right">
																Generates an excerpt from the post content.
															</td>
														</tr>
														
														<tr>
															<td class="option-left">
																<h4>About The Author Module</h4>
																
																<?php
																	$about_the_author_module = get_option( 'about_the_author_module', 'Yes' );
																?>
																<select id="about_the_author_module" name="about_the_author_module" style="width: 100%;">
																	<option <?php if ( $about_the_author_module == 'Yes' ) { echo 'selected="selected"'; } ?>>Yes</option>
																	
																	<option <?php if ( $about_the_author_module == 'No' ) { echo 'selected="selected"'; } ?>>No</option>
																</select>
															</td>
															
															<td class="option-right">
																Enable/disable.
															</td>
														</tr>
														
														
														<tr>
															<td class="option-left">
																<h4>Related Posts</h4>
																
																<?php
																	$pixelwars__related_posts = get_option( 'pixelwars__related_posts', 'Yes' );
																?>
																
																<select id="pixelwars__related_posts" name="pixelwars__related_posts">
																	<option <?php if ( $pixelwars__related_posts == 'Yes' ) { echo 'selected="selected"'; } ?>>Yes</option>
																	
																	<option <?php if ( $pixelwars__related_posts == 'No' ) { echo 'selected="selected"'; } ?>>No</option>
																</select>
															</td>
															
															<td class="option-right">
																Enable/disable.
															</td>
														</tr>
														
														
														<tr>
															<td class="option-left">
																<h4>Numbered Pagination</h4>
																
																<?php
																	$pagination = get_option( 'pagination', 'No' );
																?>
																<select id="pagination" name="pagination" style="width: 100%;">
																	<option <?php if ( $pagination == 'Yes' ) { echo 'selected="selected"'; } ?>>Yes</option>
																	
																	<option <?php if ( $pagination == 'No' ) { echo 'selected="selected"'; } ?>>No</option>
																</select>
															</td>
															
															<td class="option-right">
																Use numbered pagination instead of Older-Newer links.
															</td>
														</tr>
														
														<tr>
															<td class="option-left">
																<input type="submit" name="submit" class="button button-primary button-large" value="Save Changes">
																
																<input type="hidden" name="settings-submit" value="Y">
															</td>
															
															<td class="option-right">
																
															</td>
														</tr>
													</table>
												</form>
											</div>
										</div>
									<?php
								break;
								
								
								case 'seo' :
									
									if ( esc_attr( @$_GET['saved'] ) == 'true' )
									{
										echo '<div class="alert-success" title="Click to close"><p><strong>Saved.</strong></p></div>';
									}
									
									?>
										<div class="postbox">
											<div class="inside">
												<form class="ajax-form" method="post" action="<?php admin_url( 'themes.php?page=theme-options' ); ?>">
													<?php
														wp_nonce_field( "settings-page" );
													?>
													
													
													<table>
														<tr>
															<td class="option-left">
																<h4>Tracking Code (/head)</h4>
																
																<?php
																	$tracking_code_head = stripcslashes( get_option( 'tracking_code_head' ) );
																?>
																<textarea id="tracking_code_head" name="tracking_code_head" class="code2" rows="8" cols="50"><?php echo $tracking_code_head; ?></textarea>
															</td>
															
															<td class="option-right">
																Paste your Google Analytics (or other) tracking code here. It will be inserted before the closing head tag.
															</td>
														</tr>
														
														<tr>
															<td class="option-left">
																<h4>Tracking Code (/body)</h4>
																
																<?php
																	$tracking_code_body = stripcslashes( get_option( 'tracking_code_body' ) );
																?>
																<textarea id="tracking_code_body" name="tracking_code_body" class="code2" rows="8" cols="50"><?php echo $tracking_code_body; ?></textarea>
															</td>
															
															<td class="option-right">
																Paste your Google Analytics (or other) tracking code here. It will be inserted before the closing body tag.
															</td>
														</tr>
														
														<tr>
															<td class="option-left">
																<input type="submit" name="submit" class="button button-primary button-large" value="Save Changes">
																
																<input type="hidden" name="settings-submit" value="Y">
															</td>
															
															<td class="option-right">
																
															</td>
														</tr>
													</table>
												</form>
											</div>
											<!-- end .inside -->
										</div>
										<!-- end .postbox -->
									<?php
								break;
							}
							// end tab content
						}
						// end settings page
					?>
				</div>
				<!-- end #poststuff -->
			</div>
			<!-- end .wrap2 -->
		<?php
	}
	// end theme_options_page


/* ============================================================================================================================================ */


	function theme_save_settings()
	{
		global $pagenow;
		
		if ( $pagenow == 'themes.php' && $_GET['page'] == 'theme-options' )
		{
			if ( isset ( $_GET['tab'] ) )
			{
				$tab = $_GET['tab'];
			}
			else
			{
				$tab = 'general';
			}
			
			
			switch ( $tab )
			{
				case 'general' :
				
					update_option( 'logo_type', $_POST['logo_type'] );
					
					update_option( 'logo_image', $_POST['logo_image'] );
					update_option( 'image_logo_height', $_POST['image_logo_height'] );
					
					update_option( 'select_text_logo', $_POST['select_text_logo'] );
					update_option( 'theme_site_title', $_POST['theme_site_title'] );
					
					update_option( 'select_tagline', $_POST['select_tagline'] );
					update_option( 'theme_tagline', $_POST['theme_tagline'] );
					
					update_option( 'logo_login', $_POST['logo_login'] );
					update_option( 'logo_login_hide', $_POST['logo_login_hide'] );
					
					update_option( 'favicon', $_POST['favicon'] );
					update_option( 'apple_touch_icon', $_POST['apple_touch_icon'] );
					
					update_option( 'copyright_text', $_POST['copyright_text'] );
				
				break;
				
				
				case 'style' :
				
					update_option( 'char_set_latin', $_POST['char_set_latin'] );
					update_option( 'char_set_latin_ext', $_POST['char_set_latin_ext'] );
					update_option( 'char_set_cyrillic', $_POST['char_set_cyrillic'] );
					update_option( 'char_set_cyrillic_ext', $_POST['char_set_cyrillic_ext'] );
					update_option( 'char_set_greek', $_POST['char_set_greek'] );
					update_option( 'char_set_greek_ext', $_POST['char_set_greek_ext'] );
					update_option( 'char_set_vietnamese', $_POST['char_set_vietnamese'] );
					
					update_option( 'extra_font_styles', $_POST['extra_font_styles'] );
					update_option( 'mobile_zoom', $_POST['mobile_zoom'] );
					update_option( 'pixelwars__gallery_type_for_posts_and_pages', $_POST['pixelwars__gallery_type_for_posts_and_pages'] );
					
					update_option( 'custom_css', $_POST['custom_css'] );
					update_option( 'external_css', $_POST['external_css'] );
					update_option( 'external_js', $_POST['external_js'] );
					
				break;
				
				
				case 'homepage' :
				
					update_option( 'pixelwars_homepage_photowall_animation', $_POST['pixelwars_homepage_photowall_animation'] );
					update_option( 'pixelwars_homepage_photowall_interval', $_POST['pixelwars_homepage_photowall_interval'] );
					update_option( 'pixelwars_homepage_photowall_max_step', $_POST['pixelwars_homepage_photowall_max_step'] );
					
					update_option( 'pixelwars_homepage_ken_slider_animation', $_POST['pixelwars_homepage_ken_slider_animation'] );
					update_option( 'pixelwars_homepage_ken_slider_interval', $_POST['pixelwars_homepage_ken_slider_interval'] );
					
					update_option( 'pixelwars_homepage_owl_carousel_items', $_POST['pixelwars_homepage_owl_carousel_items'] );
					update_option( 'pixelwars_homepage_owl_carousel_loop', $_POST['pixelwars_homepage_owl_carousel_loop'] );
					update_option( 'pixelwars_homepage_owl_carousel_center', $_POST['pixelwars_homepage_owl_carousel_center'] );
					update_option( 'pixelwars_homepage_owl_carousel_mouse_drag', $_POST['pixelwars_homepage_owl_carousel_mouse_drag'] );
					update_option( 'pixelwars_homepage_owl_carousel_nav_links', $_POST['pixelwars_homepage_owl_carousel_nav_links'] );
					update_option( 'pixelwars_homepage_owl_carousel_nav_dots', $_POST['pixelwars_homepage_owl_carousel_nav_dots'] );
					update_option( 'pixelwars_homepage_owl_carousel_autoplay', $_POST['pixelwars_homepage_owl_carousel_autoplay'] );
					update_option( 'pixelwars_homepage_owl_carousel_autoplay_speed', $_POST['pixelwars_homepage_owl_carousel_autoplay_speed'] );
					update_option( 'pixelwars_homepage_owl_carousel_autoplay_timeout', $_POST['pixelwars_homepage_owl_carousel_autoplay_timeout'] );
				
				break;
				
				
				case 'portfolio' :
					
					update_option( 'pixelwars_portfolio_page_content', $_POST['pixelwars_portfolio_page_content'] );
					update_option( 'pixelwars_portfolio_gallery_loading', $_POST['pixelwars_portfolio_gallery_loading'] );
					update_option( 'pixelwars_full_size_image_resizing', $_POST['pixelwars_full_size_image_resizing'] );
					update_option( 'gallery_style', $_POST['gallery_style'] );
					update_option( 'gallery_share_button', $_POST['gallery_share_button'] );
					update_option( 'gallery_fullscreen_button', $_POST['gallery_fullscreen_button'] );
					update_option( 'gallery_background_color_opacity', $_POST['gallery_background_color_opacity'] );
					update_option( 'gallery_row_height', $_POST['gallery_row_height'] );
					update_option( 'gallery_image_load_effect', $_POST['gallery_image_load_effect'] );
					
				break;
				
				
				case 'blog' :
				
					update_option( 'blog_type', $_POST['blog_type'] );
					update_option( 'category_archive_type', $_POST['category_archive_type'] );
					update_option( 'tag_archive_type', $_POST['tag_archive_type'] );
					update_option( 'author_archive_type', $_POST['author_archive_type'] );
					update_option( 'date_archive_type', $_POST['date_archive_type'] );
					update_option( 'search_result_type', $_POST['search_result_type'] );
					update_option( 'blog_sidebar', $_POST['blog_sidebar'] );
					update_option( 'post_sidebar', $_POST['post_sidebar'] );
					update_option( 'theme_excerpt', $_POST['theme_excerpt'] );
					update_option( 'about_the_author_module', $_POST['about_the_author_module'] );
					update_option( 'pixelwars__related_posts', $_POST['pixelwars__related_posts'] );
					update_option( 'pagination', $_POST['pagination'] );
				
				break;
				
				
				case 'sidebar' :
					
					update_option( 'no_sidebar_name', esc_attr( $_POST['new_sidebar_name'] ) );
					
					if ( esc_attr( $_POST['new_sidebar_name'] ) != "" )
					{
						$sidebars_with_commas = get_option( 'sidebars_with_commas', "" );
						
						if ( $sidebars_with_commas == "" )
						{
							update_option( 'sidebars_with_commas', esc_attr( $_POST['new_sidebar_name'] ) );
						}
						else
						{
							update_option( 'sidebars_with_commas', get_option( 'sidebars_with_commas' ) . ',' . esc_attr( $_POST['new_sidebar_name'] ) );
						}
					}
				
				break;
				
				
				case 'seo' :
				
					update_option( 'tracking_code_head', $_POST['tracking_code_head'] );
					update_option( 'tracking_code_body', $_POST['tracking_code_body'] );
				
				break;
			}
		}
	}


/* ============================================================================================================================================ */


	function load_settings_page()
	{
		if ( isset( $_POST["settings-submit"] ) == 'Y' )
		{
			check_admin_referer( "settings-page" );
			
			theme_save_settings();
			
			$url_parameters = isset( $_GET['tab'] ) ? 'tab=' . $_GET['tab'] . '&saved=true' : 'saved=true';
			
			wp_redirect( admin_url( 'themes.php?page=theme-options&' . $url_parameters ) );
			
			exit;
		}
	}


/* ============================================================================================================================================ */


	function my_theme_menu()
	{
		$settings_page = add_theme_page('Theme Options',
										'Theme Options',
										'edit_theme_options',
										'theme-options',
										'theme_options_page' );
		
		add_action( "load-{$settings_page}", 'load_settings_page' );
	}
	
	add_action( 'admin_menu', 'my_theme_menu' );


/* ============================================================================================================================================ */


?>