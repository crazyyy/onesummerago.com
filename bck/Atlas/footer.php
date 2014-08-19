<?php
/**
 * The template for displaying the footer.
 *
 * @package WordPress
 * @subpackage Kratong
 */
?>

	<br class="clear"/>
	<div id="footer">
		<div id="copyright">© 2013 <a href="http://onesummerago.com/">Ольга Мирошниченко</a></div>
	</div>
</div>
<div class="socialbutt">
	<ul>
		<li class="socbu01"><a href="http://vk.com/t_rexa" target="_blank" rel="nofollow"><img src="http://onesummerago.com/wp-content/themes/Atlas/img/ico-vk.jpg" alt="http://vk.com/t_rexa"></a></li>
		<li class="socbu02"><a href="https://www.facebook.com/olga.t.rexa" target="_blank" rel="nofollow"><img src="http://onesummerago.com/wp-content/themes/Atlas/img/ico-fb.jpg" alt="https://www.facebook.com/olga.t.rexa"></a></li>
		<li class="socbu03"><a href="http://instagram.com/onesummerago" target="_blank" rel="nofollow"><img src="http://onesummerago.com/wp-content/themes/Atlas/img/ico-ins.jpg" alt="http://instagram.com/onesummerago"></a></li>
		<li class="socbu04"><a href="https://twitter.com/one_summer_ago" target="_blank" rel="nofollow"><img src="http://onesummerago.com/wp-content/themes/Atlas/img/ico-tw.jpg" alt="https://twitter.com/one_summer_ago"></a></li>
		<li class="socbu05"><a href="http://www.youtube.com/user/onesummerago" target="_blank" rel="nofollow"><img src="http://onesummerago.com/wp-content/themes/Atlas/img/ico-yt.jpg" alt="http://www.youtube.com/user/onesummerago"></a></li>	
		<li class="socbu06"><a href="http://onesummerago.deviantart.com/" target="_blank" rel="nofollow"><img src="http://onesummerago.com/wp-content/themes/Atlas/img/ico-dev.jpg" alt="http://onesummerago.deviantart.com/"></a></li>
		<li class="socbu07"><a href="http://t-rexa.livejournal.com/" target="_blank" rel="nofollow"><img src="http://onesummerago.com/wp-content/themes/Atlas/img/ico-lj.jpg" alt="http://t-rexa.livejournal.com/"></a></li>
	</ul>
</div>
	<?php include (TEMPLATEPATH . "/google-analytic.php"); ?>
	
	<?php
	

		wp_enqueue_script("custom_js", get_stylesheet_directory_uri()."/js/custom.js", false, $pp_theme_version);
	
	?> 
	
	
	<?php wp_footer(); ?>
</body>
</html>
