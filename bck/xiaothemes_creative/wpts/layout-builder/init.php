<?php

						require_once("widgets.php");
						
						require_once("layouts.php");

						/// READING

						function readWidget($w) {
							$class = $w[1];
							$content = $w[2];
							$fn = $class;
							//var_dump($w);
							return wpts_content_formatter( $fn($content) );
						}
						
						function readLayout($l) {
							$class = $l[1];
							$widgets = $l[2];
							
							$off = 0;
							$c = 0;
							$length = count($widgets);
							$output = '';
							
							while($c < $length) {
								$output .= readWidget(array_slice($widgets, $off, 4));
								$c += 4;
								$off += 4;
							}
							
							//$fn = $class;
							//$fn($output);
							
							builder_column($output, $class);
						}
						
						

?>