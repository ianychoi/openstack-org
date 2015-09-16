 <% loop $Schedule %>
    <% loop $Me %>
        <% loop $Me %>
            <% if FirstOfDay %>
                <div class="clearfix"></div>
                <div class="day">$StartDate.Format('F j')</div>
                <div class="event_wrapper">
            <% end_if %>
                    <div class="event {$SummitTypes} event_type_{$Type.ID}" style="background-color:{$Type.Color}">
                        <div>
                            $Title
                            <div class="time">$StartDate.Format('g:ia') - $EndDate.Format('g:ia')</div>
                            <div class="time">$getLocationName()</div>
                        </div>
                        <div>
                            <% if getSpeakers().toArray() %>
                                <span class="glyphicon glyphicon-volume-up"><span>
                                <% loop getSpeakers() %>
                                    $LastName
                                <% end_loop %>
                            <% end_if %>
                        </div>
                        <div class="event_details" id="event_details_$ID">
                            <% if Top.isAttendee($Summit.ID) %>
                                <% if Top.isScheduled($Summit.ID, $ID) %>
                                    <button onclick="removeFromSchedule($ID)" class="btn btn-xs btn-danger remove_from_schedule">Remove From My Schedule</button>
                                <% else %>
                                            <button onclick="addToSchedule($ID)" class="btn btn-xs btn-success add_to_schedule">Add to My Schedule</button>
                                <% end_if %>
                            <% end_if %>
                            <button type="button" data-event_id="$ID" class="btn btn-xs btn-info" data-toggle="button">Go to Event</button>
                            <div class="socials">
                                <div class="fb-like" data-href="http://openstack.org" data-layout="button" data-action="like" data-show-faces="false" data-share="false"></div>
                                <a href="https://twitter.com/share" class="twitter-share-button" data-via="tipit" data-count="none">Tweet</a>
                            </div>
                            <hr>
                            Date: $StartDate.Format('F j') ($StartDate.Format('g:ia') - $EndDate.Format('g:ia')) <br>
                            Location: $getLocationName() <br>
                            Summary:
                            <div class="description">
                                $Description
                            </div>
                            <hr>
                            Topics:<br>
                            <% loop getTopics() %>
                                $Title
                            <% end_loop %>
                            <hr>
                            Speakers:<br>
                            <% loop getSpeakers() %>
                                $ProfilePhoto(50)
                            <% end_loop %>
                        </div>
                    </div>
        <% end_loop %>
    <% end_loop %>
                </div>
<% end_loop %>