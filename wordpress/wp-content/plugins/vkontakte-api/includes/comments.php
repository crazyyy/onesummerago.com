<?php

if ( ! defined( 'DB_NAME' ) ) {
	die;
	bitch;
	die;
}

class Darx_Comments extends Darx_Parent {

	public function __construct() {
		// add sub-page
		add_action( 'admin_menu', array( $this, 'add_page' ), 1 );
		// register settings
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		// add post meta box
		add_action( 'do_meta_boxes', array( $this, 'add_post_meta_box' ), 1 );
		// save post meta
		add_filter( 'save_post', array( $this, 'save_post' ), 1, 3 );
		// add head meta
		add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ) );
		// widgets
		add_action( 'widgets_init', array( $this, 'widgets_init' ) );
		// meta tags
		add_action( 'wp_head', array( $this, 'add_meta_tags' ), 1024 );
		// render
		if ( get_option( 'vkapi_show_comm' ) || get_option( 'fbapi_show_comm' ) ) {
			add_filter( 'comments_template', array( $this, 'wrap_comments_template' ), 1024 ); # no wp comments
			// without wp comments?
			if ( get_option( 'vkapi_close_wp' ) ) {
				add_action( 'vkapi_comments_template', array( $this, 'add_tabs' ), 1024 ); # add comments
				add_filter( 'get_comments_number', array( $this, 'do_empty' ), 1 ); # recount
				add_filter( 'woocommerce_product_review_count', array( $this, 'do_empty' ), 1 ); # wc
			} else {
				add_filter( 'vkapi_comments_template', array( $this, 'add_tabs' ), 1024 ); # add comments
				add_filter( 'get_comments_number', array( $this, 'do_non_empty' ), 1, 2 ); # recount
				add_filter( 'woocommerce_product_review_count', array( $this, 'do_non_empty' ), 1, 2 ); # wc
			}
			// recalculate comments count
			add_action( 'wp_ajax_darx.comments', array( $this, 'ajax_comments' ) );
			add_action( 'wp_ajax_nopriv_darx.comments', array( $this, 'ajax_comments' ) );
		}
		// profile
		add_action( 'profile_personal_options', array( $this, 'profile_render' ) );
		add_action( 'personal_options_update', array( $this, 'profile_save' ) );
	}

	public function add_meta_tags() {
		if ( $vkapi_appid = get_option( 'vkapi_appid' ) ) {
			echo "<meta property=\"vk:app_id\" content=\"$vkapi_appid\" />";
		}
	}

	public function add_page() {
		if ( get_option( 'vkapi_show_comm' ) && get_option( 'vkapi_appid' ) ) {
			add_comments_page(
				'Social API - ' . __( 'Last Comments', 'vkapi' ),
				__( 'Social Comments', 'vkapi' ),
				'manage_options',
				'darx-comments-last',
				array( $this, 'page_comments_last' )
			);
		}

		add_submenu_page(
			'darx-modules',
			'Comments Settings — Social API',
			'Comments Settings',
			'manage_options',
			'darx-comments-settings',
			array( $this, 'page_comments_settings' )
		);
	}

	// todo: https://developers.facebook.com/tools/comments/222202141193335/
	public function page_comments_last() {
		$h1    = __( 'Social Comments', 'vkapi' );
		$appID = get_option( 'vkapi_appid' );
		echo "
			<div class='wrap'>
			
				<h1><span class='dashicons-before dashicons-format-chat'>{$h1}</span></h1>
				
				<div id='vkapi_comments'>
					<script type='text/javascript' src='//vk.com/js/api/openapi.js' async></script>
					<script type='text/javascript'>
						window.vkAsyncInit = function () { 
							VK.init({ apiId: $appID });
							VK.Widgets.CommentsBrowse('vkapi_comments', { mini: 1})
						};
					</script>
				</div>
				
			</div>";
	}

	public function page_comments_settings() {
		?>

		<style>
			a:focus {
				box-shadow: none;
			}

			.card {
				max-width: 100%;
			}

			.darx-tab {
				display: none;
			}

			.darx-tab.active {
				display: block;
			}
		</style>

		<div class="wrap">

			<h1>Comments Settings</h1>

			<h2 class="nav-tab-wrapper wp-clearfix">
				<a data-tab="vk" href="" class="nav-tab">VKontakte</a>
				<a data-tab="fb" href="" class="nav-tab">Facebook</a>
				<a data-tab="base" href="" class="nav-tab"><?php _e( 'Base', 'vkapi' ); ?></a>
			</h2>

			<form action="options.php" method="post" novalidate="novalidate">

				<?php settings_fields( 'darx-comments' ); ?>

				<div class="darx-tab" id="tab-base">
					<div class="card">
						<?php do_settings_sections( 'darx-comments-settings-base' ); ?>
					</div>
				</div>

				<div class="darx-tab" id="tab-vk">
					<p>
						<?php printf(
							__(
								'If you dont have <b>Application ID</b> and <b>Secure key</b> : go this <a href="%s" target="_blank">link</a> and select <b>Web-site</b>. It\'s easy.',
								'vkapi'
							),
							'https://vk.com/editapp?act=create'
						); ?>

						<br/>

						<?php printf(
							__(
								'If don\'t remember: go this <a href="%s" target="_blank">link</a> and choose need application.',
								'vkapi'
							),
							'https://vk.com/apps?act=manage'
						); ?>

					</p>

					<div class="card">
						<?php do_settings_sections( 'darx-comments-settings-vk' ); ?>
					</div>

				</div>

				<div class="darx-tab" id="tab-fb">

					<p>
						<?php printf(
							__(
								'Facebook <b>App ID</b> : go this <a href="%s" target="_blank">link</a> and register your site(blog). It\'s easy.',
								'vkapi'
							),
							'https://developers.facebook.com/apps'
						); ?>
					</p>

					<div class="card">
						<?php do_settings_sections( 'darx-comments-settings-fb' ); ?>
					</div>
				</div>

				<?php submit_button(); ?>

			</form>

			<script>
				jQuery(function ($) {
					var $navs = $('.nav-tab');
					$navs.on('click', function (e) {
						e.preventDefault();

						var $this = $(this);
						var tab = $this.data('tab');
						window.location.hash = tab;

						$('.nav-tab').removeClass('nav-tab-active');
						$this.addClass('nav-tab-active');

						$('.darx-tab').removeClass('active');
						$('#tab-' + tab).addClass('active');

						return false;
					});

					if (window.location.hash) {
						var hash = window.location.toString().split('#')[1]; // coz firefox bug
						var $nav = $('a[data-tab="' + hash + '"]');
						if ($nav.length) $nav.triggerHandler('click');
					} else {
						$navs.first().triggerHandler('click');
					}

					$('#submit').on('mousedown', function () {
						var hash = window.location.toString().split('#')[1]; // coz firefox bug
						var $ref = $(this).closest('form').find('input[name="_wp_http_referer"]');
						$ref.val($ref.val() + '#' + hash);

					});
				});
			</script>

		</div>

		<?php
	}

	public function register_settings() {

		// sections base

		add_settings_section(
			'darx-comments-base', // id
			'', // title
			'__return_null', // callback
			'darx-comments-settings-base' // page
		);

		register_setting( 'darx-comments', 'vkapi_show_first' );
		add_settings_field(
			'vkapi_show_first', // id
			'', // title
			array( $this, 'render_settings_field' ), // callback
			'darx-comments-settings-base', // page
			'darx-comments-base', // section
			array(
				'label_for' => 'vkapi_show_first',
				'type'      => 'select',
				'values'    => array(
					'vk' => sprintf( __( 'Show first %s comments', 'vkapi' ), 'VKontakte' ),
					'fb' => sprintf( __( 'Show first %s comments', 'vkapi' ), 'The Facebook' ),
					'wp' => sprintf( __( 'Show first %s comments', 'vkapi' ), 'WordPress' ),
				),
				'descr'     => '',
			) // args
		);

		// sections vk

		add_settings_section(
			'darx-comments-api', // id
			'', // title
			'__return_null', // callback
			'darx-comments-settings-vk' // page
		);

		add_settings_section(
			'darx-comments-base', // id
			__( 'Base', 'vkapi' ), // title
			'__return_null', // callback
			'darx-comments-settings-vk' // page
		);

		add_settings_section(
			'darx-comments-media', // id
			__( 'Media' ), // title
			'__return_null', // callback
			'darx-comments-settings-vk' // page
		);

		// settings

		register_setting( 'darx-comments', 'vkapi_show_comm' );
		add_settings_field(
			'vkapi_show_comm', // id
			__( 'Enable', 'vkapi' ), // title
			array( $this, 'render_settings_field' ), // callback
			'darx-comments-settings-vk', // page
			'darx-comments-api', // section
			array(
				'label_for' => 'vkapi_show_comm',
				'type'      => 'checkbox',
				'descr'     => '',
			) // args
		);

		register_setting( 'darx-comments', 'vkapi_appid' );
		add_settings_field(
			'vkapi_appid', // id
			__( 'Application ID', 'vkapi' ), // title
			array( $this, 'render_settings_field' ), // callback
			'darx-comments-settings-vk', // page
			'darx-comments-api', // section
			array(
				'label_for' => 'vkapi_appid',
				'type'      => 'number',
				'descr'     => '
<style>
label[for=vkapi_appid].valid:after {
	display: block;
	content: "valid";
	color: #46b450;
	position: absolute;
}
label[for=vkapi_appid].invalid:after {
	display: block;
	content: "invalid";
	color: #a00;
	position: absolute;
}
</style>
<script>
jQuery(function($){
	var $input = $("#vkapi_appid");
	$input.on("change", function() {
		if ($input.val()) {
			$.getJSON("https://api.vk.com/method/apps.get?app_id=" + $input.val() + "&callback=?", function (r) {
				if (r.hasOwnProperty("response") && r.response.type === "site") {
					$("label[for=vkapi_appid]").removeClass("invalid").addClass("valid");
				} else {
					$("label[for=vkapi_appid]").removeClass("valid").addClass("invalid");
				}
			});
		}
	});
});
</script>',
			) // args
		);

		register_setting( 'darx-comments', 'vkapi_api_secret' );
		add_settings_field(
			'vkapi_api_secret', // id
			__( 'Secure key', 'vkapi' ), // title
			array( $this, 'render_settings_field' ), // callback
			'darx-comments-settings-vk', // page
			'darx-comments-api', // section
			array(
				'label_for' => 'vkapi_api_secret',
				'type'      => 'text',
				'descr'     => '',
			) // args
		);

		// base

		register_setting( 'darx-comments', 'vkapi_comm_height' );
		add_settings_field(
			'vkapi_comm_height', // id
			__( 'Height of widget', 'vkapi' ), // title
			array( $this, 'render_settings_field' ), // callback
			'darx-comments-settings-vk', // page
			'darx-comments-base', // section
			array(
				'label_for' => 'vkapi_comm_height',
				'type'      => 'number',
				'descr'     => '0=auto',
			) // args
		);

		register_setting( 'darx-comments', 'vkapi_comm_width' );
		add_settings_field(
			'vkapi_comm_width', // id
			__( 'Width of widget', 'vkapi' ), // title
			array( $this, 'render_settings_field' ), // callback
			'darx-comments-settings-vk', // page
			'darx-comments-base', // section
			array(
				'label_for' => 'vkapi_comm_width',
				'type'      => 'number',
				'descr'     => '0=auto',
			) // args
		);

		register_setting( 'darx-comments', 'vkapi_comm_limit' );
		add_settings_field(
			'vkapi_comm_limit', // id
			__( 'Number of comments', 'vkapi' ), // title
			array( $this, 'render_settings_field' ), // callback
			'darx-comments-settings-vk', // page
			'darx-comments-base', // section
			array(
				'label_for' => 'vkapi_comm_limit',
				'type'      => 'number',
				'descr'     => '5-100',
			) // args
		);

		register_setting( 'darx-comments', 'vkapi_comm_autoPublish' );
		add_settings_field(
			'vkapi_comm_autoPublish', // id
			__( 'AutoPublish to vk user wall', 'vkapi' ), // title
			array( $this, 'render_settings_field' ), // callback
			'darx-comments-settings-vk', // page
			'darx-comments-base', // section
			array(
				'label_for' => 'vkapi_comm_autoPublish',
				'type'      => 'checkbox',
				'descr'     => '',
			) // args
		);

		register_setting( 'darx-comments', 'vkapi_close_wp' );
		add_settings_field(
			'vkapi_close_wp', // id
			__( 'Hide WordPress comments', 'vkapi' ), // title
			array( $this, 'render_settings_field' ), // callback
			'darx-comments-settings-vk', // page
			'darx-comments-base', // section
			array(
				'label_for' => 'vkapi_close_wp',
				'type'      => 'checkbox',
				'descr'     => '',
			) // args
		);

		register_setting( 'darx-comments', 'vkapi_notice_admin' );
		add_settings_field(
			'vkapi_notice_admin', // id
			__( 'Notice by email about new comment', 'vkapi' ), // title
			array( $this, 'render_settings_field' ), // callback
			'darx-comments-settings-vk', // page
			'darx-comments-base', // section
			array(
				'label_for' => 'vkapi_notice_admin',
				'type'      => 'checkbox',
				'descr'     => '',
			) // args
		);

		register_setting( 'darx-comments', 'vkapi_comm_is_postid' );
		add_settings_field(
			'vkapi_comm_is_postid', // id
			__( 'Attach to post id', 'vkapi' ), // title
			array( $this, 'render_settings_field' ), // callback
			'darx-comments-settings-vk', // page
			'darx-comments-base', // section
			array(
				'label_for' => 'vkapi_comm_is_postid',
				'type'      => 'checkbox',
				'descr'     => 'Привязка комментариев к ID записи или адресу страницы. Это навечно!',
			) // args
		);

		register_setting( 'darx-comments', 'vkapi_comm_is_switcher' );
		add_settings_field(
			'vkapi_comm_is_switcher', // id
			__( 'Comments switcher', 'vkapi' ), // title
			array( $this, 'render_settings_field' ), // callback
			'darx-comments-settings-vk', // page
			'darx-comments-base', // section
			array(
				'label_for' => 'vkapi_comm_is_switcher',
				'type'      => 'checkbox',
				'descr'     => '',
			) // args
		);

		// media

		register_setting( 'darx-comments', 'vkapi_comm_graffiti' );
		add_settings_field(
			'vkapi_comm_graffiti', // id
			__( 'Graffiti', 'vkapi' ), // title
			array( $this, 'render_settings_field' ), // callback
			'darx-comments-settings-vk', // page
			'darx-comments-media', // section
			array(
				'label_for' => 'vkapi_comm_graffiti',
				'type'      => 'checkbox',
				'descr'     => '',
			) // args
		);

		register_setting( 'darx-comments', 'vkapi_comm_photo' );
		add_settings_field(
			'vkapi_comm_photo', // id
			__( 'Photo', 'vkapi' ), // title
			array( $this, 'render_settings_field' ), // callback
			'darx-comments-settings-vk', // page
			'darx-comments-media', // section
			array(
				'label_for' => 'vkapi_comm_photo',
				'type'      => 'checkbox',
				'descr'     => '',
			) // args
		);

		register_setting( 'darx-comments', 'vkapi_comm_audio' );
		add_settings_field(
			'vkapi_comm_audio', // id
			__( 'Audio', 'vkapi' ), // title
			array( $this, 'render_settings_field' ), // callback
			'darx-comments-settings-vk', // page
			'darx-comments-media', // section
			array(
				'label_for' => 'vkapi_comm_audio',
				'type'      => 'checkbox',
				'descr'     => '',
			) // args
		);

		register_setting( 'darx-comments', 'vkapi_comm_video' );
		add_settings_field(
			'vkapi_comm_video', // id
			__( 'Video', 'vkapi' ), // title
			array( $this, 'render_settings_field' ), // callback
			'darx-comments-settings-vk', // page
			'darx-comments-media', // section
			array(
				'label_for' => 'vkapi_comm_video',
				'type'      => 'checkbox',
				'descr'     => '',
			) // args
		);

		register_setting( 'darx-comments', 'vkapi_comm_link' );
		add_settings_field(
			'vkapi_comm_link', // id
			__( 'Link', 'vkapi' ), // title
			array( $this, 'render_settings_field' ), // callback
			'darx-comments-settings-vk', // page
			'darx-comments-media', // section
			array(
				'label_for' => 'vkapi_comm_link',
				'type'      => 'checkbox',
				'descr'     => '',
			) // args
		);

		// FB add section

		add_settings_section(
			'darx-comments-api', // id
			'', // title
			'__return_null', // callback
			'darx-comments-settings-fb' // page
		);

		register_setting( 'darx-comments', 'fbapi_show_comm' );
		add_settings_field(
			'fbapi_show_comm', // id
			__( 'Enable', 'vkapi' ), // title
			array( $this, 'render_settings_field' ), // callback
			'darx-comments-settings-fb', // page
			'darx-comments-api', // section
			array(
				'label_for' => 'fbapi_show_comm',
				'type'      => 'checkbox',
				'descr'     => '',
			) // args
		);

		register_setting( 'darx-comments', 'fbapi_appid' );
		add_settings_field(
			'fbapi_appid', // id
			__( 'Application ID', 'vkapi' ), // title
			array( $this, 'render_settings_field' ), // callback
			'darx-comments-settings-fb', // page
			'darx-comments-api', // section
			array(
				'label_for' => 'fbapi_appid',
				'type'      => 'number',
				'descr'     => '',
			) // args
		);
	}

	public function add_post_meta_box( $page ) {
		add_meta_box(
			'vkapi_meta_box_comments',
			'VKapi: ' . __( 'Social comments', 'vkapi' ),
			array( $this, 'render_post_meta_box_comments' ),
			$page,
			'advanced'
		);
	}

	public function render_post_meta_box_comments() {
		global $post;
		$option = get_post_meta( $post->ID, 'vkapi_comments', true );
		if ( $option === '' ) {
			$option = 1;
		}

		echo '<input type="radio" name="vkapi_comments" value="1"';
		checked( $option, 1 );
		echo '/>' . __( 'Enable', 'vkapi' );

		echo '<br />';

		echo '<input type="radio" name="vkapi_comments" value="0"';
		checked( $option, 0 );
		echo '/>' . __( 'Disable', 'vkapi' );
	}

	public function save_post( $post_id ) {
		if ( isset( $_REQUEST['vkapi_comments'] ) && $_REQUEST['vkapi_comments'] === '0' ) {
			update_post_meta( $post_id, 'vkapi_comments', $_REQUEST['vkapi_comments'] );
		}
	}

	public function wp_enqueue_scripts() {
		if ( get_option( 'vkapi_show_comm' ) ) {
			add_action( 'wp_footer', array( $this, 'js_async_vkapi' ), 1 );
		}

		if ( get_option( 'fbapi_show_comm' ) ) {
			if ( $id = get_option( 'fbapi_admin_id' ) ) {
				echo "<meta property='fb:admins' content='{$id}' />\n";
			}
			add_action( 'wp_footer', array( $this, 'js_async_fbapi' ), 1 );
		}
	}

	// todo: refactor
	public function ajax_comments() {
		switch ( $_POST['provider'] ) {
			case 'vk':
				switch ( $_POST['job'] ) {
					case 'add':
						$hash = md5( get_option( 'vkapi_api_secret' ) . $_POST['date'] . $_POST['num'] . $_POST['last_comment'] );

						if ( $hash !== $_POST['sign'] ) {
							exit( '-1' );
						}

						update_post_meta( $_POST['id'], 'vkapi_comm', $_POST['num'], false );

						$emails = array();
						$post   = get_post( $_POST['id'] );

						if ( get_user_meta( $post->post_author, 'vkapi_notice_comments', true ) === '1' ) {
							$emails[] = get_the_author_meta( 'email', $post->post_author );
						}

						if ( get_option( 'vkapi_notice_admin' ) === '1' ) {
							$emails[] = get_bloginfo( 'admin_email' );
						}

						if ( ! empty( $emails ) ) {
							$blog_url = home_url();
							$blog_url = str_replace( array( 'http://', 'https://' ), '', $blog_url );

							$notify_message = 'VKapi: ' . __( 'Page has just commented!', 'vkapi' ) . '<br />';
							$notify_message .= get_permalink( $_POST['id'] ) . '<br /><br />';
							$notify_message .= __( 'Comment: ', 'vkapi' ) . '<br />' . $_POST['last_comment'] . '<br /><br />';

							$subject = '[Social API] ' . __( 'Website:', 'vkapi' ) . ' "' . $blog_url . '"';

							add_filter( 'wp_mail_content_type', create_function( '', 'return "text/html";' ) );
							wp_mail( $emails, $subject, $notify_message );
						}

						exit( '0' );
					case 'remove':
						if ( time() - strtotime( $_POST['date'] ) > 86400 ) {
							exit( '-1' );
						}

						$hash = md5( get_option( 'vkapi_api_secret' ) . $_POST['date'] . $_POST['num'] . $_POST['last_comment'] );

						if ( $hash !== $_POST['sign'] ) {
							exit( '-1' );
						}

						update_post_meta( $_POST['id'], 'vkapi_comm', $_POST['num'], false );

						exit( '0' );
				}
				break;
			case 'fb':
				switch ( $_POST['job'] ) {
					case 'add':
						$url  = preg_replace( '/^https/', 'http', get_permalink( $_POST['id'] ) );
						$data = wp_remote_get( 'https://graph.facebook.com/?ids=' . $url );
						if ( is_wp_error( $data ) ) {
							exit( '-1' );
						}
						$resp = json_decode( $data['body'], true );
						foreach ( $resp as $key => $value ) {
							$num = $value['comments'];
						}
						if ( ! isset( $num ) ) {
							exit( '-1' );
						}

						update_post_meta( $_POST['id'], 'fbapi_comm', $num, false );

						$emails = array();
						$post   = get_post( $_POST['id'] );

						if ( get_user_meta( $post->post_author, 'vkapi_notice_comments', true ) === '1' ) {
							$emails[] = get_the_author_meta( 'email', $post->post_author );
						}

						if ( get_option( 'vkapi_notice_admin' ) === '1' ) {
							$emails[] = get_bloginfo( 'admin_email' );
						}

						if ( ! empty( $emails ) ) {
							$blog_url = home_url();
							$blog_url = str_replace( array( 'http://', 'https://' ), '', $blog_url );

							$notify_message = 'FBapi: ' . __( 'Page has just commented!', 'vkapi' ) . '<br />';
							$notify_message .= get_permalink( $_POST['id'] ) . '<br /><br />';

							$subject = '[Social API] ' . __( 'Website:', 'vkapi' ) . ' "' . $blog_url . '"';

							add_filter( 'wp_mail_content_type', create_function( '', 'return "text/html";' ) );
							wp_mail( $emails, $subject, $notify_message );
						}

						exit( '0' );
					case 'remove':
						$url  = preg_replace( '/^https/', 'http', get_permalink( $_POST['id'] ) );
						$data = wp_remote_get( 'https://graph.facebook.com/?ids=' . $url );
						if ( is_wp_error( $data ) ) {
							exit( '-1' );
						}
						$resp = json_decode( $data['body'], true );
						foreach ( $resp as $key => $value ) {
							$num = $value['comments'];
						}
						if ( ! isset( $num ) ) {
							exit( '-1' );
						}
						update_post_meta( $_POST['id'], 'fbapi_comm', $num, false );
						exit( '0' );
				}
				break;
		}

		exit( '-1' );
	}

	// todo: refactor
	public function js_async_vkapi() {
		if ( get_option( 'vkapi_appid' ) ):
			?>
			<script type="text/javascript">
				window.vkAsyncInit = function () {
					VK.Observer.subscribe('widgets.comments.new_comment', function (num, last_comment, date, sign) {
						var data = {
							action: 'darx.comments',
							provider: 'vk',
							job: 'add',
							id: document.getElementById("comments_post_id").value,
							num: num,
							last_comment: last_comment,
							date: date,
							sign: sign
						};
						darx.post('<?php echo admin_url( 'admin-ajax.php' ) ?>', data);
					});

					VK.Observer.subscribe('widgets.comments.delete_comment', function (num, last_comment, date, sign) {
						var data = {
							action: 'darx.comments',
							provider: 'vk',
							job: 'remove',
							id: document.getElementById("comments_post_id").value,
							num: num,
							last_comment: last_comment,
							date: date,
							sign: sign
						};
						darx.post('<?php echo admin_url( 'admin-ajax.php' ) ?>', data);
					});
				};
			</script>
		<?php endif;
		Darx_JS::add('vk', 'https://vk.com/js/api/openapi.js');
	}

	// todo: refactor
	public function js_async_fbapi() {
		if ( get_option( 'fbapi_appid' ) ):
			?>
			<style scoped="scoped">
				.fb-comments span, .fb-comments iframe {
					width: 100% !important;
				}
			</style>
			<script>
				window.fbAsyncInit = function () {
					FB.Event.subscribe('comment.create', function () {
						var data = {
							action: 'darx.comments',
							provider: 'fb',
							job: 'add',
							id: document.getElementById("comments_post_id").value
						};
						darx.post('<?php echo admin_url( 'admin-ajax.php' ) ?>', data);
					});

					FB.Event.subscribe('comment.remove', function () {
						var data = {
							action: 'darx.comments',
							provider: 'fb',
							job: 'remove',
							id: document.getElementById("comments_post_id").value
						};
						darx.post('<?php echo admin_url( 'admin-ajax.php' ) ?>', data);
					});
				};
			</script>
		<?php endif;
		$locale = get_locale();
		$option = get_option( 'fbapi_appid' );
		Darx_JS::add('fb', 'https://connect.facebook.net/' . $locale . '/all.js#xfbml=1&status=1&cookie=1&version=v2.6&appId=' . $option);
	}

	public function widgets_init() {
		register_widget( 'VKAPI_Community' );
		register_widget( 'VKAPI_Recommend' );
		register_widget( 'VKAPI_Comments' );
		if ( get_option( 'fbapi_appid' ) ) {
			register_widget( 'FBAPI_LikeBox' );
		}
		// coz most "tags clouds" plugin don`t support WP > 3
		register_widget( 'VKAPI_Cloud' );
	}

	public function wrap_comments_template( $file ) {
		global $comments_template_file;
		$comments_template_file = $file;
		return dirname( __FILE__ ) . '/../templates/wrap_comments_template.php';
	}

	public function add_tabs( $file ) {
		global $post;
		if ( is_singular() && get_post_meta( $post->ID, 'vkapi_comments', true ) !== '0' ) {
			$count = 0;
			// VK
			if ( get_option( 'vkapi_show_comm' ) ) {
				add_action( 'add_tabs_button_action', array( $this, 'add_tabs_button_vk' ), 5 );
				add_action( 'add_tabs_comment_action', array( $this, 'add_vk_comments' ) );
				++ $count;
			}
			// FB
			if ( get_option( 'fbapi_show_comm' ) ) {
				add_action( 'add_tabs_button_action', array( $this, 'add_tabs_button_fb' ), 5 );
				add_action( 'add_tabs_comment_action', array( $this, 'add_fb_comments' ) );
				++ $count;
			}
			// hook start buttons
			if ( ! get_option( 'vkapi_close_wp' ) ) {
				add_action( 'add_tabs_button_action', array( $this, 'add_tabs_button_wp' ), 5 );
				++ $count;
			}
			if ( $count > 1 ) {
				add_action( 'add_tabs_button_action', array( $this, 'add_tabs_button_start' ), 1 );
				add_action( 'add_tabs_button_action', create_function( '', 'echo \'</div>\';' ), 1024 );
				if ( get_option( 'vkapi_comm_is_switcher') ) {
					do_action( 'add_tabs_button_action' );
					echo '<script>
					darx.addEvent(document, "DOMContentLoaded", function() {
						var id;
						if (id = document.getElementById("vk-comments")) {
							id.style.transition = "max-height 0ms linear 0s";
							id.style.overflow = "hidden";
						}
						if (id = document.getElementById("fb-comments")) {
							id.style.transition = "max-height 0ms linear 0s";
							id.style.overflow = "hidden";
						}
						if (id = document.getElementById("wp-comments")) {
							id.style.transition = "max-height 0ms linear 0s";
							id.style.overflow = "hidden";
						}
					});
					 
					if (!requestAnimationFrame) {
						requestAnimationFrame = function(callback) {
							setTimeout(callback, 1000 / 75); 
						}
					}
					 
					function showVK() {
      					requestAnimationFrame(function() {
	                        var id;
							if (id = document.getElementById("vk-comments")) {
								id.style.maxHeight = "4096px";
								id.style.transitionDuration = "250ms";
							}
							if (id = document.getElementById("fb-comments")) {
								id.style.maxHeight = "0";
								id.style.transitionDuration = "150ms";
							}
							if (id = document.getElementById("wp-comments")) {
								id.style.maxHeight = "0";
								id.style.transitionDuration = "150ms";
							}
      					});
					}
					function showFB() {
						requestAnimationFrame(function() {
							var id;
							if (id = document.getElementById("vk-comments")) {
								id.style.maxHeight = "0";
								id.style.transitionDuration = "150ms";
							}
							if (id = document.getElementById("fb-comments")) {
								id.style.maxHeight = "4096px";
								id.style.transitionDuration = "250ms";
							}
							if (id = document.getElementById("wp-comments")) {
								id.style.maxHeight = "0";
								id.style.transitionDuration = "150ms";
							}
						});
					}
					function showWP() {
						requestAnimationFrame(function() {
							var id;
							if (id = document.getElementById("vk-comments")) {
								id.style.maxHeight = "0";
								id.style.transitionDuration = "150ms";
							}
							if (id = document.getElementById("fb-comments")) {
								id.style.maxHeight = "0";
								id.style.transitionDuration = "150ms";
							}
							if (id = document.getElementById("wp-comments")) {
								id.style.maxHeight = "4096px";
								id.style.transitionDuration = "250ms";
							}
						});
					}
					</script>';

					switch ( get_option( 'vkapi_show_first' ) ) {
						case 'vk':
							echo '<script type="text/javascript">darx.addEvent(document, "DOMContentLoaded", showVK);</script>';
							break;
						case 'fb':
							echo '<script type="text/javascript">darx.addEvent(document, "DOMContentLoaded", showFB);</script>';
							break;
						case 'wp':
						default:
							echo '<script type="text/javascript">darx.addEvent(document, "DOMContentLoaded", showWP);</script>';
							break;
					}
				}
			}
			do_action( 'add_tabs_comment_action' );
		}

		return $file;
	}

	public function add_tabs_button_vk() {
		$text = __( 'VKontakte', 'vkapi' );
		echo "
			<div>
			    <button style='white-space:nowrap' class='submit' onclick='showVK()'>
			        {$text} (<span id='vkapi_comm_vk_count'>X</span>)
			    </button>
			</div>";
	}

	public function add_tabs_button_fb() {
		$url  = preg_replace( '/^https/', 'http', get_permalink() );
		$text = __( 'Facebook', 'vkapi' );
		echo "
			<div>
			    <button style='white-space:nowrap' class='submit' onclick='showFB()'>
			        {$text} (<span class='fb-comments-count' data-href='{$url}'>X</span>)
			    </button>
			</div>";
	}

	public function add_tabs_button_wp() {
		global $post;
		$vkapi_comm = get_post_meta( $post->ID, 'vkapi_comm', true );
		$fbapi_comm = get_post_meta( $post->ID, 'fbapi_comm', true );
		$comm_wp    = get_comments_number() - $vkapi_comm - $fbapi_comm;
		$text       = __( 'Site', 'vkapi' );
		echo "<div>
			    <button style='white-space:nowrap'
			            class='submit'
			            onclick='showWP()'>
			        {$text} ({$comm_wp})
			    </button>
			</div>";
	}

	public function add_tabs_button_start() {
		$text = __( 'Comments:', 'vkapi' );
		echo "
			<style scoped='scoped'> 
				#vkapi_wrapper > div:not(:first-child) { margin-left: 10px; }
				@media (min-width: 768px) { #vkapi_wrapper > div { display: inline-block } }  
				@media (max-width: 767px) { #vkapi_wrapper > div:first-child { margin-left: 10px; } }  
			</style>
			<div id='vkapi_wrapper'
                 style='width:auto; margin:10px auto 20px 0; max-width:100%'>
                <div style='white-space:nowrap'><h3>{$text}</h3></div>";
	}

	// todo-now: add option "url or postid"
	public function add_vk_comments() {
		global $post;

		$attach = array();
		if ( get_option( 'vkapi_comm_graffiti' ) ) {
			$attach[] = 'graffiti';
		}
		if ( get_option( 'vkapi_comm_photo' ) ) {
			$attach[] = 'photo';
		}
		if ( get_option( 'vkapi_comm_audio' ) ) {
			$attach[] = 'audio';
		}
		if ( get_option( 'vkapi_comm_video' ) ) {
			$attach[] = 'video';
		}
		if ( get_option( 'vkapi_comm_link' ) ) {
			$attach[] = 'link';
		}
		if ( empty( $attach ) ) {
			$attach = 'false';
		} else {
			$attach = implode( ',', $attach );
		}

		if ( get_option( 'vkapi_comm_autoPublish' ) ) {
			$autoPublish = '1';
		} else {
			$autoPublish = '0';
		}

		$width       = get_option( 'vkapi_comm_width' );
		$width       = $width == 0 ? '100%' : $width . 'px';
		$height      = get_option( 'vkapi_comm_height' );
		$limit       = get_option( 'vkapi_comm_limit' );
		$url         = get_permalink();
		$vkapi_appid = get_option( 'vkapi_appid' );

		$is_postid = get_option( 'vkapi_comm_is_postid' );
		if ( $is_postid ) {
			$post_id = $post->ID;
		} else {
			$post_id = 0;
		}

		echo "
			<div id='vk-comments' style='max-width:{$width}'>
				<div id='vk-comments-widget'></div>
				<script type='text/javascript'>
					(function(){
						darx.addEvent(document, 'vk', function(){
		                    VK.Widgets.Comments(
		                        'vk-comments-widget', {
		                            width: 0,
		                            height: {$height},
		                            limit: {$limit},
		                            attach: '{$attach}',
		                            autoPublish: {$autoPublish},
		                            mini: 1,
		                            pageUrl: '{$url}'
		                        }, {$post_id});
						});
						var data = {
							v: 5.52,
							widget_api_id: {$vkapi_appid},
							page_id: {$post_id},
							url: '{$url}'
						};
						darx.getJSON('https://api.vk.com/method/widgets.getComments', data, function (r) {
							document.getElementById('vkapi_comm_vk_count').innerHTML = r.response.count;
						});
					})();
				</script>
			</div>";
	}

	public function add_fb_comments() {
		$width = get_option( 'vkapi_comm_width' );
		$width = $width == 0 ? '100%' : $width . 'px';
		$limit = get_option( 'vkapi_comm_limit' );
		$url   = preg_replace( '/^https/', 'http', get_permalink() );
		echo "
			<div id='fb-comments' style='width:100%'>
			<div style='background:white;width:100%;max-width:{$width}'
			     class='fb-comments'
			     data-href='{$url}'
			     data-num-posts='{$limit}'
			     data-colorscheme='light'></div></div>";
	}

	public function do_empty() {
		global $post;
		$vkapi_comm = get_post_meta( $post->ID, 'vkapi_comm', true );
		$fbapi_comm = get_post_meta( $post->ID, 'fbapi_comm', true );

		return $vkapi_comm + $fbapi_comm;
	}

	public function do_non_empty( $count, $post_id = null ) {
		if ( $post_id === null ) {
			global $post;
			$post_id = $post->ID;
		}
		$vkapi_comm = get_post_meta( $post_id, 'vkapi_comm', true );
		$fbapi_comm = get_post_meta( $post_id, 'fbapi_comm', true );

		return $count + $vkapi_comm + $fbapi_comm;
	}

	public function profile_render( $profile ) {
		if ( current_user_can( 'publish_posts' ) ) {
			$meta_value = get_user_meta( $profile->ID, 'vkapi_notice_comments', true );
			?>
			<table class="form-table">
				<tr>
					<th scope="row">
						<label for="vkapi_notice_comments">
							<?php _e( 'Email about new social comments', 'vkapi' ); ?>
						</label>
					</th>
					<td>
						<input
							type="checkbox"
							value="1"
							id="vkapi_notice_comments"
							name="vkapi_notice_comments"
							<?php checked( '1', $meta_value ) ?>
						/>
					</td>
				</tr>
			</table>
			<?php
		}
	}

	public function profile_save( $user_id ) {
		if ( current_user_can( 'edit_user', $user_id ) ) {
			update_user_meta( $user_id, 'vkapi_notice_comments', $_POST['vkapi_notice_comments'] );
		}
	}
}

new Darx_Comments();