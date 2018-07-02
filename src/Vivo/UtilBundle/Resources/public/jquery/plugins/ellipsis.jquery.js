Ellipsis = {
    init: function() {
        jQuery('.ellipsis').each(function(){
            jQuery(this).attr('title', jQuery(this).text().trim());

            if (jQuery(this).hasClass('full-screen')) {
                jQuery(this).css('max-width', (parseFloat(jQuery(window).width()) - 50));
            }
        });
    }
};
