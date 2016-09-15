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
jQuery(document).ready(function($) {

    //delete account
    $('.dupes-member-delete-account').click(function(e){
        e.preventDefault();
        var btn = $(this);

        swal({
            title: 'Are you sure?',
            text: 'You will not be able to recover this member account!',
            type: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'No, keep it',
        }).then(function() {
            var member_id = btn.attr('data-id');
            $.ajax({
                async:true,
                type: 'POST',
                url: 'api/v1/dupes-members/'+member_id+'/delete-request',
                dataType: "json",
                success: function (data,textStatus,jqXHR) {
                    btn.parent().fadeOut(500, function(){
                        var li = $(this);
                        checkEmptyWarning(li);
                        li.remove();
                        swal(
                            'About your request',
                            'Your request to delete the duplicate account has been sent to the email address on file for that account. If we do not receive a response for the delete request within 48 hours, we will restore the alert until it is dismissed from your account',
                            'success'
                        );
                    });
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    ajaxError( jqXHR, textStatus, errorThrown);
                }
            });
        });
        return false;
    });

    //merge
    $('.dupes-member-merge-account').click(function(e){
        e.preventDefault();
        var btn = $(this);
        swal({
            title: 'Are you sure?',
            text: 'Are you sure?',
            type: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, merge it!',
            cancelButtonText: 'No, keep it',
        }).then(function() {
            var member_id = btn.attr('data-id');
            $.ajax({
                async:true,
                type: 'POST',
                url: 'api/v1/dupes-members/'+member_id+'/merge-request',
                dataType: "json",
                success: function (data,textStatus,jqXHR) {
                    btn.parent().fadeOut(500, function(){
                        var li = $(this);
                        checkEmptyWarning(li);
                        li.remove();
                        swal(
                            'About your request',
                            'Your request to merge the duplicate account has been sent to the email address on file for that account. If we do not receive a response for the merge request within 48 hours, we will restore the alert until it is dismissed from your account',
                            'success'
                        );
                    });
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    ajaxError( jqXHR, textStatus, errorThrown);
                }
            });
        });
        return false;
    });

    $("#dupes-dismiss").click(function(e){
        swal({
            title: 'Are you sure?',
            text: 'Do you want to dismiss this warning?',
            type: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, dismiss it!',
            cancelButtonText: 'No, keep it',
        }).then(function() {
            $.ajax({
                async:true,
                type: 'PATCH',
                url: 'api/v1/dupes-members/show/profile/false',
                dataType: "json",
                success: function (data,textStatus,jqXHR) {

                },
                error: function (jqXHR, textStatus, errorThrown) {
                    ajaxError( jqXHR, textStatus, errorThrown);
                }
            });
        });
    });

    // not my account
    $('.dupes-member-not-my-account').click(function(e){
        e.preventDefault();
        var btn = $(this);
        swal({
            title: 'Are you sure?',
            text: 'Are you sure?',
            type: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'No, keep it',
        }).then(function() {
            var foreign_id = btn.attr('data-id');
            $.ajax({
                async:true,
                type: 'PATCH',
                url: 'api/v1/dupes-members/foreign-account/'+foreign_id,
                dataType: "json",
                success: function (data,textStatus,jqXHR) {
                    btn.parent().fadeOut(500, function(){
                        var li = $(this);
                        checkEmptyWarning(li);
                        li.remove();
                    });
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    ajaxError( jqXHR, textStatus, errorThrown);
                }
            });
        });
        return false;
    });
});

function checkEmptyWarning(li){
    var list  = li.parent();
    var count = $('li',list).length;
    if(count == 1){
        $('#dupes-email-warning-container').fadeOut(300, function(){
            $(this).remove();
        })
    }
    $('.span-qty').text(count - 1);
}