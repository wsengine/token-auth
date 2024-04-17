<?php
/*
	Plugin Name: Wsengine Token Authentication
	Plugin URI: httpd://github.com/wsengine/token-auth/
	Description: A simple token authentication plugin for Wordpress. Required for wsengine.com auto-login functionality.
	Author: wsengine.com
	Version: 1.0.0
	Author URI: https://www.wsengine.com/
*/

add_action('login_init', function()
{
	// do not continue if logged in or no token
	if( is_user_logged_in() || !$_GET['auth_token'] )
		return;

	// decode token
	$token = base64_decode($_GET['auth_token']);
	list($ts, $user, $pass) = explode(':', $token, 3);

	// check timestamp, user, pass
	// ts must be no older than 1 minute
	$ts = intval($ts);
	if( !$ts || !$user || !$pass || ($ts-time()) > 60 )
		return;

	// perform login
	$auth =	wp_signon([
				'user_login'    => $user,
				'user_password' => $pass,
				'remember'      => true,
			]);

	// check for errors
	if( is_wp_error( $auth ) )
		return;

	// redirect to admin
	wp_safe_redirect('/wp-admin/');
	exit;
});
