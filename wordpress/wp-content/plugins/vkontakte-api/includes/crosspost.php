<?php

if ( ! defined( 'DB_NAME' ) ) {
	die;
	bitch;
	die;
}

// todo: wall.post -- check if user has permission to post in group
// todo: wall.edit -- check if user can edit post in group, maybe time expired

class Darx_Crosspost extends Darx_Parent {

	public function __construct() {
		// add sub-page
		add_action( 'admin_menu', array( $this, 'add_page' ), 1 );
		// register settings
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		// if enabled
		if ( get_option( 'vkapi_at' ) && get_option( 'vkapi_vk_group' ) ) {
			// render post box
			add_action( 'post_submitbox_misc_actions', array( $this, 'add_post_submit' ), 1024 );
		}
		// init crosspost
		add_filter( 'transition_post_status', array( $this, 'transition_post_status' ), 1, 3 );
		// scheduled post
		add_action( 'scheduled_vk_crosspost', array( $this, 'vk_crosspost' ) );
		// dashboard widget
		// todo: add vk group stats
		// add_action( 'wp_dashboard_setup', array( $this, 'dashboard_widget' ) );
	}

	public function add_page() {
		add_submenu_page(
			'darx-modules',
			'Crosspost Settings — Social API',
			'Crosspost Settings',
			'manage_options',
			'darx-crosspost-settings',
			array( $this, 'page_crosspost_settings' )
		);
	}

	public function page_crosspost_settings() {
		?>

		<div class="wrap">

			<h1>Crosspost Settings</h1>

			<form action="options.php" method="post" novalidate="novalidate">

				<?php settings_fields( 'darx-crosspost' ); ?>

				<div class="card" style="max-width:100%">
					<?php do_settings_sections( 'darx-crosspost-settings' ); ?>
				</div>

				<?php submit_button(); ?>

			</form>

		</div>

		<?php
	}

	public function register_settings() {

		// sections

		add_settings_section(
			'darx-crosspost-vkontakte', // id
			'VK.com', // title
			'__return_null', // callback
			'darx-crosspost-settings' // page
		);

		// settings

		register_setting( 'darx-crosspost', 'vkapi_at' );
		add_settings_field(
			'vkapi_at', // id
			__( 'Access Token', 'vkapi' ), // title
			array( $this, 'render_settings_field' ), // callback
			'darx-crosspost-settings', // page
			'darx-crosspost-vkontakte', // section
			array(
				'label_for' => 'vkapi_at',
				'type'      => 'text',
				'descr'     => '
Для генерации Ключа Доступа перейдите по ссылке и
скопируйте содержимое адресной строки в поле выше (после подтверждения).<br/>
Ключ хранится у вас на сервере и только вы имеете к нему доступ.<br/>
Будут запрошены права:<br/>
<span class="dashicons dashicons-yes"></span> groups, wall — для возможности публикации записей на стене группы<br/>
<span class="dashicons dashicons-yes"></span> photos — для возможности загрузки фото<br/>
<span class="dashicons dashicons-yes"></span> offline — для возможности публикации в фоне<br/>
<strong>Никому не передавайте его, он содержит права на публикацию!</strong><br/>
<a target="_blank" href="data:text/html,<meta http-equiv=\'refresh\'; content=\'0; url=https://oauth.vk.com/authorize?client_id=2742215&scope=groups,wall,photos,offline&redirect_uri=blank.html&display=page&response_type=token\';>">Ссылка</a>.
<style>
label[for=vkapi_at].valid:after {
	display: block;
	content: "valid";
	color: #46b450;
	position: absolute;
}
label[for=vkapi_at].invalid:after {
	display: block;
	content: "invalid";
	color: #a00;
	position: absolute;
}
</style>
<script>
jQuery(function($){
	var $input = $("#vkapi_at");
	$input.on("change", function() { 
		if ($input.val().substring(0, 4) == "http") {
			var search = $input.val().split("#")[1];
			var parts = search.split("&");
			var $_GET = {};
			for (var i = 0; i < parts.length; i++) {
				var temp = parts[i].split("=");
				$_GET[decodeURIComponent(temp[0])] = decodeURIComponent(temp[1]);
			}
			$input.val($_GET["access_token"]);
		}
		$.getJSON("https://api.vk.com/method/account.getAppPermissions?access_token=" + $input.val() + "&callback=?", function (r) {
			// groups 262144 + wall 8192 + photos 4 + offline 65536 = 335876
			if ( (parseInt(r.response) & 335876) === 335876) {
				$("label[for=vkapi_at]").removeClass("invalid").addClass("valid");
			} else {
				$("label[for=vkapi_at]").removeClass("valid").addClass("invalid");
			}
		});
	}).triggerHandler("change");
});
</script>',
			) // args
		);

		register_setting( 'darx-crosspost', 'vkapi_vk_group' );
		add_settings_field(
			'vkapi_vk_group', // id
			__( 'Group ID', 'vkapi' ), // title
			array( $this, 'render_settings_field' ), // callback
			'darx-crosspost-settings', // page
			'darx-crosspost-vkontakte', // section
			array(
				'label_for' => 'vkapi_vk_group',
				'type'      => 'text',
				'descr'     => '
Скопируйте ссылку на группу (паблик) в поле выше. Скрипт автоматически определит:<br/>
<span class="dashicons dashicons-arrow-right"></span> положительный ID для персональной страницы<br/>
<span class="dashicons dashicons-arrow-right"></span> отрицательный ID для группы
<script>
jQuery(function($){
	var $input = $("#vkapi_vk_group");
	$input.on("change", function() {
		if (isNaN($input.val())) {
			var parts = $input.val().split("/");
			var screen_name = parts[parts.length-1];
			$.getJSON("https://api.vk.com/method/utils.resolveScreenName?screen_name=" + screen_name + "&callback=?", function (r) {
				if (r.response.hasOwnProperty("type")) {
					if (r.response.type === "user") {
						$input.val(r.response.object_id);	
					} else if (r.response.type === "group") {
						$input.val(-r.response.object_id);
					} else {
						alert("wrong type: " + r.response.type);
					}
				} else {
					alert("group not found");
				}
			});
		}
	});
});
</script>',
			) // args
		);

		register_setting( 'darx-crosspost', 'vkapi_crosspost_default' );
		add_settings_field(
			'vkapi_crosspost_default', // id
			__( 'Enable by default', 'vkapi' ), // title
			array( $this, 'render_settings_field' ), // callback
			'darx-crosspost-settings', // page
			'darx-crosspost-vkontakte', // section
			array(
				'label_for' => 'vkapi_crosspost_default',
				'type'      => 'checkbox',
				'descr'     => '',
			) // args
		);

		register_setting( 'darx-crosspost', 'vkapi_crosspost_post_types' );
		add_settings_field(
			'vkapi_crosspost_post_types', // id
			__( 'Post types', 'vkapi' ), // title
			array( $this, 'render_settings_field' ), // callback
			'darx-crosspost-settings', // page
			'darx-crosspost-vkontakte', // section
			array(
				'label_for' => 'vkapi_crosspost_post_types',
				'type'      => 'post_types',
				'descr'     => '',
			) // args
		);

		register_setting( 'darx-crosspost', 'vkapi_crosspost_title' );
		add_settings_field(
			'vkapi_crosspost_title', // id
			__( 'Title' ), // title
			array( $this, 'render_settings_field' ), // callback
			'darx-crosspost-settings', // page
			'darx-crosspost-vkontakte', // section
			array(
				'label_for' => 'vkapi_crosspost_title',
				'type'      => 'checkbox',
				'descr'     => '',
			) // args
		);

		register_setting( 'darx-crosspost', 'vkapi_crosspost_length' );
		add_settings_field(
			'vkapi_crosspost_length', // id
			__( 'Text length', 'vkapi' ), // title
			array( $this, 'render_settings_field' ), // callback
			'darx-crosspost-settings', // page
			'darx-crosspost-vkontakte', // section
			array(
				'label_for' => 'vkapi_crosspost_length',
				'type'      => 'number',
				'descr'     => __( '(0=unlimited, -1=Don\'t send text)', 'vkapi' ),
			) // args
		);

		register_setting( 'darx-crosspost', 'vkapi_crosspost_images_count' );
		add_settings_field(
			'vkapi_crosspost_images_count', // id
			__( 'Images count', 'vkapi' ), // title
			array( $this, 'render_settings_field' ), // callback
			'darx-crosspost-settings', // page
			'darx-crosspost-vkontakte', // section
			array(
				'label_for' => 'vkapi_crosspost_images_count',
				'type'      => 'number',
				'descr'     => '',
			) // args
		);

		register_setting( 'darx-crosspost', 'vkapi_crosspost_delay' );
		add_settings_field(
			'vkapi_crosspost_delay', // id
			__( 'Publication delay (in minutes)', 'vkapi' ), // title
			array( $this, 'render_settings_field' ), // callback
			'darx-crosspost-settings', // page
			'darx-crosspost-vkontakte', // section
			array(
				'label_for' => 'vkapi_crosspost_delay',
				'type'      => 'number',
				'descr'     => '',
			) // args
		);

		register_setting( 'darx-crosspost', 'vkapi_tags' );
		add_settings_field(
			'vkapi_tags', // id
			__( 'Convert tags to hashtags', 'vkapi' ), // title
			array( $this, 'render_settings_field' ), // callback
			'darx-crosspost-settings', // page
			'darx-crosspost-vkontakte', // section
			array(
				'label_for' => 'vkapi_tags',
				'type'      => 'checkbox',
				'descr'     => '',
			) // args
		);

		register_setting( 'darx-crosspost', 'vkapi_crosspost_is_categories' );
		add_settings_field(
			'vkapi_crosspost_is_categories', // id
			__( 'Convert categories to hashtags', 'vkapi' ), // title
			array( $this, 'render_settings_field' ), // callback
			'darx-crosspost-settings', // page
			'darx-crosspost-vkontakte', // section
			array(
				'label_for' => 'vkapi_crosspost_is_categories',
				'type'      => 'checkbox',
				'descr'     => '',
			) // args
		);

		register_setting( 'darx-crosspost', 'vkapi_crosspost_link' );
		add_settings_field(
			'vkapi_crosspost_link', // id
			__( 'Show link', 'vkapi' ), // title
			array( $this, 'render_settings_field' ), // callback
			'darx-crosspost-settings', // page
			'darx-crosspost-vkontakte', // section
			array(
				'label_for' => 'vkapi_crosspost_link',
				'type'      => 'checkbox',
				'descr'     => '',
			) // args
		);

		register_setting( 'darx-crosspost', 'vkapi_crosspost_signed' );
		add_settings_field(
			'vkapi_crosspost_signed', // id
			__( 'Signed by author', 'vkapi' ), // title
			array( $this, 'render_settings_field' ), // callback
			'darx-crosspost-settings', // page
			'darx-crosspost-vkontakte', // section
			array(
				'label_for' => 'vkapi_crosspost_signed',
				'type'      => 'checkbox',
				'descr'     => '',
			) // args
		);
	}

	/**
	 * todo: refactor
	 *
	 * @param WP_Post $post
	 */
	public function add_post_submit( $post ) {
		// param $post added only in 4.4.0
		if ( ! $post ) {
			global $post;
		}

		// check post type
		if ( ! ($post instanceof WP_Post) || $post->post_type === '') {
			echo '<div class="misc-pub-section">Тип записи отсутствует</div>';

			return;
		} elseif ( ! in_array( $post->post_type, (array) get_option( 'vkapi_crosspost_post_types', true ) ) ) {
			echo '<div class="misc-pub-section">Для этого типа записи кросспост не активирован</div>';

			return;
		}

		if ( ! $post->ID ) {
			echo 'test1';
			$option_crosspost_enabled      = get_option( 'vkapi_crosspost_default' );
			$option_crosspost_length       = get_option( 'vkapi_crosspost_length' );
			$option_crosspost_images_count = get_option( 'vkapi_crosspost_images_count' );
		} else {
			$option_crosspost_enabled      = get_post_meta( $post->ID, 'vkapi_crosspost_enabled', true);
			$option_crosspost_length       = get_post_meta( $post->ID, 'vkapi_crosspost_length', true);
			$option_crosspost_images_count = get_post_meta( $post->ID, 'vkapi_crosspost_images_count', true);
		}

		?>
		<div class="misc-pub-section">

		<label>
			<input type="hidden" value="0" name="vkapi_crosspost_submit"/>
			<input type="checkbox"
			       value="1"
			       name="vkapi_crosspost_submit"
				<?php checked( $option_crosspost_enabled, 1) ?>
			/>
			<?php _e( 'CrossPost to VK.com Wall', 'vkapi' ); ?>
		</label>

		<br/>

		<label>
			<input type="text"
			       name="vkapi_crosspost_length"
			       style="width: 50px;"
			       value="<?php echo $option_crosspost_length; ?>"
			/>
			<?php _e( 'Text length', 'vkapi' ); ?>
		</label>

		<br/>

		<label>
			<input type="number" min="0" max="10"
			       name="vkapi_crosspost_images_count"
			       style="width: 50px;"
			       value="<?php echo $option_crosspost_images_count; ?>"
			/>
			<?php _e( 'Images count', 'vkapi' ); ?>
		</label>

		</div><?php
	}

	/**
	 * @param WP_Post|null $post
	 */
	private function save_post( WP_Post $post = null ) {
		// todo: remove this in 2017
		if ( !$post) {
			global $post;
		}

		// check post type

		if ( ! in_array( $post->post_type, (array) get_option( 'vkapi_crosspost_post_types' ), true ) ) {
			return;
		}

		// crosspost enabled

		$option = isset( $_REQUEST['vkapi_crosspost_submit'] )
			? $_REQUEST['vkapi_crosspost_submit']
			: get_option( 'vkapi_crosspost_default' );

		update_post_meta( $post->ID, 'vkapi_crosspost_enabled', $option );

		// crosspost text length

		$option = isset( $_REQUEST['vkapi_crosspost_length'] )
			? $_REQUEST['vkapi_crosspost_length']
			: get_option( 'vkapi_crosspost_length' );

		update_post_meta( $post->ID, 'vkapi_crosspost_length', $option );

		// crosspost image count

		$option = isset( $_REQUEST['vkapi_crosspost_images_count'] )
			? $_REQUEST['vkapi_crosspost_images_count']
			: get_option( 'vkapi_crosspost_images_count' );

		update_post_meta( $post->ID, 'vkapi_crosspost_images_count', $option );
	}

	/**
	 * After WP update "transition_post_status" called before "save_post" action
	 *
	 * @param $new_status
	 * @param null $old_status
	 * @param WP_Post|null $post
	 */
	public function transition_post_status( $new_status, $old_status = null, WP_Post $post = null ) {
		$this->save_post($post);

		if ( $new_status !== 'publish' ) {
			return;
		}

		// todo: poll or option
		// check password protect
		// if ( ! empty( $post->post_password ) ) {
		// 	return;
		// }

		// init crosspost

		$enabled = get_post_meta( $post->ID, 'vkapi_crosspost_enabled', true );

		if ( $enabled ) {
			$delay = intval( get_option( 'vkapi_crosspost_delay' ) );
			if ( $delay && $old_status !== 'publish' ) {
				wp_schedule_single_event( time() + $delay * 60, 'scheduled_vk_crosspost', array( $post->ID ) );
				if ( $old_status !== 'future' ) {
					$this->_notice_success( 'CrossPost: ' . __( 'Added to scheduler!', 'vkapi' ) );
				}
			} else {
				$this->vk_crosspost( $post->ID );
			}
		}
	}

	public function vk_crosspost( $post_id ) {
		// prevent abort

		set_time_limit( 0 );
		ignore_user_abort( true );

		// process attachments

		$attachments = array();

		// process photos

		$vkapi_crosspost_images_count = get_post_meta( $post_id, 'vkapi_crosspost_images_count', true );

		if ( $vkapi_crosspost_images_count > 1 || ( $vkapi_crosspost_images_count == 1 && ! has_post_thumbnail( $post_id ) ) ) {
			$post = get_post( $post_id );
			$text = do_shortcode( $post->post_content );

			$images = $this->_get_post_images_full( $text, $vkapi_crosspost_images_count );

			$upload_dir = wp_upload_dir();
			$upload_dir = $upload_dir['basedir'] . DIRECTORY_SEPARATOR;

			foreach ( $images as $image ) {

				$image_name  = explode( '/', $image );
				$image_name  = array_pop( $image_name );
				$upload_path = $upload_dir . $image_name;

				// download from web

				$fp = fopen( $upload_path, 'w+b' );
				if ( $fp === false ) {
					$this->_notice_error( 'fopen', - 1, 'Cant create tmp file ' . $upload_path );
					break;
				}

				$ch = curl_init( $image );
				if ( $ch === false ) {
					$this->_notice_error( 'php_curl', - 1, 'Cant create cURL handle' );
					break;
				}

				curl_setopt( $ch, CURLOPT_TIMEOUT, 25 );
				curl_setopt( $ch, CURLOPT_FILE, $fp );
				curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );

				// upload to vk.com

				if ( curl_exec( $ch ) === false ) {
					$this->_notice_error_curl( $ch );
				} else {
					rewind( $fp );
					$temp = $this->_vk_api_uploadPhoto( get_option( 'vkapi_vk_group' ), $upload_path );
					if ( $temp !== false ) {
						$attachments[] = $temp;
					}
				}

				curl_close( $ch );
				fclose( $fp );
				unlink( $upload_path );
			}

			if ( empty( $attachments ) ) {
				$vkapi_crosspost_images_count = 1;
			}
		}

		if ( $vkapi_crosspost_images_count == 1 ) {
			// need thumbnail? no problem!
			$file_id = get_post_thumbnail_id( $post_id );
			if ( $file_id !== false ) {
				// get absolute path
				$file_path = get_attached_file( $file_id );
				if ( $file_path !== false ) {
					$temp = $this->_vk_api_uploadPhoto( get_option( 'vkapi_vk_group' ), $file_path );
					if ( $temp !== false ) {
						$attachments[] = $temp;
					}
				}
			}
		}

		// link

		if ( get_option( 'vkapi_crosspost_link' ) ) {
			$attachments[] = get_permalink( $post_id );
		}

		// text and tags

		$vkapi_crosspost_length = get_post_meta( $post_id, 'vkapi_crosspost_length', true );
		$text                   = '';

		if ( $vkapi_crosspost_length >= 0 ) {
			$post = get_post( $post_id );
			$text = do_shortcode( $post->post_content );
			$text = $this->_html2text( $text );

			if ( $vkapi_crosspost_length > 0 ) {
				$text_len = mb_strlen( $text );
				$text     = mb_substr( $text, 0, $vkapi_crosspost_length );

				if ( mb_strlen( $text ) != $text_len ) {
					$text .= '…';
				}
			}

			if ( get_option( 'vkapi_crosspost_title' ) ) {
				$text = $post->post_title . "\n\n" . $text;
			}
		}

		// hashtags

		$hashtags = array();

		if ( get_option( 'vkapi_crosspost_is_categories' ) ) {
			$cats = wp_get_post_categories( $post_id, array( 'fields' => 'names' ) );
			$hashtags = array_merge($hashtags, $cats);
		}

		if ( get_option( 'vkapi_tags' ) ) {
			$tags = wp_get_post_tags( $post_id, array( 'fields' => 'names' ) );
			$hashtags = array_merge($hashtags, $tags);
		}

		if ( count( $hashtags ) !== 0 ) {
			$hashtags = array_unique($hashtags);
			// to hell slowpokes
			if ( version_compare( phpversion(), '5.3.10') !== -1 ) {
				$hashtags = preg_replace( '/\W/u', '_', $hashtags);
			}
			$text .= "\n\n#" . implode( ' #', $hashtags );
		}

		$text = trim( $text );

		// process wall.post

		$params                 = array();
		$params['v']            = '3.0';
		$params['from_group']   = 1;
		$params['access_token'] = get_option( 'vkapi_at' );
		$params['signed']       = get_option( 'vkapi_crosspost_signed' );
		$params['owner_id']     = get_option( 'vkapi_vk_group' );
		$params['message']      = $text;
		if ( count( $attachments ) ) {
			$params['attachments'] = implode( ',', $attachments );
		}

		// mini-test

		if ( ! isset( $params['attachments'] ) && mb_strlen( $params['message'] ) === 0 ) {
			$this->_notice_error( 'crosspost', - 1, 'Ни текста ни медиа-приложений' );

			return;
		}

		// post new or edit

		$temp = get_post_meta( $post_id, 'vkapi_crossposted', true );
		if ( empty( $temp ) ) {
			$vkapi_method = 'wall.post';
		} else {
			$params['post_id'] = $temp;
			$vkapi_method      = 'wall.edit';
		}

		// publish post

		if ( $response = $this->_vk_call( $vkapi_method, $params, true ) ) {
			if ( isset( $params['post_id'] ) ) {
				$vk_post_id = $params['post_id'];
			} else {
				$vk_post_id = $response['response']['post_id'];
			}

			if ( $params['owner_id'] > 0 ) {
				$page = 'id' . ( - $params['owner_id'] );
			} else {
				$page = 'club' . abs( $params['owner_id'] );
			}

			$post_link = "https://vk.com/{$page}?w=wall{$params['owner_id']}_{$vk_post_id}%2Fall";
			$post_href = "<a href='{$post_link}' target='_blank'>" . __( 'Link' ) . "</a>";

			$this->_notice_success( 'CrossPost: Success ! ' . $post_href );

			update_post_meta( $post_id, 'vkapi_crossposted', $vk_post_id );
		}
	}

	/**
	 * @param integer $vk_group
	 * @param string $image_path
	 *
	 * @return bool
	 */
	private function _vk_api_uploadPhoto( $vk_group, $image_path ) {
		$at = get_option( 'vkapi_at' );

		// get wall upload server

		$params                 = array();
		$params['access_token'] = $at;
		$params['uid']          = $vk_group;
		$params['v']            = '3.0';
		$response               = $this->_vk_call( 'photos.getWallUploadServer', $params );
		if ( $response === false ) {
			return false;
		}

		// upload photo to server

		$params          = array();
		$params['photo'] = '@' . $image_path;
		$result          = $this->_request( $response['response']['upload_url'], $params, true );
		if ( $result === false ) {
			return false;
		}

		$response = json_decode( $result, true );
		if ( isset( $response['error'] ) ) {
			$this->_notice_error_vk( $response['error'] );

			return false;
		}

		// save photo

		if ( $response['photo'] === '[]' ) {
			$this->_notice_error_vk( array(
				'error_code' => - 1,
				'error_msg'  => 'Security Breach2: ВКонтакте не понравился файл. Сообщи разработчику об ошибке. ' . $image_path,
			) );

			return false;
		}

		$params                 = array();
		$params['access_token'] = $at;
		$params['server']       = $response['server'];
		$params['photo']        = $response['photo'];
		$params['hash']         = $response['hash'];
		$params['v']            = '3.0';
		$response               = $this->_vk_call( 'photos.saveWallPhoto', $params );
		if ( $response === false ) {
			return false;
		}

		// Return Photo ID
		return $response['response'][0]['id'];
	}

	private function _get_post_images_full( $html, $count = 5 ) {
		// get all images in img tag
		$images = $this->_get_images_from_html( $html, $count );

		// get wp upload dir params
		$wp_upload_dir = wp_upload_dir();

		foreach ( $images as $index => $image ) {
			// check relative path
			if ( $image[0] === '/' && $image[1] !== '/' ) {
				$image = get_home_url() . $image;
			}

			// check if image in our wp installation
			if ( strncmp( $image, $wp_upload_dir['baseurl'], strlen( $wp_upload_dir['baseurl'] ) ) !== 0 ) {
				continue;
			}

			// remove suffix like "-200x300"
			$image_full = preg_replace( '/-[\d]+x[\d]+(?=\.(jpg|jpeg|png|gif)$)/ui', '', $image );

			// check if file really exists
			$image_local = str_replace( $wp_upload_dir['baseurl'], $wp_upload_dir['basedir'], $image_full );
			if ( file_exists( $image_local ) ) {
				$images[ $index ] = $image_full;
			}
		}

		return $images;
	}

	/**
	 * Convert HTML to text
	 *
	 * @param string $html
	 *
	 * @return string
	 */
	private function _html2text( $html ) {
		// new line for special html tags
		$tags = array(
			'#<h[123456][^>]*>#si',
			'#<table[^>]*>#si',
			'#<tr[^>]*>#si',
			'#<li[^>]*>#si',
			'#<br[^>]*>#si',
			'#<div[^>]*>#si',
		);
		$html = preg_replace( $tags, "\n", $html );

		// double new line for paragraph
		$html = preg_replace( array( '#<p[^>]*>#si', '#</p>#i' ), "\n\n", $html );

		// style table cells
		$html = preg_replace( '#</t(d|h)>\s*<t(d|h)[^>]*>#si', ' - ', $html );

		// remove invisible tags with content and strip other tags
		$tags = array(
			'#<style.+</style>#si',
			'#<script.+</script>#si',
			'#<noscript.+</noscript>#si',
			'#<[^>]+>#s',
		);
		$html = preg_replace( $tags, '', $html );

		// trim whitespaces
		$html = trim( $html );

		return $html;
	}
}

new Darx_Crosspost();