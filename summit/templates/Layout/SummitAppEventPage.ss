<div class="container">
    <h1>$Event.Title</h1>
    <hr>
    When: $Event.StartDate <br>
    Where: $Event.Location.Name <br>
    Capacity: $Event.Location.Capacity <br>
    Audience: $Event.AllowedSummitTypes.Audience <br>
    Description: $Event.Description<br>
    <hr>
    <% if Event.getSpeakers().toArray() %>
        Speakers: <br>
        <% loop Event.getSpeakers() %>
            <div class="row">
                <div class="speaker_pic col-md-2"> $ProfilePhoto(50) </div>
                <div class="speaker_profile col-md-6">
                    <div> $Title $FirstName $LastName </div>
                    <div> $CurrentAffiliation().Role, $CurrentAffiliation().Org().Name </div>
                    <div> $Bio </div>
                </div>
            </div>
        <% end_loop %>
        <hr>
    <% end_if %>


    <% if Event.Attendees() %>
        Attendees ($Event.Atendees.TotalItems): <br>
        <% loop Event.Attendees() %>
            <div class="attendee_pic col-md-2"> $Member.ProfilePhoto(50) </div>
        <% end_loop %>
    <% end_if %>
</div>
