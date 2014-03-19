<div class="wrap">
    <h2 id="smm-maintenance"><?php echo SMM_NETWORK_PAGE_MAINTENANCE_TITLE; ?></h2>

    <?php if( isset( $form_message ) ) { ?>
        <div id="message" class="updated">
            <p>
                <strong>
                    <?php echo $form_message; ?>
                </strong>
            </p>
        </div>
    <?php
        }

        $smm_maintenance = get_site_option(SMM_SLUG_MODE_OPTION , "no" );
        switch( $smm_maintenance ) {
            case 'no':
                $action = SMM_SLUG_ACTION_ACTIVATE;
                $button = SMM_NETWORK_PAGE_MAINTENANCE_ACTIVATE;
                break;
            case 'yes':
            default :
                $action = SMM_SLUG_ACTION_DEACTIVATE;
                $button = SMM_NETWORK_PAGE_MAINTENANCE_DEACTIVATE;
                break;
            }
    ?>

    <form method="post" action="<?php echo network_admin_url('settings.php?page=' . SMM_SLUG_NETWORK_ACTION . '&action=' . $action); ?>">

    
        <?php wp_nonce_field( SMM_DOMAIN ); ?>

        <p>
            <?php echo SMM_TEXT_NETWORK_ACTIVATE_INFO; ?>
            <br /><br />
            <?php echo SMM_TEXT_NETWORK_DEACTIVATE_INFO; ?>
        </p>

        <p class="submit">
            <input class='button button-primary' type='submit' value=' <?php echo $button; ?>' />
        </p>

    </form>

      <h3 id="smm-maintenance"><?php echo SMM_PAGE_MAINTENANCE_TEMPLATE_TITLE; ?></h3>
    <form method="post" action="<?php echo network_admin_url('settings.php?page=' . SMM_SLUG_NETWORK_ACTION . '&action=' . SMM_SLUG_PATH_ACTION); ?>">

    
        <?php wp_nonce_field( SMM_DOMAIN ); ?>

        <p>
            <?php echo SMM_TEXT_MAINTENANCE_TEMPLATE_INFO; ?>
        </p>

        <p>
            <?php
            $dirs = explode('/', WP_CONTENT_DIR);
            $content_dir = end($dirs);
            echo $content_dir . '/';
            $custom_template = get_site_option(SMM_SLUG_PATH_OPTION , "" );
            ?>

            <input name="<?php echo SMM_SLUG_INPUT_PATH; ?>" type="text" class="regular-text" value="<?php echo $custom_template; ?>"/>
        </p>

        <p>
             <?php echo SMM_TEXT_MAINTENANCE_TEMPLATE_WARNING; ?>
        </p>

        <p class="submit">
            <input name="<?php echo SMM_SLUG_BUTTON_ACTIVATE_TEMPLATE; ?>" class='button button-primary' type='submit' value='<?php echo SMM_PAGE_TEMPLATE_ACTIVATE; ?>' />
            <?php if(!empty($custom_template)) { ?>
                <input name="<?php echo SMM_SLUG_BUTTON_DEACTIVATE_TEMPLATE; ?>" class='button button-primary' type='submit' value='<?php echo SMM_PAGE_TEMPLATE_DEACTIVATE; ?>' />
            <?php } ?>   
        </p>

    </form>
</div>