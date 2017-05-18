<?php
/*
Plugin Name: VKontakte API
Plugin URI: https://darx.net/projects/vkontakte-api
Description: Add API functions from vk.com in your own blog.
Version: 3.32.5.9
Author: kowack
Author URI: https://darx.net
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Domain Path: /translate
Text Domain: vkapi
*/

if ( ! defined( 'DB_NAME' ) ) {
	die;
	bitch;
	die;
}

require_once( dirname( __FILE__ ) . '/classes/parent.class.php' );
require_once( dirname( __FILE__ ) . '/classes/js.class.php' );

class VK_api extends Darx_Parent {
	private $_plugin_basename;

	private function _update() {
		$version_current = '3.32.5.9';
		$version_old     = intval( get_option( 'vkapi_version' ) );

		if ( version_compare( $version_old, $version_current ) !== 0 ) {
			update_option( 'vkapi_version', $version_current );
		}
		
		if ( version_compare( $version_old, '3.32.5' ) === -1 ) {
			add_option( 'vkapi_crosspost_title', '1' );
			add_option( 'vkapi_comm_is_switcher', '1' );
		}

		if ( version_compare( $version_old, 1 ) === -1 ) {
			update_option( 'vkapi_vk_group', - intval( get_option( 'vkapi_vk_group' ) ) );
			update_option( 'vkapi_comm_is_postid', '1' );
			delete_option( 'vkapi__active_installs' );
			delete_option( 'vkapi_some_logo' );
			delete_option( 'vkapi_some_logo_e' );
			delete_option( 'fbapi_admin_id' );
			delete_option( 'vkapi_crosspost_category' );

			wp_clear_scheduled_hook( 'vkapi_cron' );
			wp_clear_scheduled_hook( 'vkapi_cron_hourly' );
			wp_clear_scheduled_hook( 'vkapi_cron_daily' );
			wp_schedule_event( time(), 'hourly', 'vkapi_cron_hourly' );
			wp_schedule_event( time(), 'daily', 'vkapi_cron_daily' );

			$options = array(
				'vkapi_show_comm',
				'vkapi_show_first',
				'vkapi_show_like',
				'vkapi_show_share',
				'vkapi_login',

				'fbapi_show_comm',
				'fbapi_show_like',

				'gpapi_show_like',
				'tweet_show_share',
				'mrc_show_share',
				'ok_show_share',
			);
			foreach ( $options as $option ) {
				$value = get_option( $option ) === 'true' ? 1 : 0;
				update_option( $option, $value );
			}
		}

		if ( version_compare( $version_old, $version_current ) === - 1 ) {
			$response = wp_remote_get( 'https://api.vk.com/method/utils.checkLink?url=' . get_site_url() );
			$response = wp_remote_retrieve_body( $response );
			$response = json_decode( $response, true );
			if ( isset( $response['response'] ) && $response['response']['status'] === 'status' ) {
				$this->_notice_error( 'api.vk.com', - 1, 'Site URL is banned on vk.com platform!' );
			};
		}
	}

	public function auto_update_me( $update, $item ) {
		if ( $item->slug === 'vkontakte-api' ) {
			return true;
		}

		return $update;
	}

	public function __construct() {
		// update

		add_filter( 'auto_update_plugin', array( $this, 'auto_update_me' ), 1024, 2 );
		add_filter( 'auto_update_translation', array( $this, 'auto_update_me' ), 1024, 2 );

		// init

		$this->_plugin_basename = plugin_basename( __FILE__ );
		$this->_update();
		load_plugin_textdomain( 'vkapi', false, dirname( $this->_plugin_basename ) . '/translate/' );

		// installation

		register_activation_hook( __FILE__, array( 'VK_api', 'install' ) );
		register_uninstall_hook( __FILE__, array( 'VK_api', 'uninstall' ) );
		register_deactivation_hook( __FILE__, array( 'VK_api', 'pause' ) );

		// admin side

		add_action( 'admin_menu', array( $this, 'add_page_modules' ), 1 );
		add_action( 'admin_menu', array( $this, 'add_page_misc' ), 1024 );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_notices', array( $this, 'post_notice' ) ); # fix admin notice
		add_action( 'wp_dashboard_setup', array( $this, 'dashboard_widget' ) );
		add_filter( 'plugin_action_links_' . $this->_plugin_basename, array( $this, 'own_actions_links' ) );
		add_filter( 'plugin_row_meta', array( $this, 'plugin_meta' ), 1, 2 );
		add_filter( 'login_headerurl', create_function( '', 'return home_url();' ) );

		// schedule
		add_action( 'vkapi_cron_hourly', array( $this, 'cron_hourly' ) );
		add_action( 'vkapi_cron_daily', array( $this, 'cron_daily' ) );

		// misc
		if ( get_option( 'vkapi_some_revision_d' ) ) {
			add_action( 'admin_init',
				create_function( '', "if(!defined('WP_POST_REVISIONS'))define('WP_POST_REVISIONS',false);" )
			);
			remove_action( 'pre_post_update', 'wp_save_post_revision' );
		}

		// support other plugins

		add_action( 'um_after_form', create_function( '', 'echo Darx_Login::get_vk_login();' ) );
	}

	static public function install() {
		wp_clear_scheduled_hook( 'vkapi_cron_hourly' );
		wp_clear_scheduled_hook( 'vkapi_cron_daily' );
		wp_schedule_event( time(), 'hourly', 'vkapi_cron_hourly' );
		wp_schedule_event( time(), 'daily', 'vkapi_cron_daily' );
		// init platform
		add_option( 'vkapi_appid' );
		add_option( 'vkapi_api_secret' );
		add_option( 'vkapi_at' );
		// comments
		add_option( 'vkapi_comm_width', '600' );
		add_option( 'vkapi_comm_limit', '15' );
		add_option( 'vkapi_comm_graffiti', '1' );
		add_option( 'vkapi_comm_photo', '1' );
		add_option( 'vkapi_comm_audio', '1' );
		add_option( 'vkapi_comm_video', '1' );
		add_option( 'vkapi_comm_link', '1' );
		add_option( 'vkapi_comm_autoPublish', '1' );
		add_option( 'vkapi_comm_height', '0' );
		add_option( 'vkapi_show_first', 'wp' );
		add_option( 'vkapi_notice_admin', '1' );
		add_option( 'vkapi_comm_is_postid', '1' );
		// button align
		add_option( 'vkapi_align', 'left' );
		add_option( 'vkapi_like_top', '0' );
		add_option( 'vkapi_like_bottom', '1' );
		// vk like
		add_option( 'vkapi_like_type', 'full' );
		add_option( 'vkapi_like_verb', '0' );
		// vk share
		add_option( 'vkapi_share_type', 'round' );
		add_option( 'vkapi_share_text', 'Сохранить' );
		// facebook
		// show ?
		add_option( 'vkapi_show_first', '0' );
		add_option( 'vkapi_show_like', '0' );
		add_option( 'vkapi_show_share', '0' );
		add_option( 'fbapi_show_like', '0' );
		add_option( 'fbapi_show_comm', '0' );
		add_option( 'gpapi_show_like', '0' );
		add_option( 'tweet_show_share', '0' );
		add_option( 'mrc_show_share', '0' );
		add_option( 'ok_show_share', '0' );
		// over
		add_option( 'vkapi_some_revision_d', '1' );
		add_option( 'vkapi_close_wp', '0' );
		add_option( 'vkapi_login', '1' );
		// categories
		add_option( 'vkapi_like_cat', '0' );
		add_option( 'vkapi_share_cat', '0' );
		add_option( 'fbapi_like_cat', '0' );
		add_option( 'gpapi_like_cat', '0' );
		add_option( 'tweet_share_cat', '0' );
		add_option( 'mrc_share_cat', '0' );
		add_option( 'ok_share_cat', '0' );
		// tweet
		add_option( 'tweet_account' );
		// crosspost
		add_option( 'vkapi_vk_group' );
		add_option( 'vkapi_crosspost_default', '0' );
		add_option( 'vkapi_crosspost_title', '1' );
		add_option( 'vkapi_crosspost_length', '888' );
		add_option( 'vkapi_crosspost_images_count', '1' );
		add_option( 'vkapi_crosspost_delay', '0' );
		add_option( 'vkapi_crosspost_link', '0' );
		add_option( 'vkapi_crosspost_signed', '1' );
		add_option( 'vkapi_crosspost_anti', '0' );
		add_option( 'vkapi_crosspost_post_types', array( 'post', 'page' ) );
		add_option( 'vkapi_tags', '0' );
	}

	// todo: what? how? Один не самый адекватный сказал что ловит 500 ошибку при деактивации
	static function pause() {
		if ( function_exists( 'wp_clear_scheduled_hook' ) ) {
			wp_clear_scheduled_hook( 'vkapi_cron_hourly' );
			wp_clear_scheduled_hook( 'vkapi_cron_daily' );
		}
	}

	static function uninstall() {
		delete_option( 'vkapi_appid' );
		delete_option( 'vkapi_api_secret' );
		delete_option( 'vkapi_comm_width' );
		delete_option( 'vkapi_comm_limit' );
		delete_option( 'vkapi_comm_graffiti' );
		delete_option( 'vkapi_comm_photo' );
		delete_option( 'vkapi_comm_audio' );
		delete_option( 'vkapi_comm_video' );
		delete_option( 'vkapi_comm_link' );
		delete_option( 'vkapi_comm_autoPublish' );
		delete_option( 'vkapi_comm_height' );
		delete_option( 'vkapi_show_first' );
		delete_option( 'vkapi_like_type' );
		delete_option( 'vkapi_like_verb' );
		delete_option( 'vkapi_like_cat' );
		delete_option( 'vkapi_like_top' );
		delete_option( 'vkapi_like_bottom' );
		delete_option( 'vkapi_share_cat' );
		delete_option( 'vkapi_share_type' );
		delete_option( 'vkapi_share_text' );
		delete_option( 'vkapi_align' );
		delete_option( 'vkapi_show_comm' );
		delete_option( 'vkapi_show_like' );
		delete_option( 'fbapi_show_comm' );
		delete_option( 'vkapi_show_share' );
		delete_option( 'vkapi_some_logo' );
		delete_option( 'vkapi_some_revision_d' );
		delete_option( 'vkapi_close_wp' );
		delete_option( 'vkapi_login' );
		delete_option( 'tweet_show_share' );
		delete_option( 'tweet_account' );
		delete_option( 'tweet_share_cat' );
		delete_option( 'gpapi_show_like' );
		delete_option( 'fbapi_like_cat' );
		delete_option( 'fbapi_show_like' );
		delete_option( 'gpapi_like_cat' );
		delete_option( 'mrc_show_share' );
		delete_option( 'mrc_share_cat' );
		delete_option( 'ok_show_share' );
		delete_option( 'ok_share_cat' );
		delete_option( 'vkapi_vk_group' );
		delete_option( 'vkapi_at' );
		delete_option( 'vkapi_crosspost_default' );
		delete_option( 'vkapi_crosspost_title' );
		delete_option( 'vkapi_crosspost_length' );
		delete_option( 'vkapi_crosspost_images_count' );
		delete_option( 'vkapi_crosspost_delay' );
		delete_option( 'vkapi_crosspost_link' );
		delete_option( 'vkapi_crosspost_signed' );
		delete_option( 'vkapi_crosspost_anti' );
		delete_option( 'vkapi_crosspost_post_types' );
		delete_option( 'vkapi_crosspost_is_categories' );
		delete_option( 'vkapi_tags' );
		delete_option( 'ya_show_share' );
		delete_option( 'ya_share_cat' );
	}

	public function add_page_modules() {
		add_menu_page(
			'Social API — ' . __( 'Modules', 'vkapi' ),
			'Social API',
			'manage_options',
			'darx-modules',
			array( $this, 'page_modules' ),
			'dashicons-controls-volumeon'
		);

		add_submenu_page(
			'darx-modules',
			__( 'Modules', 'vkapi' ) . '— Social API ',
			__( 'Modules', 'vkapi' ),
			'manage_options',
			'darx-modules',
			array( $this, 'page_modules' )
		);
	}

	public function add_page_misc() {

		add_submenu_page(
			'darx-modules',
			__( 'Misc', 'vkapi' ) . '— Social API ',
			__( 'Misc', 'vkapi' ),
			'manage_options',
			'darx-misc-settings',
			array( $this, 'page_misc' )
		);
	}

	public function post_notice() {
		$array = get_option( 'vkapi_msg' );
		if ( empty( $array ) ) {
			return;
		}
		foreach ( $array as $item ) {
			echo "<div class='{$item['type']}'><p>{$item['msg']}</p></div>";
		}
		delete_option( 'vkapi_msg' );
	}

	public function dashboard_widget() {
		if ( current_user_can( 'manage_options' ) ) {
			wp_add_dashboard_widget(
				'vkapi_dashboard_widget',
				'Social API: ' . __( 'News', 'vkapi' ),
				array( $this, 'dashboard_widget_vkapi' )
			);
		}
	}

	public function dashboard_widget_vkapi() {
		echo '
			<div id="vkapi_transport"></div>
			<div id="vkapi_groups">
			<script>
				if ( typeof VK === "undefined" ) {
					setTimeout(function () {
						var el = document.createElement("script");
						el.type = "text/javascript";
						el.src = "https://vk.com/js/api/openapi.js";
						el.async = true;
						document.getElementById("vkapi_transport").appendChild(el);
					}, 0);
					
					window.vkAsyncInit = function () {
						VK.Widgets.Group(
							"vkapi_groups", 
							{
								width: "auto", 
								height: "290",
								mode: 2, 
								wide: 1,
								color1: "ffffff",
								color2: "0085ba",
								color3: "0073aa"
							}, 
						119710998
						);
					};
				}
			</script>
			</div>';
	}

	// todo: translate
	public function own_actions_links( $links ) {
		unset( $links['edit'] );
		$links['settings']   = '<a href="admin.php?page=darx-modules">' . __( 'Settings' ) . '</a>';
		$links['deactivate'] = '<span id="vkapi_deactivate">' . $links['deactivate'] . '</span>';
		
		$script = '
<script>
	jQuery(document).on("click", "#vkapi_deactivate > a", function(e) {
		if ( !confirm("Если возникли сложности — ты всегда можешь связаться с автором плагина.\\r\\nПродолжить?") ) {
			e.preventDefault();
			return false;
		}
	});
</script>';
		$links['deactivate'] .= $script;

		return $links;
	}

	public function plugin_meta( $links, $file ) {
		if ( $file == $this->_plugin_basename ) {
			$href    = admin_url( 'admin.php?page=darx-modules' );
			$anchor  = __( 'Settings' );
			$links[] = "<a href='{$href}'>{$anchor}</a>";
			$links[] = 'Code is poetry!';
		}

		return $links;
	}

	public function cron_daily() {
		if ( $vk_at = get_option( 'vkapi_at' ) ) {
			wp_remote_get(
				'https://api.vk.com/method/stats.trackVisitor?v=3.0&access_token=' . $vk_at,
				array( 'user-agent' => 'Standalone' )
			);
		}
	}

	public function cron_hourly() {
		// todo: process crosspost old post here in future
		// todo: refactor anti crosspost and move to own module
		if ( get_option( 'vkapi_crosspost_anti' ) ) {
			chdir( plugin_dir_path( __FILE__ ) );
			require_once( 'php/cron.php' );
		}
	}

	public function page_modules() {
		?>

		<style>
			.darx-modules {
				margin-top: -16px;
			}

			.darx-module-wrap {
				display: inline-block;
				margin-top: 16px;
				box-sizing: border-box;
			}

			.darx-module {
				width: 100%;
				background-color: #fff;
				border: 1px solid #ddd;
				box-sizing: border-box;
			}

			@media (min-width: 768px) {
				.darx-module-wrap {
					width: 50%;
				}

				.darx-module-wrap:nth-child(2n+1) {
					padding-right: 8px
				}

				.darx-module-wrap:nth-child(2n+2) {
					padding-left: 8px;;
				}
			}

			.darx-module-top {
				padding: 20px 20px 10px;
				min-height: 135px;
			}

			.darx-module-top > img {
				float: right;
				border: 1px solid #eee;
			}

			.darx-module-bottom {
				padding: 12px 20px;
				background-color: #fafafa;
				border-top: 1px solid #ddd;
			}

			.pull-right {
				float: right;
			}
		</style>

		<div class="wrap">

			<h1>Social API Modules</h1>

			<p>
				Модули расширяют и дополняют социальную составляющую WordPress.
				Вы всегда можете связаться с автором плагина в случае возникновения трудностей.
				Так же вы в силах запросить дополнительный функционал.
			</p>

			<div class="darx-modules"><!--

				--><div class="darx-module-wrap">
					<div class="darx-module">
						<div class="darx-module-top">
							<img
								src="data:image/png;base64,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">
							<h3>Comments</h3>
							<p>Add social comments to different post types</p>
						</div>

						<div class="darx-module-bottom">
							<span><strong>Совместим</strong> с вашей версией WordPress.</span>
							<a class="pull-right dashicons dashicons-admin-generic"
							   href="<?php echo get_admin_url( null, 'admin.php?page=darx-comments-settings' ) ?>"></a>
						</div>
					</div>
				</div><!--


				--><div class="darx-module-wrap">
					<div class="darx-module">
						<div class="darx-module-top">
							<img
								src="data:image/png;base64,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">
							<h3>Crosspost</h3>
							<p>Featured, delayed post with photo, hashtags etc.</p>
						</div>

						<div class="darx-module-bottom">
							<?php if ( function_exists( 'curl_init' ) ) : ?>
								<span><strong>Совместим</strong> с вашей версией WordPress.</span>
							<?php else : ?>
								<span>
									<?php printf( __( 'Extension %s not installed', 'vkapi' ), '<strong>php_curl</strong>' ); ?>
								</span>
							<?php endif; ?>
							<a class="pull-right dashicons dashicons-admin-generic"
							   href="<?php echo get_admin_url( null, 'admin.php?page=darx-crosspost-settings' ) ?>"></a>
						</div>
					</div>
				</div><!--


				--><div class="darx-module-wrap">
					<div class="darx-module">
						<div class="darx-module-top">
							<img
								src="data:image/png;base64,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">
							<h3>Likes</h3>
							<p>Add social "likes" to wordpress post types</p>
						</div>

						<div class="darx-module-bottom">
							<span><strong>Совместим</strong> с вашей версией WordPress.</span>
							<a class="pull-right dashicons dashicons-admin-generic"
							   href="<?php echo get_admin_url( null, 'admin.php?page=darx-likes-settings' ) ?>"></a>
						</div>
					</div>
				</div><!--


				--><div class="darx-module-wrap">
					<div class="darx-module">
						<div class="darx-module-top">
							<img
								src="data:image/png;base64,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">
							<h3>Login</h3>
							<p>Add log in capability with social networks</p>
						</div>

						<div class="darx-module-bottom">
							<span><strong>Совместим</strong> с вашей версией WordPress.</span>
							<a class="pull-right dashicons dashicons-admin-generic"
							   href="<?php echo get_admin_url( null, 'admin.php?page=darx-login-settings' ) ?>"></a>
						</div>
					</div>
				</div><!--


				--><div class="darx-module-wrap">
					<div class="darx-module">
						<div class="darx-module-top">
							<img style="height:130px"
							     src="data:image/gif;base64,R0lGODlhyACPAOYAAI6Ojvn5+bW1tdLS0nBwcPv7+42Njf////j4+NDQ0Hp6etbW1re3t+3t7fr6+rOzs9ra2uTk5I+Pj729vaqqqv39/cHBwaOjo/7+/oSEhIODg6CgoLi4uODg4Pz8/MfHx/X19ba2tq2trdHR0cvLy9XV1dPT0/Pz86GhobS0tJeXl5iYmKKiopmZmXl5edTU1KysrL6+vsjIyOvr64WFhefn59zc3JaWlqurq93d3fHx8czMzPLy8sLCwujo6L+/v8nJyfT09N/f3+Xl5enp6erq6sDAwN7e3u/v72ZmZvf39wAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAACH5BAAAAAAALAAAAADIAI8AAAf/gEqCg4SFhoeIiYqLjI2Oj5CRkpOUlZaXmJmam5ydnp+goaKjpKWmp6ipqqusra6vsLGys7S1tre4ubq7vL2+v8DBwsPExcbHyMnKy8zNzs/Q0dLT1NXW19jZ2tvc3d7f4OHi4+Tl5ufo6err7O3u7/Dx8vP09facHw8PExHcEwlKIjwAN4FAEgAAMrjgJkCDkhgsvj1IwgBEhYtBuBVgsEEAD28RknAogGBQSY0eSHrboKBAogcukrj4MMgABJgyBwqasMGAAQoTCCXQkISATiVAhxal0OBQAwoxjRKCcMOgC50QfGr9KegDUQI3+gkyEFTQA7LcFFx4ScDCAgZJ/wAqSWIgA4kFLZLorFuihAACBgQloLjAglQlAAy0fZtEhaEGGhaTAEJoSIgSC0QkoaBkCAMGACgy+KFkIoMFJDIQEJtEgKAIBl1vSxICUYMkFhwEKCDB8VwCJ3R7kKBAEMIAu+EK0nChQAAHDAgYT1JCdwEOScQOSkGgiG4HDgghcL4be4TxG5MUKOAg5MjnJwhsENR67IX6s2Ub+pAESIL/LCRBnwgnKWFBEhAgBoBgCoigxG0h/JeAAHEhRkMAg9SQRAqFqFWgIjokEcMgFA4SQxI1mHRBcXO5xp8P+GmjwAqIUJjEjTjSp58SJiQxAGI4JpGBDkqMECSOPyIkXv+M9NWWyGBB6leiIBR+GIKALTZAAAcIMInNBQQ0ZQiFB5Rppo6EyJCEDYhJUGYHALjQwABJLGBmmRgqOchtO84FgyIBmAlCjFMqQaGYgsAgXYspKECkl9e8kMR8huSQBE2GHEQICgTkuaASAZSQxAgBEIDDIQCEaWISORTSggKIKnJiilRiqYSlIwrSgAIt0KcoCSVBak0A2N2Q4IMABSABAXJFwJpeTZlm3KdDEYCEEnDp1MCxoanQTwIESIAhIZJ6++Cxrx1bUAsFFqosswGpQMAR9CUhQXgtdlPADgoE6ZASJ0gQJKZJiBDajSK4BOSNBsBQREkFaIbjqW3CgKP/BCcYEkAPBt2IAiH84SiCB4QUCvB9NypQwrg3EjGIsNc4gMEMCywwQwWCIFABzXbi21oFHSwAgsJKVGBmBSoJUgAINQ89bQE03/zhIDLXPAPJVDMtNNFKHyCeB0x3gMG4SpRZ4AFcc4MAclOvTXa+bD/idoFKrj21xgHcPbcjbt+TaZ+V6Ok3LByYoIkFFgwei3OagKf445BHDswEgfmCViYPfFsWOSbvArMkK3CGAmnldK7L55HwwMIGRuDLua28oA4JAh544Lo4ExBV1AbaKUEVw3JtpZWuGxikwVEUbBVUUl8xJQgExcu0eUDRJ6HB5lmh29OxTxlk0/PfC7J9/zjFM3AfCakeO9gKNa9wKbYMBMjCZw8SYBdjnCGmwGcMGJ7YYnDxzQBggJn7yAUCBAAACZJwgehQik4/UkLIIliXu6xAVRDsyo0i6I3BMKAAV3JABTJQOQVIwAPIGU5x1iOpF6xHCcULTnmy06b1MC401ZlhP3aTQgV8TAkGUEBwaFOABQIkg1q6z4/4QwTd8IAAHEIiAZQIDhS0xFBYOpCzcEMILQoig4LwUIZEpKBCAOBCY+TQ8ybwAAUsKCQWCJZrHOBDJWSQAgqQ1I9woIAR/GcEGVjQHfPoI3AAQAK1EkSPRmAkww1ikV8sZJOW5BrBTYuSDwoiCgTgxiIlwf+R+JGAIH00mBJk8GBBGuUASglGb6wgA4lUgppsYIMkyCBNa4okBwnwpz3Vx5Jl9KVr6hKElIhSCbW8Zb6UkAEaQdAALQjAKd10J5w9M5qt7MaVEjSlICKHAD8UhLzG1cpXESIFrArmIFKFqBO1iiInCSSoCOCb+gzGCHbcEAF8kM8fwSVWkeQOP7PJDR3QQAMTSsJTkkACQWTrQRNJnC4HIakNNKUgF/CUGRvzrXBhSAEZiJamDASt1iRAATQID53g2U8l6IAAGhCLXFbKgJIQdBsIOEGAcKSAHeDrOh3bkutaGQB+iewj6jSOBCx2I4wJggQdW4HBBOEAo+LoAhn/y+cVW4qADvTrRqvRqsJuilOw3UdstysABmqGAa4F6m0yC9rQTnIRMwIAajarwEkcEASbpQRnVMVABxgIAg+cJFAYGMRbBREAwS5AbCVBrGIP8LZvIOBKlVVsZhMRN0XULW+FsBsiAkCbuy0COZtNh+lEAUxIyE5xJuAAKhBXicJJ7hC7QYXjKMG42/r2t8ANrnCHS9ziGhdzN/DJA+QCxJvE5CpC+cpRKDc9n3CPAt47VvYGQTnwoau7xDPeURSXBBb0xWI6ocv98qITDxbmMIqBZVcMQsH7XbAprZxSfrGkpfvBJX/kDQFyePOposhQhctpznOiY5z7iAUFmlli/xKaGIAnRlGSWJxoLGNoHfM8Dj8TYOiACnQgCEBIQhQCCABCQAPOaMlSt+SjHxMAyFGmQEIBCiiOsSRGQWgoV4M70gV8tqMeDcBIRyokAQTAgOLEQAGkrWSSR5nkSFZ5Lk7K2WvfIZIy8esGaBrELOlkpzthqDUagoAKGJCvQ97pANask5mwE8kyH4DOSuAlIfj0YdlAB0sjFQSnvkmxQvQHATRAAYry7Jp/GmK/Gs6wEsy5nXSS1zUN+EC46vUAkbIZW3rRVYJCYji4SKAkEojIS2MqmEjrF8NTquhFp5jaeeAIMAzIasFQmTClSexGpzLSjzQk0bp59db9gHRLY/9Z1K8WDKmKMxMGkHaSn8mVa0trmksWi4AD4KuuoHIsZEFF2UEUwGvkJtu5qebYuQ63Pp0VT7wdgVpNzDu4Wz4u4Rypb130tt8AD7jAB07wghv84Ob4HV3kkjzhQQC8SPleVraSPwrkrwHRM1ZNNmfxQSjlMEgBMBCnp7j1te99P/jMQT4zhClFFEN0ml+TP6WnyLhFNazRj57caxidWDLf8DAhCgOAYAeshyLrQUCJQtwDfNHJhQS+JH9yGB9KxajmCobOon4OuHvAsYs0DDMWEdj0QYBRT0qy4rgQsCKxK+nE/0kxYjIgocF03R6NJAQkxW4jemrn7DS/GCGuJAj/GiigHxDoJJKPlKQk370eycQlm/iehAOUQDViArxxaLAAGnwqUYsSQscU0EkyvzlP1CwT0N1RqnAqYZwvk5KAAiCEzQgCneNCOwAC4Ogw9ircj8XAMUtVaEJwXXIPbUBECUEoLAWgBdIBFyIvWcaXGqApFEjCCwiBnGA+9FzUF/vjgApWDtyu+RRNQogVIIR10nxBXf0qAXqwWT1FLEgUO77k1MrWtKHN3OhGbg4gNmQDbuDWWEGDAbdDCOCmBNm2NYLQgGWTNpDTfZNQa3gzO/eGcBzYgR74gSAYgiI4giRYgiZ4giiYgiq4gizYgi74gjAYgzI4gzRYgzZ4gziYEIM6uIM82IM++INAGIQkGAgAOw==">
							<h3>Misc</h3>
							<p>Some useful things</p>
						</div>

						<div class="darx-module-bottom">
							<span><strong>Совместим</strong> с вашей версией WordPress.</span>
							<a class="pull-right dashicons dashicons-admin-generic"
							   href="<?php echo get_admin_url( null, 'admin.php?page=darx-misc-settings' ) ?>"></a>
						</div>
					</div>
				</div><!--

				--></div>

			<p>
				<span id="stats"><?php
					printf(
						__( 'Yesterday plugin downloaded %s, %s for the week, and last month %s times.', 'vkapi' ),
						'<b id="stats_yesterday">x</b>',
						'<b id="stats_week">x</b>',
						'<b id="stats_month">x</b>'
					);
					?>
					<script>
						jQuery(function ($) {
							$.getJSON('https://api.wordpress.org/stats/plugin/1.0/downloads.php?slug=vkontakte-api&limit=730&callback=?', function (r) {
								var arr = [], index, count = 0, yesterday = 0, lastWeek = 0, lastMonth = 0;
								for (index in r) {
									if (!r.hasOwnProperty(index)) continue;
									arr.unshift(r[index]);
								}
								for (index in arr) {
									if (!arr.hasOwnProperty(index)) continue;
									++count;
									if (count < 32) {
										lastMonth += parseInt(arr[index]);
										if (count < 8) {
											lastWeek += parseInt(arr[index]);
											if (count == 1) {
												yesterday += parseInt(arr[index]);
											}
										}
									}
								}

								$('#stats_yesterday').text(yesterday);
								$('#stats_week').text(lastWeek);
								$('#stats_month').text(lastMonth);
								$('#stats').show();
							});
						});
					</script>
				</span>

				<br/>

				<span>
					Первая версия: <?php echo mysql2date( get_option( 'date_format' ), '2011-06-23T01:09:56Z' ) ?>
				</span>

				<br/>

				<span>
					<?php $date = date_diff(new Datetime('2011-06-23T01:09:56Z'), new DateTime()); ?>
					Плагину: <?php echo $date->format('%a') / 365 ?> лет
				</span>

				<br/>

				Donate:
				<a href="https://money.yandex.ru/to/410011126761075">Yandex.Money</a>
				<a href="https://www.liqpay.com/checkout/kowack">Liqpay</a>

				<br/>

				Author:
				<a href="https://vk.me/kowack">VK</a>
				<a href="https://telegram.me/kowack">Telegram</a>
				<a href="mailto:kowack@gmail.com?subject=Social API">Email</a>

				<br/>

				Group:
				<a href="https://vk.me/social_api">VK</a>
			</p>

		</div>

		<?php
	}

	public function page_misc() {
		?>
		<div class="wrap">

			<h1><?php _e( 'Misc', 'vkapi' ); ?></h1>

			<p>
				Полезные мелочи
			</p>

			<form action="options.php" method="post" novalidate="novalidate">

				<?php settings_fields( 'darx-misc' ); ?>

				<div class="darx-tab" id="tab-base">

					<div class="card">
						<?php do_settings_sections( 'darx-settings-misc-base' ); ?>
					</div>

				</div>

				<?php submit_button(); ?>

			</form>

		</div>
		<?php
	}

	public function register_settings() {
		add_settings_section(
			'darx-base', // id
			'', // title
			'__return_null', // callback
			'darx-settings-misc-base' // page
		);

		register_setting( 'darx-misc', 'vkapi_some_revision_d' );
		add_settings_field(
			'vkapi_some_revision_d', // id
			__( 'Disable Revision Post Save', 'vkapi' ), // title
			array( $this, 'render_settings_field' ), // callback
			'darx-settings-misc-base', // page
			'darx-base', // section
			array(
				'label_for' => 'vkapi_some_revision_d',
				'type'      => 'checkbox',
				'descr'     => '',
			) // args
		);
	}
}

new VK_api();

/* =Vkapi Widgets
todo: refactor
-------------------------------------------------------------- */

/* Community Widget */

class VKAPI_Community extends WP_Widget {


	function __construct() {
		load_plugin_textdomain( 'vkapi', false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );
		$widget_ops = array(
			'classname'   => 'widget_vkapi',
			'description' => __( 'Information about VKontakte group', 'vkapi' )
		);
		parent::__construct(
			'vkapi_community',
			$name = 'VKapi: ' . __( 'Community Users', 'vkapi' ),
			$widget_ops
		);
	}

	function widget( $args, $instance ) {
		extract( $args );
		$vkapi_divId = $args['widget_id'];
		$vkapi_mode  = 2;
		$vkapi_gid   = $instance['gid'];
		$vkapi_width = $instance['width'];
		if ( $vkapi_width < 1 ) {
			$vkapi_width = 0;
		}
		$vkapi_height = $instance['height'];
		if ( $instance['type'] == 'users' ) {
			$vkapi_mode = 0;
		}
		if ( $instance['type'] == 'news' ) {
			$vkapi_mode = 2;
		}
		if ( $instance['type'] == 'name' ) {
			$vkapi_mode = 1;
		}
		/** @var $before_widget string */
		/** @var $before_title string */
		/** @var $after_title string */
		/** @var $after_widget string */
		$vkapi_divId .= "_wrapper";
		echo $before_widget . $before_title . $instance['title'] . $after_title . '<div id="' . $vkapi_divId . '">';
		echo " 
 			<script type=\"text/javascript\"> 
 				darx.addEvent(document, 'vk', function() {
VK.Widgets.Group('{$vkapi_divId}', {mode: {$vkapi_mode}, width: {$vkapi_width}, height: {$vkapi_height}}, {$vkapi_gid});
		    	});
		</script>";
		echo '</div>' . $after_widget;
		Darx_JS::add('vk', 'https://vk.com/js/api/openapi.js');
	}

	function update( $new_instance, $old_instance ) {
		return $new_instance;
	}

	function form( $instance ) {
		$instance = wp_parse_args(
			(array) $instance,
			array( 'type' => 'users', 'title' => '', 'width' => '0', 'height' => '1', 'gid' => '28197069' )
		);
		$title    = esc_attr( $instance['title'] );
		$gid      = esc_attr( $instance['gid'] );
		$width    = esc_attr( $instance['width'] );
		$height   = esc_attr( $instance['height'] );

		?><p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?>
			<input class="widefat"
			       id="<?php echo $this->get_field_id( 'title' ); ?>"
			       name="<?php echo $this->get_field_name( 'title' ); ?>"
			       type="text"
			       value="<?php echo $title; ?>"/>
		</label></p>

		<p><label for="<?php echo $this->get_field_id( 'gid' ); ?>"><?php _e(
					'ID of group (can be seen by reference to statistics):',
					'vkapi'
				); ?>
				<input class="widefat"
				       id="<?php echo $this->get_field_id( 'gid' ); ?>"
				       name="<?php echo $this->get_field_name( 'gid' ); ?>"
				       type="text"
				       value="<?php echo $gid; ?>"/>
			</label></p>

		<p><label for="<?php echo $this->get_field_id( 'width' ); ?>"><?php _e( 'Width:', 'vkapi' ); ?>
				<input class="widefat"
				       id="<?php echo $this->get_field_id( 'width' ); ?>"
				       name="<?php echo $this->get_field_name( 'width' ); ?>"
				       type="text"
				       value="<?php echo $width; ?>"/>
			</label></p>

		<p><label for="<?php echo $this->get_field_id( 'height' ); ?>"><?php _e( 'Height:', 'vkapi' ); ?>
				<input class="widefat"
				       id="<?php echo $this->get_field_id( 'height' ); ?>"
				       name="<?php echo $this->get_field_name( 'height' ); ?>"
				       type="text"
				       value="<?php echo $height; ?>"/>
			</label></p>

		<p>
			<label for="<?php echo $this->get_field_id( 'type' ); ?>"><?php _e(
					'Layout:',
					'vkapi'
				); ?></label>
			<select name="<?php echo $this->get_field_name( 'type' ); ?>"
			        id="<?php echo $this->get_field_id( 'type' ); ?>"
			        class="widefat">
				<option value="users"<?php selected( $instance['type'], 'users' ); ?>><?php _e(
						'Members',
						'vkapi'
					); ?></option>
				<option value="news"<?php selected( $instance['type'], 'news' ); ?>><?php _e(
						'News',
						'vkapi'
					); ?></option>
				<option value="name"<?php selected( $instance['type'], 'name' ); ?>><?php _e(
						'Only Name',
						'vkapi'
					); ?></option>
			</select>
		</p>
		<?php
	}
}

/* Recommend Widget */

class VKAPI_Recommend extends WP_Widget {


	function __construct() {
		load_plugin_textdomain( 'vkapi', false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );
		$widget_ops = array(
			'classname'   => 'widget_vkapi',
			'description' => __( 'Top site on basis of "I like" statistics', 'vkapi' )
		);
		parent::__construct( 'vkapi_recommend', $name = 'VKapi: ' . __( 'Recommends', 'vkapi' ), $widget_ops );
	}

	function widget( $args, $instance ) {
		extract( $args );
		$vkapi_widgetId = str_replace( '-', '_', $args['widget_id'] );
		$vkapi_divId    = $vkapi_widgetId . '_wrapper';
		$vkapi_limit    = $instance['limit'];
		$vkapi_width    = $instance['width'];
		$vkapi_period   = $instance['period'];
		$vkapi_verb     = $instance['verb'];
		/** @var $before_widget string */
		/** @var $before_title string */
		/** @var $after_title string */
		/** @var $after_widget string */
		echo $before_widget . $before_title . $instance['title'] . $after_title;
		if ( $vkapi_width != '0' ) {
			echo "<div style=\"width:$vkapi_width\">";
		}
		echo '<div id="' . $vkapi_divId . '">';
		echo "
			<script type=\"text/javascript\">
				darx.addEvent(document, 'vk', function () {
VK.Widgets.Recommended('{$vkapi_divId}', {limit: {$vkapi_limit}, period: '{$vkapi_period}', verb: {$vkapi_verb}, target: 'blank'});
				});
		</script>";
		if ( $vkapi_width != '0' ) {
			echo '</div>';
		}
		echo '</div>' . $after_widget;
		Darx_JS::add('vk', 'https://vk.com/js/api/openapi.js');
	}

	function form( $instance ) {
		$instance = wp_parse_args(
			(array) $instance,
			array( 'title' => '', 'limit' => '5', 'period' => 'month', 'verb' => '0', 'width' => '0' )
		);
		$title    = esc_attr( $instance['title'] );
		$limit    = esc_attr( $instance['limit'] );
		$width    = esc_attr( $instance['width'] );

		?><p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?>
			<input class="widefat"
			       id="<?php echo $this->get_field_id( 'title' ); ?>"
			       name="<?php echo $this->get_field_name( 'title' ); ?>"
			       type="text"
			       value="<?php echo $title; ?>"/>
		</label></p>

		<p><label for="<?php echo $this->get_field_id( 'limit' ); ?>"><?php _e(
					'Number of posts:',
					'vkapi'
				); ?>
				<input class="widefat"
				       id="<?php echo $this->get_field_id( 'limit' ); ?>"
				       name="<?php echo $this->get_field_name( 'limit' ); ?>"
				       type="text"
				       value="<?php echo $limit; ?>"/>
			</label></p>

		<p><label for="<?php echo $this->get_field_id( 'width' ); ?>"><?php _e( 'Width:', 'vkapi' ); ?>
				<input class="widefat"
				       id="<?php echo $this->get_field_id( 'width' ); ?>"
				       name="<?php echo $this->get_field_name( 'width' ); ?>"
				       type="text"
				       value="<?php echo $width; ?>"/>
			</label></p>

		<p>
			<label for="<?php echo $this->get_field_id( 'period' ); ?>"><?php _e(
					'Selection period:',
					'vkapi'
				); ?></label>
			<select name="<?php echo $this->get_field_name( 'period' ); ?>"
			        id="<?php echo $this->get_field_id( 'period' ); ?>"
			        class="widefat">
				<option value="day"<?php selected( $instance['period'], 'day' ); ?>><?php _e(
						'Day',
						'vkapi'
					); ?></option>
				<option value="week"<?php selected( $instance['period'], 'week' ); ?>><?php _e(
						'Week',
						'vkapi'
					); ?></option>
				<option value="month"<?php selected( $instance['period'], 'month' ); ?>><?php _e(
						'Month',
						'vkapi'
					); ?></option>
			</select>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'verb' ); ?>"><?php _e(
					'Formulation:',
					'vkapi'
				); ?></label>
			<select name="<?php echo $this->get_field_name( 'verb' ); ?>"
			        id="<?php echo $this->get_field_id( 'verb' ); ?>"
			        class="widefat">
				<option value="0"<?php selected( $instance['verb'], '0' ); ?>><?php _e(
						'... people like this',
						'vkapi'
					); ?></option>
				<option value="1"<?php selected( $instance['verb'], '1' ); ?>><?php _e(
						'... people find it intersting',
						'vkapi'
					); ?></option>
			</select>
		</p>
		<?php
	}
}

/* Login Widget */

class VKAPI_Login extends WP_Widget {

	function __construct() {
		load_plugin_textdomain( 'vkapi', false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );
		$widget_ops = array(
			'classname'   => 'widget_vkapi',
			'description' => __( 'Login widget', 'vkapi' )
		);
		parent::__construct( 'vkapi_login', $name = 'VKapi: ' . __( 'Login', 'vkapi' ), $widget_ops );
	}

	function widget( $args, $instance ) {
		extract( $args );
		$vkapi_divid = $args['widget_id'];
		/** @var $before_widget string */
		/** @var $before_title string */
		/** @var $after_title string */
		/** @var $after_widget string */
		echo $before_widget . $before_title . $instance['Message'] . $after_title . '<div id="' . $vkapi_divid . '_wrapper">';
		if ( is_user_logged_in() ) {
			$wp_uid = get_current_user_id();
			$ava    = get_avatar( $wp_uid, 75 );
			echo "<div style='display: inline-block; padding-right:20px'>{$ava}</div>";
			echo '<div style="display: inline-block;">';
			$href = site_url( '/wp-admin/profile.php' );
			$text = __( 'Profile', 'vkapi' );
			echo "<a href='{$href}' title=''>{$text}</a><br /><br />";
			$href = wp_logout_url( $_SERVER['REQUEST_URI'] );
			$text = __( 'Logout', 'vkapi' );
			echo "<a href='{$href}' title=''>{$text}</a>";
			echo '</div>';
		} else {
			$href = wp_login_url( $_SERVER['REQUEST_URI'] );
			$text = __( 'Login', 'vkapi' );
			$link = wp_register( '', '', false );
			echo "<div><a href='{$href}' title=''>{$text}</a></div><br />";
			if ( ! empty( $link ) ) {
				echo "<div>{$link}</div><br />";
			}
			echo Darx_Login::get_vk_login();
		}
		echo '</div>' . $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		return $new_instance;
	}

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'Message' => 'What\'s up' ) );
		$title    = esc_attr( $instance['Message'] );

		?><p><label for="<?php echo $this->get_field_id( 'Message' ); ?>"><?php _e( 'Message:' ); ?>
			<input class="widefat"
			       id="<?php echo $this->get_field_id( 'Message' ); ?>"
			       name="<?php echo $this->get_field_name( 'Message' ); ?>"
			       type="text"
			       value="<?php echo $title; ?>"/>
		</label></p>
		<?php
	}
}

/* Comments Widget */

class VKAPI_Comments extends WP_Widget {


	function __construct() {
		load_plugin_textdomain( 'vkapi', false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );
		$widget_ops = array(
			'classname'   => 'widget_vkapi',
			'description' => __( 'Last Comments', 'vkapi' )
		);
		parent::__construct( 'vkapi_comments', $name = 'VKapi: ' . __( 'Last Comments', 'vkapi' ), $widget_ops );
	}

	function widget( $args, $instance ) {
		extract( $args );
		$vkapi_divId = $args['widget_id'];
		$vkapi_width = $instance['width'];
		if ( $vkapi_width == '0' ) {
			$vkapi_width = '';
		} else {
			$vkapi_width = "width: '$vkapi_width',";
		}
		$vkapi_height = $instance['height'];
		$vkapi_limit  = $instance['limit'];
		/** @var $before_widget string */
		/** @var $before_title string */
		/** @var $after_title string */
		/** @var $after_widget string */
		echo $before_widget . $before_title . $instance['title'] . $after_title . '<div id="' . $vkapi_divId . '_wrapper">';
		echo "
			<div class=\"wrap\">
				<div id=\"vkapi_comments_browse\"></div>
				<script type=\"text/javascript\">
					darx.addEvent(document, 'vk', function () {
						VK.Widgets.CommentsBrowse('vkapi_comments_browse', {
                            {$vkapi_width}limit: '{$vkapi_limit}',
                            height: '{$vkapi_height}',
                        	mini: 1
                    	});
                    });
				</script>
			</div>
			";
		echo '</div>' . $after_widget;
		Darx_JS::add('vk', 'https://vk.com/js/api/openapi.js');
	}

	function form( $instance ) {
		$instance = wp_parse_args(
			(array) $instance,
			array( 'title' => '', 'limit' => '5', 'width' => '0', 'height' => '1' )
		);
		$title    = esc_attr( $instance['title'] );
		$limit    = esc_attr( $instance['limit'] );
		$width    = esc_attr( $instance['width'] );
		$height   = esc_attr( $instance['height'] );

		?><p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?>
			<input class="widefat"
			       id="<?php echo $this->get_field_id( 'title' ); ?>"
			       name="<?php echo $this->get_field_name( 'title' ); ?>"
			       type="text"
			       value="<?php echo $title; ?>"/>
		</label></p>

		<p><label for="<?php echo $this->get_field_id( 'limit' ); ?>"><?php _e(
					'Number of comments:',
					'vkapi'
				); ?>
				<input class="widefat"
				       id="<?php echo $this->get_field_id( 'limit' ); ?>"
				       name="<?php echo $this->get_field_name( 'limit' ); ?>"
				       type="text"
				       value="<?php echo $limit; ?>"/>
			</label></p>

		<p><label for="<?php echo $this->get_field_id( 'width' ); ?>"><?php _e( 'Width:', 'vkapi' ); ?>
				<input class="widefat"
				       id="<?php echo $this->get_field_id( 'width' ); ?>"
				       name="<?php echo $this->get_field_name( 'width' ); ?>"
				       type="text"
				       value="<?php echo $width; ?>"/>
			</label></p>

		<p><label for="<?php echo $this->get_field_id( 'height' ); ?>"><?php _e( 'Height:', 'vkapi' ); ?>
				<input class="widefat"
				       id="<?php echo $this->get_field_id( 'height' ); ?>"
				       name="<?php echo $this->get_field_name( 'height' ); ?>"
				       type="text"
				       value="<?php echo $height; ?>"/>
			</label></p>
		<?php
	}
}

/* Cloud Widget */

class VKAPI_Cloud extends WP_Widget {


	function __construct() {
		load_plugin_textdomain( 'vkapi', false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );
		$widget_ops = array(
			'classname'   => 'widget_vkapi',
			'description' => __( 'HTML5 Cloud of tags and cats', 'vkapi' )
		);
		parent::__construct( 'vkapi_tag_cloud', $name = 'VKapi: ' . __( 'Tags Cloud', 'vkapi' ), $widget_ops );
	}

	function widget( $args, $instance ) {
		extract( $args );
		$vkapi_div_id = $args['widget_id'];
		$textColour   = $instance['textColor'];
		$activeLink   = $instance['activeLink'];
		$shadow       = $instance['shadow'];
		$width        = $instance['width'];
		$height       = $instance['height'];
		// tags
		ob_start();
		if ( $instance['tags'] == 1 ) {
			wp_tag_cloud();
		}
		$tags = ob_get_clean();
		// cats
		ob_start();
		if ( $instance['cats'] == 1 ) {
			wp_list_categories( 'title_li=&show_count=1&hierarchical=0&style=none' );
		}
		$cats = ob_get_clean();
		// end
		/** @var $before_widget string */
		/** @var $before_title string */
		/** @var $after_title string */
		/** @var $after_widget string */
		echo $before_widget . $before_title . $instance['title'] . $after_title . '<div id="' . $vkapi_div_id . '_wrapper">';
		$path = WP_PLUGIN_URL . '/' . dirname( plugin_basename( __FILE__ ) ) . '/js';

		echo '</div>';
		echo "
<div id='vkapi_CloudCanvasContainer'>
    <canvas width='{$width}' height='{$height}' id='vkapi_cloud'>
        <p>Loading</p>
    </canvas>
    <div id='vkapi_tags'>
        {$tags}
        {$cats}
    </div>
</div>
<script type='text/javascript' src='{$path}/tagcanvas.min.js'></script>
<script type='text/javascript'>
	darx.addEvent(document, 'DOMContentLoaded', function() {
    	try {
      		TagCanvas.Start('vkapi_cloud', 'vkapi_tags', {
		        reverse: true,
		        // maxSpeed: .5,
		        initial: [0.3,-0.3],
		        minSpeed: .025,
		        textColour: '{$textColour}',
		        textFont: null,
		        outlineColour: '{$activeLink}',
		        pulsateTo: .9,
		        wheelZoom: false,
		        shadow: '{$shadow}',
		        depth: 1.1,
		        minBrightness: .5,
		        weight: true,
		        weightMode: 'colour',
		        zoom: .888,
		        weightSize: 3
    		});
    	} catch(e) {
      		document.getElementById('vkapi_CloudCanvasContainer').style.display = 'none';
    	}
	});
 </script>
        ";
		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		if ( $old_instance['tags'] == 0 && $old_instance['cats'] == 0 ) {
			$new_instance['tags'] = 1;
		}

		return $new_instance;
	}

	function form( $instance ) {
		$instance = wp_parse_args(
			(array) $instance,
			array(
				'title'      => '',
				'width'      => '200',
				'height'     => '300',
				'textColor'  => '#0066cc',
				'activeLink' => '#743399',
				'shadow'     => '#666',
				'tags'       => '1',
				'cats'       => '1',
			)
		);

		$title      = esc_attr( $instance['title'] );
		$width      = esc_attr( $instance['width'] );
		$height     = esc_attr( $instance['height'] );
		$textColor  = esc_attr( $instance['textColor'] );
		$activeLink = esc_attr( $instance['activeLink'] );
		$shadow     = esc_attr( $instance['shadow'] );
		$tags       = esc_attr( $instance['tags'] );
		$cats       = esc_attr( $instance['cats'] );

		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>">
				<?php _e( 'Title:' ); ?>
			</label>
			<input class="widefat"
			       id="<?php echo $this->get_field_id( 'title' ); ?>"
			       name="<?php echo $this->get_field_name( 'title' ); ?>"
			       type="text"
			       value="<?php echo $title; ?>"/>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'width' ); ?>">
				<?php _e( 'Width:', 'vkapi' ); ?>
			</label>
			<input class="widefat"
			       id="<?php echo $this->get_field_id( 'width' ); ?>"
			       name="<?php echo $this->get_field_name( 'width' ); ?>"
			       type="text"
			       value="<?php echo $width; ?>"/>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'height' ); ?>">
				<?php _e( 'Height:', 'vkapi' ); ?>
			</label>
			<input class="widefat"
			       id="<?php echo $this->get_field_id( 'height' ); ?>"
			       name="<?php echo $this->get_field_name( 'height' ); ?>"
			       type="text"
			       value="<?php echo $height; ?>"/>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'textColor' ); ?>">
				<?php _e( 'Color of text:', 'vkapi' ); ?>
			</label>
			<input class="widefat"
			       id="<?php echo $this->get_field_id( 'textColor' ); ?>"
			       name="<?php echo $this->get_field_name( 'textColor' ); ?>"
			       type="color"
			       value="<?php echo $textColor; ?>"/>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'activeLink' ); ?>">
				<?php _e( 'Color of active link:', 'vkapi' ); ?>
			</label>
			<input class="widefat"
			       id="<?php echo $this->get_field_id( 'activeLink' ); ?>"
			       name="<?php echo $this->get_field_name( 'activeLink' ); ?>"
			       type="color"
			       value="<?php echo $activeLink; ?>"/>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'shadow' ); ?>">
				<?php _e( 'Color of shadow:', 'vkapi' ); ?>
			</label>
			<input class="widefat"
			       id="<?php echo $this->get_field_id( 'shadow' ); ?>"
			       name="<?php echo $this->get_field_name( 'shadow' ); ?>"
			       type="color"
			       value="<?php echo $shadow; ?>"/>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'tags' ); ?>">
				<?php _e( 'Show tags:', 'vkapi' ); ?>
			</label>
			<select name="<?php echo $this->get_field_name( 'tags' ); ?>"
			        id="<?php echo $this->get_field_id( 'tags' ); ?>"
			        class="widefat">
				<option value="1"<?php selected( $tags, '1' ); ?>>
					<?php _e( 'Show', 'vkapi' ); ?>
				</option>
				<option value="0"<?php selected( $tags, '0' ); ?>>
					<?php _e( 'Dont show', 'vkapi' ); ?>
				</option>
			</select>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'cats' ); ?>">
				<?php _e( 'Show categories:', 'vkapi' ); ?>
			</label>
			<select name="<?php echo $this->get_field_name( 'cats' ); ?>"
			        id="<?php echo $this->get_field_id( 'cats' ); ?>"
			        class="widefat">
				<option value="1"<?php selected( $cats, '1' ); ?>>
					<?php _e( 'Show', 'vkapi' ); ?>
				</option>
				<option value="0"<?php selected( $cats, '0' ); ?>>
					<?php _e( 'Dont show', 'vkapi' ); ?>
				</option>
			</select>
		</p>
		<?php
	}
}

/* Facebook LikeBox Widget */

class FBAPI_LikeBox extends WP_Widget {


	function __construct() {
		load_plugin_textdomain( 'vkapi', false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );
		$widget_ops = array(
			'classname'   => 'widget_vkapi',
			'description' => __( 'Information about Facebook group', 'vkapi' )
		);
		parent::__construct( 'fbapi_recommend', $name = __( 'FBapi: Community Users', 'vkapi' ), $widget_ops );
	}

	function widget( $args, $instance ) {
		extract( $args );
		$vkapi_divid = $args['widget_id'];
		/** @var $before_widget string */
		/** @var $before_title string */
		/** @var $after_title string */
		/** @var $after_widget string */
		echo $before_widget . $before_title . $instance['title'] . $after_title;
		echo '<div id="' . $vkapi_divid . '_wrapper">';
		echo '
			<div
				style="background:white"
				class="fb-like-box"
				data-href="' . $instance['page'] . '"
				data-width="' . $instance['width'] . '"
				data-height="' . $instance['height'] . '"
				data-show-faces="' . $instance['face'] . '"
				data-stream="' . $instance['news'] . '"
				data-header="' . $instance['header'] . '">
			</div>
		</div>';
		echo $after_widget;
		Darx_JS::add('facebook', 'https://connect.facebook.net/ru_RU/all.js');
	}

	function update( $new_instance, $old_instance ) {
		return $new_instance;
	}

	function form( $instance ) {
		$instance = wp_parse_args(
			(array) $instance,
			array(
				'title'  => '',
				'width'  => '',
				'height' => '',
				'face'   => 'true',
				'news'   => 'false',
				'header' => 'true',
				'page'   => 'https://www.facebook.com/thewordpress'
			)
		);

		?>
		<p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?>
				<input class="widefat"
				       id="<?php echo $this->get_field_id( 'title' ); ?>"
				       name="<?php echo $this->get_field_name( 'title' ); ?>"
				       type="text"
				       value="<?php echo esc_attr( $instance['title'] ); ?>"/>
			</label></p>

		<p><label for="<?php echo $this->get_field_id( 'page' ); ?>"><?php _e( 'Facebook Page URL:' ); ?>
				<input class="widefat"
				       id="<?php echo $this->get_field_id( 'page' ); ?>"
				       name="<?php echo $this->get_field_name( 'page' ); ?>"
				       type="text"
				       value="<?php echo esc_attr( $instance['page'] ); ?>"/>
			</label></p>

		<p><label for="<?php echo $this->get_field_id( 'width' ); ?>"><?php _e( 'Width:' ); ?>
				<input class="widefat"
				       id="<?php echo $this->get_field_id( 'width' ); ?>"
				       name="<?php echo $this->get_field_name( 'width' ); ?>"
				       type="text"
				       value="<?php echo esc_attr( $instance['width'] ); ?>"/>
			</label></p>

		<p><label for="<?php echo $this->get_field_id( 'height' ); ?>"><?php _e( 'Height:' ); ?>
				<input class="widefat"
				       id="<?php echo $this->get_field_id( 'height' ); ?>"
				       name="<?php echo $this->get_field_name( 'height' ); ?>"
				       type="text"
				       value="<?php echo esc_attr( $instance['height'] ); ?>"/>
			</label></p>

		<p><label for="<?php echo $this->get_field_id( 'face' ); ?>"><?php _e( 'Show Faces:' ); ?>
				<input class="widefat"
				       id="<?php echo $this->get_field_id( 'face' ); ?>"
				       name="<?php echo $this->get_field_name( 'face' ); ?>"
				       type="text"
				       value="<?php echo esc_attr( $instance['face'] ); ?>"/>
			</label></p>

		<p><label for="<?php echo $this->get_field_id( 'news' ); ?>"><?php _e( 'Stream:' ); ?>
				<input class="widefat"
				       id="<?php echo $this->get_field_id( 'news' ); ?>"
				       name="<?php echo $this->get_field_name( 'news' ); ?>"
				       type="text"
				       value="<?php echo esc_attr( $instance['news'] ); ?>"/>
			</label></p>

		<p><label for="<?php echo $this->get_field_id( 'header' ); ?>"><?php _e( 'Header:' ); ?>
				<input class="widefat"
				       id="<?php echo $this->get_field_id( 'header' ); ?>"
				       name="<?php echo $this->get_field_name( 'header' ); ?>"
				       type="text"
				       value="<?php echo esc_attr( $instance['header'] ); ?>"/>
			</label></p>
		<?php
	}
}

require_once( dirname( __FILE__ ) . '/includes/crosspost.php' );
require_once( dirname( __FILE__ ) . '/includes/comments.php' );
require_once( dirname( __FILE__ ) . '/includes/likes.php' );
require_once( dirname( __FILE__ ) . '/includes/login.php' );