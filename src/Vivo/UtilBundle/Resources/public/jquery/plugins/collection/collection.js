jQuery(document).ready(function() {
    function updateRanks(collectionContainer)
    {
        var rank = 1;
        collectionContainer.find('input.rank').each(function() {
            jQuery(this).val(rank++);
        });
    }

    var sortableCollections = jQuery('.collection-container.is-sortable');

    if (sortableCollections.size() > 0) {
        var fixHelper = function(e, ui) {
            ui.children().each(function() {
                jQuery(this).width(jQuery(this).width());
            });

            return ui;
        };

        sortableCollections.each(function() {
            jQuery('.collection tbody.collection-body:first', $(this)).sortable({
                helper:      fixHelper,
                placeholder: 'placeholder',
                handle:      '.handle',
                update:      function (event, ui) {
                    updateRanks(ui.item.closest('.collection-container'));
                }
            });
        });
    }

    jQuery('.collection-container')
        .delegate('.collection-delete', 'click', function(event){
            event.preventDefault();

            var collectionContainer = jQuery(this).parents('.collection-container:first');
            jQuery(this).parents('.collection-row:first').remove();

            if (collectionContainer.find('.collection-row:first').size() < 1) {
                collectionContainer.find('.collection:first').hide();
                collectionContainer.find('.collection-empty:first').show();
            }
        })
        .delegate('input.collection-remove', 'click', function(event){
            var fields = jQuery(this).parents('.collection-row:first')
                .find('input, select, textarea');

            if (jQuery(this).is(":checked")) {
                fields.not('.collection-remove').attr('disabled', 'disabled');

                fields.not('.collection-remove-name-set').each(function() {
                    jQuery(this).addClass('collection-removal')
                        .data('remove-name', jQuery(this).attr('name'))
                        .attr('name', 'collection-removal');
                });
            } else {
                fields.not('.collection-remove').removeAttr('disabled', 'disabled');

                fields.each(function() {
                    if (jQuery(this).data('remove-name')) {
                        jQuery(this).attr('name', jQuery(this).data('remove-name'));
                    }
                });
            }
        })
        .delegate('.collection-add', 'click', function(event){
            event.preventDefault();

            var collectionContainer = jQuery(this).parents('.collection-container:first');
            var html                = collectionContainer.data('prototype');
            var index               = 0;

            var emptyDiv   = jQuery('<div/>');
            var firstInput = emptyDiv.append(html).find('input, textarea, select').eq(0);

            if (firstInput.attr('id')) {
                while (jQuery('#' + firstInput.attr('id').replace(/__name__/g, index)).size() > 0) {
                    index++;

                    if (index > 1000){
                        break;
                    }
                }
            }

            collectionContainer.find('.collection-empty:first').hide();
            collectionContainer.find('.collection .collection-body:first').append(html.replace(/__name__/g, index));
            collectionContainer.find('.collection:first').show();

            updateRanks(jQuery(this).closest('.collection-container'));
        })
    ;
});