<?php
/**
 * Remember plugin path & URL
 */
define( 'SMM_PATH', plugin_basename( realpath( dirname( __FILE__ ).'/..') ) );
define( 'SMM_COMPLETE_PATH', WP_PLUGIN_DIR.'/'.SMM_PATH );
define( 'SMM_URL', WP_PLUGIN_URL.'/'.SMM_PATH );
define( 'SMM_DEFAULT_TEMPLATE_PATH', WP_PLUGIN_URL.'/'.SMM_PATH );

/**
 * Translation domain name for this plugin
 */
define( 'SMM_DOMAIN', 'simple-multisite-maintenance' );

/**
 * Slugs
 */
define( 'SMM_SLUG_NETWORK_ACTION', 'smm-maintenance-network-action' );
define( 'SMM_SLUG_ACTION', 'smm-maintenance-action' );
define( 'SMM_SLUG_ACTION_ACTIVATE', 'smm-maintenance-activate' );
define( 'SMM_SLUG_ACTION_DEACTIVATE', 'smm-maintenance-deactivate' );
define( 'SMM_SLUG_PATH_ACTION', 'smm-maintenance-template-path' );
define( 'SMM_SLUG_MODE_OPTION', 'smm_maintenance' );
define( 'SMM_SLUG_PATH_OPTION', 'smm_maintenance_template_path' );
define( 'SMM_SLUG_COLUMN_MAINTENANCE', 'smm_maintenance_template_path' );
define( 'SMM_SLUG_BUTTON_ACTIVATE_TEMPLATE', 'activate-template' );
define( 'SMM_SLUG_BUTTON_DEACTIVATE_TEMPLATE', 'deactivate-template' );
define( 'SMM_SLUG_INPUT_PATH', 'template-path' );

/**
 * Actions
 */
define( 'SMM_ACTION_NONCE', 'SMM_action_nonce' );
define( 'SMM_ACTION_NAME', 'SMM_action' );