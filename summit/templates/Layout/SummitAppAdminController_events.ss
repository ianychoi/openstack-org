<div id="wrapper">
    <!-- Sidebar -->
    <div id="sidebar-wrapper">
        <% include SummitAdmin_SidebarMenu AdminLink=$Top.Link, SummitID=$Summit.ID %>
    </div><!-- /#sidebar-wrapper -->
    <!-- Page Content -->
    <div id="page-content-wrapper">
        <div class="page-header">
            <h1>$Summit.Title<small></small></h1>
        </div>
        <table class="table">
            <tbody>
                <% loop Events %>
                <tr>
                    <td>
                        $Title
                    </td>
                    <td>
                        $StartDate
                    </td>
                    <td>
                        $EndDate
                    </td>
                    <td>
                        $LocationName
                    </td>
                    <td>
                        $TypeName
                    </td>
                    <td>
                        <a href="$Top.Link/{$SummitID}/events/{$ID}}" class="btn btn-primary btn-sm active" role="button">Control Panel</a>
                    </td>
                    <td>
                        <button type="button" class="btn btn-danger active btn-sm">Delete</button>
                    </td>
                </tr>
                <% end_loop %>
        </table>

        <nav>
            <ul class="pagination">
                <li class="disabled">
                    <a href="#" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>
                <li  class="active"><a href="#">1</a></li>
                <li><a href="#">2</a></li>
                <li><a href="#">3</a></li>
                <li><a href="#">4</a></li>
                <li><a href="#">5</a></li>
                <li>
                    <a href="#" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
    <!-- /#page-content-wrapper -->