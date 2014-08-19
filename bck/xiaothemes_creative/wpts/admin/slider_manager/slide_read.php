<?php
	function full_slider($ops, $id, $idinner ) {
	?>
		<?php
								if($ops != null) :
									foreach($ops as $slidei) {
										?>
											<div class="slide-single setting-block">
												<a href="#" class="delete-single">X</a>
												<div class="slide-preview">
													<?php if($slidei[0] != '') : ?>
														<img src="<?php echo $slidei[0]; ?>" alt="Preview" />
													<?php endif; ?>
												</div>
												<div class="wpts_input">
													<label>Image</label>
													<input type="text" class="upload-admin-input" name="sliders[<?php echo $id; ?>][3][<?php echo $idinner; ?>][0]" value="<?php echo $slidei[0]; ?>" /> <input class="upload-admin" type="button" value="Upload Image" />
												<div class="clearboth"></div>
												</div>
												<div class="wpts_input">
													<label>Thumbnail</label>
													<input type="text" class="upload-admin-input" name="sliders[<?php echo $id; ?>][3][<?php echo $idinner; ?>][1]" value="<?php echo $slidei[1]; ?>" /> <input class="upload-admin" type="button" value="Upload Image" />
												<div class="clearboth"></div>
												</div>
												
											</div>
										<?php
										$idinner++;
										echo '
										<script>
											idinner++;
										</script>';
									} // end if
								endif;
	}
	
	// NIVO SLIDER
	
	function nivo_slider($ops, $id, $idinner ) {
	?>
		<?php
								if($ops != null) :
									foreach($ops as $slidei) {
										?>
											<div class="slide-single setting-block">
												<a href="#" class="delete-single">X</a>
												<div class="slide-preview">
													<?php if($slidei[0] != '') : ?>
														<img src="<?php echo $slidei[0]; ?>" alt="Preview" />
													<?php endif; ?>
												</div>
												<div class="wpts_input">
													<label>Image</label>
													<input type="text" class="upload-admin-input" name="sliders[<?php echo $id; ?>][3][<?php echo $idinner; ?>][0]" value="<?php echo $slidei[0]; ?>" /> <input class="upload-admin" type="button" value="Upload Image" />
												<div class="clearboth"></div>
												</div>
												<div class="wpts_input">
													<label>Title</label>
													<input type="text" name="sliders[<?php echo $id; ?>][3][<?php echo $idinner; ?>][1]" value="<?php echo $slidei[1]; ?>" />
												<div class="clearboth"></div>
												</div>
												<div class="wpts_input">
													<label>Label</label>
													<textarea name="sliders[<?php echo $id; ?>][3][<?php echo $idinner; ?>][2]"><?php echo $slidei[2]; ?></textarea>
												<div class="clearboth"></div>
												</div>
												<div class="wpts_input">
													<label>Button Text</label>
													<input type="text" name="sliders[<?php echo $id; ?>][3][<?php echo $idinner; ?>][3]" value="<?php echo $slidei[3]; ?>" />
												<div class="clearboth"></div>
												</div>
												<div class="wpts_input">
													<label>Button Href</label>
													<input type="text" name="sliders[<?php echo $id; ?>][3][<?php echo $idinner; ?>][4]" value="<?php echo $slidei[4]; ?>" />
												<div class="clearboth"></div>
												</div>
											</div>
										<?php
										$idinner++;
										echo '
										<script>
											idinner++;
										</script>';
									} // end if
								endif;
	}
	
	function bx_slider($ops, $id, $idinner ) {
	?>
		<?php
								if($ops != null) :
									foreach($ops as $slidei) {
										?>
											<div class="slide-single setting-block">
												<a href="#" class="delete-single">X</a>
												<div class="slide-preview">
													<?php if($slidei[0] != '') : ?>
														<img src="<?php echo $slidei[0]; ?>" alt="Preview" />
													<?php endif; ?>
												</div>
												<div class="wpts_input">
													<label>Image</label>
													<input type="text" class="upload-admin-input" name="sliders[<?php echo $id; ?>][3][<?php echo $idinner; ?>][0]" value="<?php echo $slidei[0]; ?>" /> <input class="upload-admin" type="button" value="Upload Image" />
												<div class="clearboth"></div>
												</div>
												<div class="wpts_input">
													<label>Title</label>
													<input type="text" name="sliders[<?php echo $id; ?>][3][<?php echo $idinner; ?>][1]" value="<?php echo $slidei[1]; ?>" />
												<div class="clearboth"></div>
												</div>
												<div class="wpts_input">
													<label>Label</label>
													<textarea name="sliders[<?php echo $id; ?>][3][<?php echo $idinner; ?>][2]"><?php echo $slidei[2]; ?></textarea>
												<div class="clearboth"></div>
												</div>
												<div class="wpts_input">
													<label>Button Text</label>
													<input type="text" name="sliders[<?php echo $id; ?>][3][<?php echo $idinner; ?>][3]" value="<?php echo $slidei[3]; ?>" />
												<div class="clearboth"></div>
												</div>
												<div class="wpts_input">
													<label>Button Href</label>
													<input type="text" name="sliders[<?php echo $id; ?>][3][<?php echo $idinner; ?>][4]" value="<?php echo $slidei[4]; ?>" />
												<div class="clearboth"></div>
												</div>
											</div>
										<?php
										$idinner++;
										echo '
										<script>
											idinner++;
										</script>';
									} // end if
								endif;
	}
?>