<div class="row">
        <div class="jumbotron">
            <h1>Summit Directory</h1>
        </div>
</div>
<div class ="row">
    <div class="col-md-12">
        <form class="form-inline">
            <div class="form-group">
                <label for="summit-name">Summit Name</label>
                <input type="text" class="form-control" id="summit-name" placeholder="Summit Name">
            </div>
            <div class="form-group">
                <label for="start-date">Start Date</label>
                <input type="text" class="form-control" id="start-date" placeholder="mm/dd/YYYY">
            </div>
            <div class="form-group">
                <label for="end-date">End Date</label>
                <input type="text" class="form-control" id="end-date" placeholder="mm/dd/YYYY">
            </div>
            <button type="submit" class="btn btn-lg btn-success active">Create New Summit</button>
        </form>
    </div>
</div>
<div class ="row" style="padding-top: 2em;">
    <div class="col-md-12">
        <table class="table">
            <tbody>
            <% loop Summits %>
                <tr>
                    <td>
                        $Title
                    </td>
                    <td>
                        $SummitBeginDate
                    </td>
                    <td>
                        $SummitEndDate
                    </td>
                    <td>
                        <a href="$Top.Link/{$ID}/dashboard" class="btn btn-primary btn-sm active" role="button">Control Panel</a>
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger active btn-sm">Delete</button>
                    </td>
                </tr>
            <% end_loop %>
            </tbody>
        </table>
    </div>
</div>