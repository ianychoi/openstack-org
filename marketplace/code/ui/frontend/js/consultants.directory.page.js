/**
 * Copyright 2014 Openstack Foundation
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * http://www.apache.org/licenses/LICENSE-2.0
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 **/
jQuery(document).ready(function($){

    $('#name-term').autocomplete({
        source: 'consultants/names',
        minLength: 2,
        select: function (event, ui) {
            if(ui.item) $('#name-term').val(ui.item.value);
            $('.filter-label').trigger("click");
        }
    })
    .keydown(function (e) {
            if (e.keyCode === 13) {
                $('.filter-label').trigger("click");
            }
    });

    $('#service-term').prepend("<option value='' selected='selected'>-- Select a Service--</option>");
    $('#service-term').chosen({disable_search_threshold: 3, width:'auto'});
    $('#service-term').change(function () {
        $('.filter-label').trigger("click");
    });
    $('#location-term').prepend("<option value='' selected='selected'>-- Select a Location--</option>");
    $('#location-term').chosen({disable_search_threshold: 3, width:'auto'});
    $('#location-term').change(function () {
        $('.filter-label').trigger("click");
    });

    var last_filter_request = null;

    $('.filter-label').live('click', function (event) {
        var params = {
            name_term     : $('#name-term').val(),
            service_term  : $('#service-term').val(),
            location_term : $('#location-term').val()
        }
        if(last_filter_request!=null)
            last_filter_request.abort();

        $('#map').slideUp('slow');
        $('#show-map').show();

        last_filter_request = $.ajax({
                type:        "POST",
                url:         'consultants/search',
                contentType: "application/json; charset=utf-8",
                dataType:    "html",
                data:        JSON.stringify(params),
                success: function (data,textStatus,jqXHR) {
                    $('#consultants-list').html(data);
                    last_filter_request = null;
                },
                error: function (jqXHR,  textStatus,  errorThrown) {
                    if(errorThrown === 'abort') return;
                    $('#consultants-list').html('<div>There are no Consultants matching your criteria.</div>');
                    last_filter_request = null;
                }
        });
    });

    //init map widget

    var places = [];

    if(typeof(offices)!=='undefined' && offices.length > 0){
        places = offices;
    }

    $('#show-map').click(function(event){
        event.preventDefault();
        event.stopPropagation();
        $(this).hide();
        $('#map').slideDown( "slow" );
        return false;
    });

    $('#map').google_map({
        places : places,
        getInfo:function(place){
            return '<b>'+place.owner+'</b><br>'+
                '<b>'+place.name+'</b><br>'+
                place.address;
        }
    });

});