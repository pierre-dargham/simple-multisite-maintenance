<?php
class SMM_Admin {

    /**
     * Register hooks used on admin side by the plugin
     */
    public static function hooks() {
        if (is_network_admin()) {
            add_action( 'network_admin_menu', array( __CLASS__, 'smm_network_menu_maintenance' ) );
            add_action( 'admin_init', array( __CLASS__, 'admin_init' ) );
        }

        if(is_admin()) {
            add_action( 'admin_menu', array( __CLASS__, 'smm_menu_maintenance' ) );
        }
    }

    /**
     * Do some actions at the beginning of an admin script
     */
    public static function admin_init() {
        // Hook to columns on network sites listing
        add_filter( 'wpmu_blogs_columns', array( __CLASS__, 'smm_blogs_columns' ) );
        // Hook to manage column data on network sites listing
        add_action( 'manage_sites_custom_column', array( __CLASS__, 'smm_sites_custom_column'), 10, 2 );
        // Hook to update options values
        add_action( 'wpmuadminedit', array( __CLASS__, 'update_smm_maintenance'), 10, 1 );
    }

    /** 
    * Add maintenance option in network menu
    */
    public static function smm_network_menu_maintenance() {
        add_submenu_page( 'settings.php', SMM_NETWORK_PAGE_MAINTENANCE_TITLE, SMM_NETWORK_PAGE_MAINTENANCE_TITLE, 'manage_network', SMM_SLUG_NETWORK_ACTION, array( __CLASS__, 'network_page_admin_smm_maintenance' ) );
    }

    /** 
    * Add maintenance option in menu
    */
    public static function smm_menu_maintenance() {
        add_submenu_page( 'options-general.php', SMM_PAGE_MAINTENANCE_TITLE, SMM_PAGE_MAINTENANCE_TITLE, 'manage_sites', SMM_SLUG_ACTION, array( __CLASS__, 'page_admin_smm_maintenance' ) );
    }

   /**
     * Network Admin page - Maintenance
     */
    public static function network_page_admin_smm_maintenance() {
        // Capabilities test
        if( !is_super_admin() ) {
            wp_die(SMM_ERROR_CAPABILITIES);
        }

        // Manage Form Post
        if ( isset($_REQUEST['action'])) {
            switch($_REQUEST['action']) {
                case SMM_SLUG_ACTION_ACTIVATE :
                    update_site_option( SMM_SLUG_MODE_OPTION , "yes");
                    $form_message = SMM_NETWORK_PAGE_ACTIVATED_MSG;
                    break;
                case SMM_SLUG_ACTION_DEACTIVATE :
                    update_site_option( SMM_SLUG_MODE_OPTION , "no");
                    $form_message = SMM_NETWORK_PAGE_DEACTIVATED_MSG;
                    break;                
                case SMM_SLUG_PATH_ACTION :
                    if(isset($_POST[SMM_SLUG_BUTTON_DEACTIVATE_TEMPLATE])) {
                        delete_site_option(SMM_SLUG_PATH_OPTION);
                        $form_message = SMM_PAGE_TEMPLATE_DEACTIVATED_MSG;
                    }
                    else if(isset($_POST[SMM_SLUG_INPUT_PATH]) && !empty($_POST[SMM_SLUG_INPUT_PATH])) {
                        update_site_option(SMM_SLUG_PATH_OPTION , $_POST[SMM_SLUG_INPUT_PATH]);
                        $form_message = SMM_PAGE_TEMPLATE_ACTIVATED_MSG;
                    }
                    else {
                        wp_die( SMM_TEMPLATE_PATH_ERROR );
                    }
                    break;         
            }
        }

            $nonce_string = SMM_SLUG_NETWORK_ACTION;
            require_once SMM_COMPLETE_PATH . '/template/admin_network_maintenance.php';
 
    }

       /**
     * Network Admin page - Maintenance
     */
    public static function page_admin_smm_maintenance() {
        // Capabilities test
        if( !current_user_can( 'manage_options' ) ) {
            wp_die(WPDS_GAL_ERROR_CAPABILITIES);
        }

        // Manage Form Post
        if ( isset($_REQUEST['action'])) {
            switch($_REQUEST['action']) {
                case SMM_SLUG_ACTION_ACTIVATE :
                    update_blog_option( get_current_blog_id(), SMM_SLUG_MODE_OPTION, "yes");
                    $form_message = SMM_PAGE_ACTIVATED_MSG;
                    break;
                case SMM_SLUG_ACTION_DEACTIVATE :
                     update_blog_option( get_current_blog_id(), SMM_SLUG_MODE_OPTION , "no");
                    $form_message = SMM_PAGE_DEACTIVATED_MSG;
                    break;
                case SMM_SLUG_PATH_ACTION :
                    if(isset($_POST[SMM_SLUG_BUTTON_DEACTIVATE_TEMPLATE])) {
                        delete_blog_option(get_current_blog_id(), SMM_SLUG_PATH_OPTION);
                        $form_message = SMM_PAGE_TEMPLATE_DEACTIVATED_MSG;
                    }
                    else if(isset($_POST[SMM_SLUG_INPUT_PATH]) && !empty($_POST[SMM_SLUG_INPUT_PATH])) {
                        update_blog_option(get_current_blog_id(), SMM_SLUG_PATH_OPTION , $_POST[SMM_SLUG_INPUT_PATH]);
                        $form_message = SMM_PAGE_TEMPLATE_ACTIVATED_MSG;
                    }
                    else {
                        wp_die( SMM_TEMPLATE_PATH_ERROR );
                    }
                    break;         
            }
        }

            $nonce_string = SMM_SLUG_ACTION;
            require_once SMM_COMPLETE_PATH . '/template/admin_maintenance.php';

 
    }


    /**
     * Add columns to the post types lists
     */
    public static function smm_blogs_columns( $columns ) {
        global $wp_list_table;

        $current_screen = get_current_screen();

        // get_current_screen() could return null (in AJAX context for ex, when quick editing a post)
        if( !$current_screen )
            return $columns;

        $post_type_obj = get_post_type_object( $current_screen->post_type );

        // If we cannot create posts of that type, we cannot see duplicatas
        if( !is_super_admin() )
            return $columns;

        $columns+= array( SMM_SLUG_COLUMN_MAINTENANCE => SMM_STR_MAINTENANCE_COLUMN_TITLE );

        return $columns;
    }

    /**
     * Display data for added columns
     */
    public static function smm_sites_custom_column( $column_name, $blog_id ) {
        if ( $column_name == SMM_SLUG_COLUMN_MAINTENANCE ) {
            $smm_maintenance = get_blog_option( $blog_id, SMM_TEMPLATE_PATH_ERROR , 'no' );
            switch( $smm_maintenance ) {
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

    public static function update_smm_maintenance() {
        if(isset($_GET['id']) && isset($_GET['action'])) {
            switch ( $_GET['action'] ) {
                case SMM_SLUG_ACTION_ACTIVATE:
                    update_blog_option( $_GET['id'], SMM_TEMPLATE_PATH_ERROR, "yes");
                break;
                case SMM_SLUG_ACTION_DEACTIVATE:
                    update_blog_option( $_GET['id'], SMM_TEMPLATE_PATH_ERROR, "no");
                break;
            }
        }
    }

}