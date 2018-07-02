jQuery(document).ready(function(){
    jQuery('form').delegate('input.vivo_site_site_domain_primary', 'click', function(){
        jQuery('input.vivo_site_site_domain_primary').not(this).prop('checked', false);
        jQuery(this).prop('checked', true);
    });
});
