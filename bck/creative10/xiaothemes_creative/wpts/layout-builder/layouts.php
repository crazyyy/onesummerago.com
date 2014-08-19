<?php

	/// LAYOUTS
	
	function full($output) {
		echo '<div class="builder-full">';
		echo $output; 
		echo '</div>';
	}
	
	function builder_column($output, $type) {
		if($type == "full") : full($output); return; endif;
		
		$class = str_replace("_last", " last", $type);
		echo '<div class="'.$class.'">';
		echo $output; 
		echo '</div>';
		if(strpos($class,"last") > 0) 
			echo '<div class="clearboth"></div>';
	}
						
	function one_half($output) {
		echo '<div class="one_half">';
		echo $output; 
		echo '</div>';
	}
						
	function one_half_last($output) {
		echo '<div class="one_half last">';
		echo $output; 
		echo '</div>';
		echo '<div class="clearboth"></div>';
	}
	
	function one_third($output) {
		echo '<div class="one_third">';
		echo $output; 
		echo '</div>';
	}
						
	function one_third_last($output) {
		echo '<div class="one_third last">';
		echo $output; 
		echo '</div>';
		echo '<div class="clearboth"></div>';
	}
						
?>