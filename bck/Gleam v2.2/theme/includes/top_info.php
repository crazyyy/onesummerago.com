<?php
	$et_page_title = '';
	$et_tagline = '';
	if( is_home() ) {
		$et_page_title = get_option( 'gleam_homepage_title' );
		$et_tagline = get_option( 'gleam_homepage_tagline' );
	} elseif( is_tag() ) {
		$et_page_title = esc_html__('Posts Tagged &quot;','Gleam') . single_tag_title('',false) . '&quot;';
	} elseif (is_day()) {
		$et_page_title = esc_html__('Posts made in','Gleam') . ' ' . get_the_time('F jS, Y');
	} elseif (is_month()) {
		$et_page_title = esc_html__('Posts made in','Gleam') . ' ' . get_the_time('F, Y');
	} elseif (is_year()) {
		$et_page_title = esc_html__('Posts made in','Gleam') . ' ' . get_the_time('Y');
	} elseif (is_search()) {
		$et_page_title = esc_html__('Search results for','Gleam') . ' ' . get_search_query();
	} elseif (is_category()) {
		$et_page_title = single_cat_title('',false);
		$et_tagline = category_description();
	} elseif (is_author()) {
		global $wp_query;
		$curauth = $wp_query->get_queried_object();
		$et_page_title = esc_html__('Posts by ','Gleam') . $curauth->nickname;
	} elseif ( is_page() ) {
		$et_page_title = get_the_title();
		$et_tagline = get_post_meta($post->ID,'Description',true) ? get_post_meta($post->ID,'Description',true) : '';
	} elseif ( is_single() ){
		$et_page_title = get_the_title();
	}
?>
<div id="top_title">
	<h1><?php echo esc_html( $et_page_title ); ?></h1>
	<?php if ( '' != $et_tagline ){ ?>
		<p><?php echo esc_html( $et_tagline ); ?></p>
	<?php } ?>
</div> <!-- #top_title -->