jQuery(document).ready(function(){
    jQuery('i[data-toggle="tooltip"], a[data-toggle="tooltip"]').not('[data-original-title]').tooltip();

    jQuery('i[data-toggle="popover"], a[data-toggle="popover"]').not('[data-original-title]').popover();

    jQuery(document).mouseup(function(e){
        var popocontainer = jQuery(".popover");
        if (popocontainer.has(e.target).length === 0){
            jQuery('.popover').toggleClass('in').remove();
        }
    });

    Ellipsis.init();

    $('.admin-nav.disable-active-subnav .nav.navbar-nav ul .active').removeClass('active');

    jQuery('form')
        .delegate('select.auto-submit, input[type=file].auto-submit', 'change', function(e){
            e.preventDefault();

            jQuery(this).closest('form').submit();
        })
        .delegate('input', 'keydown', function(e){
            if ($(this).attr('name')) {
                var keyCode = e.keyCode || e.which;

                if ("undefined" !== typeof keyCode && 13 === keyCode) {
                    var form = $(this).parents('form:first');

                    if (form.find('button.btn-primary:first')) {
                        form.find('button.btn-primary:first').trigger('click');

                        event.preventDefault();
                        return false;
                    }
                }
            }
        })
        .delegate('a.submit-form', 'click', function(e){
            e.preventDefault();

            jQuery(this).closest('form').submit();
        })
    ;

    jQuery('a.fancy-image').fancybox();

    jQuery(":file").each(function() {
        var buttonText  = jQuery(this).data('button-text')  ? jQuery(this).data('button-text')  : 'Upload';
        var textField   = jQuery(this).data('text-field')   ? jQuery(this).data('text-field')   : false;
        var classButton = jQuery(this).data('class-button') ? jQuery(this).data('class-button') : '';
        var classText   = jQuery(this).data('class-text')   ? jQuery(this).data('class-text')   : '';
        var icon        = jQuery(this).data('icon')         ? jQuery(this).data('icon')         : true;
        var classIcon   = jQuery(this).data('class-icon')   ? jQuery(this).data('class-icon')   : 'icon-arrow-up';

        jQuery(this).filestyle({
            'buttonText'  : buttonText,
            'textField'   : textField,
            'classButton' : classButton,
            'classText'   : classText,
            'icon'        : icon,
            'classIcon'   : classIcon
        });
    });

    jQuery(document).delegate('a.confirm', 'click', function(e){
        e.preventDefault();

        var url = jQuery(this).attr('href');
        var msg = jQuery(this).data('confirm-message') ? jQuery(this).data('confirm-message') : "Are you sure?";

        bootbox.confirm(msg, function(result) {
            if (true == result) {
                window.location.href = url;
            }
        });
    });

    jQuery('#search_list_results').delegate('table tbody tr td:not(.no-click)', 'dblclick', function(e) {
        var parent = $(this).parents('tr:last');

        var link = parent.find('td:last .btn-group a.btn:first:not(.confirm)');

        if (1 == link.size() && link.attr('href')) {
            if ("undefined" !== typeof e.button && 1 == e.button) {
                window.open(
                    link.attr('href'),
                    '_blank'
                );
            } else {
                window.open(
                    link.attr('href'),
                    '_self'
                );
            }

            return false;
        }

        return true;
    });

    jQuery('.nested-tree').delegate('.item-container', 'dblclick', function(e) {
        if (jQuery(this).find('.nav:first .dropdown-menu:first li a:not(.confirm)').size() > 0) {
            jQuery(this).find('.nav:first .dropdown-menu:first li a:not(.confirm)').each(function() {
                if (jQuery(this).attr('href')) {
                    if ("undefined" !== typeof e.button && 1 == e.button) {
                        window.open(
                            jQuery(this).attr('href'),
                            '_blank'
                        );
                    } else {
                        window.open(
                            jQuery(this).attr('href'),
                            '_self'
                        );
                    }

                    return false;
                }

                return true;
            });
        }
    });
});

jQuery(window).load(function() {
    try {
        jQuery('form[method=post]:not(.ignore-unsaved-form)').vivoUtilUnsavedForm();
    } catch(err) {

    }
});

jQuery(window).resize(function(){
    Ellipsis.init();
});