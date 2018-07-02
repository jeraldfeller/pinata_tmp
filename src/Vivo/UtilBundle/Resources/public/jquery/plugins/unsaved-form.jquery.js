(function($) {
    $.fn.vivoUtilUnsavedForm = function(options) {
        var settings = $.extend({
            message : 'You have unsaved changes on this page.'
        }, options);

        this.each( function() {
            var form = jQuery(this);

            form.data('original-data', form.serialize()).addClass('unsaved-form-check');
            form.data('unsaved-form-message', settings.message);

            jQuery('body').delegate('form.unsaved-form-check', 'submit' ,function() {
                jQuery(this).addClass('unsaved-form-is-submitting');
            });
        });
    };

    var existingHandler = window.onbeforeunload;
    window.onbeforeunload = function(event) {
        if (existingHandler) {
            existingHandler(event);
        }

        var message;

        jQuery('form.unsaved-form-check').each(function() {
            if (jQuery(this).hasClass('unsaved-form-is-submitting')) {
                jQuery(this).removeClass('unsaved-form-is-submitting');
            } else {
                if ($(this).serialize() !== $(this).data('original-data')) {
                    message = jQuery(this).data('unsaved-form-message');
                }
            }
        });

        return message;
    }
}(jQuery));
