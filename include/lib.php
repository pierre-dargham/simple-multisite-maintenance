<?php

class SMM_Lib {

	public static function is_network_in_maintenance() {
		if(is_multisite() && SMM_Lib::is_network_activated()) {
			return (get_site_option(SMM_SLUG_MODE_OPTION , "no" ) == "yes");
		}

		return false;		
	}

	public static function is_network_activated() {
		if(!is_multisite()) {
			return false;
		}
		$network_plugins = get_site_option('active_sitewide_plugins');
	 	return (isset($network_plugins['simple-multisite-maintenance/simple-multisite-maintenance.php']));	
	}

	public static function is_current_blog_in_maintenance() {
		if(is_multisite()) {
			return (get_blog_option(get_current_blog_id(), SMM_SLUG_MODE_OPTION , "no" ) == "yes");	
		}

		return (get_option(SMM_SLUG_MODE_OPTION , "no" ) == "yes");	
	}

	public static function get_maintenance_template($network = false) {

		$template_mod = SMM_Lib::get_option(SMM_SLUG_OPTION_TEMPLATE_MOD , "no", $network );

		switch($template_mod) {
			case 'smm_template_mod_network' :
				if(is_multisite()) {
					return self::get_maintenance_template(true);
				}
				$template_content = '';
				break;
			case 'smm_template_mod_path' :
				$template_content = @file_get_contents(apply_filters('smm_template_path', SMM_Lib::content_dir() . '/' . SMM_Lib::get_option(SMM_SLUG_INPUT_PATH , false, $network ) ));
				break;
			case 'smm_template_mod_bloc' :
				$template_content = SMM_Lib::get_option('smm_template_bloc', '', $network);
				break;
			default :
				$template_content = '';
				break;					
		}

		return empty($template_content) ?  self::default_template() : $template_content;		
	}

	public static function get_maintenance_template_option() {
		if(is_network_admin()) {
			return get_site_option(SMM_SLUG_PATH_OPTION, "" );
		}
		else if(is_multisite()) {
			return get_blog_option(get_current_blog_id(), SMM_SLUG_PATH_OPTION, "" );
		}
		else {
			return get_option( SMM_SLUG_PATH_OPTION , "" );
		}
	}

	public static function default_template() {
		return file_get_contents(SMM_COMPLETE_PATH . '/template/template_maintenance_example.php');
	}

	public static function user_can_view_blog_in_maintenance() {
		if (current_user_can( 'manage_options' ) || current_user_can( 'administrator' ) ||  current_user_can( 'super admin' )) {
			return 'yes' === SMM_Lib::get_option(SMM_SLUG_OPTION_ADMIN_CAN_VIEW , "no" );
		}
		return false;
	}

	public static function no_maintenance_for_this_blog($blog_id = null ) {
		if(!isset($blog_id)) {
			$blog_id = get_current_blog_id();
		}
		return in_array($blog_id, self::blogs_to_exclude());
	}

	public static function blogs_to_exclude() {
		$exclude_ids = array();
		return apply_filters('smm_exlude_blogs', $exclude_ids);
	}

	public static function init_blogs_options() {
        if(is_network_admin()) {
            $network_blogs = wp_get_sites();
            foreach( $network_blogs as $blog ){
                $blog_id = $blog['blog_id'];
                add_blog_option( $blog_id, SMM_SLUG_MODE_OPTION, "no");
            }
        } else {
            if(is_multisite()) {
                add_blog_option( get_current_blog_id(), SMM_SLUG_MODE_OPTION, "no");
            }
            else {
                add_option(SMM_SLUG_MODE_OPTION, "no");
            }
            
        }
	}

	public static function init_network_options() {
        if(is_network_admin() && is_multisite()) {
            add_site_option( SMM_SLUG_MODE_OPTION, "no");
        }
	}

	public static function delete_blogs_options() {
        if(is_network_admin()) {
            $network_blogs = wp_get_sites();
            foreach( $network_blogs as $blog ){
                $blog_id = $blog['blog_id'];
                delete_blog_option( $blog_id, SMM_SLUG_MODE_OPTION);
                delete_blog_option( $blog_id, SMM_SLUG_PATH_OPTION);
            }
        }
        else {
            delete_option( SMM_SLUG_MODE_OPTION);
            delete_option( SMM_SLUG_PATH_OPTION);
        }
	}

	public static function delete_network_options() {
		if(is_network_admin() && is_multisite()) {
	    	delete_site_option( SMM_SLUG_MODE_OPTION);
	    	delete_site_option( SMM_SLUG_PATH_OPTION);
	    }
	}

	public static function get_settings_url($title) {
		if(is_network_admin() && self::is_network_activated()) {
			return '<a href="'.network_admin_url('settings.php?page=' . SMM_SLUG_ACTION).'" title="'.$title.'">'.$title.'</a>';
		}
		return '<a href="'.admin_url('options-general.php?page=' . SMM_SLUG_ACTION).'" title="'.$title.'">'.$title.'</a>';
	}

	public static function get_updated_message($string, $error = false) {
		$formatted_message = "";
		if(is_string($string)) {
			$error === true ? $class = "error" : $class = "updated";
        	$formatted_message .= '<div id="message" class="'.$class.'">';
            $formatted_message .= '	<p>';
            $formatted_message .= '	    <strong>';
            $formatted_message .= '			' . $string;
            $formatted_message .= '	    </strong>';
            $formatted_message .= '	</p>';
        	$formatted_message .= '</div>';	
		}
		return $formatted_message;
	}

	public static function updated_message($string) {
		echo self::get_updated_message($string);
	}

	public static function content_dir() {
            $dirs = explode('/', WP_CONTENT_DIR);
            $content_dir = end($dirs);
            return $content_dir . '/';		
	}

	public static function smm_admin($network_string, $blog_string) {
		if(is_network_admin()) {
			return $network_string;
		}
		else {
			return $blog_string;
		}
	}

	public static function get_option($key, $default_value = false, $network = false) {
		if(is_network_admin() || $network===true ) {
			return get_site_option($key, $default_value);
		}
		else if (is_multisite()) {
			return get_blog_option(get_current_blog_id(), $key, $default_value);
		}
		else {
			return get_option($key, $default_value);
		}
	}

	public static function update_option($key, $value) {
		if(is_network_admin()) {
			return update_site_option($key, $value);
		}
		else if (is_multisite()) {
			return update_blog_option(get_current_blog_id(), $key, $value);
		}
		else {
			return update_option($key, $value);
		}
	}

	public static function delete_option($key, $value) {
		if(is_network_admin()) {
			return delete_site_option($key);
		}
		else if (is_multisite()) {
			return delete_blog_option(get_current_blog_id(), $key);
		}
		else {
			return delete_option($key);
		}
	}
}