<?php

	/// WIDGETS
						
	function rich_text($content) {
	
		$content = stripslashes($content);
		$content = apply_filters("pre_code_be", $content);
		$content = do_shortcode( str_replace("&nbsp;", "", $content ) );
		$content = apply_filters("pre_code_af", $content);
		return $content;
	}
	
	function divider_line($content) {
		return do_shortcode($content);
	}
	
	function divider_empty($content) {
		return do_shortcode($content);
	}
						
?>