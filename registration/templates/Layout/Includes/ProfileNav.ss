<% if UpcomingSummit && UpcomingSummit.isAttendeesRegistrationOpened && not CurrentMember.getUpcomingSummitAttendee %>
    <div class="alert alert-success alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <strong>Hey!</strong> Attendees registration process is opened for $Top.UpcomingSummit.Title Summit.
        <a href="{$Link}attendeeInfoRegistration" class="alert-link">Check here!</a>
    </div>
<% end_if %>
<h2 class="profile-tabs">
<a href="{$Link}" <% if CurrentTab=1 %>class="active"<% end_if %> >Your Details</a>
<% if CurrentMember.isFoundationMember %>
<a href="{$Link}election" <% if CurrentTab=2 %>class="active"<% end_if %> >Election</a>
<% end_if %>
<a href="{$Link}agreements"  <% if CurrentTab=3 %>class="active"<% end_if %> >Legal Agreements</a>
<% if CurrentMember.isTrainingAdmin %>
	<a href="{$Link}training"  <% if CurrentTab=4 %>class="active"<% end_if %> >Training</a>
<% end_if %>
<% if CurrentMember.isMarketPlaceAdmin %>
    <a href="{$Link}marketplace-administration"  <% if CurrentTab=5 %>class="active"<% end_if %> >MarketPlace Administration</a>
<% end_if %>
<a href="{$Link}speaker"  <% if CurrentTab=7 %>class="active"<% end_if %> >Speaker Details</a>
<% if UpcomingSummit && UpcomingSummit.isAttendeesRegistrationOpened %>
    <a href="{$Link}attendeeInfoRegistration"  <% if CurrentTab=8 %>class="active"<% end_if %> >Attendee Registration</a>
<% end_if %>
$NavActionsExtensions
</h2>
