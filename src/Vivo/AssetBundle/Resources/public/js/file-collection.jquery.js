(function($) {
    $.fn.vivoFileCollection = function(options) {
        var errorCount = 0;

        var settings = $.extend({
            upload_path   : '',
            upload_expiry : null,
            asset_class   : '',
            csrf          : '',
            error_timeout : 0,
            multiple      : true,
            updateRanks   : function () {
                var rank = 0;
                this.find('input.rank').each(function() {
                    jQuery(this).val(rank);
                    rank++;
                });
            },
            displayErrors: function (file, errors) {
                for (i in errors) {
                    errorCount++;

                    var error = errors[i];
                    var errorBox = this.find('.errors:first .clone-me:first')
                        .clone()
                        .removeClass('clone-me')
                        .prependTo(this.find('.errors:first'));

                    if (file) {
                        errorBox.find('.message').html(file.name + ': ' + error);
                    } else {
                        errorBox.find('.message').html(error);
                    }

                    errorBox.fadeIn('fast');

                    if (settings.error_timeout > 0) {
                        setTimeout(function() {
                            errorBox.fadeOut(1000, function () {
                                errorBox.remove();
                                errorCount--;
                            });
                        }, (settings.error_timeout + (250 * errorCount)));
                    }
                }
            },
            sanitiseResponse: function (responseText) {
                var response = null;
                try {
                    response = JSON.parse(responseText);
                } catch (e) {
                }

                if (response && 'object' == typeof response) {
                    return response;
                }

                return response;
            },
            uploadSuccess: function (file, response) {
                var assetContainer = this.find('.asset-container:first .assets:first');
                var index = 0;

                if (this.data('index')) {
                    index = parseInt(this.data('index')) + 1;
                } else {
                    if (this.find('.asset').size() > 0) {
                        index = this.find('.asset').size();
                    }
                }

                var htmlElement = jQuery('<div/>');

                htmlElement.html(this.data('prototype'));
                htmlElement.html(htmlElement.html().replace(/__name__/g, index));
                this.data('index', index);

                for (i in response.asset) {
                    var field = htmlElement.find('input.' + i + ', textarea.' + i + ', select.' + i);
                    if (1 == field.size()) {
                        field.attr('value', response.asset[i]);
                    }
                }

                htmlElement.find('.preview').html("<a href=\"" + response.preview.link + "\" class=\"" + response.preview['class'] + "\" title=\"" + response.asset.title + "\" target=\"_blank\"><img src=\"" + response.preview.image + "\" alt=\"\" class=\"drag-handle\" /></a>");
                htmlElement = htmlElement.find('.asset');

                this.find('.empty').fadeOut('fast');
                assetContainer.append(htmlElement);

                if (!settings.multiple && assetContainer.find('.asset').size() > 0) {
                    this.find('.btn-toolbar.uploader').hide();
                } else {
                    this.find('.btn-toolbar.uploader').show();
                }

                if ($.isFunction(settings.updateAssetView) ) {
                    settings.updateAssetView.call(this, htmlElement, htmlElement.find('.asset-form'));
                }
            },
            updateAssetView: function(assetContainer, assetForm) {
                assetForm.find(':input').each(function() {
                    var value;

                    if ($(this).is('select')) {
                        value = $(this).find('option:selected').text();
                    } else {
                        value = $(this).val();
                    }

                    if (value.length > 0) {
                        assetContainer.find('span.copy-' + $(this).attr('id')).text(value).attr('title', value);
                    } else {
                        assetContainer.find('span.copy-' + $(this).attr('id')).each(function() {
                            value = $(this).data('placeholder');
                            if ('undefined' === typeof value) {
                                value = '-';
                            }

                            $(this).text(value).attr('title', value);
                        });
                    }
                });
            }
        }, options);

        this.each( function() {
            var fileCollectionContainer = jQuery(this);

            fileCollectionContainer
                .delegate('.vivo-asset-asset-file-fancy .cancel', 'click', function(){
                    $.fancybox.close();
                })
                .delegate('.vivo-asset-asset-file-fancy .save', 'click', function(){
                    jQuery('.fancybox-inner .asset-form input, .fancybox-inner .asset-form textarea, .fancybox-inner .asset-form select').each(function() {
                        if ('radio' == jQuery(this).attr('type') || 'checkbox' == jQuery(this).attr('type')) {
                            jQuery(this).data('value', jQuery(this).is(":checked") ? "1" : "0");
                        } else {
                            jQuery(this).data('value', jQuery(this).val());
                        }
                    });

                    $.fancybox.close();
                })
                .delegate('.vivo-asset-asset-file-fancy input', 'keydown', function(e) {
                    var code = parseInt(e.keyCode || e.which);

                    if (code == 27) {
                        $.fancybox.close();
                    } else if (code == 13) {
                        jQuery(this).parents('.vivo-asset-asset-file-fancy:first').find('.save').trigger('click');
                        e.preventDefault();

                        return false;
                    }
                })
            ;

            fileCollectionContainer.find('a.fancybox').fancybox();

            fileCollectionContainer.on('click', 'a.edit', function() {
                $.fancybox({
                    href: $(this).attr('href'),
                    parent: "#" + fileCollectionContainer.attr('id'),
                    beforeShow: function() {
                        console.log(fileCollectionContainer.attr('id'));
                        jQuery('.fancybox-inner .asset-form input, .fancybox-inner .asset-form textarea, .fancybox-inner .asset-form select').each(function() {
                            var attr = jQuery(this).data('value');

                            if (typeof attr === 'undefined' || attr === false) {
                                if ('radio' == jQuery(this).attr('type') || 'checkbox' == jQuery(this).attr('type')) {
                                    jQuery(this).data('value', jQuery(this).is(":checked") ? "1" : "0");
                                } else {
                                    jQuery(this).data('value', jQuery(this).val());
                                }
                            }
                        });
                    },
                    afterShow: function() {
                        jQuery('.fancybox-inner .asset-form input:not([type="hidden"]):first').focus();
                    },
                    beforeClose: function() {
                        var assetForm = jQuery('.fancybox-inner .asset-form');

                        assetForm.find('input, textarea, select').each(function() {
                            if ('radio' == jQuery(this).attr('type') || 'checkbox' == jQuery(this).attr('type')) {
                                jQuery(this).val(jQuery(this).is(":checked") ? "1" : "0");
                            } else {
                                jQuery(this).val(jQuery(this).data('value'));
                            }
                        });

                        if ($.isFunction(settings.updateAssetView) ) {
                            settings.updateAssetView.call(fileCollectionContainer, jQuery('#' + assetForm.data('asset-container')), assetForm);
                        }
                    }
                });

                return false;
            });

            if (!fileCollectionContainer.hasClass('single')) {
                fileCollectionContainer.find('.assets').sortable({
                    'handle': '.drag-handle',
                    'placeholder': 'placeholder',
                    'forcePlaceholderSize': true,
                    'update': function (event, ui) {
                        if ($.isFunction(settings.updateRanks)) {
                            settings.updateRanks.call(fileCollectionContainer);
                        }
                    }
                });
            }

            jQuery(this)
                .delegate('.remove', 'click', function(e) {
                    e.preventDefault();

                    var assetContainer = jQuery(this).parents('.asset-container:first');

                    jQuery(this).parents('.asset:first').fadeOut('fast', function() {
                        jQuery(this).remove();

                        if (assetContainer.find('.asset').size() < 1) {
                            assetContainer.find('.empty').fadeIn('fast');

                            fileCollectionContainer.find('.btn-toolbar.uploader').show();
                        }
                    });
                })
                .delegate('input[type=file]', 'change', function(e) {
                    e.preventDefault();

                    var fileInput  = jQuery(this);
                    var files      = document.getElementById(fileInput.attr('id')).files;
                    var fileNum    = 0;

                    if (files.length < 1) {
                        fileInput.val('');

                        return;
                    }

                    var maxFiles = settings.multiple ? files.length : 1;

                    if (!settings.multiple && fileCollectionContainer.find('.asset').size() > 0) {
                        fileInput.val('');

                        return;
                    }

                    do {
                        // Clone the progress bar
                        var progressContainer = fileCollectionContainer.find('.progress-bars:first .clone-me:first')
                            .clone()
                            .removeClass('clone-me')
                            .appendTo(fileCollectionContainer.find('.progress-bars:first'))
                            .fadeIn('fast');

                        progressContainer.find('.filename:first')
                            .text(files[fileNum].name);

                        var file = files[fileNum];
                        fileNum++;

                        (function(uploadUrl, file, fileCollectionContainer, progressContainer) {
                            var xhr = new XMLHttpRequest();
                            var fd  = new FormData();

                            fd.append('file', file);
                            fd.append('asset_class', settings.asset_class);
                            fd.append('upload_expiry', settings.upload_expiry);
                            fd.append('csrf_token', settings.csrf_token);

                            xhr.upload.addEventListener("progress", function (event) {
                                var progressBar = progressContainer.find('.bar:first');

                                if (event.lengthComputable) {
                                    var percentComplete = Math.round(event.loaded * 100 / event.total);
                                    var percentString   = percentComplete.toString() + '%';

                                    progressBar.css('width', percentString).text(percentString);
                                }
                                else {
                                    progressBar.css('width', '100%').text('Uploading...');
                                }
                            }, false);

                            xhr.addEventListener("load", function (event) {
                                if ("200" != event.target.status) {
                                    if ($.isFunction(settings.displayErrors) ) {
                                        settings.displayErrors.call(fileCollectionContainer, file, [event.target.statusText + ' (' + event.target.status + ')']);
                                    }
                                } else {
                                    response = null;
                                    if ($.isFunction(settings.sanitiseResponse) ) {
                                        response = settings.sanitiseResponse.call(fileCollectionContainer, event.target.responseText);
                                    }

                                    if (null == response) {
                                        if ($.isFunction(settings.displayErrors) ) {
                                            settings.displayErrors.call(fileCollectionContainer, file, ['Invalid response from server.']);
                                        }
                                    } else {
                                        if (response.errors && response.errors.length > 0) {
                                            if ($.isFunction(settings.displayErrors) ) {
                                                settings.displayErrors.call(fileCollectionContainer, file, response.errors);
                                            }
                                        } else {
                                            if ($.isFunction(settings.uploadSuccess) ) {
                                                settings.uploadSuccess.call(fileCollectionContainer, file, response);
                                            }

                                            if ($.isFunction(settings.updateRanks) ) {
                                                settings.updateRanks.call(fileCollectionContainer);
                                            }
                                        }


                                    }
                                }

                                progressContainer.fadeOut('fast', function() {
                                    jQuery(this).remove();
                                });
                            }, false);

                            xhr.addEventListener("error", function (event) {
                                if ($.isFunction(settings.displayErrors) ) {
                                    settings.displayErrors.call(fileCollectionContainer, file, ['An unexpected error occurred.']);
                                }

                                progressContainer.fadeOut('fast', function() {
                                    jQuery(this).remove();
                                });
                            }, false);

                            xhr.addEventListener("abort", function (event) {
                                progressContainer.fadeOut('fast', function() {
                                    jQuery(this).remove();
                                });
                            }, false);

                            xhr.open("POST", uploadUrl, true);
                            xhr.send(fd);
                        })(settings.upload_path, file, fileCollectionContainer, progressContainer);
                    } while (fileNum < maxFiles);

                    fileInput.val('');
                })
            ;
        });
    }
}(jQuery));
