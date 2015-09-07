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

$(document).ready(function(){
    $('.summit_type_filter').click(function(){
        toggleCheckboxButton($(this));

        // hide using classes
        $('.summit_type_'+$(this).data('summit_type_id')).toggle();

        // apply event type filter
        var event_type_id = $('.summit_event_type_filter').val();
        if (event_type_id != -1) {
            $('.event').not('.event_type_'+event_type_id).hide();
        }

        // hide pulling filtered events
        /*var summit_type_ids = getSummitTypeFilters();
        var filters = {summit_types: summit_type_ids};
        getSchedule(filters);*/
    });

    $('.summit_event_type_filter').change(function(){
        var event_type_id = $(this).val();
        if (event_type_id != -1) {
            $('.event').hide();
            $('.event_type_'+event_type_id).show();
        } else {
            $('.event').show();
        }

        //apply summit type filter
        $('.summit_type_filter').each(function(){
            if (!$(this).hasClass('checked')) {
                $('.summit_type_'+$(this).data('summit_type_id')).hide();
            }
        });

    });

    var summit_type_ids = getSummitTypeFilters();
    var filters = {summit_types: summit_type_ids};
    getSchedule(filters);

});

function getSummitTypeFilters() {
    var summit_type_ids = '';
    $('.summit_type_filter').each(function(){
        if ($(this).hasClass('checked')) {
            summit_type_ids += $(this).data('summit_type_id')+',';
        }
    });

    summit_type_ids = summit_type_ids.slice(0, -1);
    return summit_type_ids;
}





function toggleCheckboxButton(button_elem) {
    var icon = $('.glyphicon',button_elem);
    button_elem.blur();

    button_elem.toggleClass('checked');

    if(icon.hasClass('glyphicon-unchecked')) {
        icon.removeClass('glyphicon-unchecked').addClass('glyphicon-check');

        button_elem.addClass('btn-primary').removeClass('btn-default');
    } else {
        icon.removeClass('glyphicon-check').addClass('glyphicon-unchecked');
        button_elem.addClass('btn-default').removeClass('btn-primary');

    }
}

function getSchedule(filters) {
    var summit_id = $('#summit_id').val();
    $.ajax({
        type: 'PUT',
        url: 'api/v1/summitapp/'+summit_id+'/get-schedule',
        data: JSON.stringify(filters),
        contentType: "application/json; charset=utf-8",
        success: function (schedule_html) {
            $('#schedule_container').html(schedule_html);
            $('.event').popover({
                placement: "right",
                trigger: "hover",
                html : true,
                content: function() {
                    return $(".description",this).html();
                }
            });
        },
        error: function (jqXHR, textStatus, errorThrown) {
            ajaxError(jqXHR, textStatus, errorThrown);
        }
    });
}