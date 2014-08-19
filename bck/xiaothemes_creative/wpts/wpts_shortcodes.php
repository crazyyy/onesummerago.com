<?php

if(!is_admin()) {
	require_once("shortcodes/shortcodes.php");
	require_once("shortcodes/custom_shortcodes.php");
}
else {
	require_once("shortcodes/tinymce/tinymce_shortcodes.php");
}

?>