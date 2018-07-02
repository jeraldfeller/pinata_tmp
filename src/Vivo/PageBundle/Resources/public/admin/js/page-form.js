jQuery(document).ready(function(){
    PlaceholderClone.setFields();

    jQuery('select.page_type_instance').change(function(e){
        e.preventDefault();

        if (typeof CKEDITOR !== 'undefined') {
            for (instance in CKEDITOR.instances) {
                CKEDITOR.instances[instance].updateElement();
            }
        }

        jQuery(this).closest('form').find('button.soft_post').trigger('click');
    });
});

jQuery(document).bind("DOMNodeInserted",function(){
    PlaceholderClone.setFields();
});
