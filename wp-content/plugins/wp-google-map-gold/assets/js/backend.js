(function($) {
    "use strict";

    $(document).ready(function() {

        if(typeof google_customizer_fonts !== 'undefined'){

            var result = Object.keys(google_customizer_fonts).map(function(key) {
              return google_customizer_fonts[key];
            });

            if (result && result.length > 0) {
                for (var i in result ) {
                    var font = result[i];

                    if (font.indexOf(',') >= 0) {
                        font = font.split(",");
                        font = font[0];
                    }
                    if (font.indexOf('"') >= 0) {
                        font = font.replace('"', '');
                        font = font.replace('"', '');
                    }
                    WebFont.load({
                        google: {
                            families: [font]
                        }
                    });

                }
            }
        }

        var wpgmp_timeouts = [];
        $('body').on('click', 'input[name="fc-geocoding-abort"]', function(e) {

            $(this).hide();
            var new_locations = $(this).parent().parent().find('.fc-location-new-set');
            var progress = $(this).parent().parent().find('.fc-geocoding-progress');
            var geocode_instructions = settings_obj.geocode_success;
            var geocode_stats = settings_obj.geocode_stats;
            for (var i = 0; i < wpgmp_timeouts.length; i++) {
                clearTimeout(wpgmp_timeouts[i]);
            }

            $('.fcdoc-loader').hide();
            $('.fc-geocoding').hide();
            $(progress).hide();
            if ($(new_locations).val() != '') {
                var final_records = JSON.parse($(new_locations).val());
                $('.wpgmp-status').html('<div class="fc-msg fc-success">' + final_records.length + " " + geocode_stats + '. ' + geocode_instructions + ".</div>").show();
                $('.fc-geocoding-updates').show();
            } else {
                $('.wpgmp-status').html('<div class="fc-msg fc-danger"> 0 ' + geocode_stats + '.</div>').show();
            }

        });
        $('body').on('click', 'input[name="fc-geocoding"]', function(e) {
            e.preventDefault();
            $(this).hide();
            $(".wpgmp_geo_adv").hide();
            $(this).parent().parent().find('.fcdoc-loader').show();
            $(this).parent().parent().find('.fc-geocoding-abort').show();
            var progress = $(this).parent().parent().find('.fc-geocoding-progress');
            var is_advanced = $("input[name='wpgmp_geo_adv']").is(":checked");
            var new_locations = $(this).parent().parent().find('.fc-location-new-set');
            var geocode_instructions = settings_obj.geocode_success;
            var geocode_stats = settings_obj.geocode_stats;
            /* Start Geocoding */
            var source_csv_records = JSON.parse($(this).parent().parent().find('.fc-location-data-set').val());
            var final_records = [];
            var delay = 1000; //0.5 second
            var geocoder = new google.maps.Geocoder();
            var object_count = 0;
            $.each(source_csv_records, function(id, csv_record) {
                object_count++;
            });

            var new_object_count = 0;
            $.each(source_csv_records, function(id, csv_record) {

                wpgmp_timeouts[new_object_count] = setTimeout(function() {

                    var geocode_options = {
                        'address': csv_record.address
                    };

                    if (is_advanced === true) {

                        var componentRestrictions = {};

                        if (csv_record.country) {
                            componentRestrictions["country"] = csv_record.country;
                        }

                        if (csv_record.postal_code) {
                            componentRestrictions["postalCode"] = csv_record.postal_code;
                        }

                        if (componentRestrictions.country || componentRestrictions.postalCode) {
                            geocode_options["componentRestrictions"] = componentRestrictions;
                        }

                        if (csv_record.state) {
                            geocode_options["region"] = csv_record.state;
                        }

                    }

                    geocoder.geocode(geocode_options, function(results, status) {

                        if (results != null && results.length > 0) {
                            var lat = results[0].geometry.location.lat() ? results[0].geometry.location.lat() : '';
                            var lng = results[0].geometry.location.lng() ? results[0].geometry.location.lng() : '';
                            var current_record_output = '{"id":"' + id + '","latitude": "' + lat + '", "longitude": "' + lng + '"}';
                            var current_record_output_obj = JSON.parse(current_record_output);
                            final_records.push(current_record_output_obj);
                            $(new_locations).val(JSON.stringify(final_records));
                            $(progress).html(final_records.length + ' ' + geocode_stats + '.');
                        }

                        if (new_object_count == object_count) {

                        }
                    });
                }, delay);
                new_object_count++;
                delay += 500;
            });

            setTimeout(function() {
                $('.fcdoc-loader').hide();
                $('.fc-geocoding').hide();
                $('.fc-geocoding-abort').hide();
                $('.fc-geocoding-progress').hide();

                if ($(new_locations).val() != '') {
                    var final_records = JSON.parse($(new_locations).val());
                    $('.wpgmp-status').html('<div class="fc-msg fc-success">' + final_records.length + " " + geocode_stats + '. ' + geocode_instructions + ".</div>").show();
                    $('.fc-geocoding-updates').show();
                } else {
                    $('.wpgmp-status').html('<div class="fc-msg fc-danger"> 0 ' + geocode_stats + '.</div>').show();
                }

            }, delay + 2000);

        });

        $('.wpgmp_datepicker').datepicker({
            dateFormat: 'dd-mm-yy'
        });

        var wpgmp_image_id = '';
        //intialize add more...

        $(".wpgmp_check_key").click(function() {
            $('.wpgmp_maps_preview').html("...");
            var wpgmp_maps_key = $("input[name='wpgmp_api_key']").val();
            var address = 'london';
            $.get("https://maps.googleapis.com/maps/api/geocode/json?address=1600+Amphitheatre+Parkway,+Mountain+View,+CA&key=" + wpgmp_maps_key, function(data) {
                if (data.status == 'OK') {
                    $('.wpgmp_maps_preview').html("Perfect!");
                } else {
                    $('.wpgmp_maps_preview').html(data.error_message);
                }

            });

        });

        $(".cancel_import").click(function() {
            var wpgmp_bid = confirm("Do you want to cancel import process?.");
            if (wpgmp_bid == true) {
                $(this).closest("form").find("input[name='operation']").val("cancel_import");
                $(this).closest("form").submit();
                return true;
            } else {
                return false;
            }
        });

        $("select[name='map_id']").change(function() {
            $(this).closest('form').submit();
        });
        $("select[name='filter_location']").change(function(event) {

            event.preventDefault();

            var what_value = $(this).val();

            if (what_value > 0) {
                $("tr[class^='filter_group_cat']").hide();
                $(".filter_group_cat" + $(this).val()).show("slow");
            } else {
                $("tr[class^='filter_group_cat']").show("slow");
            }

        });

        $(".wpgmp_search_input").keyup(function() {
            map_id = $(this).attr("rel");
            $(".wpgmp_locations_listing[rel='" + map_id + "']").addClass("wpgmp_loading");
            wpgmp_filter_locations(map_id, 1);
        });

        $(".wpgmp_toggle_container").click(function() {
            $(".wpgmp_toggle_main_container").toggle("slow");
            if ($(this).text() == "Hide") {
                $(this).text("Show");
            } else {
                $(this).text("Hide");
            }
        });

        $(".wpgmp_mcurrent_loction").click(function() {
            wpgmp_get_current_location();
        });

        $(".wpgmp-select-all").click(function() {
            var checkAll = $(".wpgmp-select-all").prop('checked');
            if (checkAll) {
                $(this).closest('table').find(".wpgmp-location-checkbox").prop("checked", true);
            } else {
                $(this).closest('table').find(".wpgmp-location-checkbox").prop("checked", false);
            }
        });

        $(".wpgmp-location-checkbox").click(function() {
            if ($(".wpgmp-location-checkbox").length == $(".wpgmp-location-checkbox:checked").length) {
                $(".wpgmp-select-all").prop("checked", true);
            } else {
                $(".wpgmp-select-all").prop("checked", false);
            }
        });

        var maptable = $('#wpgmp_google_map_data_table').dataTable({
            "lengthMenu": [
                [10, 25, 50, 100, 200, 500, -1],
                [10, 25, 50, 100, 200, 500, "All"]
            ],
            "order": [
                [1, "desc"]
            ],
            "aoColumns": [{
                sWidth: '5%',
                "bSortable": false
            }, {
                sWidth: '40%'
            }, {
                sWidth: '30%'
            }, {
                sWidth: '20%'
            }]
        });

        var route_maptable = $('#wpgmp_google_map_route_data_table').dataTable({
            "lengthMenu": [
                [10, 25, 50, 100, 200, 500, -1],
                [10, 25, 50, 100, 200, 500, "All"]
            ],
            "aoColumns": [{
                sWidth: '10%'
            }, {
                sWidth: '35%'
            }, {
                sWidth: '35%'
            }, {
                sWidth: '20%'
            }]
        });

        $('input[name="save_entity_data"]').click(function() {
            var data = maptable.$('input[type="checkbox"]:checked');
            var selected_val = [];
            if (data.length > 0) {
                $.each(data, function(index, chk) {
                    selected_val.push($(chk).val());
                });
                $('input[name="map_locations"]').val(selected_val);
            }

            return true;
        });

        $('select[name="select_all"]').change(function() {
            if ($(this).val() == 'select_all')
                $('input[name="map_locations[]"]').attr('checked', true);
            else
                $('input[name="map_locations[]"]').attr('checked', false);

        });

        $('input[name="save_route_data"]').click(function() {
            var data = maptable.$('input[type="checkbox"]:checked');
            var selected_val = [];
            if (data.length > 0) {
                $.each(data, function(index, chk) {
                    selected_val.push($(chk).val());
                });
                $('input[name="route_way_points"]').val(selected_val);
            }

            return true;
        });

        $('.switch_onoff').change(function() {
            var target = $(this).data('target');
            if ($(this).attr('type') == 'radio') {
                $(target).closest('.form-group').hide();
                target += '_' + $(this).val();
            }
            if ($(this).is(":checked")) {
                $(target).closest('.form-group').show();
            } else {
                $(target).closest('.form-group').hide();
                if ($(target).hasClass('switch_onoff')) {
                    $(target).attr('checked', false);
                    $(target).trigger("change");
                }
            }


        });

        $.each($('.switch_onoff'), function(index, element) {
            if (true == $(this).is(":checked")) {
                $(this).trigger("change");
            }

        });

        $('.wpgmp-overview .color').wpColorPicker();

    });

    var re = /([^&=]+)=?([^&]*)/g;
    var decodeRE = /\+/g; // Regex for replacing addition symbol with a space
    var decode = function(str) {
        return decodeURIComponent(str.replace(decodeRE, " "));
    };
    $.parseParams = function(query) {
        var params = {},
            e;
        while (e = re.exec(query)) {
            var k = decode(e[1]),
                v = decode(e[2]);
            if (k.substring(k.length - 2) === '[]') {
                k = k.substring(0, k.length - 2);
                (params[k] || (params[k] = [])).push(v);
            } else params[k] = v;
        }
        return params;
    };

})(jQuery);

function send_icon_to_map(imagesrc, target) {
        jQuery('#remove_image_' + target).show();
        jQuery('#image_' + target).attr('src', imagesrc).show();
        jQuery('#input_' + target).val(imagesrc);
        tb_remove();
}