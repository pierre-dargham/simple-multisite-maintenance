<div class="wrap">

	<h2 id="smm-maintenance"><?php echo SMM_Lib::smm_admin(SMM_NETWORK_PAGE_MAINTENANCE_TITLE, SMM_PAGE_MAINTENANCE_TITLE); ?></h2>

    <?php

    if(isset($form_error)) { SMM_Lib::updated_message($form_error, true); }
    else if(isset($form_message)) { SMM_Lib::updated_message($form_message); }
    
    ?>


	<form method="post" action="<?php echo SMM_Lib::smm_admin( network_admin_url('settings.php?page=' . SMM_SLUG_ACTION), admin_url('options-general.php?page=' . SMM_SLUG_ACTION) ); ?>">

		<?php wp_nonce_field( SMM_DOMAIN ); ?>

    <table class="form-table">

    <tr>
        <th scope="row">Réglages généraux</th>
        <td><fieldset>
            <legend class="screen-reader-text"><span>Réglages généraux</span></legend>
            <label for="<?php echo SMM_SLUG_MODE_OPTION; ?>"><input name="<?php echo SMM_SLUG_MODE_OPTION; ?>" type="checkbox" id="<?php echo SMM_SLUG_MODE_OPTION; ?>" value="yes" <?php echo $maintenance_mod_checked; ?> /><?php echo SMM_PAGE_MAINTENANCE_ACTIVATE; ?></label>
            <br />
            <label for="<?php echo SMM_SLUG_OPTION_ADMIN_CAN_VIEW; ?>"><input name="<?php echo SMM_SLUG_OPTION_ADMIN_CAN_VIEW; ?>" type="checkbox" id="<?php echo SMM_SLUG_OPTION_ADMIN_CAN_VIEW; ?>" value="yes" <?php echo $admin_can_view_checked; ?> <?php echo $admin_can_view_disabled; ?>><?php echo SMM_PAGE_ADMIN_CAN_VIEW; ?></label>
        </fieldset></td>
    </tr>

<tr>
<th scope="row">Page de maintenance</th>
<td>
    <fieldset><legend class="screen-reader-text"><span>Page de maintenance</span></legend>

    <?php if ( SMM_Lib::is_network_activated() ) { ?>
    <label title='Converver les réglages du réseau'><input type='radio' name='<?php echo SMM_SLUG_OPTION_TEMPLATE_MOD; ?>' id="smm_template_mod_network" value='smm_template_mod_network' <?php echo $template_mod_network_checked; ?> /> <span>Converver les réglages du réseau</span></label>
    <br />
    <?php } ?>

    <label title='Utiliser le fichier suivant'><input type='radio' name='<?php echo SMM_SLUG_OPTION_TEMPLATE_MOD; ?>' id="smm_template_mod_path" value='smm_template_mod_path' <?php echo $template_mod_path_checked; ?> /> <span>Utiliser le fichier suivant</span></label><br />
    <?php echo SMM_Lib::content_dir(); ?>
    <input style="width: 87%;" name="<?php echo SMM_SLUG_INPUT_PATH; ?>" type="text" id="smm_template_path" <?php echo $template_path_disabled; ?> value="<?php echo $template_path_content; ?>"/>

    <br />

    <label title='Afficher le code suivant :'><input type="radio" name="<?php echo SMM_SLUG_OPTION_TEMPLATE_MOD; ?>" id="smm_template_mod_bloc" value="smm_template_mod_bloc" <?php echo $template_mod_bloc_checked; ?> />Afficher le code suivant :</label>
    <textarea style="margin-left: 20px; width: 95%;" name="smm_template_bloc" rows="12" cols="30" id="smm_template_bloc" class="code" <?php echo $template_bloc_disabled; ?> ><?php echo $template_bloc_content; ?> </textarea>

    </fieldset>
</td>
</tr>

    </table>
 


        <?php echo SMM_Lib::smm_admin('<p> ' . SMM_TEXT_MAINTENANCE_TEMPLATE_WARNING . '</p>', ""); ?>

        <p class="submit">
            <input name="<?php echo SMM_SLUG_BUTTON_ACTIVATE_TEMPLATE; ?>" class='button button-primary' type='submit' value='<?php echo __('Save Changes'); ?>' /> 
        </p>

    </form>
</div>