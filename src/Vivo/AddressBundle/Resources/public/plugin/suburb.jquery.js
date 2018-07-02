(function($) {
    $.fn.vivoAddressSuburb = function(options) {
        var settings = $.extend({
            defaultValue         : '',
            country              : '',
            postcodeElement      : null,
            stateElement         : null,
            selectSuburbLabel    : '',
            emptyPostcodeLabel   : '',
            invalidPostcodeLabel : '',
            requestUrl           : null,
            getSuburbLabel       : function (result) {
                return result.name + ", " + result.state;
            }
        }, options);

        this.each( function() {
            if (null === settings.postcodeElement || settings.postcodeElement.size() < 1) {
                console.error('Postcode element cannot be found.');

                return;
            }

            var suburbField = $(this);
            var postcodeElement = settings.postcodeElement;
            var lastPostcode = postcodeElement.val();
            var requestTimeout = null;
            var ajaxRequest = null;

            if (settings.suburbElement && settings.suburbElement.size() > 0 && settings.stateElement && settings.stateElement.size() > 0) {
                settings.suburbElement.change(function() {
                    if ($(this).find(':selected').data('state')) {
                        settings.stateElement.val($(this).find(':selected').data('state'));
                    }
                });
            }

            postcodeElement.keyup(function(e) {
                if ($(this).val() === lastPostcode && false !== lastPostcode) {
                    return;
                }

                if (!parseInt($(this).val())) {
                    suburbField.html('<option>' + settings.invalidPostcodeLabel + '</option>');
                    lastPostcode = $(this).val();

                    if (requestTimeout) {
                        clearTimeout(requestTimeout);
                    }

                    if (ajaxRequest) {
                        ajaxRequest.abort();
                    }

                    return;
                }

                suburbField.html('<option>Loading suburbs...</option>');

                if (requestTimeout) {
                    clearTimeout(requestTimeout);
                }

                if (ajaxRequest) {
                    ajaxRequest.abort();
                }

                requestTimeout = setTimeout(function(){
                    ajaxRequest = $.ajax({
                        url      : settings.requestUrl,
                        type     : "GET",
                        dataType : "json",
                        data     : {
                            countryCode : settings.countryCode,
                            postcode     : postcodeElement.val()
                        },
                        error: function(response, error){
                            lastPostcode = false;

                            if ("abort" != error) {
                                suburbField.html('<option>Error. Try again.</option>');
                            }
                        },
                        success: function(response){
                            var results = $(response.results);
                            lastPostcode = postcodeElement.val();
                            var html = '';

                            if (results.size() < 1) {
                                html += '<option>' + settings.invalidPostcodeLabel + '</option>';
                            } else {
                                html += '<option>' + settings.selectSuburbLabel + '</option>';

                                results.each(function(key, suburb) {
                                    var label = '';

                                    if ($.isFunction(settings.getSuburbLabel) ) {
                                        label = settings.getSuburbLabel.call(this, suburb);
                                    }

                                    html += '<option value="' + suburb.name + '" data-state="' + suburb.state + '">' + label + '</option>';
                                });
                            }

                            suburbField.html(html);

                            var selected = suburbField.find('option[value="' + settings.defaultValue + '"]:first');
                            if (1 !== selected.size()) {
                                selected = suburbField.find('option:first');
                            }
                            suburbField.val(selected.val());
                        }
                    });
                }, 200);
            });
        });
    }
}(jQuery));