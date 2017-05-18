<?php

if ( ! defined( 'DB_NAME' ) ) {
	die;
	bitch;
	die;
}

// todo: update widget

class Darx_Login extends Darx_Parent {

	static public $login_url_vk = 'social-api/login/vk';
	static public $login_url_vk_full = '';

	public function __construct() {
		// urls
		self::$login_url_vk_full = site_url() . '/' . self::$login_url_vk;
		// add sub-page
		add_action( 'admin_menu', array( $this, 'add_page' ), 1 );
		// register settings
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		if ( get_option( 'vkapi_login' ) && get_option( 'vkapi_appid' ) && get_option( 'vkapi_api_secret' ) ) {
			// add api page
			add_action( 'parse_request', array( $this, 'parse_request' ) );
			// widgets
			add_action( 'widgets_init', array( $this, 'widgets_init' ) );
			// profile render
			add_action( 'profile_personal_options', array( $this, 'profile_render' ) );
			// profile ajax
			add_action( 'wp_ajax_vkapi_profile', array( $this, 'profile_process_ajax' ) );
			// login form render
			add_action( 'login_form', array( $this, 'add_login_form' ), 1 );
			add_action( 'register_form', array( $this, 'add_login_form' ), 1 );
			// admin bar
			add_action( 'admin_bar_menu', array( $this, 'user_links' ) );
			// avatar
			add_filter( 'get_avatar', array( $this, 'get_avatar' ), 5, 1024 );
		}
	}

	public function add_page() {
		add_submenu_page(
			'darx-modules',
			'Login Settings — Social API',
			'Login Settings',
			'manage_options',
			'darx-login-settings',
			array( $this, 'page_login_settings' )
		);
	}

	public function page_login_settings() {
		?>

		<div class="wrap">

			<h1>Login Settings</h1>

			<p>
				<?php printf(
					__(
						'If you dont have <b>Application ID</b> and <b>Secure key</b> : go this <a href="%s" target="_blank">link</a> and select <b>Web-site</b>. It\'s easy.', 'vkapi' ),
					'https://vk.com/editapp?act=create'
				); ?>

				<br/>

				<?php printf(
					__(
						'If don\'t remember: go this <a href="%s" target="_blank">link</a> and choose need application.', 'vkapi' ),
					'https://vk.com/apps?act=manage'
				); ?>

			</p>

			<form action="options.php" method="post" novalidate="novalidate">

				<?php settings_fields( 'darx-login' ); ?>

				<div class="card" style="max-width:100%">
					<?php do_settings_sections( 'darx-login-settings' ); ?>
				</div>

				<?php submit_button(); ?>

			</form>

		</div>

		<?php
	}

	public function register_settings() {
		// sections

		add_settings_section(
			'darx-login-vk', // id
			'VK.com', // title
			'__return_null', // callback
			'darx-login-settings' // page
		);

		// vk

		register_setting( 'darx-login', 'vkapi_appid' );
		add_settings_field(
			'vkapi_appid', // id
			__( 'Application ID', 'vkapi' ), // title
			array( $this, 'render_settings_field' ), // callback
			'darx-login-settings', // page
			'darx-login-vk', // section
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
	}).triggerHandler("change");
});
</script>',
			) // args
		);

		register_setting( 'darx-login', 'vkapi_api_secret' );
		add_settings_field(
			'vkapi_api_secret', // id
			__( 'Secure key', 'vkapi' ), // title
			array( $this, 'render_settings_field' ), // callback
			'darx-login-settings', // page
			'darx-login-vk', // section
			array(
				'label_for' => 'vkapi_api_secret',
				'type'      => 'text',
				'descr'     => '',
			) // args
		);

		register_setting( 'darx-login', 'vkapi_login' );
		add_settings_field(
			'vkapi_login', // id
			'', // title
			array( $this, 'render_settings_field' ), // callback
			'darx-login-settings', // page
			'darx-login-vk', // section
			array(
				'label_for' => 'vkapi_login',
				'type'      => 'checkbox',
				'descr'     => '',
			) // args
		);
	}

	// todo: check hook documentation
	public function add_login_form() {
		if ( $vkapi_appid = get_option( 'vkapi_appid' ) ) {
			global $action;

			if ( $action == 'login' || $action == 'register' ) {
				// todo: add like option and chech this
				if ( is_user_logged_in() ) {
					wp_redirect( home_url() );
					exit;
				}

				echo '
					<script src="//vk.com/js/api/openapi.js"></script>
					<div id="vkapi_login_button" onclick="VK.Auth.login(authLogin)">
						<script>
							function sendRequest(url, cb, postData) {
								var XMLHttpFactories = [
									function () {return new XMLHttpRequest()},
									function () {return new ActiveXObject("Msxml2.XMLHTTP")},
									function () {return new ActiveXObject("Msxml3.XMLHTTP")},
									function () {return new ActiveXObject("Microsoft.XMLHTTP")}
								];
								var req = false;
								for (var i=0; i<XMLHttpFactories.length; ++i) {
									try {
										req = XMLHttpFactories[i]();
									} catch (e) {
										continue;
									}
									break;
								}
								
								if (!req) return;
								var method = (postData) ? "POST" : "GET";
								req.open(method, url, true);
								req.setRequestHeader("X-Requested-With", "XMLHttpRequest");
								if (postData) req.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
								req.onreadystatechange = function () {
										if (req.readyState != 4) return;
										if (req.status !== 404 && req.status !== 304) {
											cb(req);
										}
									};
								if (req.readyState == 4) return;
								req.send(postData);
							}
							
							function authLogin(r) {
									var params = {
										user_ids: r.session.mid,
										fields: "domain,first_name,last_name,photo_200"
									};
									VK.Api.call("users.get", params, function(r) {
										sendRequest(
											"' . self::$login_url_vk_full . '"+location.search, 
											function(r) {
												r = JSON.parse(r.response);
												if (r.error) {
													alert(r.error.msg);
												} else {
													if ( /^http:/.test(r.response.redirect_to) ) {
														window.location.href = r.response.redirect_to;
													} else {
														window.location.pathname = r.response.redirect_to;
													}
												}
											},
											r.response[0]
										);									
									});
								}
							
							VK.UI.button("vkapi_login_button");
							VK.init({apiId: ' . $vkapi_appid . '});
						</script>
					</div><br />';
			}
		}
	}

	static public function get_vk_login() {
		if ( get_option( 'vkapi_login' ) ) {
			$random_string = wp_generate_password( 12, false, false );
			Darx_JS::add('vk', 'https://vk.com/js/api/openapi.js');

			return '<div onclick="VK.Util.getPageData();VK.Auth.login(authLogin)">
						<div id="vkapi_login_button-' . $random_string . '" class="vkapi_vk_login">
		                    <script>								
								function authLogin(r) {
									var params = {
										user_ids: r.session.mid,
										fields: "domain,first_name,last_name,photo_200"
									};
									VK.Api.call("users.get", params, function(r) {
										darx.post(
											"' . self::$login_url_vk_full . '"+location.search,
											r.response[0],
											function(r) {
												r = JSON.parse(r);
												if (r.error) {
													alert(r.error.msg);
												} else {
													if ( /^http:/.test(r.response.redirect_to) ) {
														window.location.href = r.response.redirect_to;
													} else {
														window.location.pathname = r.response.redirect_to;
													}
												}
											}
										);									
									});
								}
								
		                        darx.addEvent(document, "vk", function () {
		                            VK.UI.button("vkapi_login_button-' . $random_string . '");
		                        });
		                    </script>
						</div>
						<style type="text/css" scoped="scoped">
							.vkapi_vk_login {
								padding: 0 !important; 
								border: 0 !important; 
								width: 125px !important;
							}
							
							.vkapi_vk_login table {
								table-layout: auto !important; 
							}
							
							.vkapi_vk_login table td, .vkapi_vk_login table tr {
								width: auto !important;
								padding: 0 !important;
								margin: 0 !important;
								vertical-align: top !important;
								border: 0 !important;
								word-wrap: normal !important;
							}
						
							.vkapi_vk_login table div {
							    box-sizing: content-box !important;
							}
						</style>
					</div>';
		}

		return '';
	}

	private function _authOpenAPIMember() {
		$session    = array();
		$member     = false;
		$valid_keys = array( 'expire', 'mid', 'secret', 'sid', 'sig' );
		$app_cookie = $_COOKIE[ 'vk_app_' . get_option( 'vkapi_appid' ) ];
		if ( $app_cookie ) {
			$session_data = explode( '&', $app_cookie, 10 );
			foreach ( $session_data as $pair ) {
				list( $key, $value ) = explode( '=', $pair, 2 );
				if ( empty( $key ) || empty( $value ) || ! in_array( $key, $valid_keys ) ) {
					continue;
				}
				$session[ $key ] = $value;
			}
			foreach ( $valid_keys as $key ) {
				if ( ! isset( $session[ $key ] ) ) {
					return $member;
				}
			}
			ksort( $session );

			$sign = '';
			foreach ( $session as $key => $value ) {
				if ( $key != 'sig' ) {
					$sign .= ( $key . '=' . $value );
				}
			}
			$sign .= get_option( 'vkapi_api_secret' );
			$sign = md5( $sign );
			if ( $session['sig'] == $sign && $session['expire'] > time() ) {
				$member = array(
					'id'     => intval( $session['mid'] ),
					'secret' => $session['secret'],
					'sid'    => $session['sid']
				);
			}
		}

		return $member;
	}

	/**
	 * @param $id
	 *
	 * @return WP_User | false
	 */
	private function _vk_wpuser_get( $id ) {
		return call_user_func( 'reset', get_users( array(
			'meta_key'    => 'vkapi_uid',
			'meta_value'  => $id,
			'number'      => 1,
			'count_total' => false
		) ));
	}

	/**
	 * @param integer $id
	 *
	 * @return WP_User | false
	 */
	private function _vk_wpuser_create( $id ) {
		if ( function_exists( 'curl_init' ) ) {
			$params   = array(
				'user_ids' => $id,
				'fields'   => 'domain,first_name,last_name,photo_200',
			);
			$response = $this->_vk_call( 'users.get', $params );
			$user     = $response['response'][0];
		} else {
			$user = array(
				'domain'     => $_POST['nickname'],
				'first_name' => $_POST['first_name'],
				'last_name'  => $_POST['last_name'],
				'photo_200'  => $_POST['photo_200'],
			);
		}

		$userdata                 = array();
		$userdata['user_login']   = 'vk_id' . $id;
		$userdata['nickname']     = $user['domain'];
		$userdata['first_name']   = $user['first_name'];
		$userdata['last_name']    = $user['last_name'];
		$userdata['user_pass']    = wp_generate_password();
		$userdata['display_name'] = "{$userdata['first_name']} {$userdata['last_name']}";

		$uid = wp_insert_user( $userdata );
		if ( is_wp_error( $uid ) ) {
			return $uid;
		}

		add_user_meta( $uid, 'vkapi_ava', $user['photo_200'], false );
		add_user_meta( $uid, 'vkapi_uid', $id, true );

		$user = get_user_by( 'id', $uid );

		return $user;
	}

	public function parse_request( $wp ) {
		if ( $wp->request === self::$login_url_vk ) {
			$member = $this->_authOpenAPIMember();

			if ( $member === false ) {
				if( array_key_exists( 'HTTP_X_REQUESTED_WITH', $_SERVER) &&
				    strtolower( $_SERVER[ 'HTTP_X_REQUESTED_WITH' ]) === 'xmlhttprequest' ) {
					echo json_encode(array(
						'error' => array(
							'code' => '-1',
							'msg' => __( 'The signature is not correct', 'vkapi' ),
						)
					));
				} else {
					// todo: maybe redirect to vk site login
					wp_redirect( wp_login_url( $_SERVER['REQUEST_URI'] ) );
				}
				exit;
			}

			$user = $this->_vk_wpuser_get( $member['id'] );

			if ( $user === false ) {
				$user = $this->_vk_wpuser_create( $member['id'] );
			}

			wp_set_auth_cookie( $user->ID, false );
			do_action( 'wp_login', $user->user_login, $user );

			if ( is_wp_error($user) ) {
				/** @var WP_Error $user */
				echo json_encode(array(
					'error' => array(
						'code' => '-1',
						'msg' => $user->get_error_message(),
					)
				));
				exit;
			}

			/** @var WP_User $user */

			// code from wp-login.php with options and filters!

			if ( isset( $_REQUEST['redirect_to'] ) ) {
				$redirect_to = $requested_redirect_to = $_REQUEST['redirect_to'];
				if ( get_user_option('use_ssl', $user->ID ) && false !== strpos($redirect_to, 'wp-admin') ) {
					$redirect_to = preg_replace('|^http://|', 'https://', $redirect_to);
				}
			} else {
				$redirect_to = admin_url();
				$requested_redirect_to = '';
			}

			$redirect_to = apply_filters( 'login_redirect', $redirect_to, $requested_redirect_to, $user );

			if ( empty( $redirect_to ) || $redirect_to == 'wp-admin/' || $redirect_to == admin_url() ) {
				if ( is_multisite() && !get_active_blog_for_user($user->ID) && !is_super_admin( $user->ID ) ) {
					$redirect_to = user_admin_url();
				} elseif ( is_multisite() && !$user->has_cap('read') ) {
					$redirect_to = get_dashboard_url( $user->ID );
				} elseif ( !$user->has_cap('edit_posts') ) {
					$redirect_to = $user->has_cap( 'read' ) ? admin_url( 'profile.php' ) : home_url();
				}
			}

			if( array_key_exists( 'HTTP_X_REQUESTED_WITH', $_SERVER) &&
			    strtolower( $_SERVER[ 'HTTP_X_REQUESTED_WITH' ]) === 'xmlhttprequest' ) {
				echo json_encode(array(
					'response' => array(
						'redirect_to' => $redirect_to,
					)
				));
			} else {
				wp_safe_redirect( $redirect_to );
			}

			exit;
		}
	}

	public function widgets_init() {
		register_widget( 'VKAPI_Login' );
	}

	// todo: remove JS and user URL
	public function profile_render( $profile ) {
		?>
		<table class="form-table">
			<tbody>
			<tr>
				<th scope="row">
					<?php _e( 'VKontakte', 'vkapi' ); ?>
				</th>
				<td id="vkapi_profile_vk" data-nonce="<?php echo wp_create_nonce( 'vkapi_profile_vk' ) ?>">
					<?php $profile_vk = get_user_meta( $profile->ID, 'vkapi_uid', true ); ?>
					<input type="button"
					       class="button-secondary"
					       value="<?php _e( 'Log in with VK', 'vkapi' ); ?>"
					       onclick="VK.Auth.login(vkapi_profile_vk); return false;"
					       id="vk_add"
					       data-show="vk_remove"
						<?php if ( $profile_vk )
							echo 'style="display:none"' ?>/>
					<input type="button"
					       class="button-secondary"
					       value="<?php _e( 'Disconnect from VK', 'vkapi' ); ?>"
					       onclick="vkapi_profile_update('vk', 'remove'); return false;"
					       id="vk_remove"
					       data-show="vk_add"
						<?php if ( ! $profile_vk )
							echo 'style="display:none"' ?>/>
				</td>
			</tr>
			</tbody>
		</table>
		<script src="//vk.com/js/api/openapi.js"></script>
		<script type="text/javascript">
			VK.init({
				apiId: <?php echo get_option( 'vkapi_appid' ); ?>
			});

			function vkapi_profile_update(provider, job) {
				var $input = jQuery('#' + provider + '_' + job);
				$input.hide();

				var data = {
					action: 'vkapi_profile',
					nonce: jQuery('#vkapi_profile_vk').data('nonce'),
					provider: provider,
					job: job
				};

				jQuery.post(ajaxurl, data, function (response) {
					if (response == '0') {
						jQuery('#' + $input.data('show')).show();
					} else {
						$input.html('<span style="color:#a00" class="dashicons-before dashicons-no-alt"> Error</span>');
					}
				});
			}

			function vkapi_profile_vk(response) {
				if (response.session) {
					vkapi_profile_update('vk', 'add');
				}
			}
		</script>
		<?php
	}

	public function profile_process_ajax() {
		if ( ! wp_verify_nonce( $_POST['nonce'], 'vkapi_profile_vk' ) ) {
			exit( '-1' );
		}

		$user = wp_get_current_user();

		switch ( $_POST['provider'] ) {
			case 'vk':
				switch ( $_POST['job'] ) {
					case 'add':
						$member = $this->_authOpenAPIMember();
						if ( $member === false ) {
							exit( '-1' );
						}
						if ( add_user_meta( $user->ID, 'vkapi_uid', $member['id'], true ) ) {
							exit( '0' );
						}
						exit( '-1' );
					case 'remove':
						if ( delete_user_meta( $user->ID, 'vkapi_uid' ) ) {
							exit( '0' );
						}
						exit( '-1' );
				}
				break;
		}

		exit( '-1' );
	}

	public function user_links( $wp_admin_bar ) {
		/** @var $wp_admin_bar WP_Admin_Bar */
		$user      = wp_get_current_user();
		$vkapi_uid = get_user_meta( $user->ID, 'vkapi_uid', true );
		if ( ! empty( $vkapi_uid ) ) {
			$wp_admin_bar->add_node(
				array(
					'id'     => 'vkapi-profile',
					'parent' => 'user-actions',
					'title'  => __( 'VKontakte Profile', 'vkapi' ),
					'href'   => "https://vk.com/id{$vkapi_uid}",
					'meta'   => array(
						'target' => '_blank',
					)
				)
			);
		}
	}

	// todo: find action only to avatar source
	public function get_avatar( $avatar, $id_or_email, $size, $default, $alt ) {
		if ( is_numeric( $id_or_email ) ) {
			$id = $id_or_email;
		} elseif ( is_string( $id_or_email ) ) {
			$user = get_user_by( 'email', $id_or_email );
			$id   = $user->ID;
		} elseif ( is_object( $id_or_email ) ) {
			// $id_or_email is comment object
			$id = $id_or_email->user_id;
		}

		if ( isset( $id ) ) {
			$src = get_user_meta( $id, 'vkapi_ava', true );
			if ( ! empty( $src ) ) {
				$avatar = "<img src='{$src}' alt='{$alt}' class='avatar avatar-{$size}' width='{$size}' height='{$size}' />";
			}
		}

		return $avatar;
	}
}

new Darx_Login();