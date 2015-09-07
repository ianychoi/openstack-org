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
                        <div class="description">
                            $Description
                        </div>
                    </div>
        <% end_loop %>
    <% end_loop %>
    </div>

<% end_loop %>