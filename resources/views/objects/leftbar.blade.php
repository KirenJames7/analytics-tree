    <!-- Left Sidebar -->
    <aside id="leftsidebar" class="sidebar">
    @include('objects.userinfo')
        <!-- Menu -->
        <div class="menu">
            <ul class="list">
                <li class="header">MAIN MENU</li>
                <li id="file" class="tag">
                    <a href="javascript:void(0);" class="menu-toggle">
                        <i class="material-icons">folder</i>
                        <span>File Management</span>
                    </a>
                        <ul class="ml-menu">
                            <li id="import" class="subtag">
                                <a href="javascript:void(0);" data-toggle="tooltip" data-placement="right" title="Import excel files">Import</a>
                            </li>
                            <li id="delete" class="subtag">
                                <a href="javascript:void(0);" data-toggle="tooltip" data-placement="right" title="Delete data set">Delete</a>
                            </li>
                        </ul>
                </li>
                <li id="reports" class="tag">
                    <a href="javascript:void(0);" class="menu-toggle">
                        <i class="material-icons">trending_up</i>
                        <span>Reports</span>
                    </a>
                        <ul class="ml-menu">
                            <li id="utilizationsummary" class="subtag">
                                <a href="javascript:void(0);" data-toggle="tooltip" data-placement="right" title="Load Utilization Summary">Utilization Summary</a>
                            </li>
                            <li id="associateutilizationsummary" class="subtag">
                                <a href="javascript:void(0);" data-toggle="tooltip" data-placement="right" title="Load Associate Utilization Summary">Associate Utilization Summary</a>
                            </li>
                        </ul>
                </li>
                <li class="tag" id="admin">
                    <a href="javascript:void(0);">
                        <i class="material-icons">security</i>
                        <span>Administration</span>
                    </a>
                </li>
                <li class="tag active" id="signout">
                    <a href="javascript:void(0);">
                        <i class="material-icons">input</i>
                        <span>Sign Out</span>
                    </a>
                </li>

            </ul>
        </div>
        <!-- #Menu -->
        @include('objects.footer')
    </aside>
    <!-- #END# Left Sidebar -->
    @show