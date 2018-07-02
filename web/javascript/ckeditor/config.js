/**
 * @license Copyright (c) 2003-2013, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
    // Define changes to default configuration here. For example:
    // config.language = 'fr';
    // config.uiColor = '#AADC6E';

    config.stylesSet = [
        { name: 'Primary Button Link', element: 'a', attributes: {'class': 'btn btn-primary'} },
        { name: 'Intro', element: 'p', attributes: {'class': 'intro'} },
        { name: 'Full Width Image', element: 'img', attributes: {'class': 'full-width-image'} },
        { name: 'Inline Image', element: 'img', attributes: {'class': 'inline-image'} }
    ];
};
