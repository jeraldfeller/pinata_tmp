PlaceholderClone = {
    setFields: function() {
        jQuery('input[data-placeholder-clone]').not('.placeholder-clone-set').each(function(){
            $(this).addClass('placeholder-clone-set');

            if (jQuery(this).data('placeholder-clone') && jQuery(jQuery(this).data('placeholder-clone')).size() > 0) {
                var child = jQuery(this);
                jQuery(jQuery(this).data('placeholder-clone')).keyup(function(e) {
                    child.attr('placeholder', $(this).val() ? $(this).val() : $(this).attr('placeholder')).trigger('keyup');
                }).trigger('keyup');
            }
        });
    }
};
