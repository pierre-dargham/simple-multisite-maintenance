<?php
/**
 * Plugin Name:         Simple Multisite Maintenance
 * Plugin URI:          
 * Description:         Maintenance mode for multisite
 * Author:              Pierre DARGHAM, GLOBALIS media systems
 * Author URI:          http://www.globalis-ms.com
 *
 * Version:             0.1.0
 * Requires at least:   3.5.0
 * Tested up to:        3.8.1
 */

if( !class_exists( 'SMM' ) ) {
    // Load configuration
    require_once realpath( dirname( __FILE__ ) ) . '/include/config.php';

    // Load textdomain
    load_plugin_textdomain( SMM_DOMAIN, NULL, SMM_PATH . '/language/' );

    // Load language
    require_once SMM_COMPLETE_PATH . '/include/lang.php';

    if( is_admin() ) {
        require_once SMM_COMPLETE_PATH . '/include/admin.php';
        SMM_Admin::hooks();
    }

    /**
     * Main class of the plugin
     */
    class SMM {
        /**
         * Register hooks used by the plugin
         */
        public static function hooks() {
            // Register (de)activation hook
            register_activation_hook( __FILE__, array( __CLASS__, 'activate' ) );
            register_deactivation_hook( __FILE__, array( __CLASS__, 'deactivate' ) );
            register_uninstall_hook( __FILE__, array( __CLASS__, 'uninstall' ) );

            add_action( 'init', array( __CLASS__, 'init' ) );
        }

        /**
         * What to do on plugin activation
         */
        public static function activate() {
            $network_blogs = wp_get_sites();
            foreach( $network_blogs as $blog ){
                $blog_id = $blog['blog_id'];
                update_blog_option( $blog_id, SMM_SLUG_MODE_OPTION, "no");
            }
            update_site_option( SMM_SLUG_MODE_OPTION, "no");
        }

        /**
         * What to do on plugin deactivation
         */
        public static function deactivate() {
            $network_blogs = wp_get_sites();
            foreach( $network_blogs as $blog ){
                $blog_id = $blog['blog_id'];
                delete_blog_option( $blog_id, SMM_SLUG_MODE_OPTION);
                delete_blog_option( $blog_id, SMM_SLUG_PATH_OPTION);
            }
            delete_site_option( SMM_SLUG_MODE_OPTION);
            delete_site_option( SMM_SLUG_PATH_OPTION);
        }

        /**
         * What to do on plugin uninstallation
         */
        public static function uninstall() {
            // Nothing for now.
        }

        /**
         * Plugin init:
         */
        public static function init() {
            add_action( 'get_header', array( __CLASS__, 'simple_multisite_maintenance' ) );

            if ( !defined( 'WP_CONTENT_DIR' ) )
                define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );
        }

        public static function simple_multisite_maintenance() {
            if ( !(is_admin() || current_user_can( 'manage_options' ) || current_user_can( 'administrator' ) ||  current_user_can( 'super admin' ))) {
                if( get_site_option(SMM_SLUG_MODE_OPTION , "no" ) == "yes" || get_blog_option(get_current_blog_id(), SMM_SLUG_MODE_OPTION , "no" ) == "yes") {

                    if( $template_path = get_blog_option(get_current_blog_id(), SMM_SLUG_PATH_OPTION)) {
                        require_once WP_CONTENT_DIR . '/' . $template_path;
                    }
                    else if( $template_path = get_site_option(SMM_SLUG_PATH_OPTION)) {
                        require_once WP_CONTENT_DIR . '/' . $template_path;
                    }
                    else {
                        require_once SMM_COMPLETE_PATH . '/template/maintenance.php';
                    }

                    die();
                }

            }
        }
    
    }
    SMM::hooks();
}