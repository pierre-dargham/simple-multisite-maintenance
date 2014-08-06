<?php

if(!function_exists('get_blog_option')) {
	function get_blog_option( $id, $option, $default = false ) {
			return get_option( $option, $default );
	}
}

if(!function_exists('add_blog_option')) {
	function add_blog_option( $id, $option, $value ) {
			return add_option( $option, $value );
	}
}

if(!function_exists('delete_blog_option')) {
	function delete_blog_option( $id, $option ) {
			return delete_option( $option );
	}
}

if(!function_exists('update_blog_option')) {
	function update_blog_option( $id, $option, $value, $deprecated = null ) {
			return update_option( $option, $value );
	}
}

if(!function_exists('switch_to_blog')) {
	function switch_to_blog( $new_blog, $deprecated = null ) {
		return true;
	}
}

if(!function_exists('restore_current_blog')) {
	function restore_current_blog() {
		return true;
	}
}

if(!function_exists('ms_is_switched')) {
	function ms_is_switched() {
		return false;
	}
}

if(!function_exists('is_multisite')) {
	function is_multisite() {
		return false;
	}
}

if(!function_exists('is_network_admin')) {
	function is_network_admin() {
		return false;
	}
}

if(!function_exists('get_current_blog_id')) {
	function get_current_blog_id() {
		return 1;
	}
}

if(!function_exists('get_current_site')) {
	function get_current_site() {
		$current_site = new stdClass;
		$current_site->id = 1;
		$current_site->domain = get_option('siteurl');
		$current_site->path = '/';
		$current_site->site_name = get_option('blogname');
		return $current_site;
	}
}

if(!function_exists('wp_is_large_network')) {
	function wp_is_large_network( $using = 'sites' ) {
		return false;
	}
}

if(!function_exists('wp_get_sites')) {
	function wp_get_sites( $args = array() ) {
		return array(
			array(
				'blog_id'		=> 1,
				'site_id'		=> 1,
				'domain'		=> get_option('siteurl'),
				'path'			=> '/',
				'registered'	=> null,
				'last_updated'	=> null,
				'public'		=> 1,
				'archived'		=> 0,
				'mature'		=> 0,	
				'spam'			=> 0,
				'deleted'		=> 0,
				'lang_id'		=> null,
			),
		);
	}
}