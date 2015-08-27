<% if IncludeFormTag %>
    <form $FormAttributes role="form">
<% end_if %>
<% if Message %>
        <p id="{$FormName}_error" class="message $MessageType">$Message</p>
<% else %>
        <p id="{$FormName}_error" class="message $MessageType" style="display: none"></p>
<% end_if %>


<div class="row form-inline">
    <div class="col-md-4">
        <label for="$FormName_Name" class="left">Summit</label>
        $Fields.dataFieldByName(Name)
    </div>
    <div class="col-md-4">
        <label for="$FormName_SummitBeginDate" class="left">Begin Date</label>
        $Fields.dataFieldByName(SummitBeginDate)
    </div>
    <div class="col-md-4">
        <label for="$FormName_SummitEndDate" class="left">End Date</label>
        $Fields.dataFieldByName(SummitEndDate)
    </div>
</div>
<hr>
<label> Event Types </label>
$Fields.dataFieldByName(EventTypes)


<fieldset>


</fieldset>



<% if Actions %>
        <div class="Actions">
            <% loop Actions %>
                $Field
            <% end_loop %>
        </div>
<% end_if %>
<% if IncludeFormTag %>
    </form>
<% end_if %>