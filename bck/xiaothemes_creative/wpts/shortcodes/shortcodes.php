<?php
/**
 * WordPress Toolset 2.0 Shortcodes
 *
 */
 
 function wpts_addvar() {
	echo '<script type="text/javascript">var THEME_DIR = "'.get_template_directory_uri().'";</script>';
 }
 
 add_action( 'wp_print_scripts', 'wpts_addvar' );

/*-----------------------------------------------------------------------------------*/
/* Enqueue Scripts
/*-----------------------------------------------------------------------------------*/

function wpts_enqueue_scripts() {

	$key = '';
	
	
	
}
add_action( 'init', 'wpts_enqueue_scripts' );

/*-----------------------------------------------------------------------------------*/
/* Enqueue the styles used by ShortCodes
/*-----------------------------------------------------------------------------------*/

function wpts_enqueue_styles() {
	/** REGISTER assets/css/shortcodes.css **/
	wp_register_style( 'shortcodes-style', get_template_directory_uri() . '/wpts/shortcodes/assets/css/shortcodes.css', array(), '1', 'all' );
	wp_enqueue_style( 'shortcodes-style' );
}
add_action( 'init', 'wpts_enqueue_styles' );

/*-----------------------------------------------------------------------------------*/
/* WP Auto Formatting Fix w/Raw shortocde
/*-----------------------------------------------------------------------------------*/

if( ! function_exists( 'wpts_content_formatter' ) ) {
	function wpts_content_formatter( $content ) {
		$new_content = '';
		$pattern_full = '{(\[raw\].*?\[/raw\])}is';
		$pattern_contents = '{\[raw\](.*?)\[/raw\]}is';
		$pieces = preg_split($pattern_full, $content, -1, PREG_SPLIT_DELIM_CAPTURE);
		foreach ($pieces as $piece) {
			if (preg_match($pattern_contents, $piece, $matches)) {
				$new_content .= $matches[1];
			} else {
				$new_content .= shortcode_unautop( wptexturize( wpautop( $piece ) ) );
			}
		}
		return $new_content;
	}
}
remove_filter( 'the_content', 'wpautop' );
remove_filter( 'the_content', 'wptexturize' );
remove_filter( 'the_content', 'shortcode_unautop' );
add_filter( 'the_content', 'wpts_content_formatter', 99 );

/*-----------------------------------------------------------------------------------*/
/* [p] [div] [br] Markup Shortcodes
/*-----------------------------------------------------------------------------------*/

function wpts_div($atts, $content = null, $code) {
	extract(shortcode_atts(array(
		'id' => '',
		'class' => '',
	), $atts));
		
	if($id != '') { $id = ' id="'.$id.'"'; }
	if($class!= '') { $class = ' class="'.$class.'"'; }

		
	return '<div '.$id.$class.'>' .do_shortcode($content). '</div>';
}
add_shortcode('div', 'wpts_div');
	
function wpts_p($atts, $content = null, $code) {
	extract(shortcode_atts(array(
		'id' => '',
		'class' => '',
		'align' => '',
	), $atts));
		
	if($id != '') { $id = ' id="'.$id.'"'; }
	if($align!= '') { $align = ' align-'.$align; }
	
	return '<p '.$id.' class="'.$class.$align.'">' .do_shortcode($content). '</p>';
}
add_shortcode('p', 'wpts_p');
	
function wpts_br($atts, $content = null, $code) {
	return '<br />';
}
add_shortcode('br', 'wpts_br');

/*-----------------------------------------------------------------------------------*/
/* [video] Display a video player
/*-----------------------------------------------------------------------------------*/

function wpts_video($atts){
	if(isset($atts['type'])){
		switch($atts['type']){
			case 'html5':
				return wpts_video_html5($atts);
				break;
			case 'flash':
				return wpts_video_flash($atts);
				break;
			case 'youtube':
				return wpts_video_youtube($atts);
				break;
			case 'vimeo':
				return wpts_video_vimeo($atts);
				break;
		}
	}
	return '';
}

add_shortcode('video', 'wpts_video');

function wpts_video_html5($atts){
	extract(shortcode_atts(array(
		'mp4' => '',
		'webm' => '',
		'ogg' => '',
		'poster' => '',
		'width' => false,
		'height' => false,
		'preload' => false,
		'autoplay' => false,
		'links' => 'false',
	), $atts));

	if ($height && !$width) $width = intval($height * 16 / 9);
	if (!$height && $width) $height = intval($width * 9 / 16);
	if (!$height && !$width){

	}

	// MP4 Source Supplied
	if ($mp4) {
		$mp4_source = '<source src="'.$mp4.'" type=\'video/mp4; codecs="avc1.42E01E, mp4a.40.2"\'>';
		$mp4_link = '<a href="'.$mp4.'">MP4</a>';
	}

	// WebM Source Supplied
	if ($webm) {
		$webm_source = '<source src="'.$webm.'" type=\'video/webm; codecs="vp8, vorbis"\'>';
		$webm_link = '<a href="'.$webm.'">WebM</a>';
	}

	// Ogg source supplied
	if ($ogg) {
		$ogg_source = '<source src="'.$ogg.'" type=\'video/ogg; codecs="theora, vorbis"\'>';
		$ogg_link = '<a href="'.$ogg.'">Ogg</a>';
	}

	if ($poster) {
		$poster_attribute = 'poster="'.$poster.'"';
		$image_fallback = <<<_end_
			<img src="$poster" width="$width" height="$height" alt="Poster Image" title="No video playback capabilities." />
_end_;
	}

	if ($preload) {
		$preload_attribute = 'preload="auto"';
		$flow_player_preload = ',"autoBuffering":true';
	} else {
		$preload_attribute = 'preload="none"';
		$flow_player_preload = ',"autoBuffering":false';
	}

	if ($autoplay) {
		$autoplay_attribute = "autoplay";
		$flow_player_autoplay = ',"autoPlay":true';
	} else {
		$autoplay_attribute = "";
		$flow_player_autoplay = ',"autoPlay":false';
	}

	$uri = get_template_directory_uri();

	if($links == 'true') {
		$links = "<p class=\"vjs-no-video\"><strong>Download Video:</strong>
		{$mp4_link}
		{$webm_link}
		{$ogg_link}
		</p>";
	}	
	else {
		$links = '';
	}
	
	$output = <<<HTML
<div class="video_frame_html5 video-js-box">
	<video class="video-js" width="{$width}" height="{$height}" {$poster_attribute} controls {$preload_attribute} {$autoplay_attribute}>
		{$mp4_source}
		{$webm_source}
		{$ogg_source}
		<object class="vjs-flash-fallback" width="{$width}" height="{$height}" type="application/x-shockwave-flash"
			data="http://releases.flowplayer.org/swf/flowplayer-3.2.5.swf">
			<param name="movie" value="http://releases.flowplayer.org/swf/flowplayer-3.2.5.swf" />
			<param name="allowfullscreen" value="true" />
			<param name="wmode" value="opaque" />
			<param name="flashvars" value='config={"clip":{"url":"$mp4" $flow_player_autoplay $flow_player_preload ,"wmode":"opaque"}}' />
			{$image_fallback}
		</object>
	</video>
	{$links}
</div>

HTML;

	return '[raw] '.$output.' [/raw]';

}

function wpts_video_flash($atts) {
	extract(shortcode_atts(array(
		'src' 	=> '',
		'width' 	=> false,
		'height' 	=> false,
		'play'			=> 'false',
		'flashvars' => '',
	), $atts));
	
	if ($height && !$width) $width = intval($height * 16 / 9);
	if (!$height && $width) $height = intval($width * 9 / 16);
	if (!$height && !$width){
		/*$height = wpts_get_option('video','flash_height');
		$width = wpts_get_option('video','flash_width');*/
	}

	$uri = get_template_directory_uri();
	if (!empty($src)){
		return <<<HTML
<div class="video_frame">
<object width="{$width}" height="{$height}" type="application/x-shockwave-flash" data="{$src}">
	<param name="movie" value="{$src}" />
	<param name="allowFullScreen" value="true" />
	<param name="allowscriptaccess" value="always" />
	<param name="expressInstaller" value="{$uri}/swf/expressInstall.swf"/>
	<param name="play" value="{$play}"/>
	<param name="wmode" value="opaque" />
	<embed src="$src" type="application/x-shockwave-flash" wmode="opaque" allowscriptaccess="always" allowfullscreen="true" width="{$width}" height="{$height}" />
</object>
</div>
HTML;
	}
}

function wpts_video_vimeo($atts) {
	extract(shortcode_atts(array(
		'clip_id' 	=> '',
		'width' => false,
		'height' => false,
		'title' => 'false',
	), $atts));

	if ($height && !$width) $width = intval($height * 16 / 9);
	if (!$height && $width) $height = intval($width * 9 / 16);
	if (!$height && !$width){
		/*$height = wpts_get_option('video','vimeo_height');
		$width = wpts_get_option('video','vimeo_width');*/
	}
	if($title!='false'){
		$title = 1;
	}else{
		$title = 0;
	}

	if (!empty($clip_id) && is_numeric($clip_id)){
		return "<div class='video_frame'><iframe src='http://player.vimeo.com/video/$clip_id?title={$title}&amp;byline=0&amp;portrait=0' width='$width' height='$height' frameborder='0'></iframe></div>";
	}
}

function wpts_video_youtube($atts, $content=null) {
	extract(shortcode_atts(array(
		'clip_id' 	=> '',
		'width' 	=> false,
		'height' 	=> false,
	), $atts));
	
	if ($height && !$width) $width = intval($height * 16 / 9);
	if (!$height && $width) $height = intval($width * 9 / 16) + 25;
	if (!$height && !$width){
		/*$height = wpts_get_option('video','youbube_height');
		$width = wpts_get_option('video','youbube_width');*/
	}

	if (!empty($clip_id)){
		return "<div class='video_frame'><iframe src='http://www.youtube.com/embed/$clip_id' width='$width' height='$height' frameborder='0'></iframe></div>";
	}
}


?>