<div class="wrap">
    <h2 id="smm-maintenance">Mode maintenance</h2>

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

        $smm_maintenance = get_site_option('smm_maintenance' , "no" );
        switch( $smm_maintenance ) {
            case 'yes': ?>
                <div id="message" class="error">
                    <p>
                        <strong>
                            Attention, le mode maintenance est activé sur tout le réseau. Ce site apparaît donc en mode maintenance, quelque soit sa configuration spécifique.
                        </strong>
                    </p>
                </div>
            <?php
                break;
            }

        $smm_maintenance = get_blog_option(get_current_blog_id(), 'smm_maintenance' , "no" );
        switch( $smm_maintenance ) {
            case 'no':
                $action = SMM_SLUG_ACTION_ACTIVATE;
                $button = SMM_PAGE_MAINTENANCE_ACTIVATE;
                break;
            case 'yes':
            default :
                $action = SMM_SLUG_ACTION_DEACTIVATE;
                $button = SMM_PAGE_MAINTENANCE_DEACTIVATE;
                break;
            }
    ?>

    <form method="post" action="<?php echo admin_url('options-general.php?page=' . SMM_SLUG_ACTION . '&action=' . $action); ?>">

    
        <?php wp_nonce_field( SMM_DOMAIN ); ?>

        <p>
            Lorsque le mode maintenance est activé, seuls les administrateurs connectés peuvent voir le site.
        </p>

        <p class="submit">
            <input class='button button-primary' type='submit' value=' <?php echo $button; ?>' />
        </p>

    </form>

    <h3 id="smm-maintenance">Custom maintenance template</h3>
    <form method="post" action="<?php echo admin_url('options-general.php?page=' . SMM_SLUG_ACTION . '&action=' . SMM_SLUG_PATH_ACTION); ?>">

    
        <?php wp_nonce_field( SMM_DOMAIN ); ?>

        <p>
            Pour utiliser une page de maintenance personnalisée pour ce site, indiquez le chemin du template :
        </p>

        <p>
            <?php
            $dirs = explode('/', WP_CONTENT_DIR);
            $content_dir = end($dirs);
            echo $content_dir . '/';
            $custom_template = get_blog_option(get_current_blog_id(), 'smm_maintenance_template_path' , "" );
            ?>
            <input name="template-path" type="text" class="regular-text" title="Chemin du template"  value="<?php echo $custom_template; ?>"/>
        </p>

        <p class="submit">
            <input id="deactivate-template" name="activate-template" class='button button-primary' type='submit' value='Enregistrer le template' />
            <?php if(!empty($custom_template)) { ?>
                <input id="deactivate-template" name="deactivate-template" class='button button-primary' type='submit' value='Désactiver le template personnalisé' />
            <?php } ?>   
        </p>

    </form>
</div>