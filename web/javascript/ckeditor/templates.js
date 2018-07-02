// Register a template definition set named "default".
CKEDITOR.addTemplates( 'default', {
    // The name of the subfolder that contains the preview images of the templates.
    imagesPath : CKEDITOR.getUrl( CKEDITOR.plugins.getPath( 'templates' ) + 'templates/images/' ),

    // Template definitions.
    templates : [
        {
            title: '2 Column Content',
            description: '2 Column Content',
            html:
            '<p>&nbsp;</p>'+
            '<div class="equal-cols" contenteditable="false" unselectable="on">'+
                '<div class="col" contenteditable="false" unselectable="on">'+
                    '<div class="inner-content" contenteditable="true" unselectable="off">'+
                        '<p>Left column.</p>'+
                    '</div>'+
                '</div>'+
                '<div class="col" contenteditable="false" unselectable="on">'+
                    '<div class="inner-content" contenteditable="true" unselectable="off">'+
                        '<p>Right column.</p>'+
                    '</div>'+
                '</div>'+
            '</div>'+
            '<p>&nbsp;</p>'
        }
    ]
});
