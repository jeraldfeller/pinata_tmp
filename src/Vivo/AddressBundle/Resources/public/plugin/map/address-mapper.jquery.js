(function($) {
    $.fn.vivoAddressMapper = function(options) {
        var mapContainer = null;
        var map = null;
        var marker = null;
        var mapInitiated = false;
        var refreshingMap;

        var settings = $.extend({
            googleApiBrowserKey: null,
            latitudeElement  : null,
            longitudeElement : null,
            zoomElement      : null,
            addressElements  : {
                countryCode  : null,
                addressLine1 : null,
                addressLine2 : null,
                postcode     : null,
                suburb       : null
            },
            mapOptions       : {
                mapTypeId: google.maps.MapTypeId.ROADMAP,
                scrollwheel: false
            },
            zoomDefaults     : {
                ROOFTOP            : 18,
                RANGE_INTERPOLATED : 16,
                GEOMETRIC_CENTER   : 14,
                APPROXIMATE        : 14
            },
            emptyDefaults : {
                latLng : new google.maps.LatLng(-25.1651733, 134.4726563),
                zoom   : 3
            }
        }, options);

        function getAddressString()
        {
            var address = '';

            if(settings.addressElements.addressLine1.val()){
                address += settings.addressElements.addressLine1.val() + ' ';
            }
            if (settings.addressElements.addressLine2.val()) {
                address += settings.addressElements.addressLine2.val() + ' ';
            }
            if(settings.addressElements.suburb.val()){
                address += settings.addressElements.suburb.val() + ' ';
            }
            if(settings.addressElements.state.val()){
                address += settings.addressElements.state.val() + ' ';
            }
            if(settings.addressElements.postcode.val()){
                address += settings.addressElements.postcode.val() + ' ';
            }

            address = $.trim(address);

            return address.length > 0 ? address : false;
        }

        function initMap()
        {
            if (true == mapInitiated) {
                return;
            }

            mapInitiated = true;
            if (settings.latitudeElement.size() < 1) {
                console.error('Latitude element cannot be found.');
                return;
            } else if (settings.longitudeElement.size() < 1) {
                console.error('Longitude element cannot be found.');
                return;
            } else if (settings.zoomElement.size() < 1) {
                console.error('Zoom element cannot be found.');
                return;
            }

            var mapCanvas, latitude, longitude, zoom, latLng;

            mapCanvas = mapContainer.find('.map-canvas:first');

            if (mapCanvas.size() < 1) {
                console.error('.map-canvas could not be found.');
                return;
            }

            latitude = parseFloat(settings.latitudeElement.val() ? settings.latitudeElement.val() : 0);
            longitude = parseFloat(settings.longitudeElement.val() ? settings.longitudeElement.val() : 0);
            zoom = parseInt(settings.zoomElement.val() ? settings.zoomElement.val() : 0);

            if (0 == zoom) {
                zoom = settings.emptyDefaults.zoom;
            }

            if (0 == latitude && 0 == longitude) {
                // No location set
                if (false !== getAddressString()) {
                    mapContainer.find('.map-message:first').html('<div class="alert alert-danger"><strong>Error: </strong> Could not determine the latitude/longitude automatically.');
                }

                latLng = settings.emptyDefaults.latLng;
                zoom = settings.emptyDefaults.zoom;
            } else {
                latLng = new google.maps.LatLng(latitude, longitude);
            }

            var mapOptions = $.extend(settings.mapOptions, {
                zoom   : zoom,
                center : latLng
            });

            map = new google.maps.Map(mapCanvas.get(0), mapOptions);

            marker = new google.maps.Marker({
                position  : latLng,
                map       : map,
                title     : 'Drag to move',
                draggable : true
            });

            marker.setMap(map);

            google.maps.event.addListener(marker, 'dragend', function(event) {
                settings.latitudeElement.val(event.latLng.lat());
                settings.longitudeElement.val(event.latLng.lng());
            });

            google.maps.event.addListener(map, 'zoom_changed', function() {
                settings.zoomElement.val(map.getZoom());
            });

            settings.zoomElement.val(map.getZoom());
        };

        function updateMap(button)
        {
            if (button.hasClass('loading')) {
                return;
            }

            var buttonWas = button.html();
            button.html('Loading...');
            button.addClass('loading');

            var address = getAddressString();

            if(false == address){
                mapContainer.find('.map-message:first').html('<div class="alert alert-danger"><strong>Error: </strong> Please enter a full address.');
                button.html(buttonWas);
                button.removeClass('loading');
                return;
            }

            if(refreshingMap){
                refreshingMap.abort();
            }

            var geocodeUrl = "https://maps.googleapis.com/maps/api/geocode/json?address=" + address + "&region=" + settings.addressElements.countryCode.val();

            if (null !== settings.googleApiBrowserKey) {
                geocodeUrl += '&key=' + settings.googleApiBrowserKey;
            }

            refreshingMap = $.ajax({
                type: "POST",
                url: geocodeUrl,
                dataType:"json",
                error: function(response, error) {
                    if ("abort" != error) {
                        button.html(buttonWas);
                        button.removeClass('loading');
                        mapContainer.find('.map-message:first').html('<div class="alert alert-danger"><strong>Error: </strong> Failed to load map. Please try again.');
                    }
                },
                success: function(response) {
                    if(response.results[0]){
                        var resultLatLng = new google.maps.LatLng(response.results[0].geometry.location.lat, response.results[0].geometry.location.lng);

                        settings.latitudeElement.val(resultLatLng.lat());
                        settings.longitudeElement.val(resultLatLng.lng());

                        if (settings.zoomDefaults[response.results[0].geometry.location_type]) {
                            map.setZoom(settings.zoomDefaults[response.results[0].geometry.location_type])
                        } else {
                            map.setZoom(14);
                        }

                        marker.setPosition(resultLatLng);
                        map.panTo(resultLatLng);

                        button.html(buttonWas);
                        button.removeClass('loading');
                        mapContainer.find('.map-message:first').html('');
                    }
                    else {
                        button.html(buttonWas);
                        button.removeClass('loading');
                        mapContainer.find('.map-message:first').html('<div class="alert alert-danger"><strong>Error: </strong> Could not determine the latitude/longitude automatically.');
                    }
                }
            });
        }

        this.each(function() {
            mapContainer = $(this);

            mapContainer.find('.update-map').click(function(){
                updateMap($(this));
            });

            initMap();
        });
    }
}(jQuery));
