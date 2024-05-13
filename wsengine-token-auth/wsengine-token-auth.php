<?php
/*
	Plugin Name: Wsengine Token Authentication
	Plugin URI: httpd://github.com/wsengine/token-auth/
	Description: A simple token authentication plugin for Wordpress. Required for wsengine.com auto-login functionality.
	Author: wsengine.com
	Version: 1.0.1
	Author URI: https://www.wsengine.com/
*/

add_action('login_init', function()
{
	// do not continue if no token
	if( !array_key_exists('auth_token', $_GET) || !$_GET['auth_token'] )
		return;

	// redirect to admin panel if already logged in
	if( is_user_logged_in() )
	{
		// disable page cache
		header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");

		// redirect to admin panel
		wp_safe_redirect('/wp-admin/');
		exit;
	}

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

	// disable page cache
	header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Pragma: no-cache");

	// redirect to admin panel
	wp_safe_redirect('/wp-admin/');
	exit;
});
