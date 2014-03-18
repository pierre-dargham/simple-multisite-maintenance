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
            wp_die(WPDS_GAL_ERROR_CAPABILITIES);
        }

        // Manage Form Post
        if ( isset($_REQUEST['action'])) {
            switch($_REQUEST['action']) {
                case SMM_SLUG_ACTION_ACTIVATE :
                    update_site_option( 'smm_maintenance' , "yes");
                    $form_message = "Le réseau est désormais en mode maintenance";
                    break;
                case SMM_SLUG_ACTION_DEACTIVATE :
                    update_site_option( 'smm_maintenance' , "no");
                    $form_message = "Le réseau n'est plus désormais en mode maintenance";
                    break;                
                case SMM_SLUG_PATH_ACTION :
                    if(isset($_POST['deactivate-template'])) {
                        delete_site_option('smm_maintenance_template_path');
                        $form_message = "Le template personnalisé a été désactivé";
                    }
                    else if(isset($_POST['template-path']) && !empty($_POST['template-path'])) {
                        update_site_option('smm_maintenance_template_path' , $_POST['template-path']);
                        $form_message = "Votre page de maintenance personnalisée a été enregistrée";
                    }
                    else {
                        wp_die( 'Vous devez indiquer un chemin de template valide' );
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
                    update_blog_option( get_current_blog_id(), 'smm_maintenance', "yes");
                    $form_message = "Le site est désormais en mode maintenance";
                    break;
                case SMM_SLUG_ACTION_DEACTIVATE :
                     update_blog_option( get_current_blog_id(), 'smm_maintenance' , "no");
                    $form_message = "Le site n'est plus désormais en mode maintenance";
                    break;
                case SMM_SLUG_PATH_ACTION :
                    if(isset($_POST['deactivate-template'])) {
                        delete_blog_option(get_current_blog_id(), 'smm_maintenance_template_path' , "");
                        $form_message = "Le template personnalisé a été désactivé";
                    }
                    else if(isset($_POST['template-path']) && !empty($_POST['template-path'])) {
                        update_blog_option(get_current_blog_id(), 'smm_maintenance_template_path' , $_POST['template-path']);
                        $form_message = "Votre page de maintenance personnalisée a été enregistrée";
                    }
                    else {
                        wp_die( 'Vous devez indiquer un chemin de template valide' );
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

        $columns+= array( 'smm_maintenance' => SMM_STR_MAINTENANCE_COLUMN_TITLE );

        return $columns;
    }

    /**
     * Display data for added columns
     */
    public static function smm_sites_custom_column( $column_name, $blog_id ) {
        if ( $column_name == 'smm_maintenance' ) {
            $smm_maintenance = get_blog_option( $blog_id, 'smm_maintenance' , 'no' );
            switch( $smm_maintenance ) {
                case 'no':
                    echo '<a href="' . network_admin_url('sites.php?action=activate_smm_maintenance&amp;id=' . $blog_id) . '">Activer le mode maintenance</a>';
                    break;
                case 'yes':
                default :
                    echo '<a href="' . network_admin_url('sites.php?action=deactivate_smm_maintenance&amp;id=' . $blog_id) . '">Désactiver le mode maintenance</a>';
                    break;
                }
        }
    }

    public static function update_smm_maintenance() {
        if(isset($_GET['id']) && isset($_GET['action'])) {
            switch ( $_GET['action'] ) {
                case 'activate_smm_maintenance':
                    update_blog_option( $_GET['id'], 'smm_maintenance', "yes");
                break;
                case 'deactivate_smm_maintenance':
                    update_blog_option( $_GET['id'], 'smm_maintenance', "no");
                break;
            }
        }
    }

}