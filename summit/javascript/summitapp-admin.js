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

    var form = $("#create_summit");

    //validation
    form_validator = form.validate({
        onfocusout: false,
        focusCleanup: true,
        ignore: [],
        rules: {
            summit_name: { required: true , ValidPlainText:true, maxlength: 100, minlength: 1},
            start_date: { required: true , date:true },
            end_date: { required: true , date:true}
        },
        focusInvalid: false,
        errorPlacement: function(error, element) {
            return false;
        }
    });

    form.submit(function( event ) {
        event.preventDefault();

        if(!form.valid()) return false;

        var url     = 'api/v1/summitapp/new-summit';
        var request = {
            name: $('#summit-name',form).val(),
            start_date: convertDateFormat($('#start-date',form).val()),
            end_date: convertDateFormat($('#end-date',form).val())
        };

        $.ajax({
            type: 'PUT',
            url: url,
            data: JSON.stringify(request),
            contentType: "application/json; charset=utf-8",
            success: function (summit_html) {
                $('tbody','#summit_table').append(summit_html);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                ajaxError(jqXHR, textStatus, errorThrown);
            }
        });
    });

    $('.delete_summit').click(function(){
        var summit_id = $(this).data('summit-id');
        $('.modal_delete_btn','#delete_summit_modal').data('summit-id',summit_id);
        $('.modal_summit_name','#delete_summit_modal').html($('.summit_name','#summit_'+summit_id).html());
    });

    $('.modal_delete_btn').click(function(){
        $("#delete_summit_modal").modal('hide');
        var summit_id = $(this).data('summit-id');

        $.ajax({
            type: 'PUT',
            url: 'api/v1/summitapp/'+summit_id+'/delete',
            success: function () {
                $('#summit_'+summit_id).remove();
            },
            error: function (jqXHR, textStatus, errorThrown) {
                ajaxError(jqXHR, textStatus, errorThrown);
            }
        });

    });


});

