jQuery(document).ready(function() {
    jQuery('input.vivo_admin_change_password').change(function() {
        if (jQuery(this).is(':checked')) {
            jQuery('div.vivo_admin_change_password').stop(true, true).slideDown('fast');
        } else {
            jQuery('div.vivo_admin_change_password').stop(true, true).slideUp('fast');
        }
    }).change();
});
