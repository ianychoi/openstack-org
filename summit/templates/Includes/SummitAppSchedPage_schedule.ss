 <% loop $Schedule %>
    <% loop $Me %>
        <% loop $Me %>
            <% if FirstOfDay %>
                <div class="clearfix"></div>
                <div class="day">$StartDate.Format('F j')</div>
                <div class="event_wrapper">
            <% end_if %>
                    <div class="event {$SummitTypes}" style="background-color:{$Type.Color}">
                        <div>
                            $Title
                            <div class="time">$StartDate.Format('g:ia') - $EndDate.Format('g:ia')</div>
                        </div>
                        <div>
                            <% if $getSpeakers() %>
                                <% loop $getSpeakers() %>
                                    $FirstName
                                <% end_loop %>
                            <% end_if %>
                        </div>
                    </div>
        <% end_loop %>
    <% end_loop %>
    </div>

<% end_loop %>