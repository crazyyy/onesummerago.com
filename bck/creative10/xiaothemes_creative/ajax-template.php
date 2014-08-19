<?php
	if(isset($_GET["ajax"])){

		if( isset($_GET["paged"]) || !isset($_GET["p"]) ) {
			get_template_part("ajax", "blog");
		}

		if(isset($_GET["p"])) {
			get_template_part("ajax", "single");
		}
		
		exit;
	}
?>