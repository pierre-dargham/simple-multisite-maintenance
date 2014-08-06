jQuery(document).ready(function() {

    jQuery('#smm_maintenance_mod').change(function() {
        if(jQuery(this).is(":checked")) {
            jQuery('#smm_admin_can_view').prop('disabled', false);

        }
        else {
            jQuery('#smm_admin_can_view').prop('disabled', true);        }
    });

    jQuery('#smm_template_mod_network').change(function() {
        if(jQuery(this).is(":checked")) {
            jQuery('#smm_template_path').prop('disabled', true);
            jQuery('#smm_template_bloc').prop('disabled', true);
        }   
    });

    jQuery('#smm_template_mod_path').change(function() {
        if(jQuery(this).is(":checked")) {
            jQuery('#smm_template_path').prop('disabled', false);
            jQuery('#smm_template_bloc').prop('disabled', true);
        }   
    });

    jQuery('#smm_template_mod_bloc').change(function() {
        if(jQuery(this).is(":checked")) {
            jQuery('#smm_template_path').prop('disabled', true);
            jQuery('#smm_template_bloc').prop('disabled', false);
        }   
    });

});