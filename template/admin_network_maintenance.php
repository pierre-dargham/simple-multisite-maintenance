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
            Activer le mode maintenance sur l'ensemble du réseau permet de mettre tout le multisite en mode maintenance.
            <br /><br />Attention, désactiver le mode maintenance sur l'ensemble du réseau ne désactive pas le mode maintenance pour chacun des sites.
        </p>

        <p class="submit">
            <input class='button button-primary' type='submit' value=' <?php echo $button; ?>' />
        </p>

    </form>

      <h3 id="smm-maintenance">Custom maintenance template</h3>
    <form method="post" action="<?php echo network_admin_url('settings.php?page=' . SMM_SLUG_NETWORK_ACTION . '&action=' . SMM_SLUG_PATH_ACTION); ?>">

    
        <?php wp_nonce_field( SMM_DOMAIN ); ?>

        <p>
            Pour utiliser une page de maintenance personnalisée pour le réseau, indiquez le chemin du template :
        </p>

        <p>
            <?php
            $dirs = explode('/', WP_CONTENT_DIR);
            $content_dir = end($dirs);
            echo $content_dir . '/';
            $custom_template = get_site_option('smm_maintenance_template_path' , "" );
            ?>

            <input name="template-path" type="text" class="regular-text" title="Chemin du template"  value="<?php echo $custom_template; ?>"/>
        </p>

        <p>
            Attention, si vous avez configuré un template personnalisé pour un sous-site, c'est ce template qui sera utilisé, et non celui du réseau.
        </p>

        <p class="submit">
            <input id="deactivate-template" name="activate-template" class='button button-primary' type='submit' value='Enregistrer le template' />
            <?php if(!empty($custom_template)) { ?>
                <input id="deactivate-template" name="deactivate-template" class='button button-primary' type='submit' value='Désactiver le template personnalisé' />
            <?php } ?>   
        </p>

    </form>
</div>