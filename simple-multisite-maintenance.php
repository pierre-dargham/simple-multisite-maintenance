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

    require_once SMM_COMPLETE_PATH . '/include/admin.php';

    require_once SMM_COMPLETE_PATH . '/include/functions.php';
    require_once SMM_COMPLETE_PATH . '/include/lib.php';

    if( is_admin() ) {
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
            SMM_Lib::init_blogs_options();
            SMM_Lib::init_network_options();
        }

        /**
         * What to do on plugin deactivation
         */
        public static function deactivate() {
            // Nothing for now
        }

        /**
         * What to do on plugin uninstallation
         */
        public static function uninstall() {
            SMM_Lib::delete_blogs_options();
            SMM_Lib::delete_network_options();
        }

        /**
         * Plugin init:
         */
        public static function init() {
            add_action( 'get_header', array( __CLASS__, 'simple_multisite_maintenance' ), 0 );
        }

        /**
         * Main function of the plugin :
         * if maintenance mode is active, redirect to maintenance template
         */
        public static function simple_multisite_maintenance() {
                 if ( !SMM_Lib::user_can_view_blog_in_maintenance() && !SMM_Lib::no_maintenance_for_this_blog()) {
                    if( SMM_Lib::is_current_blog_in_maintenance() || SMM_Lib::is_network_in_maintenance() ) {
                        $template =  SMM_Lib::get_maintenance_template();
                        echo $template;
                        die();
                    }
                }               
            }    
    }

    SMM::hooks();
}