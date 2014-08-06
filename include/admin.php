<?php

class SMM_Admin {

    /**
     * Register hooks used on admin side by the plugin
     */
    public static function hooks() {
            if (is_network_admin()) {
                add_action( 'network_admin_menu', array( __CLASS__, 'smm_network_menu' ) );
                add_action( 'admin_init', array( __CLASS__, 'smm_network_init' ) );
            }
            else {
                add_action( 'admin_menu', array( __CLASS__, 'smm_admin_menu' ) );
                add_filter( 'plugin_action_links', array( __CLASS__, 'add_settings_link'), 10, 2 );
             }
            add_action( 'admin_init', array( __CLASS__, 'blog_maintenance_alert' ) );
    }

    public static function blog_maintenance_alert() {

        $message = "";
        $link = "";

        if(SMM_Lib::is_network_in_maintenance()) {
            if(current_user_can( 'manage_sites' )) {
                $link = ' <a href="'.network_admin_url('settings.php?page=' . SMM_SLUG_ACTION).'" title="Modifier les paramètres">Modifier les paramètres</a>';
            }
            $message = 'Attention, le réseau est en mode maintenance.'. $link;
        }
        else if(!is_network_admin() && SMM_Lib::is_current_blog_in_maintenance()) {
            if(current_user_can( 'manage_options' )) {
                $link = ' <a href="'.admin_url('options-general.php?page=' . SMM_SLUG_ACTION).'" title="Modifier les paramètres">Modifier les paramètres</a>';
            }

            if(get_option(SMM_SLUG_OPTION_ADMIN_CAN_VIEW , "no" ) == "yes") {
                $can_view = ' Il est visible seulement par les administrateurs connectés.';
            }
            else {
                $can_view = "";
            }
            $message = 'Attention, le site est en mode maintenance.' . $can_view . $link;
        }

        if( $message!=="") {
            $admin_notices = '<div id="message" class="error fade"><p>' . $message . '</p></div>';

            if(is_network_admin()) {
                if( !(isset($_GET['page']) && $_GET['page'] == 'smm-maintenance-action') ) {
                    add_action( 'network_admin_notices', create_function( '', "echo '$admin_notices';" ) );
                }
            }
            else {
                if( SMM_Lib::is_network_in_maintenance() || !(isset($_GET['page']) && $_GET['page'] == 'smm-maintenance-action') ) {
                   add_action( 'admin_notices', create_function( '', "echo '$admin_notices';" ) );
                }
            }
        } 
    }

    /**
     * Do some actions at the beginning of an admin script
     */
    public static function smm_network_init() {
        // Hook to columns on network sites listing
        add_filter( 'wpmu_blogs_columns', array( __CLASS__, 'smm_network_blogs_columns' ) );
        // Hook to manage column data on network sites listing
        add_action( 'manage_sites_custom_column', array( __CLASS__, 'smm_network_blogs_columns_data'), 10, 2 );
        // Hook to update options values
        add_action( 'wpmuadminedit', array( __CLASS__, 'smm_network_blogs_columns_save'), 10, 1 );
        // Settings link in plugins.php
        add_filter( 'network_admin_plugin_action_links', array( __CLASS__, 'add_settings_link'), 10, 2 );
    }

    public static function add_settings_link($links, $file ) {
        if ( SMM_PATH . '.php' === basename($file)  ) {
            array_unshift($links, SMM_Lib::get_settings_url( __('Settings') ) );
        }
        
        return $links;
    }

    /** 
    * Add maintenance option in network menu
    */
    public static function smm_network_menu() {
        add_submenu_page( 'settings.php', SMM_NETWORK_PAGE_MAINTENANCE_TITLE, SMM_NETWORK_PAGE_MAINTENANCE_TITLE, 'manage_network', SMM_SLUG_ACTION, array( __CLASS__, 'smm_admin_menu_page' ) );
    }

    /** 
    * Add maintenance option in menu
    */
    public static function smm_admin_menu() {
        if(!SMM_Lib::no_maintenance_for_this_blog()) {
            add_submenu_page( 'options-general.php', SMM_PAGE_MAINTENANCE_TITLE, SMM_PAGE_MAINTENANCE_TITLE, 'manage_options', SMM_SLUG_ACTION, array( __CLASS__, 'smm_admin_menu_page' ) );
        }
    }

    /**
     * Network Admin page - Maintenance
     */
    public static function smm_admin_menu_page() {
        // Capabilities test
        if(is_network_admin()) {
            if( !current_user_can( 'manage_sites' ) ) {
                wp_die(SMM_ERROR_CAPABILITIES);
            }            
        }
        else {
            if( !current_user_can( 'manage_options' ) ) {
                wp_die(SMM_ERROR_CAPABILITIES);
            }              
        }

        if(isset($_POST) && !empty($_POST) ) {
            if(isset($_POST[SMM_SLUG_MODE_OPTION]) && $_POST[SMM_SLUG_MODE_OPTION]=="yes" ) {
                SMM_Lib::update_option(SMM_SLUG_MODE_OPTION, "yes");

                if(isset($_POST[SMM_SLUG_OPTION_ADMIN_CAN_VIEW]) && $_POST[SMM_SLUG_OPTION_ADMIN_CAN_VIEW]=="yes" ) {
                    SMM_Lib::update_option(SMM_SLUG_OPTION_ADMIN_CAN_VIEW, "yes");
                }
                else {
                    SMM_Lib::update_option(SMM_SLUG_OPTION_ADMIN_CAN_VIEW, "no");
                }
            }
            else {
                SMM_Lib::update_option(SMM_SLUG_MODE_OPTION, "no");
            }

            if(isset($_POST[SMM_SLUG_OPTION_TEMPLATE_MOD]) ) {
                SMM_Lib::update_option(SMM_SLUG_OPTION_TEMPLATE_MOD, $_POST[SMM_SLUG_OPTION_TEMPLATE_MOD]);

                switch($_POST[SMM_SLUG_OPTION_TEMPLATE_MOD]) {
                    case 'smm_template_mod_network' :
                        break;
                    case 'smm_template_mod_path' :
                        if( isset( $_POST[SMM_SLUG_INPUT_PATH] ) && !empty( $_POST[SMM_SLUG_INPUT_PATH] ) ) {
                            SMM_Lib::update_option(SMM_SLUG_INPUT_PATH, $_POST[SMM_SLUG_INPUT_PATH]);
                        }
                        break;
                    case 'smm_template_mod_bloc' :
                        if( isset( $_POST['smm_template_bloc'] )  ) {
                            SMM_Lib::update_option('smm_template_bloc', stripslashes($_POST['smm_template_bloc']) );
                        }
                        break;
                }
               
            }

            $form_message = __('Settings saved.');
        }

        $maintenance_mod_checked = checked( 'yes', SMM_Lib::get_option(SMM_SLUG_MODE_OPTION , "no" ), false );
        $admin_can_view_checked = checked( 'yes', SMM_Lib::get_option(SMM_SLUG_OPTION_ADMIN_CAN_VIEW , "no" ), false );

        $template_mod = SMM_Lib::get_option(SMM_SLUG_OPTION_TEMPLATE_MOD , "smm_template_mod_bloc" );

        $template_mod_network_checked = checked( 'smm_template_mod_network', $template_mod, false );
        $template_mod_path_checked = checked( 'smm_template_mod_path', $template_mod, false );
        $template_mod_bloc_checked = checked( "smm_template_mod_bloc", $template_mod, false );

        $admin_can_view_disabled = empty($maintenance_mod_checked) ? 'disabled' : '';
        $template_path_disabled = empty($template_mod_path_checked) ? 'disabled' : '';
        $template_bloc_disabled = empty($template_mod_bloc_checked) ? 'disabled' : '';

        $template_path_content = get_option(SMM_SLUG_INPUT_PATH, '');

        $template_bloc_content = get_option('smm_template_bloc', '');
        $template_bloc_content = empty($template_bloc_content) ? file_get_contents(SMM_COMPLETE_PATH . '/template/template_maintenance_example.php') : $template_bloc_content;

        $nonce_string = SMM_Lib::smm_admin(SMM_SLUG_ACTION, SMM_SLUG_ACTION);
        wp_enqueue_script( 'smm-admin', SMM_URL . '/js/smm_admin.js' ); 
        require_once SMM_COMPLETE_PATH . '/template/smm_admin.php';
    }

    /**
     * Add columns to the post types lists
     */
    public static function smm_network_blogs_columns( $columns ) {
        global $wp_list_table;

        $current_screen = get_current_screen();

        // get_current_screen() could return null (in AJAX context for ex, when quick editing a post)
        if( !$current_screen )
            return $columns;

        $post_type_obj = get_post_type_object( $current_screen->post_type );

        if( !is_super_admin() )
            return $columns;

        $columns+= array( SMM_SLUG_COLUMN_MAINTENANCE => SMM_STR_MAINTENANCE_COLUMN_TITLE );

        return $columns;
    }

    /**
     * Display data for added columns
     */
    public static function smm_network_blogs_columns_data( $column_name, $blog_id ) {
        if ( $column_name == SMM_SLUG_COLUMN_MAINTENANCE ) {
            $smm_maintenance = get_blog_option( $blog_id, SMM_SLUG_MODE_OPTION , 'no' );
            switch( $smm_maintenance ) {
                case 'ref':
                    echo '';
                    break;
                case 'no':
                    echo '<a href="' . network_admin_url('sites.php?action='. SMM_SLUG_ACTION_ACTIVATE .'&amp;id=' . $blog_id) . '">' . SMM_NETWORK_PAGE_MAINTENANCE_ACTIVATE . '</a>';
                    break;
                case 'yes':
                default :
                    echo '<a href="' . network_admin_url('sites.php?action='. SMM_SLUG_ACTION_DEACTIVATE .'&amp;id=' . $blog_id) . '">' . SMM_NETWORK_PAGE_MAINTENANCE_DEACTIVATE . '</a>';
                    break;
                }
        }
    }

    public static function smm_network_blogs_columns_save() {
        if(isset($_GET['id']) && isset($_GET['action'])) {
            switch ( $_GET['action'] ) {
                case SMM_SLUG_ACTION_ACTIVATE:
                    update_blog_option( $_GET['id'], SMM_SLUG_MODE_OPTION, "yes");
                break;
                case SMM_SLUG_ACTION_DEACTIVATE:
                    update_blog_option( $_GET['id'], SMM_SLUG_MODE_OPTION, "no");
                break;
            }
        }
    }

}